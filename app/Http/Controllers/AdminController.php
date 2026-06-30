<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Project;
use App\Models\Payment;
use App\Models\Student;
use App\Models\Client;
use App\Services\AnalyticsService;

class AdminController extends Controller
{
    public function __construct(private AnalyticsService $analyticsService) {}

    public function dashboard()
    {
        $stats = [
            'total_users' => User::count(),
            'total_students' => Student::count(),
            'total_clients' => Client::count(),
            'total_projects' => Project::count(),
            'active_projects' => Project::whereIn('status', ['in_progress', 'in_review', 'revision'])->count(),
            'completed_projects' => Project::where('status', 'completed')->count(),
            'total_revenue' => Payment::where('status', 'released')->sum('platform_fee'),
            'held_amount' => Payment::where('status', 'held')->sum('amount'),
            'disputed_projects' => Project::where('status', 'disputed')->count(),
        ];

        $recentUsers = User::latest()->limit(10)->get();
        $recentProjects = Project::with('client.user', 'selectedStudent.user')->latest()->limit(10)->get();
        $recentPayments = Payment::with('project', 'client.user')->latest()->limit(10)->get();
        $disputedProjects = Project::where('status', 'disputed')->with('client.user', 'selectedStudent.user')->get();

        return view('admin.dashboard', compact('stats', 'recentUsers', 'recentProjects', 'recentPayments', 'disputedProjects'));
    }

    public function resolveDispute(Project $project, string $action)
    {
        abort_if(!in_array($action, ['release', 'refund']), 400);

        if ($action === 'release') {
            $project->payment->update(['status' => 'released', 'released_at' => now()]);
            $project->update(['status' => 'completed']);
            $msg = 'Dana dicairkan ke mahasiswa.';
        } else {
            $project->payment->update(['status' => 'refunded', 'refunded_at' => now()]);
            $project->update(['status' => 'cancelled']);
            $msg = 'Dana dikembalikan ke client.';
        }

        return back()->with('success', "Dispute diselesaikan: {$msg}");
    }
}
