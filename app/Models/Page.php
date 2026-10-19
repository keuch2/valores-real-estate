<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

class Page extends Model
{
    public const KEYS = [
        'home' => 'Inicio',
        'proyectos' => 'Proyectos',
        'noticias' => 'Noticias',
        'nosotros' => 'Nosotros',
        'trabaja' => 'Trabaja con nosotros',
        'contacto' => 'Contacto',
        'investor' => 'Investor Pass',
    ];

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return ['content' => 'array'];
    }

    public static function for(string $key): self
    {
        return once(fn () => static::firstOrNew(['key' => $key]));
    }

    public function get(string $path, mixed $default = null): mixed
    {
        return Arr::get($this->content ?? [], $path, $default);
    }
}
