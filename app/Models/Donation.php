<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Donation extends Model
{
    // These are the fields Laravel is allowed to save from our controller.
    protected $fillable = [
        'receipt_number',
        'donor_name',
        'donor_email',
        'donor_phone',
        'drop_off_location',
        'donation_date',
        'notes',
    ];

    // Store and retrieve donation_date as a date object for easy formatting in Blade.
    protected $casts = [
        'donation_date' => 'date',
    ];

    // One donation can contain several donated items.
    public function items(): HasMany
    {
        return $this->hasMany(DonationItem::class);
    }
}
