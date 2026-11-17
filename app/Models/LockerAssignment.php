<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LockerAssignment extends Model
{
    protected $fillable = [
        'locker_id',
        'inventory_id',
        'quantity_assigned'
    ];

    public function locker(): BelongsTo
    {
        return $this->belongsTo(StorageLocker::class, 'locker_id');
    }

    public function inventory(): BelongsTo
    {
        return $this->belongsTo(Inventory::class, 'inventory_id');
    }
}