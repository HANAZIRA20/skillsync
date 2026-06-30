<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Workroom extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id', 'student_id', 'client_id', 'messages', 'deliverables',
        'current_deliverable_notes', 'status', 'progress_percentage',
        'submitted_at', 'approved_at',
    ];

    protected $casts = [
        'messages' => 'array',
        'deliverables' => 'array',
        'submitted_at' => 'datetime',
        'approved_at' => 'datetime',
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

    public function revisions()
    {
        return $this->hasMany(Revision::class);
    }
}
