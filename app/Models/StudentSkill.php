<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentSkill extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id', 'skill_name', 'category', 'confidence_score', 'source', 'evidence',
    ];

    protected $casts = [
        'evidence' => 'array',
        'confidence_score' => 'float',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function getConfidencePercentAttribute(): int
    {
        return (int) ($this->confidence_score * 100);
    }
}
