<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuditTrail extends Model
{
    // Audit records are append-only in the application (no edit/delete actions).
    public $timestamps = false;

    protected $fillable = [
        'action_type', 'related_type', 'related_id', 'description',
        'quantity', 'performed_by', 'created_at',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public static function log($actionType, $description, $relatedType = null, $relatedId = null, $quantity = null, $performedBy = 'Command Center Admin')
    {
        return self::create([
            'action_type' => $actionType,
            'related_type' => $relatedType,
            'related_id' => $relatedId,
            'description' => $description,
            'quantity' => $quantity,
            'performed_by' => $performedBy,
            'created_at' => now(),
        ]);
    }
}
