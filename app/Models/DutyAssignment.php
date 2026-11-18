<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DutyAssignment extends Model
{
    protected $fillable = [
        'volunteer_profile_id', 'disaster_id', 'zone_id',
        'assigned_responsibility', 'assignment_date', 'assignment_status',
    ];

    protected $casts = [
        'assignment_date' => 'date',
    ];

    public function volunteerProfile()
    {
        return $this->belongsTo(VolunteerProfile::class);
    }

    public function disaster()
    {
        return $this->belongsTo(Disaster::class);
    }

    public function zone()
    {
        return $this->belongsTo(Zone::class);
    }
}
