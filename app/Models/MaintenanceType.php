<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['name', 'category', 'recommended_km', 'recommended_months', 'set_km', 'set_months', 'notes', 'is_active'])]
class MaintenanceType extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'recommended_km' => 'integer',
            'recommended_months' => 'integer',
            'set_km' => 'integer',
            'set_months' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function records(): HasMany
    {
        return $this->hasMany(MaintenanceRecord::class);
    }

    public function hasInterval(): bool
    {
        return $this->set_km !== null || $this->set_months !== null;
    }

    public static function formatInterval(?int $km, ?int $months): string
    {
        $parts = [];
        if ($km !== null) {
            $parts[] = number_format($km, 0, ',', '.').' km';
        }
        if ($months !== null) {
            $parts[] = $months.' '.($months === 1 ? 'mes' : 'meses');
        }

        return $parts ? implode(' / ', $parts) : '—';
    }
}
