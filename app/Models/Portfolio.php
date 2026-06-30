<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Portfolio extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id', 'project_id', 'title', 'description', 'skills_used',
        'deliverable_files', 'rating', 'client_review', 'earned_amount',
        'is_verified', 'is_public', 'client_company', 'thumbnail', 'completed_at',
    ];

    protected $casts = [
        'skills_used' => 'array',
        'deliverable_files' => 'array',
        'is_verified' => 'boolean',
        'is_public' => 'boolean',
        'earned_amount' => 'decimal:2',
        'completed_at' => 'datetime',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }
}
