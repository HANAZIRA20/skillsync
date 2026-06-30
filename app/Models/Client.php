<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'company_name', 'industry', 'company_description',
        'website', 'company_size', 'city', 'province',
        'total_projects_posted', 'total_projects_completed', 'total_spent',
        'average_rating_given',
    ];

    protected $casts = [
        'total_spent' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function projects()
    {
        return $this->hasMany(Project::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }
}
