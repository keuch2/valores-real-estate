<?php

namespace App\Filament\Pages\Settings;

use App\Settings\SpamSettings;
use BackedEnum;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Pages\SettingsPage;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

class ManageSpam extends SettingsPage
{
    protected static string $settings = SpamSettings::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedShieldCheck;

    protected static string|UnitEnum|null $navigationGroup = 'Configuración';

    protected static ?int $navigationSort = 4;

    protected static ?string $navigationLabel = 'Anti-spam';

    protected static ?string $title = 'Anti-spam y reCAPTCHA';

    protected static ?string $slug = 'configuracion/anti-spam';

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Google reCAPTCHA v3')->columns(2)->columnSpanFull()
                ->description('Invisible para el visitante. Creá las claves en google.com/recaptcha/admin (tipo "v3", agregando el dominio del sitio).')
                ->schema([
                    Toggle::make('recaptcha_enabled')->label('Activar reCAPTCHA')->columnSpanFull(),
                    TextInput::make('recaptcha_site_key')->label('Clave del sitio (site key)')->requiredIf('recaptcha_enabled', true),
                    TextInput::make('recaptcha_secret')->label('Clave secreta (secret key)')->password()->revealable()
                        ->placeholder(fn () => app(SpamSettings::class)->recaptcha_secret ? '•••••••• (guardada)' : null)
                        ->helperText('Se guarda encriptada. Dejala vacía para conservar la actual.'),
                    TextInput::make('recaptcha_min_score')->label('Puntaje mínimo')->numeric()->minValue(0)->maxValue(1)->step(0.1)->required()
                        ->helperText('De 0 a 1. Con 0.5 se bloquea la mayoría de los bots sin molestar a las personas.'),
                    Toggle::make('recaptcha_fail_open')->label('Si Google no responde, aceptar el envío marcado como "posible spam"')
                        ->helperText('Si se desactiva, el envío se rechaza cuando Google no responde.'),
                ]),
            Section::make('Otras protecciones')->columns(3)->columnSpanFull()
                ->description('Siempre activas: campo trampa invisible para bots y tiempo mínimo de llenado.')
                ->schema([
                    TextInput::make('min_fill_seconds')->label('Tiempo mínimo de llenado (seg.)')->numeric()->minValue(0)->maxValue(30)->required(),
                    TextInput::make('rate_per_minute')->label('Envíos por minuto por IP')->numeric()->minValue(1)->required(),
                    TextInput::make('rate_per_hour')->label('Envíos por hora por IP')->numeric()->minValue(1)->required(),
                ]),
        ]);
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $data['recaptcha_secret'] = null;

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        if (blank($data['recaptcha_secret'] ?? null)) {
            $data['recaptcha_secret'] = app(SpamSettings::class)->recaptcha_secret;
        }

        return $data;
    }
}
