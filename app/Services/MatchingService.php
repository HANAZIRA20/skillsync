<?php

namespace App\Services;

use App\Models\Project;
use App\Models\Student;
use App\Models\Matching;
use App\Models\StudentSkill;
use App\Models\FactProjectActivity;

class MatchingService
{
    /**
     * Run AI Matching untuk project tertentu
     * Bandingkan skills mahasiswa vs required skills project
     */
    public function runMatchingForProject(Project $project): array
    {
        $requiredSkills = $project->required_skills ?? [];

        // Ambil semua mahasiswa yang sudah punya skill profile
        $students = Student::with(['skills', 'user'])
            ->whereHas('skills')
            ->where('krs_status', 'parsed')
            ->get();

        $results = [];

        foreach ($students as $student) {
            $matchResult = $this->calculateMatchScore($student, $requiredSkills, $project);

            if ($matchResult['match_score'] > 0) {
                $results[] = array_merge(['student' => $student], $matchResult);
            }
        }

        // Sort by match score descending
        usort($results, fn($a, $b) => $b['match_score'] <=> $a['match_score']);

        // Save top matches ke database
        $this->saveMatchings($project, $results);

        return $results;
    }

    /**
     * Calculate AI match score antara satu mahasiswa dengan required skills
     */
    public function calculateMatchScore(Student $student, array $requiredSkills, Project $project): array
    {
        if (empty($requiredSkills)) {
            return ['match_score' => 50, 'matched_skills' => [], 'missing_skills' => [], 'ai_recommendation' => []];
        }

        $studentSkills = $student->skills->pluck('skill_name', 'skill_name')->toArray();
        $studentSkillScores = $student->skills->keyBy('skill_name');

        $matchedSkills = [];
        $missingSkills = [];
        $totalScore = 0;
        $maxScore = count($requiredSkills) * 100;

        foreach ($requiredSkills as $required) {
            $bestMatch = $this->findBestSkillMatch($required, $studentSkills);

            if ($bestMatch) {
                $skillScore = $studentSkillScores[$bestMatch]->confidence_score ?? 0.8;
                $skillPoints = round($skillScore * 100);
                $totalScore += $skillPoints;
                $matchedSkills[] = [
                    'required' => $required,
                    'matched' => $bestMatch,
                    'score' => $skillPoints,
                ];
            } else {
                $missingSkills[] = $required;
                // Partial credit untuk partially related skills
                $partialMatch = $this->findPartialSkillMatch($required, $studentSkillScores->keys()->toArray());
                if ($partialMatch) {
                    $totalScore += 30; // 30% partial credit
                }
            }
        }

        // Calculate base match score
        $baseScore = $maxScore > 0 ? ($totalScore / $maxScore) * 100 : 0;

        // Bonus factors
        $bonuses = 0;
        if ($student->ipk >= 3.5) $bonuses += 5; // High GPA bonus
        if ($student->total_projects > 0) $bonuses += min(10, $student->total_projects * 2); // Experience bonus
        if ($student->average_rating >= 4.0) $bonuses += 5; // Rating bonus

        $finalScore = min(100, round($baseScore + $bonuses, 1));

        $aiRecommendation = $this->generateRecommendation($student, $matchedSkills, $missingSkills, $finalScore);

        return [
            'match_score' => $finalScore,
            'matched_skills' => $matchedSkills,
            'missing_skills' => $missingSkills,
            'ai_recommendation' => $aiRecommendation,
        ];
    }

    /**
     * Find best matching skill (exact or synonym match)
     */
    private function findBestSkillMatch(string $required, array $studentSkills): ?string
    {
        // Exact match
        foreach ($studentSkills as $skill) {
            if (strtolower($skill) === strtolower($required)) {
                return $skill;
            }
        }

        // Partial / synonym match
        foreach ($studentSkills as $skill) {
            if (stripos($skill, $required) !== false || stripos($required, $skill) !== false) {
                return $skill;
            }
        }

        // Synonym mapping
        $synonyms = [
            'laravel' => ['php', 'web development', 'backend'],
            'vue.js' => ['javascript', 'frontend', 'react'],
            'react' => ['javascript', 'frontend', 'vue.js'],
            'python' => ['machine learning', 'data analysis', 'scripting'],
            'postgresql' => ['sql', 'database', 'mysql'],
            'mysql' => ['sql', 'database', 'postgresql'],
            'ui/ux design' => ['figma', 'design', 'graphic design'],
            'machine learning' => ['ai', 'python', 'data mining'],
        ];

        $requiredLower = strtolower($required);
        if (isset($synonyms[$requiredLower])) {
            foreach ($synonyms[$requiredLower] as $synonym) {
                foreach ($studentSkills as $skill) {
                    if (strtolower($skill) === $synonym) {
                        return $skill;
                    }
                }
            }
        }

        return null;
    }

    /**
     * Partial skill match for giving partial credit
     */
    private function findPartialSkillMatch(string $required, array $studentSkills): ?string
    {
        $keywords = explode(' ', strtolower($required));
        foreach ($studentSkills as $skill) {
            $skillLower = strtolower($skill);
            foreach ($keywords as $keyword) {
                if (strlen($keyword) > 3 && stripos($skillLower, $keyword) !== false) {
                    return $skill;
                }
            }
        }
        return null;
    }

    /**
     * Generate AI recommendation text
     */
    private function generateRecommendation(Student $student, array $matched, array $missing, float $score): array
    {
        $matchCount = count($matched);
        $missingCount = count($missing);

        $strength = match(true) {
            $score >= 90 => 'Sangat Direkomendasikan',
            $score >= 75 => 'Direkomendasikan',
            $score >= 60 => 'Cukup Sesuai',
            $score >= 40 => 'Kurang Sesuai',
            default => 'Tidak Direkomendasikan',
        };

        return [
            'strength' => $strength,
            'summary' => "Mahasiswa ini memiliki {$matchCount} dari " . ($matchCount + $missingCount) . " skill yang dibutuhkan dengan match score {$score}%.",
            'pros' => array_map(fn($m) => "✓ Memiliki skill {$m['required']}", array_slice($matched, 0, 3)),
            'cons' => array_map(fn($s) => "✗ Belum memiliki skill {$s}", array_slice($missing, 0, 3)),
            'score_breakdown' => [
                'skill_match' => round($score - 10),
                'academic' => $student->ipk >= 3.5 ? 5 : 0,
                'experience' => min(10, $student->total_projects * 2),
            ],
        ];
    }

    /**
     * Save matchings to database
     */
    private function saveMatchings(Project $project, array $results): void
    {
        // Delete old matchings
        Matching::where('project_id', $project->id)->delete();

        foreach (array_slice($results, 0, 10) as $rank => $result) {
            $matching = Matching::create([
                'project_id' => $project->id,
                'student_id' => $result['student']->id,
                'match_score' => $result['match_score'],
                'matched_skills' => $result['matched_skills'],
                'missing_skills' => $result['missing_skills'],
                'ai_recommendation' => $result['ai_recommendation'],
                'rank' => $rank + 1,
                'status' => 'pending',
            ]);

            // Record ke fact table
            FactProjectActivity::record([
                'project_id' => $project->id,
                'student_id' => $result['student']->id,
                'client_id' => $project->client_id,
                'matching_id' => $matching->id,
                'activity_type' => 'ai_matched',
                'activity_category' => 'ai',
                'match_score' => $result['match_score'],
                'project_title' => $project->title,
                'project_category' => $project->category,
                'student_universitas' => $result['student']->universitas,
                'skills_involved' => $project->required_skills,
            ]);
        }
    }
}
