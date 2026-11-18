<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Zone extends Model
{
    protected $fillable = [
        'disaster_id', 'district', 'sub_district', 'area_name', 'severity_level', 'priority_tier',
    ];

    public function disaster()
    {
        return $this->belongsTo(Disaster::class);
    }

    public function reliefRequests()
    {
        return $this->hasMany(ReliefRequest::class);
    }

    public function inventories()
    {
        return $this->hasMany(Inventory::class);
    }

    public function dutyAssignments()
    {
        return $this->hasMany(DutyAssignment::class);
    }
}
