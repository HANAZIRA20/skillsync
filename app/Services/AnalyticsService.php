<?php

namespace App\Services;

use App\Models\FactProjectActivity;
use App\Models\Student;
use App\Models\Project;
use App\Models\Payment;
use App\Models\Matching;
use Illuminate\Support\Facades\DB;

class AnalyticsService
{
    /**
     * Student Analytics - OLAP Query
     */
    public function getStudentAnalytics(): array
    {
        $topStudents = FactProjectActivity::query()
            ->where('activity_type', 'payment_released')
            ->whereNotNull('student_id')
            ->select('student_id', 'student_universitas', DB::raw('COUNT(*) as total_projects'), DB::raw('SUM(amount) as total_earned'), DB::raw('AVG(rating) as avg_rating'))
            ->groupBy('student_id', 'student_universitas')
            ->orderByDesc('total_projects')
            ->limit(10)
            ->get();

        $topByScore = Matching::with('student.user')
            ->select('student_id', DB::raw('AVG(match_score) as avg_score'), DB::raw('COUNT(*) as total_matched'))
            ->groupBy('student_id')
            ->orderByDesc('avg_score')
            ->limit(5)
            ->get();

        $universityPerformance = FactProjectActivity::query()
            ->where('activity_type', 'payment_released')
            ->whereNotNull('student_universitas')
            ->select('student_universitas', DB::raw('COUNT(*) as projects'), DB::raw('AVG(rating) as avg_rating'), DB::raw('SUM(amount) as total_earned'))
            ->groupBy('student_universitas')
            ->orderByDesc('projects')
            ->limit(10)
            ->get();

        $totalStudents = Student::count();
        $activeStudents = Student::where('krs_status', 'parsed')->count();
        $totalEarnings = Payment::where('status', 'released')->sum('student_amount');

        return compact('topStudents', 'topByScore', 'universityPerformance', 'totalStudents', 'activeStudents', 'totalEarnings');
    }

    /**
     * AI Matching Analytics
     */
    public function getMatchingAnalytics(): array
    {
        $avgScore = Matching::avg('match_score');
        $totalMatches = Matching::count();
        $acceptedMatches = Matching::where('status', 'selected')->count();
        $acceptanceRate = $totalMatches > 0 ? round(($acceptedMatches / $totalMatches) * 100, 1) : 0;

        // Top skills that match most
        $skillFrequency = [];
        $matchings = Matching::whereNotNull('matched_skills')->get();
        foreach ($matchings as $m) {
            foreach ($m->matched_skills ?? [] as $skill) {
                $skillName = $skill['required'] ?? '';
                if ($skillName) {
                    $skillFrequency[$skillName] = ($skillFrequency[$skillName] ?? 0) + 1;
                }
            }
        }
        arsort($skillFrequency);
        $topSkills = array_slice($skillFrequency, 0, 10, true);

        $scoreDistribution = [
            '90-100' => Matching::where('match_score', '>=', 90)->count(),
            '75-89' => Matching::whereBetween('match_score', [75, 89])->count(),
            '60-74' => Matching::whereBetween('match_score', [60, 74])->count(),
            '40-59' => Matching::whereBetween('match_score', [40, 59])->count(),
            '0-39' => Matching::where('match_score', '<', 40)->count(),
        ];

        $universityTopScore = FactProjectActivity::query()
            ->where('activity_type', 'ai_matched')
            ->whereNotNull('student_universitas')
            ->select('student_universitas', DB::raw('AVG(match_score) as avg_score'), DB::raw('COUNT(*) as count'))
            ->groupBy('student_universitas')
            ->orderByDesc('avg_score')
            ->limit(5)
            ->get();

        return compact('avgScore', 'totalMatches', 'acceptedMatches', 'acceptanceRate', 'topSkills', 'scoreDistribution', 'universityTopScore');
    }

