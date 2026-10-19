<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class News extends Model
{
    public const KINDS = ['external' => 'Enlace a nota externa', 'article' => 'Nota original en el sitio'];

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return ['published_at' => 'datetime', 'is_published' => 'boolean', 'image_is_render' => 'boolean'];
    }

    /** El cuerpo de las notas originales se guarda sanitizado (sin scripts, estilos ni atributos peligrosos). */
    protected function body(): \Illuminate\Database\Eloquent\Casts\Attribute
    {
        return \Illuminate\Database\Eloquent\Casts\Attribute::make(
            set: fn (?string $value) => $value === null ? null : (new \Symfony\Component\HtmlSanitizer\HtmlSanitizer(
                (new \Symfony\Component\HtmlSanitizer\HtmlSanitizerConfig)
                    ->allowSafeElements()
                    ->allowRelativeLinks()
                    ->allowLinkSchemes(['https', 'http', 'mailto'])
                    ->allowMediaSchemes(['https', 'http'])
                    ->allowRelativeMedias()
                    ->forceAttribute('a', 'rel', 'noopener')
            ))->sanitize($value),
        );
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_published', true)
            ->where(fn ($q) => $q->whereNull('published_at')->orWhere('published_at', '<=', now()))
            ->orderByDesc('published_at')->orderByDesc('id');
    }

    public function isExternal(): bool
    {
        return $this->kind === 'external';
    }

    public function url(): string
    {
        return $this->isExternal() ? (string) $this->external_url : route('news.show', $this->slug);
    }

    public function tagLabel(): string
    {
        return $this->tag ?: ($this->project?->newsTag() ?? 'Noticias');
    }
}
