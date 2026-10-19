<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class GeneralSettings extends Settings
{
    public string $footer_tagline;

    /** @var array<int, array{label: string, value: string}> Filas de contacto (página Contacto y pie). */
    public array $contact_rows;

    public float $office_lat;

    public float $office_lng;

    /** @var array<int, array{label: string, url: string}> */
    public array $socials;

    public static function group(): string
    {
        return 'general';
    }
}
