<?php

namespace App\Services;

use App\Models\Project;
use App\Models\Portfolio;
use App\Models\FactProjectActivity;

class PortfolioService
{
    /**
     * Auto-create portfolio entry setelah project selesai
     */
    public function createPortfolioEntry(Project $project, ?int $rating = null, ?string $review = null): Portfolio
    {
        $project->load(['selectedStudent', 'client.user', 'workroom']);

        $student = $project->selectedStudent;
        $deliverables = $project->workroom?->deliverables ?? [];

        $portfolio = Portfolio::updateOrCreate(
            ['student_id' => $student->id, 'project_id' => $project->id],
            [
                'title' => $project->title,
                'description' => $project->description,
                'skills_used' => $project->required_skills ?? [],
                'deliverable_files' => $deliverables,
                'rating' => $rating,
                'client_review' => $review,
                'earned_amount' => $project->payment?->student_amount ?? $project->agreed_budget,
                'is_verified' => true,
                'is_public' => true,
                'client_company' => $project->client->company_name,
                'completed_at' => now(),
            ]
        );

        FactProjectActivity::record([
            'project_id' => $project->id,
            'student_id' => $student->id,
            'client_id' => $project->client_id,
            'activity_type' => 'portfolio_updated',
            'activity_category' => 'student',
            'rating' => $rating,
            'project_title' => $project->title,
            'skills_involved' => $project->required_skills,
            'client_company' => $project->client->company_name,
            'student_universitas' => $student->universitas,
        ]);

        return $portfolio;
    }
}
