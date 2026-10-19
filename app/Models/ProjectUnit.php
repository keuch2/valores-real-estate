<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProjectUnit extends Model
{
    public const STATUSES = [
        'disponible' => 'Disponible',
        'reservado' => 'Reservado',
        'vendido' => 'Vendido',
        'nodisponible' => 'No disponible',
    ];

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return ['area_m2' => 'float', 'floor' => 'integer'];
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }
}
