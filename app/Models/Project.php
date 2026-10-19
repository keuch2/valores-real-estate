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

    protected static function booted(): void
    {
        // Si cambia el slug, el anterior se guarda para redirigir (301) los enlaces viejos.
        static::updating(function (self $project) {
            if ($project->isDirty('slug') && $project->getOriginal('slug')) {
                $previous = array_values(array_diff($project->previous_slugs ?? [], [$project->slug]));
                $project->previous_slugs = array_values(array_unique([...$previous, $project->getOriginal('slug')]));
            }
        });
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
