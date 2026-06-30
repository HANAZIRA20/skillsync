<?php

namespace App\Http\Controllers\Analytics;

use App\Http\Controllers\Controller;
use App\Services\AnalyticsService;
use Illuminate\Support\Facades\Auth;

class AnalyticsController extends Controller
{
    public function __construct(private AnalyticsService $analyticsService) {}

    public function dashboard()
    {
        $studentAnalytics = $this->analyticsService->getStudentAnalytics();
        $matchingAnalytics = $this->analyticsService->getMatchingAnalytics();
        $revenueAnalytics = $this->analyticsService->getRevenueAnalytics();
        $projectAnalytics = $this->analyticsService->getProjectAnalytics();
        $clientAnalytics = $this->analyticsService->getClientAnalytics();

        return view('analytics.dashboard', compact(
            'studentAnalytics',
            'matchingAnalytics',
            'revenueAnalytics',
            'projectAnalytics',
            'clientAnalytics'
        ));
    }
}
