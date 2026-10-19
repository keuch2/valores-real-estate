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

class EditAboutPage extends EditSitePage
{
    protected static string $pageKey = 'nosotros';

    protected static string $publicRoute = 'about';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBuildingLibrary;

    protected static ?int $navigationSort = 4;

    protected static ?string $navigationLabel = 'Nosotros';

    protected static ?string $title = 'Página Nosotros';

    protected static ?string $slug = 'paginas/nosotros';

    protected function contentSchema(): array
    {
        return [
            Section::make('Encabezado')->columns(2)->columnSpanFull()->schema([
                TextInput::make('eyebrow')->label('Antetítulo'),
                TextInput::make('title')->label('Título')->required(),
            ]),
            Section::make('Texto e imagen')->columns(2)->columnSpanFull()->schema([
                Media::image('image', 'pages')->label('Imagen'),
                Toggle::make('image_is_render')->label('La imagen es un render')->inline(false),
                TextInput::make('image_alt')->label('Descripción de la imagen'),
                Repeater::make('paragraphs')->label('Párrafos')->reorderableWithButtons()->columnSpanFull()->addActionLabel('Agregar párrafo')
                    ->schema([Textarea::make('text')->hiddenLabel()->rows(4)->required()]),
            ]),
            Section::make('Plana directiva')->columns(2)->columnSpanFull()
                ->description('Las personas se cargan en Contenido › Plana directiva.')
                ->schema([
                    TextInput::make('board_eyebrow')->label('Antetítulo'),
                    TextInput::make('board_title')->label('Título'),
                ]),
        ];
    }
}
