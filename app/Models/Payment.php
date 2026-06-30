<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id', 'client_id', 'student_id', 'payment_code', 'amount',
        'platform_fee', 'student_amount', 'status', 'payment_method',
        'mock_callback_data', 'paid_at', 'held_at', 'released_at', 'refunded_at', 'notes',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'platform_fee' => 'decimal:2',
        'student_amount' => 'decimal:2',
        'mock_callback_data' => 'array',
        'paid_at' => 'datetime',
        'held_at' => 'datetime',
        'released_at' => 'datetime',
        'refunded_at' => 'datetime',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function getStatusBadgeAttribute(): array
    {
        return match($this->status) {
            'pending' => ['label' => 'Menunggu Pembayaran', 'color' => 'yellow'],
            'held' => ['label' => 'Dana Ditahan', 'color' => 'blue'],
            'released' => ['label' => 'Dana Dicairkan', 'color' => 'green'],
            'refunded' => ['label' => 'Dikembalikan', 'color' => 'red'],
            'failed' => ['label' => 'Gagal', 'color' => 'red'],
            default => ['label' => $this->status, 'color' => 'gray'],
        };
    }
}
