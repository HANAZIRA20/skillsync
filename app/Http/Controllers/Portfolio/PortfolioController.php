<?php

namespace App\Http\Controllers\Portfolio;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\Portfolio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PortfolioController extends Controller
{
    public function index()
    {
        $student = Auth::user()->student;
        $portfolios = Portfolio::where('student_id', $student->id)
            ->with('project')
            ->latest('completed_at')
            ->get();

        $stats = [
            'total' => $portfolios->count(),
            'verified' => $portfolios->where('is_verified', true)->count(),
            'avg_rating' => $portfolios->whereNotNull('rating')->avg('rating'),
            'total_earned' => $portfolios->sum('earned_amount'),
        ];

        return view('student.portfolio', compact('student', 'portfolios', 'stats'));
    }

    public function show(Student $student)
    {
        // Public portfolio page
        $portfolios = Portfolio::where('student_id', $student->id)
            ->where('is_public', true)
            ->with('project')
            ->latest('completed_at')
            ->get();

        return view('student.public-portfolio', compact('student', 'portfolios'));
    }

    public function toggleVisibility(Portfolio $portfolio)
    {
        abort_if($portfolio->student_id !== Auth::user()->student->id, 403);
        $portfolio->update(['is_public' => !$portfolio->is_public]);
        return back()->with('success', 'Visibilitas portfolio diperbarui!');
    }
}
