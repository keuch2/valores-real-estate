<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('general.footer_tagline', 'Desarrollamos lugares que crecen en valor.');
        $this->migrator->add('general.contact_rows', [
            ['label' => 'Dirección', 'value' => 'Avda. Santa Teresa 1827 casi Aviadores del Chaco, Paseo La Galería, Torre 3, Piso 10, Oficina 2 · Asunción'],
            ['label' => 'Teléfono', 'value' => '+595 21 600 450'],
            ['label' => 'WhatsApp', 'value' => '+595 994 442 828'],
            ['label' => 'Email', 'value' => 'valores@valores.com.py'],
            ['label' => 'Horario', 'value' => 'Lunes a viernes, 8:00 a 17:00'],
        ]);
        $this->migrator->add('general.office_lat', -25.2844);
        $this->migrator->add('general.office_lng', -57.5639);
        $this->migrator->add('general.socials', [
            ['label' => 'Instagram', 'url' => '#'],
            ['label' => 'Facebook', 'url' => '#'],
            ['label' => 'LinkedIn', 'url' => '#'],
        ]);

        $this->migrator->add('whatsapp.default_number', '595994442828');
        $this->migrator->add('whatsapp.float_number', null);
        $this->migrator->add('whatsapp.float_label', 'Hablar con un asesor');
        $this->migrator->add('whatsapp.float_message', null);
        $this->migrator->add('whatsapp.visit_number', null);
        $this->migrator->add('whatsapp.visit_template', 'Hola, me gustaría agendar una visita a {proyecto}. Mi día y horario de preferencia es: ');
        $this->migrator->add('whatsapp.info_number', null);
        $this->migrator->add('whatsapp.info_template', 'Hola, me interesa recibir información sobre {proyecto}.');
        $this->migrator->add('whatsapp.investor_number', null);
        $this->migrator->add('whatsapp.investor_template', "Hola, soy {nombre} ({pais}). Me interesa el Investor Pass, modalidad: {modalidad}.\nEmail: {email}\nTeléfono: {telefono}");

        $this->migrator->add('mail.host', null);
        $this->migrator->add('mail.port', 587);
        $this->migrator->add('mail.username', null);
        $this->migrator->addEncrypted('mail.password', null);
        $this->migrator->add('mail.encryption', 'tls');
        $this->migrator->add('mail.from_address', null);
        $this->migrator->add('mail.from_name', 'Valores Real Estate');
        $this->migrator->add('mail.recipients_contact', ['valores@valores.com.py']);
        $this->migrator->add('mail.recipients_careers', ['administracion@valores.com.py']);
        $this->migrator->add('mail.recipients_project', ['valores@valores.com.py']);
        $this->migrator->add('mail.recipients_investor', ['valores@valores.com.py']);
        $this->migrator->add('mail.notify_investor_by_email', false);

        $this->migrator->add('spam.recaptcha_enabled', false);
        $this->migrator->add('spam.recaptcha_site_key', null);
        $this->migrator->addEncrypted('spam.recaptcha_secret', null);
        $this->migrator->add('spam.recaptcha_min_score', 0.5);
        $this->migrator->add('spam.recaptcha_fail_open', true);
        $this->migrator->add('spam.min_fill_seconds', 3);
        $this->migrator->add('spam.rate_per_minute', 3);
        $this->migrator->add('spam.rate_per_hour', 10);
    }
};
