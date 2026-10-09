<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['name', 'brand', 'model', 'year', 'plate', 'current_km'])]
class Car extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'year' => 'integer',
            'current_km' => 'integer',
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

    public function description(): string
    {
        return trim(implode(' ', array_filter([$this->brand, $this->model, $this->year])));
    }
}
