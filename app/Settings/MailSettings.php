<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class MailSettings extends Settings
{
    public ?string $host;

    public int $port;

    public ?string $username;

    public ?string $password;

    public ?string $encryption; // tls | ssl | null

    public ?string $from_address;

    public string $from_name;

    /** @var string[] */
    public array $recipients_contact;

    /** @var string[] */
    public array $recipients_careers;

    /** @var string[] */
    public array $recipients_project;

    /** @var string[] */
    public array $recipients_investor;

    public bool $notify_investor_by_email;

    public static function group(): string
    {
        return 'mail';
    }

    public static function encrypted(): array
    {
        return ['password'];
    }
}
