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

if (! function_exists('icon')) {
    function icon(string $name, int $size = 18): Illuminate\Support\HtmlString
    {
        $paths = [
            'arrow' => '<path d="M5 12h14M13 6l6 6-6 6"/>',
            'back' => '<path d="M19 12H5M11 6l-6 6 6 6"/>',
            'pin' => '<path d="M12 21s-7-6.2-7-11.5A7 7 0 0 1 19 9.5C19 14.8 12 21 12 21Z"/><circle cx="12" cy="9.5" r="2.5"/>',
            'download' => '<path d="M12 4v11M7 10l5 5 5-5M5 20h14"/>',
            'external' => '<path d="M7 17L17 7M8 7h9v9"/>',
            'check' => '<path d="M5 12.5l4.5 4.5L19 7.5"/>',
            'calendar' => '<rect x="4" y="5" width="16" height="15" rx="2"/><path d="M4 10h16M9 3v4M15 3v4"/>',
            'instagram' => '<rect x="4" y="4" width="16" height="16" rx="4.5"/><circle cx="12" cy="12" r="3.6"/><circle cx="16.8" cy="7.2" r=".6" fill="currentColor"/>',
            'whatsapp' => '<path d="M4 20l1.3-3.9A8 8 0 1 1 8 19.1L4 20Z"/><path d="M9 9.5c.3 2.4 2.1 4.3 4.5 4.8l1-1.2 1.8.8-.4 1.6c-3.6.2-7-3-7.2-6.6l1.6-.4.8 1.8-1.1 1"/>',
        ];

        return new Illuminate\Support\HtmlString('<svg class="icon" width="'.$size.'" height="'.$size.'" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">'.$paths[$name].'</svg>');
    }
}

if (! function_exists('render_tag')) {
    function render_tag(?bool $isRender): Illuminate\Support\HtmlString
    {
        return new Illuminate\Support\HtmlString($isRender ? '<span class="render-tag">Imagen ilustrativa (render)</span>' : '');
    }
}
