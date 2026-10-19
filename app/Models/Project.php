<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Project extends Model
{
    use SoftDeletes;

    public const KINDS = ['units' => 'Unidades (edificio)', 'lots' => 'Lotes', 'none' => 'Sin disponibilidad'];

    public const IMPORT_FORMATS = [
        'generic' => 'Genérico (plantilla)',
        'cumbres' => 'Cumbres',
        'paraqvaria' => 'Paraqvaria',
        'ribera' => 'La Ribera',
        'sol_city' => 'Sol City',
    ];

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'previous_slugs' => 'array',
            'facts' => 'array',
            'amenities' => 'array',
            'sports_phones' => 'array',
            'hero_is_render' => 'boolean',
            'is_published' => 'boolean',
            'lat' => 'float',
            'lng' => 'float',
            'map_x' => 'float',
            'map_y' => 'float',
        ];
    }

    public function images(): HasMany
    {
        return $this->hasMany(ProjectImage::class)->orderBy('sort_order');
    }

    public function typologies(): HasMany
    {
        return $this->hasMany(ProjectTypology::class)->orderBy('sort_order');
    }

    public function units(): HasMany
    {
        return $this->hasMany(ProjectUnit::class)->orderBy('sort_order');
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_published', true)->orderBy('sort_order')->orderBy('id');
    }

    public function url(): string
    {
        return route('projects.show', $this);
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function newsTag(): string
    {
        return $this->news_tag ?: $this->name;
    }

    /** Párrafos del cuerpo, separados por línea en blanco. */
    public function paragraphs(): array
    {
        return paragraphs($this->body);
    }
}
