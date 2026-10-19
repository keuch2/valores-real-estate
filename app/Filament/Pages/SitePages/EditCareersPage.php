<?php

namespace App\Filament\Pages\SitePages;

use App\Filament\Support\Media;
use BackedEnum;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Support\Icons\Heroicon;

class EditCareersPage extends EditSitePage
{
    protected static string $pageKey = 'trabaja';

    protected static string $publicRoute = 'careers';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBriefcase;

    protected static ?int $navigationSort = 5;

    protected static ?string $navigationLabel = 'Trabaja con nosotros';

    protected static ?string $title = 'Página Trabaja con nosotros';

    protected static ?string $slug = 'paginas/trabaja-con-nosotros';

    protected function contentSchema(): array
    {
        return [
            Section::make('Contenido')->columns(2)->columnSpanFull()->schema([
                TextInput::make('eyebrow')->label('Antetítulo'),
                TextInput::make('title')->label('Título')->required(),
                Textarea::make('lead')->label('Texto')->rows(3)->columnSpanFull(),
                Media::image('image', 'pages')->label('Imagen'),
                Toggle::make('image_is_render')->label('La imagen es un render')->inline(false),
            ]),
            Section::make('Formulario')->columns(2)->columnSpanFull()
                ->description('Las postulaciones llegan a Formularios › Mensajes recibidos y por email a las casillas de Configuración › Email.')
                ->schema([
                    Repeater::make('areas')->label('Áreas de interés')->simple(TextInput::make('label')->required())
                        ->reorderableWithButtons()->columnSpanFull(),
                    TextInput::make('success_title')->label('Título al enviar'),
                    TextInput::make('success_text')->label('Texto al enviar'),
                ]),
        ];
    }
}
