<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FamilyProfile extends Model
{
    public const AREAS = [
        'Dhaka', 'Chattogram', 'Rajshahi', 'Khulna',
        'Barishal', 'Sylhet', 'Rangpur', 'Mymensingh',
    ];

    // Weights: vulnerable members count more than a regular member.
    private const W_MEMBER = 1;
    private const W_INFANT = 3;
    private const W_ELDERLY = 2;
    private const W_INJURED = 4;

    protected $fillable = [
        'user_id', 'area', 'household_size',
        'infants_count', 'elderly_count', 'injured_count',
    ];

    protected static function booted(): void
    {
        // Severity is recalculated automatically on every create/update.
        static::saving(fn (self $profile) => $profile->calculateSeverity());
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function calculateSeverity(): void
    {
        $weight = ($this->household_size * self::W_MEMBER)
            + ($this->infants_count * self::W_INFANT)
            + ($this->elderly_count * self::W_ELDERLY)
            + ($this->injured_count * self::W_INJURED);

        $this->severity_weight = $weight;
        $this->severity_level = match (true) {
            $weight >= 40 => 'critical',
            $weight >= 25 => 'high',
            $weight >= 12 => 'medium',
            default => 'low',
        };
    }

    /** Aggregated (anonymous) severity figures for one region. */
    public static function regionalSummary(string $area): object
    {
        return static::where('area', $area)
            ->selectRaw('COUNT(*) as households,
                         COALESCE(SUM(household_size),0) as people,
                         COALESCE(SUM(infants_count),0) as infants,
                         COALESCE(SUM(elderly_count),0) as elderly,
                         COALESCE(SUM(injured_count),0) as injured,
                         COALESCE(SUM(severity_weight),0) as total_weight')
            ->first();
    }
}