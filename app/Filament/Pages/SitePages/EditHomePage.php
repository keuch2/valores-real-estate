<?php

namespace App\Filament\Pages\SitePages;

use App\Filament\Support\Media;
use BackedEnum;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Support\Icons\Heroicon;

class EditHomePage extends EditSitePage
{
    protected static string $pageKey = 'home';

    protected static string $publicRoute = 'home';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedHome;

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationLabel = 'Inicio';

    protected static ?string $title = 'Página de inicio';

    protected static ?string $slug = 'paginas/inicio';

    protected function contentSchema(): array
    {
        return [
            Section::make('Portada')->columns(2)->columnSpanFull()->schema([
                Media::image('hero_image', 'pages')->label('Imagen de portada')->required(),
                Toggle::make('hero_is_render')->label('La imagen es un render')->inline(false),
                TextInput::make('hero_title')->label('Título')->required()->columnSpanFull(),
                TextInput::make('hero_text')->label('Texto')->columnSpanFull(),
                TextInput::make('hero_cta')->label('Texto del botón')->required(),
            ]),
            Section::make('Mapa de proyectos')->columns(2)->columnSpanFull()
                ->description('Los proyectos y su posición se editan en cada proyecto (pestaña Mapa).')
                ->schema([
                    TextInput::make('map_eyebrow')->label('Antetítulo'),
                    TextInput::make('map_title')->label('Título'),
                    TextInput::make('map_lead')->label('Texto')->columnSpanFull(),
                ]),
            Section::make('Proyectos')->columns(3)->columnSpanFull()->schema([
                TextInput::make('projects_eyebrow')->label('Antetítulo'),
                TextInput::make('projects_title')->label('Título'),
                TextInput::make('projects_link')->label('Texto del enlace'),
            ]),
            Section::make('Noticias')->columns(2)->columnSpanFull()->schema([
                TextInput::make('news_eyebrow')->label('Antetítulo'),
                TextInput::make('news_title')->label('Título'),
                TextInput::make('news_link')->label('Texto del enlace'),
                TextInput::make('news_count')->label('Cantidad de noticias')->numeric()->minValue(1)->maxValue(12)->default(3),
            ]),
        ];
    }
}
