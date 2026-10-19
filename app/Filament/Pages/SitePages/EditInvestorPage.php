<?php

namespace App\Filament\Pages\SitePages;

use App\Filament\Support\Media;
use BackedEnum;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Support\Icons\Heroicon;

class EditInvestorPage extends EditSitePage
{
    protected static string $pageKey = 'investor';

    protected static string $publicRoute = 'investor';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedGlobeAmericas;

    protected static ?int $navigationSort = 7;

    protected static ?string $navigationLabel = 'Investor Pass';

    protected static ?string $title = 'Página Investor Pass';

    protected static ?string $slug = 'paginas/investor-pass';

    protected function contentSchema(): array
    {
        return [
            Section::make('Encabezado')->columns(2)->columnSpanFull()->schema([
                TextInput::make('eyebrow')->label('Antetítulo'),
                TextInput::make('cta')->label('Texto del botón'),
                TextInput::make('title')->label('Título')->required()->columnSpanFull(),
                Textarea::make('lead')->label('Texto')->rows(3)->columnSpanFull(),
                Media::image('banner', 'pages')->label('Imagen'),
                Toggle::make('banner_is_render')->label('La imagen es un render')->inline(false),
            ]),
            Section::make('Modalidades')->columnSpanFull()->schema([
                TextInput::make('modes_eyebrow')->label('Antetítulo'),
                TextInput::make('modes_title')->label('Título'),
                Repeater::make('modes')->label('Tarjetas')->reorderableWithButtons()->collapsible()->columns(3)
                    ->itemLabel(fn (array $state) => $state['kicker'] ?? null)
                    ->schema([
                        TextInput::make('kicker')->label('Nombre')->required(),
                        TextInput::make('tag')->label('Etiqueta')->placeholder('Disponible'),
                        Select::make('style')->label('Color')->options(['blue' => 'Azul', 'amber' => 'Amarillo', 'info' => 'Gris (informativa)'])->default('blue')->required(),
                        TextInput::make('amount')->label('Monto')->required()->placeholder('USD 200.000'),
                        Textarea::make('text')->label('Texto')->rows(3)->columnSpan(2)
                            ->helperText('{proyectos} se reemplaza por los proyectos publicados ("A, B y C").'),
                        TextInput::make('link_label')->label('Texto del enlace (opcional)'),
                        TextInput::make('link_url')->label('Enlace (opcional)')->url()->columnSpan(2),
                    ]),
            ]),
            Section::make('Proceso')->columnSpanFull()->schema([
                TextInput::make('steps_eyebrow')->label('Antetítulo'),
                TextInput::make('steps_title')->label('Título'),
                Repeater::make('steps')->label('Pasos')->reorderableWithButtons()->columns(3)
                    ->schema([
                        TextInput::make('number')->label('Número')->required()->placeholder('01'),
                        TextInput::make('title')->label('Título')->required(),
                        Textarea::make('text')->label('Texto')->rows(2),
                    ]),
            ]),
            Section::make('Formulario')->columns(2)->columnSpanFull()
                ->description('El envío se guarda en Mensajes recibidos y abre WhatsApp. Número y mensaje: Configuración › WhatsApp.')
                ->schema([
                    TextInput::make('form_title')->label('Título'),
                    TextInput::make('submit_label')->label('Texto del botón'),
                    Textarea::make('form_lead')->label('Texto')->rows(2)->columnSpanFull(),
                    Repeater::make('modality_options')->label('Opciones de modalidad')->simple(TextInput::make('label')->required())
                        ->reorderableWithButtons()->columnSpanFull(),
                    TextInput::make('success_title')->label('Título al enviar'),
                    TextInput::make('success_text')->label('Texto al enviar'),
                ]),
        ];
    }
}
