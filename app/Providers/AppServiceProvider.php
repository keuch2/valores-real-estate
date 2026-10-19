<?php

namespace App\Providers;

use App\Settings\GeneralSettings;
use App\Settings\MailSettings;
use App\Settings\SpamSettings;
use App\Settings\WhatsappSettings;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Throwable;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // El SMTP se configura desde el CMS: se aplica justo antes de crear el primer mailer.
        $this->app->afterResolving('mail.manager', function () {
            try {
                $mail = app(MailSettings::class);
            } catch (Throwable) {
                return; // sin base de datos (ej. durante la instalación): queda la config de .env
            }
            if ($mail->host) {
                config([
                    'mail.default' => 'smtp',
                    'mail.mailers.smtp.host' => $mail->host,
                    'mail.mailers.smtp.port' => $mail->port,
                    'mail.mailers.smtp.username' => $mail->username,
                    'mail.mailers.smtp.password' => $mail->password,
                    'mail.mailers.smtp.scheme' => $mail->encryption === 'ssl' ? 'smtps' : 'smtp',
                ]);
            }
            if ($mail->from_address) {
                config(['mail.from.address' => $mail->from_address, 'mail.from.name' => $mail->from_name]);
            }
        });
    }

    public function boot(): void
    {
        View::composer(['components.layouts.site', 'pages.contact'], function ($view) {
            $view->with('general', app(GeneralSettings::class))->with('whatsapp', app(WhatsappSettings::class));
        });

        RateLimiter::for('forms', function (Request $request) {
            $spam = app(SpamSettings::class);

            return [
                Limit::perMinute(max(1, $spam->rate_per_minute))->by('forms-m|'.$request->ip()),
                Limit::perHour(max(1, $spam->rate_per_hour))->by('forms-h|'.$request->ip()),
            ];
        });
    }
}
