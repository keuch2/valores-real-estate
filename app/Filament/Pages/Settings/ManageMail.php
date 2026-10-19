<?php

namespace App\Filament\Pages\Settings;

use App\Settings\MailSettings;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Pages\SettingsPage;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Mail;
use Throwable;
use UnitEnum;

class ManageMail extends SettingsPage
{
    protected static string $settings = MailSettings::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedEnvelope;

    protected static string|UnitEnum|null $navigationGroup = 'Configuración';

    protected static ?int $navigationSort = 3;

    protected static ?string $navigationLabel = 'Email y SMTP';

    protected static ?string $title = 'Email y SMTP';

    protected static ?string $slug = 'configuracion/email';

    public function form(Schema $schema): Schema
    {
        $emails = fn (string $name, string $label) => TagsInput::make($name)->label($label)
            ->placeholder('correo@dominio.com y Enter')->nestedRecursiveRules(['email']);

        return $schema->components([
            Section::make('Destinatarios de los formularios')->columns(2)->columnSpanFull()
                ->description('Cada mensaje se guarda siempre en "Mensajes recibidos"; además se avisa por email a estas casillas.')
                ->schema([
                    $emails('recipients_contact', 'Contacto'),
                    $emails('recipients_careers', 'Trabaja con nosotros'),
                    $emails('recipients_project', 'Consultas de proyectos'),
                    $emails('recipients_investor', 'Investor Pass'),
                    Toggle::make('notify_investor_by_email')->label('Avisar también por email los envíos de Investor Pass')
                        ->helperText('Por defecto Investor Pass sólo continúa por WhatsApp.')->columnSpanFull(),
                ]),
            Section::make('Servidor SMTP')->columns(2)->columnSpanFull()
                ->description('Datos de la cuenta de correo que envía los avisos. La contraseña se guarda encriptada.')
                ->schema([
                    TextInput::make('host')->label('Servidor')->placeholder('smtp.dominio.com'),
                    TextInput::make('port')->label('Puerto')->numeric()->required()->default(587),
                    TextInput::make('username')->label('Usuario'),
                    TextInput::make('password')->label('Contraseña')->password()->revealable()
                        ->placeholder(fn () => app(MailSettings::class)->password ? '•••••••• (guardada)' : null)
                        ->helperText('Dejala vacía para conservar la actual.'),
                    Select::make('encryption')->label('Seguridad')->options(['tls' => 'TLS (587)', 'ssl' => 'SSL (465)'])->placeholder('Ninguna'),
                    TextInput::make('from_address')->label('Remitente (email)')->email(),
                    TextInput::make('from_name')->label('Remitente (nombre)')->required(),
                ]),
        ]);
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $data['password'] = null;

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        if (blank($data['password'] ?? null)) {
            $data['password'] = app(MailSettings::class)->password;
        }

        return $data;
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('test')->label('Enviar email de prueba')->icon(Heroicon::OutlinedPaperAirplane)->color('gray')
                ->schema([TextInput::make('to')->label('Enviar a')->email()->required()->default(fn () => auth()->user()?->email)])
                ->modalDescription('Usa la configuración guardada (guardá los cambios antes de probar).')
                ->action(function (array $data) {
                    try {
                        Mail::raw('Este es un email de prueba del CMS de Valores Real Estate. Si lo recibiste, el SMTP está bien configurado.',
                            fn ($message) => $message->to($data['to'])->subject('Prueba de SMTP · Valores Real Estate'));
                        Notification::make()->success()->title('Email enviado')->body("Revisá la casilla {$data['to']}.")->send();
                    } catch (Throwable $e) {
                        Notification::make()->danger()->title('No se pudo enviar')->body($e->getMessage())->persistent()->send();
                    }
                }),
        ];
    }
}
