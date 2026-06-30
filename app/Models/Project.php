<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id', 'selected_student_id', 'title', 'description', 'category',
        'required_skills', 'budget_min', 'budget_max', 'agreed_budget', 'deadline',
        'duration_days', 'status', 'notes_for_student', 'revision_count',
        'max_revisions', 'started_at', 'completed_at',
    ];

    protected $casts = [
        'required_skills' => 'array',
        'budget_min' => 'decimal:2',
        'budget_max' => 'decimal:2',
        'agreed_budget' => 'decimal:2',
        'deadline' => 'date',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function selectedStudent()
    {
        return $this->belongsTo(Student::class, 'selected_student_id');
    }

    public function matchings()
    {
        return $this->hasMany(Matching::class)->orderByDesc('match_score');
    }

    public function payment()
    {
        return $this->hasOne(Payment::class);
    }

    public function workroom()
    {
        return $this->hasOne(Workroom::class);
    }

    public function portfolio()
    {
        return $this->hasOne(Portfolio::class);
    }

    public function getBudgetRangeAttribute(): string
    {
        return 'Rp ' . number_format($this->budget_min, 0, ',', '.') . ' - Rp ' . number_format($this->budget_max, 0, ',', '.');
    }

    public function getStatusBadgeAttribute(): array
    {
        return match($this->status) {
            'open' => ['label' => 'Open', 'color' => 'green'],
            'waiting_payment' => ['label' => 'Menunggu Pembayaran', 'color' => 'yellow'],
            'in_progress' => ['label' => 'Sedang Dikerjakan', 'color' => 'blue'],
            'in_review' => ['label' => 'Review', 'color' => 'purple'],
            'revision' => ['label' => 'Revisi', 'color' => 'orange'],
            'completed' => ['label' => 'Selesai', 'color' => 'emerald'],
            'disputed' => ['label' => 'Dispute', 'color' => 'red'],
            'cancelled' => ['label' => 'Dibatalkan', 'color' => 'gray'],
            default => ['label' => $this->status, 'color' => 'gray'],
        };
    }
}
