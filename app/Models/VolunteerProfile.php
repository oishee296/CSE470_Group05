<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VolunteerProfile extends Model
{
    protected $fillable = [
        'full_name', 'email', 'phone_number', 'address', 'skills',
        'certification_name', 'certification_status', 'registration_status',
    ];

    public function dutyAssignments()
    {
        return $this->hasMany(DutyAssignment::class);
    }
}
