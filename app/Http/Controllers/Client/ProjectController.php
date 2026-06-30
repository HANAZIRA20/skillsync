<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\Matching;
use App\Models\FactProjectActivity;
use App\Services\MatchingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProjectController extends Controller
{
    public function __construct(private MatchingService $matchingService) {}

    public function index()
    {
        $client = Auth::user()->client;
        $projects = Project::where('client_id', $client->id)
            ->with(['selectedStudent.user', 'payment'])
            ->latest()
            ->paginate(10);

        $stats = [
            'total_posted' => $client->total_projects_posted,
            'total_completed' => $client->total_projects_completed,
            'active' => Project::where('client_id', $client->id)->whereIn('status', ['in_progress', 'in_review', 'revision'])->count(),
            'total_spent' => $client->total_spent,
        ];

        return view('client.projects', compact('projects', 'client', 'stats'));
    }

    public function create()
    {
        return view('client.create-project');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string|min:50',
            'category' => 'required|string|max:100',
            'required_skills' => 'required|array|min:1',
            'required_skills.*' => 'string|max:50',
            'budget_min' => 'required|numeric|min:50000',
            'budget_max' => 'required|numeric|gte:budget_min',
            'deadline' => 'required|date|after:today',
        ]);

        $client = Auth::user()->client;

        $project = Project::create([
            'client_id' => $client->id,
            'title' => $request->title,
            'description' => $request->description,
            'category' => $request->category,
            'required_skills' => $request->required_skills,
            'budget_min' => $request->budget_min,
            'budget_max' => $request->budget_max,
            'deadline' => $request->deadline,
            'duration_days' => now()->diffInDays($request->deadline),
            'status' => 'open',
            'max_revisions' => $request->max_revisions ?? 3,
        ]);

        // Update client stats
        $client->increment('total_projects_posted');

        // Record fact
        FactProjectActivity::record([
            'project_id' => $project->id,
            'client_id' => $client->id,
            'activity_type' => 'project_created',
            'activity_category' => 'client',
            'project_title' => $project->title,
            'project_category' => $project->category,
            'amount' => $project->budget_max,
            'client_industry' => $client->industry,
            'client_company' => $client->company_name,
            'skills_involved' => $project->required_skills,
            'project_status' => 'open',
        ]);

        // Run AI matching immediately
        $this->matchingService->runMatchingForProject($project);

        return redirect()->route('client.candidates', $project->id)
            ->with('success', 'Proyek berhasil dibuat! AI sedang mencarikan kandidat terbaik untuk Anda.');
    }

    public function show(Project $project)
    {
        $client = Auth::user()->client;
        abort_if($project->client_id !== $client?->id, 403);
        $project->load(['selectedStudent.user', 'client.user', 'workroom', 'payment']);
        return view('client.project-detail', compact('project'));
    }

    public function candidates(Project $project)
    {
        $client = Auth::user()->client;
        abort_if($project->client_id !== $client->id, 403);

        $matchings = $project->matchings()
            ->with('student.user', 'student.skills')
            ->orderByDesc('match_score')
            ->get();

        // Re-run matching if no candidates yet
        if ($matchings->isEmpty()) {
            $this->matchingService->runMatchingForProject($project);
            $matchings = $project->matchings()->with('student.user', 'student.skills')->orderByDesc('match_score')->get();
        }

        return view('client.candidates', compact('project', 'matchings'));
    }

    public function selectCandidate(Request $request, Project $project)
    {
        $client = Auth::user()->client;
        abort_if($project->client_id !== $client->id, 403);
        abort_if($project->status !== 'open', 400, 'Proyek tidak dalam status Open.');

        $request->validate([
            'student_id' => 'required|exists:students,id',
            'agreed_budget' => 'required|numeric|min:50000',
        ]);

        $matching = Matching::where('project_id', $project->id)
            ->where('student_id', $request->student_id)
            ->firstOrFail();

        // Update project
        $project->update([
            'selected_student_id' => $request->student_id,
            'agreed_budget' => $request->agreed_budget,
            'status' => 'waiting_payment',
        ]);

        // Update matching status
        $matching->update(['status' => 'selected', 'selected_at' => now()]);

        // Reject others
        Matching::where('project_id', $project->id)
            ->where('student_id', '!=', $request->student_id)
            ->update(['status' => 'rejected']);

        FactProjectActivity::record([
            'project_id' => $project->id,
            'client_id' => $client->id,
            'student_id' => $request->student_id,
            'activity_type' => 'candidate_selected',
            'activity_category' => 'client',
            'match_score' => $matching->match_score,
            'amount' => $request->agreed_budget,
            'project_title' => $project->title,
            'project_status' => 'waiting_payment',
        ]);

        return redirect()->route('payment.escrow', $project->id)
            ->with('success', 'Kandidat berhasil dipilih! Silakan lakukan pembayaran escrow.');
    }

    public function dashboard()
    {
        $user = Auth::user();
        $client = $user->client;

        $recentProjects = Project::where('client_id', $client->id)
            ->with(['selectedStudent.user', 'payment'])
            ->latest()
            ->limit(5)
            ->get();

        $stats = [
            'total_posted' => $client->total_projects_posted,
            'total_completed' => $client->total_projects_completed,
            'total_spent' => $client->total_spent,
            'active_projects' => Project::where('client_id', $client->id)
                ->whereIn('status', ['in_progress', 'in_review', 'waiting_payment', 'revision'])->count(),
        ];

        return view('client.dashboard', compact('user', 'client', 'recentProjects', 'stats'));
    }
}
