<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DonationItem extends Model
{
    protected $fillable = [
        'item_name',
        'quantity',
        'unit',
    ];

    // Every donated item belongs to one donation record.
    public function donation(): BelongsTo
    {
        return $this->belongsTo(Donation::class);
    }
}
