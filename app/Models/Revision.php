<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Revision extends Model
{
    use HasFactory;

    protected $fillable = [
        'workroom_id', 'requested_by', 'revision_number', 'feedback',
        'specific_changes', 'status', 'requested_at', 'submitted_at', 'approved_at',
    ];

    protected $casts = [
        'specific_changes' => 'array',
        'requested_at' => 'datetime',
        'submitted_at' => 'datetime',
        'approved_at' => 'datetime',
    ];

    public function workroom()
    {
        return $this->belongsTo(Workroom::class);
    }

    public function requester()
    {
        return $this->belongsTo(User::class, 'requested_by');
    }
}
