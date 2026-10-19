<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class SpamSettings extends Settings
{
    public bool $recaptcha_enabled;

    public ?string $recaptcha_site_key;

    public ?string $recaptcha_secret;

    public float $recaptcha_min_score;

    /** Si Google no responde: aceptar el envío marcándolo como sospechoso (true) o rechazarlo (false). */
    public bool $recaptcha_fail_open;

    public int $min_fill_seconds;

    public int $rate_per_minute;

    public int $rate_per_hour;

    public static function group(): string
    {
        return 'spam';
    }

    public static function encrypted(): array
    {
        return ['recaptcha_secret'];
    }
}
