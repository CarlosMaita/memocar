<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['maintenance_type_id', 'date', 'km', 'description', 'cost'])]
class MaintenanceRecord extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'km' => 'integer',
            'cost' => 'decimal:2',
        ];
    }

    protected static function booted(): void
    {
        // El kilometraje del carro nunca queda por debajo del último registro.
        static::saved(function (MaintenanceRecord $record) {
            $car = $record->car;
            if ($record->km !== null && $record->km > $car->current_km) {
                $car->update(['current_km' => $record->km]);
            }
        });
    }

    public function car(): BelongsTo
    {
        return $this->belongsTo(Car::class);
    }

    public function type(): BelongsTo
    {
        return $this->belongsTo(MaintenanceType::class, 'maintenance_type_id');
    }
}
