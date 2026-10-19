<?php

namespace App\Filament\Pages\SitePages;

use BackedEnum;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Support\Icons\Heroicon;

class EditProjectsPage extends EditSitePage
{
    protected static string $pageKey = 'proyectos';

    protected static string $publicRoute = 'projects.index';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBuildingOffice2;

    protected static ?int $navigationSort = 2;

    protected static ?string $navigationLabel = 'Proyectos';

    protected static ?string $title = 'Página de proyectos';

    protected static ?string $slug = 'paginas/proyectos';

    protected function contentSchema(): array
    {
        return [
            Section::make('Encabezado')->columns(2)->columnSpanFull()->schema([
                TextInput::make('intro_eyebrow')->label('Antetítulo'),
                TextInput::make('intro_title')->label('Título'),
                TextInput::make('intro_lead')->label('Texto')->columnSpanFull(),
            ]),
            Section::make('Tarjeta "Próximamente"')->columns(3)->columnSpanFull()->schema([
                Toggle::make('soon_enabled')->label('Mostrar tarjeta')->columnSpanFull(),
                TextInput::make('soon_chip')->label('Etiqueta'),
                TextInput::make('soon_title')->label('Título'),
                TextInput::make('soon_text')->label('Texto'),
            ]),
            Section::make('Mapa y comparador')->columns(2)->columnSpanFull()
                ->description('Los valores de cada proyecto en el comparador se editan en el proyecto (pestaña Comparador).')
                ->schema([
                    TextInput::make('map_eyebrow')->label('Antetítulo del mapa'),
                    TextInput::make('map_title')->label('Título del mapa'),
                    TextInput::make('compare_eyebrow')->label('Antetítulo del comparador'),
                    TextInput::make('compare_title')->label('Título del comparador'),
                    TextInput::make('compare_labels.location')->label('Fila: ubicación'),
                    TextInput::make('compare_labels.type')->label('Fila: tipo'),
                    TextInput::make('compare_labels.surface')->label('Fila: superficie'),
                    TextInput::make('compare_labels.amenities')->label('Fila: amenities'),
                    TextInput::make('compare_labels.environment')->label('Fila: entorno'),
                ]),
        ];
    }
}
