<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StorageLocker extends Model
{
    protected $fillable = [
        'warehouse_id',
        'locker_code',
        'capacity'
    ];

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(WarehouseHub::class, 'warehouse_id');
    }

    public function assignments(): HasMany
    {
        return $this->hasMany(LockerAssignment::class, 'locker_id');
    }
}
