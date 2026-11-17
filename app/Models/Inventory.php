<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Inventory extends Model
{
    protected $fillable = [
        'warehouse_id',
        'item_name',
        'quantity',
        'unit',
        'volume_per_unit'
    ];

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(WarehouseHub::class, 'warehouse_id');
    }

    public function incomingAssetLogs(): HasMany
    {
        return $this->hasMany(IncomingAssetLog::class, 'inventory_id');
    }

    public function lockerAssignments(): HasMany
    {
        return $this->hasMany(LockerAssignment::class, 'inventory_id');
    }
}