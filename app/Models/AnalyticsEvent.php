<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AnalyticsEvent extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'event_type', 'event_name', 'entity_type',
        'entity_id', 'event_data', 'ip_address', 'user_agent',
    ];

    protected $casts = [
        'event_data' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
