<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProjectTypology extends Model
{
    protected $guarded = ['id'];

    protected function casts(): array
    {
        return ['specs' => 'array', 'images' => 'array'];
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }
}
