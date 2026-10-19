<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

/**
 * Números y mensajes de WhatsApp. Los números específicos vacíos usan el número general.
 * Plantillas: {proyecto}, {nombre}, {pais}, {email}, {telefono}, {modalidad}.
 */
class WhatsappSettings extends Settings
{
    public string $default_number;

    public ?string $float_number;

    public string $float_label;

    public ?string $float_message;

    public ?string $visit_number;

    public string $visit_template;

    public ?string $info_number;

    public string $info_template;

    public ?string $investor_number;

    public string $investor_template;

    public static function group(): string
    {
        return 'whatsapp';
    }
}
