<?php

namespace App\Filament\Pages\Settings;

use App\Settings\WhatsappSettings;
use BackedEnum;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Pages\SettingsPage;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

class ManageWhatsapp extends SettingsPage
{
    protected static string $settings = WhatsappSettings::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedChatBubbleLeftRight;

    protected static string|UnitEnum|null $navigationGroup = 'Configuración';

    protected static ?int $navigationSort = 2;

    protected static ?string $navigationLabel = 'WhatsApp';

    protected static ?string $title = 'WhatsApp';

    protected static ?string $slug = 'configuracion/whatsapp';

    public function form(Schema $schema): Schema
    {
        $number = fn (string $name, string $label) => TextInput::make($name)->label($label)->tel()
            ->placeholder('Vacío = número general')->helperText('Con código de país, ej. 595994442828.');

        return $schema->components([
            Section::make('Número general')->columnSpanFull()
                ->description('Número institucional conectado al CRM. Lo usan todos los botones que no tienen un número propio.')
                ->schema([
                    TextInput::make('default_number')->label('Número de WhatsApp')->tel()->required()
                        ->helperText('Con código de país y sin espacios, ej. 595994442828.'),
                ]),
            Section::make('Botón flotante "Hablar con un asesor"')->columns(2)->columnSpanFull()->schema([
                $number('float_number', 'Número'),
                TextInput::make('float_label')->label('Texto del botón')->required(),
                Textarea::make('float_message')->label('Mensaje inicial (opcional)')->rows(2)->columnSpanFull(),
            ]),
            Section::make('"Agendá tu visita" (fichas de proyecto)')->columns(2)->columnSpanFull()->schema([
                $number('visit_number', 'Número'),
                Textarea::make('visit_template')->label('Mensaje')->rows(3)->required()->helperText('{proyecto} se reemplaza por el nombre del proyecto.'),
            ]),
            Section::make('"Escribinos por WhatsApp" (fichas de proyecto)')->columns(2)->columnSpanFull()->schema([
                $number('info_number', 'Número'),
                Textarea::make('info_template')->label('Mensaje')->rows(3)->required()->helperText('{proyecto} se reemplaza por el nombre del proyecto.'),
            ]),
            Section::make('Formulario de Investor Pass')->columns(2)->columnSpanFull()
                ->description('Al enviar el formulario, el mensaje queda guardado en el CMS y se abre WhatsApp con estos datos.')
                ->schema([
                    $number('investor_number', 'Número'),
                    Textarea::make('investor_template')->label('Mensaje')->rows(5)->required()
                        ->helperText('Variables: {nombre}, {pais}, {email}, {telefono}, {modalidad}.'),
                ]),
        ]);
    }
}
