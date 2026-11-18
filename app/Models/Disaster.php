<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Disaster extends Model
{
    protected $fillable = [
        'name', 'type', 'description', 'start_date', 'end_date', 'status',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function zones()
    {
        return $this->hasMany(Zone::class);
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