    /**
     * Revenue Analytics
     */
    public function getRevenueAnalytics(): array
    {
        $totalRevenue = Payment::where('status', 'released')->sum('platform_fee');
        $totalTransactions = Payment::count();
        $successfulPayments = Payment::where('status', 'released')->count();
        $heldAmount = Payment::where('status', 'held')->sum('amount');
        $totalRefunds = Payment::where('status', 'refunded')->sum('amount');
        $refundCount = Payment::where('status', 'refunded')->count();

        $monthlyRevenue = FactProjectActivity::query()
            ->where('activity_type', 'payment_released')
            ->select('activity_month', 'activity_year', DB::raw('SUM(platform_fee) as revenue'), DB::raw('COUNT(*) as transactions'))
            ->groupBy('activity_year', 'activity_month')
            ->orderBy('activity_year')->orderBy('activity_month')
            ->limit(12)
            ->get();

        $revenueByCategory = FactProjectActivity::query()
            ->where('activity_type', 'payment_released')
            ->whereNotNull('project_category')
            ->select('project_category', DB::raw('SUM(platform_fee) as revenue'), DB::raw('COUNT(*) as count'))
            ->groupBy('project_category')
            ->orderByDesc('revenue')
            ->limit(8)
            ->get();

        return compact('totalRevenue', 'totalTransactions', 'successfulPayments', 'heldAmount', 'totalRefunds', 'refundCount', 'monthlyRevenue', 'revenueByCategory');
    }

    /**
     * Project Analytics
     */
    public function getProjectAnalytics(): array
    {
        $totalProjects = Project::count();
        $activeProjects = Project::whereIn('status', ['in_progress', 'in_review', 'revision'])->count();
        $completedProjects = Project::where('status', 'completed')->count();
        $openProjects = Project::where('status', 'open')->count();

        $statusDistribution = Project::select('status', DB::raw('COUNT(*) as count'))
            ->groupBy('status')
            ->get()
            ->pluck('count', 'status')
            ->toArray();

        $avgBudget = Project::avg('agreed_budget') ?? Project::avg('budget_max');
        $highestBudget = Project::orderByDesc('budget_max')->first();

        $categoryPopularity = Project::select('category', DB::raw('COUNT(*) as count'))
            ->whereNotNull('category')
            ->groupBy('category')
            ->orderByDesc('count')
            ->limit(8)
            ->get();

        $mostRevised = Project::orderByDesc('revision_count')->limit(5)->with('client')->get();

        $avgDuration = FactProjectActivity::where('activity_type', 'payment_released')
            ->whereNotNull('duration_days')
            ->avg('duration_days');

        return compact('totalProjects', 'activeProjects', 'completedProjects', 'openProjects', 'statusDistribution', 'avgBudget', 'highestBudget', 'categoryPopularity', 'mostRevised', 'avgDuration');
    }

    /**
     * Client Analytics
     */
    public function getClientAnalytics(): array
    {
        $totalClients = \App\Models\Client::count();
        $activeClients = \App\Models\Client::where('total_projects_posted', '>', 0)->count();

        $topClients = \App\Models\Client::with('user')
            ->orderByDesc('total_projects_posted')
            ->limit(10)
            ->get();

        $industryDistribution = \App\Models\Client::select('industry', DB::raw('COUNT(*) as count'))
            ->whereNotNull('industry')
            ->groupBy('industry')
            ->orderByDesc('count')
            ->limit(8)
            ->get();

        $avgBudgetByIndustry = FactProjectActivity::query()
            ->where('activity_type', 'payment_released')
            ->whereNotNull('client_industry')
            ->select('client_industry', DB::raw('AVG(amount) as avg_budget'), DB::raw('COUNT(*) as projects'))
            ->groupBy('client_industry')
            ->orderByDesc('avg_budget')
            ->limit(8)
            ->get();

        $completionRates = \App\Models\Client::select('id', 'company_name', 'total_projects_posted', 'total_projects_completed')
            ->where('total_projects_posted', '>', 0)
            ->orderByDesc('total_projects_completed')
            ->limit(10)
            ->get()
            ->map(function ($c) {
                $c->completion_rate = $c->total_projects_posted > 0
                    ? round(($c->total_projects_completed / $c->total_projects_posted) * 100, 1)
                    : 0;
                return $c;
            });

        return compact('totalClients', 'activeClients', 'topClients', 'industryDistribution', 'avgBudgetByIndustry', 'completionRates');
    }
}
