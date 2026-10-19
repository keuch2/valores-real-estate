<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class GeneralSettings extends Settings
{
    public string $footer_tagline;

    /** Filas de contacto (página Contacto y pie). */
    public array $contact_rows;

    public float $office_lat;

    public float $office_lng;

    /** Redes sociales del pie: [{label, url}]. */
    public array $socials;

    public static function group(): string
    {
        return 'general';
    }
}
