<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\Matching;
use App\Services\AIService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StudentController extends Controller
{
    public function dashboard()
    {
        $user = Auth::user();
        $student = $user->student;

        $activeProjects = collect();
        $recommendedProjects = collect();
        $recentMatchings = collect();

        if ($student) {
            // Active projects (in_progress, in_review, revision)
            $activeProjects = Project::where('selected_student_id', $student->id)
                ->whereIn('status', ['in_progress', 'in_review', 'revision'])
                ->with('client.user')
                ->latest()
                ->limit(5)
                ->get();

            // AI-recommended projects (matchings with high score)
            $recentMatchings = Matching::where('student_id', $student->id)
                ->where('status', 'pending')
                ->with('project.client')
                ->orderByDesc('match_score')
                ->limit(6)
                ->get();

            // Open projects for exploration
            $recommendedProjects = Project::where('status', 'open')
                ->whereNotIn('id', $recentMatchings->pluck('project_id'))
                ->latest()
                ->limit(4)
                ->get();
        }

        $stats = [
            'total_projects' => $student?->total_projects ?? 0,
            'total_earnings' => $student?->total_earnings ?? 0,
            'average_rating' => $student?->average_rating ?? 0,
            'total_skills' => $student?->skills()->count() ?? 0,
            'krs_status' => $student?->krs_status ?? 'not_uploaded',
            'portfolio_count' => $student?->portfolios()->count() ?? 0,
        ];

        return view('student.dashboard', compact('user', 'student', 'activeProjects', 'recentMatchings', 'recommendedProjects', 'stats'));
    }

    public function profile()
    {
        $user = Auth::user();
        $student = $user->student->load('skills', 'portfolios.project');
        return view('student.profile', compact('user', 'student'));
    }

    public function updateProfile(Request $request)
    {
        $request->validate([
            'bio' => 'nullable|string|max:500',
            'linkedin_url' => 'nullable|url',
            'github_url' => 'nullable|url',
            'phone' => 'nullable|string|max:20',
        ]);

        Auth::user()->update(['phone' => $request->phone]);
        Auth::user()->student->update($request->only('bio', 'linkedin_url', 'github_url'));

        return back()->with('success', 'Profil berhasil diperbarui!');
    }

    public function projects()
    {
        $student = Auth::user()->student;
        $projects = Project::where('selected_student_id', $student->id)
            ->with('client.user', 'payment')
            ->latest()
            ->paginate(10);

        return view('student.projects', compact('projects', 'student'));
    }
}
