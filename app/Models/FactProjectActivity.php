<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FactProjectActivity extends Model
{
    use HasFactory;

    protected $table = 'fact_project_activities';

    protected $fillable = [
        'project_id', 'student_id', 'client_id', 'payment_id', 'matching_id',
        'activity_type', 'activity_category',
        'match_score', 'amount', 'platform_fee', 'revision_count', 'duration_days', 'rating',
        'project_title', 'project_category', 'student_universitas', 'student_jurusan',
        'client_industry', 'client_company', 'skills_involved', 'payment_status', 'project_status',
        'activity_date', 'activity_month', 'activity_year', 'activity_quarter',
        'extra_data',
    ];

    protected $casts = [
        'skills_involved' => 'array',
        'extra_data' => 'array',
        'amount' => 'decimal:2',
        'platform_fee' => 'decimal:2',
        'match_score' => 'float',
        'rating' => 'float',
        'activity_date' => 'date',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * Record a new activity to the fact table
     */
    public static function record(array $data): self
    {
        $date = now();
        return self::create(array_merge($data, [
            'activity_date' => $date->toDateString(),
            'activity_month' => $date->month,
            'activity_year' => $date->year,
            'activity_quarter' => 'Q' . $date->quarter,
        ]));
    }
}
