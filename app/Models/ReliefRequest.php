<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReliefRequest extends Model
{
    protected $fillable = [
        'disaster_id', 'zone_id', 'resource_name', 'quantity_requested',
        'request_type', 'description', 'priority', 'status',
        'resolved_at', 'last_escalated_at', 'escalation_level', 'previous_priority', 'created_at',
    ];

    protected $casts = [
        'resolved_at' => 'datetime',
        'last_escalated_at' => 'datetime',
    ];

    public function disaster()
    {
        return $this->belongsTo(Disaster::class);
    }

    public function zone()
    {
        return $this->belongsTo(Zone::class);
    }

    // Unresolved requests are used for active operations (R18/R19)
    public function scopeUnresolved($query)
    {
        return $query->whereNotIn('status', ['Resolved']);
    }
}
