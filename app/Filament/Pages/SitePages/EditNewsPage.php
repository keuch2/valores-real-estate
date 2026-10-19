<?php

namespace App\Filament\Pages\SitePages;

use BackedEnum;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Support\Icons\Heroicon;

class EditNewsPage extends EditSitePage
{
    protected static string $pageKey = 'noticias';

    protected static string $publicRoute = 'news.index';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedNewspaper;

    protected static ?int $navigationSort = 3;

    protected static ?string $navigationLabel = 'Noticias';

    protected static ?string $title = 'Página de noticias';

    protected static ?string $slug = 'paginas/noticias';

    protected function contentSchema(): array
    {
        return [
            Section::make('Encabezado')->columns(2)->columnSpanFull()
                ->description('Las noticias se cargan en Contenido › Noticias.')
                ->schema([
                    TextInput::make('intro_eyebrow')->label('Antetítulo'),
                    TextInput::make('intro_title')->label('Título'),
                ]),
        ];
    }
}
