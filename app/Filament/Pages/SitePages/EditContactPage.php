<?php

namespace App\Filament\Pages\SitePages;

use BackedEnum;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Support\Icons\Heroicon;

class EditContactPage extends EditSitePage
{
    protected static string $pageKey = 'contacto';

    protected static string $publicRoute = 'contact';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedPhone;

    protected static ?int $navigationSort = 6;

    protected static ?string $navigationLabel = 'Contacto';

    protected static ?string $title = 'Página de contacto';

    protected static ?string $slug = 'paginas/contacto';

    protected function contentSchema(): array
    {
        return [
            Section::make('Encabezado')->columns(2)->columnSpanFull()
                ->description('Dirección, teléfonos y horario se editan en Configuración › Contacto y redes.')
                ->schema([
                    TextInput::make('eyebrow')->label('Antetítulo'),
                    TextInput::make('title')->label('Título')->required(),
                    TextInput::make('whatsapp_cta')->label('Texto del botón de WhatsApp'),
                ]),
            Section::make('Formulario')->columns(2)->columnSpanFull()->schema([
                TextInput::make('success_title')->label('Título al enviar'),
                TextInput::make('success_text')->label('Texto al enviar'),
            ]),
            Section::make('Asesoras')->columns(2)->columnSpanFull()
                ->description('Las asesoras se cargan en Contenido › Asesoras comerciales.')
                ->schema([
                    TextInput::make('advisors_eyebrow')->label('Antetítulo'),
                    TextInput::make('advisors_title')->label('Título'),
                ]),
        ];
    }
}
