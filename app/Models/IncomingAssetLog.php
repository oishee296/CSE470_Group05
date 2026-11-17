<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class IncomingAssetLog extends Model
{
    protected $fillable = [
        'inventory_id',
        'quantity_received',
        'source',
        'received_at'
    ];

    protected $casts = [
        'received_at' => 'datetime',
    ];

    public function inventory(): BelongsTo
    {
        return $this->belongsTo(Inventory::class, 'inventory_id');
    }
}