<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Matching extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id', 'student_id', 'match_score', 'matched_skills',
        'missing_skills', 'ai_recommendation', 'rank', 'status', 'selected_at',
    ];

    protected $casts = [
        'matched_skills' => 'array',
        'missing_skills' => 'array',
        'ai_recommendation' => 'array',
        'match_score' => 'float',
        'selected_at' => 'datetime',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}
