<?php

use App\Settings\WhatsappSettings;
use Illuminate\Support\Facades\Storage;

if (! function_exists('media_url')) {
    /** URL pública de un archivo subido al CMS; con $small usa la variante "-sm" si existe. */
    function media_url(?string $path, bool $small = false): string
    {
        if (! $path) {
            return '';
        }
        if (preg_match('~^(https?:)?//~', $path)) {
            return $path;
        }
        $disk = Storage::disk('public');
        if ($small) {
            $variant = preg_replace('/(\.[a-z0-9]+)$/i', '-sm$1', $path);
            if ($variant !== $path && $disk->exists($variant)) {
                $path = $variant;
            }
        }

        return $disk->url($path);
    }
}

if (! function_exists('versioned_asset')) {
    /** Asset estático de public/ con ?v=filemtime para invalidar caché del navegador. */
    function versioned_asset(string $path): string
    {
        $file = public_path($path);

        return asset($path).(is_file($file) ? '?v='.filemtime($file) : '');
    }
}

if (! function_exists('wa_number')) {
    /** Número de WhatsApp para un uso ('float', 'visit', 'info', 'investor'); vacío = número general. */
    function wa_number(?string $purpose = null, ?string $override = null): string
    {
        $wa = app(WhatsappSettings::class);
        $number = $override ?: ($purpose ? ($wa->{$purpose.'_number'} ?? null) : null) ?: $wa->default_number;

        return preg_replace('/\D/', '', (string) $number);
    }
}

if (! function_exists('wa_url')) {
    function wa_url(string $text = '', ?string $purpose = null, ?string $override = null): string
    {
        return 'https://wa.me/'.wa_number($purpose, $override).($text === '' ? '' : '?text='.rawurlencode($text));
    }
}

if (! function_exists('fill_template')) {
    /** Reemplaza {clave} por valores en las plantillas de mensajes. */
    function fill_template(string $template, array $values): string
    {
        return strtr($template, collect($values)->mapWithKeys(fn ($v, $k) => ['{'.$k.'}' => (string) $v])->all());
    }
}

if (! function_exists('paragraphs')) {
    /** Divide un texto en párrafos por líneas en blanco. */
    function paragraphs(?string $text): array
    {
        return array_values(array_filter(array_map('trim', preg_split('/\R\s*\R/', (string) $text)), 'strlen'));
    }
}

if (! function_exists('join_names')) {
    /** "A, B, C y D" */
    function join_names(array $names): string
    {
        $names = array_values($names);
        $last = array_pop($names);

        return $names ? implode(', ', $names).' y '.$last : (string) $last;
    }
}
