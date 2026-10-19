<?php

namespace App\Filament\Pages\Settings;

use App\Settings\GeneralSettings;
use BackedEnum;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Filament\Pages\SettingsPage;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

class ManageGeneral extends SettingsPage
{
    protected static string $settings = GeneralSettings::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedGlobeAlt;

    protected static string|UnitEnum|null $navigationGroup = 'Configuración';

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationLabel = 'Contacto y redes';

    protected static ?string $title = 'Contacto y redes sociales';

    protected static ?string $slug = 'configuracion/general';

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Datos de contacto')->columnSpanFull()
                ->description('Se muestran en la página Contacto y en el pie de todas las páginas, en este orden.')
                ->schema([
                    Repeater::make('contact_rows')->hiddenLabel()->reorderableWithButtons()->columns(3)->addActionLabel('Agregar dato')
                        ->schema([
                            TextInput::make('label')->label('Etiqueta')->required()->placeholder('Teléfono'),
                            TextInput::make('value')->label('Valor')->required()->columnSpan(2),
                        ]),
                ]),
            Section::make('Ubicación de la oficina (mapa de Contacto)')->columns(2)->columnSpanFull()->schema([
                TextInput::make('office_lat')->label('Latitud')->numeric()->required(),
                TextInput::make('office_lng')->label('Longitud')->numeric()->required(),
            ]),
            Section::make('Redes sociales (pie de página)')->columnSpanFull()->schema([
                Repeater::make('socials')->hiddenLabel()->reorderableWithButtons()->columns(2)->addActionLabel('Agregar red')
                    ->schema([
                        TextInput::make('label')->label('Red')->required()->placeholder('Instagram'),
                        TextInput::make('url')->label('Enlace')->required()->placeholder('https://www.instagram.com/…'),
                    ]),
            ]),
            Section::make('Pie de página')->columnSpanFull()->schema([
                TextInput::make('footer_tagline')->label('Frase debajo del logo')->required()->maxLength(160),
            ]),
        ]);
    }
}
