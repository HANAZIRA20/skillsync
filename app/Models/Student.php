<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'nim', 'universitas', 'jurusan', 'semester', 'ipk',
        'krs_file_path', 'skill_profile', 'available_schedule', 'bio',
        'linkedin_url', 'github_url', 'average_rating', 'total_projects',
        'total_earnings', 'krs_status',
    ];

    protected $casts = [
        'skill_profile' => 'array',
        'available_schedule' => 'array',
        'ipk' => 'float',
        'average_rating' => 'float',
        'total_earnings' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function skills()
    {
        return $this->hasMany(StudentSkill::class);
    }

    public function matchings()
    {
        return $this->hasMany(Matching::class);
    }

    public function portfolios()
    {
        return $this->hasMany(Portfolio::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function getTopSkillsAttribute(): array
    {
        return $this->skills()
            ->orderByDesc('confidence_score')
            ->limit(6)
            ->pluck('skill_name')
            ->toArray();
    }
}
