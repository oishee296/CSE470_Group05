<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WarehouseHub extends Model
{
    protected $fillable = [
        'name',
        'location',
        'total_capacity'
    ];

    public function inventories(): HasMany
    {
        return $this->hasMany(Inventory::class, 'warehouse_id');
    }

    public function lockers(): HasMany
    {
        return $this->hasMany(StorageLocker::class, 'warehouse_id');
    }
}