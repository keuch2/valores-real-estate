<?php

namespace App\Filament\Resources\Projects;

use App\Filament\Resources\Projects\Pages\CreateProject;
use App\Filament\Resources\Projects\Pages\EditProject;
use App\Filament\Resources\Projects\Pages\ListProjects;
use App\Filament\Resources\Projects\RelationManagers\UnitsRelationManager;
use App\Filament\Support\Media;
use App\Models\Project;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Str;
use UnitEnum;

class ProjectResource extends Resource
{
    protected static ?string $model = Project::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBuildingOffice2;

    protected static string|UnitEnum|null $navigationGroup = 'Contenido';

    protected static ?int $navigationSort = 1;

    protected static ?string $modelLabel = 'proyecto';

    protected static ?string $pluralModelLabel = 'proyectos';

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Tabs::make('Proyecto')->columnSpanFull()->persistTabInQueryString()->tabs([
                Tab::make('General')->icon(Heroicon::OutlinedInformationCircle)->schema([
                    Grid::make(2)->schema([
                        TextInput::make('name')->label('Nombre corto')->required()->maxLength(120)
                            ->helperText('Se usa en tarjetas, mapa, filtros y comparador.')
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (string $operation, ?string $state, Set $set, Get $get) {
                                if ($operation === 'create' && blank($get('slug'))) {
                                    $set('slug', Str::slug((string) $state));
                                }
                                if (blank($get('full_name'))) {
                                    $set('full_name', $state);
                                }
                            }),
                        TextInput::make('full_name')->label('Nombre completo')->required()->maxLength(160),
                        TextInput::make('slug')->label('URL')->prefix('/proyectos/')->required()->maxLength(80)
                            ->alphaDash()->unique(ignoreRecord: true)
                            ->helperText('Si se cambia, la dirección anterior redirige a la nueva.'),
                        TextInput::make('type_label')->label('Tipo')->required()->placeholder('Barrio cerrado · Lotes'),
                        TextInput::make('location')->label('Ubicación')->required()->placeholder('San Bernardino, Cordillera'),
                        Select::make('kind')->label('Disponibilidad')->options(Project::KINDS)->required()->default('lots')
                            ->helperText('Lotes: tabla con masterplan. Unidades: tipologías y tabla de departamentos.'),
                        TextInput::make('delivery')->label('Fecha de entrega')->placeholder('Diciembre 2026'),
                        TextInput::make('instagram')->label('Instagram')->prefix('@')->placeholder('usuario'),
                    ]),
                    Grid::make(2)->schema([
                        Toggle::make('is_published')->label('Publicado')
                            ->helperText('Si está publicado aparece en inicio, proyectos, mapa, comparador y noticias.'),
                    ]),
                ]),

                Tab::make('Textos')->icon(Heroicon::OutlinedDocumentText)->schema([
                    TextInput::make('tagline')->label('Título de presentación')->maxLength(160),
                    Textarea::make('short')->label('Resumen')->rows(2)->maxLength(500)
                        ->helperText('Aparece en el listado de proyectos.'),
                    Textarea::make('body')->label('Descripción')->rows(10)
                        ->helperText('Separá los párrafos con una línea en blanco.'),
                    Textarea::make('meta_description')->label('Descripción para Google')->rows(2)->maxLength(300),
                ]),

                Tab::make('Medios')->icon(Heroicon::OutlinedPhoto)->schema([
                    Grid::make(2)->schema([
                        Media::image('hero_image', 'projects')->label('Imagen principal')->required(),
                        Toggle::make('hero_is_render')->label('La imagen principal es un render')->inline(false),
                        Media::image('masterplan_image', 'projects')->label('Masterplan'),
                        Media::pdf('brochure_path', 'projects')->label('Brochure (PDF)'),
                        Media::video('video_path', 'projects')->label('Video'),
                        Media::image('video_poster', 'projects')->label('Portada del video'),
                    ]),
                ]),

                Tab::make('Galería')->icon(Heroicon::OutlinedSquares2x2)->schema([
                    Repeater::make('images')->label('Imágenes')->relationship()->orderColumn('sort_order')
                        ->reorderableWithButtons()->collapsible()->grid(2)->addActionLabel('Agregar imagen')
                        ->itemLabel(fn (array $state) => $state['alt'] ?? null)
                        ->schema([
                            Media::image('path', 'projects')->label('Imagen')->required(),
                            TextInput::make('alt')->label('Texto alternativo')->maxLength(200),
                            Toggle::make('is_render')->label('Es un render (muestra la leyenda)'),
                        ]),
                ]),

                Tab::make('Datos')->icon(Heroicon::OutlinedListBullet)->schema([
                    Repeater::make('facts')->label('Datos destacados')->reorderableWithButtons()->grid(2)->defaultItems(0)
                        ->addActionLabel('Agregar dato')
                        ->schema([
                            TextInput::make('value')->label('Valor')->required()->placeholder('12 ha'),
                            TextInput::make('label')->label('Descripción')->required()->placeholder('Superficie del condominio'),
                        ]),
                    Section::make('Amenities')->collapsible()->schema([
                        TextInput::make('amenities_title')->label('Título')->placeholder('Bienestar y confort, todos los días'),
                        TagsInput::make('amenities')->label('Amenities')->reorderable()->placeholder('Escribí y presioná Enter'),
                    ]),
                    Section::make('Zona deportiva')->collapsible()->schema([
                        TextInput::make('sports_title')->label('Título')->placeholder('Canchas de pádel')
                            ->helperText('Dejalo vacío para ocultar el bloque.'),
                        Textarea::make('sports_text')->label('Texto')->rows(2),
                        TagsInput::make('sports_phones')->label('Teléfonos de informes')->placeholder('0981 419 177'),
                    ]),
                    Textarea::make('availability_note')->label('Nota de disponibilidad')->rows(2)->maxLength(500)
                        ->helperText('Aparece debajo de la tabla de lotes o unidades.'),
                ]),

                Tab::make('Tipologías')->icon(Heroicon::OutlinedHome)
                    ->visible(fn (Get $get) => $get('kind') === 'units')
                    ->schema([
                        Repeater::make('typologies')->label('Tipologías')->relationship()->orderColumn('sort_order')
                            ->reorderableWithButtons()->collapsible()->addActionLabel('Agregar tipología')
                            ->itemLabel(fn (array $state) => $state['name'] ?? null)
                            ->schema([
                                TextInput::make('name')->label('Nombre')->required()->placeholder('2 dormitorios · Tipo A'),
                                Repeater::make('specs')->label('Características')->reorderableWithButtons()->grid(2)
                                    ->schema([
                                        TextInput::make('label')->label('Dato')->required(),
                                        TextInput::make('value')->label('Valor')->required(),
                                    ]),
                                Repeater::make('images')->label('Plantas e imágenes')->reorderableWithButtons()->grid(2)
                                    ->schema([
                                        Media::image('path', 'projects')->label('Imagen')->required(),
                                        Toggle::make('is_render')->label('Es un render'),
                                    ]),
                            ]),
                    ]),

                Tab::make('Mapa')->icon(Heroicon::OutlinedMapPin)->schema([
                    Section::make('Mapa interactivo (Leaflet)')->schema([
                        Grid::make(2)->schema([
                            TextInput::make('lat')->label('Latitud')->numeric()->placeholder('-25.3095'),
                            TextInput::make('lng')->label('Longitud')->numeric()->placeholder('-57.2960'),
                        ]),
                    ])->description('En Google Maps: clic derecho sobre el lugar y copiá las coordenadas.'),
                    Section::make('Mapa del Paraguay (inicio)')->schema([
                        Grid::make(3)->schema([
                            TextInput::make('map_x')->label('Posición horizontal (%)')->numeric()->minValue(0)->maxValue(100),
                            TextInput::make('map_y')->label('Posición vertical (%)')->numeric()->minValue(0)->maxValue(100),
                            Select::make('map_label_side')->label('Etiqueta')->options([
                                'top' => 'Arriba', 'bottom' => 'Abajo', 'left' => 'Izquierda', 'right' => 'Derecha',
                            ])->default('top'),
                        ]),
                    ])->description('Porcentaje sobre el ancho y alto del mapa. Vacío = no aparece en el mapa del inicio.'),
                ]),

                Tab::make('Comparador')->icon(Heroicon::OutlinedTableCells)->schema([
                    TextInput::make('compare_type')->label('Tipo'),
                    TextInput::make('compare_surface')->label('Superficie'),
                    TextInput::make('compare_amenities')->label('Amenities'),
                    TextInput::make('compare_environment')->label('Entorno'),
                ]),

                Tab::make('WhatsApp')->icon(Heroicon::OutlinedChatBubbleLeftRight)->schema([
                    TextInput::make('whatsapp_number')->label('Número propio del proyecto')->tel()
                        ->helperText('Opcional. Vacío = se usa el número configurado en Configuración › WhatsApp.'),
                    Textarea::make('visit_message')->label('Mensaje de "Agendá tu visita"')->rows(2)
                        ->helperText('Opcional. Vacío = plantilla general con el nombre del proyecto.'),
                    Textarea::make('info_message')->label('Mensaje de "Escribinos por WhatsApp"')->rows(2),
                ]),

                Tab::make('Avanzado')->icon(Heroicon::OutlinedCog6Tooth)->schema([
                    TextInput::make('news_tag')->label('Etiqueta en noticias')
                        ->helperText('Vacío = nombre corto. Se usa en los filtros de Noticias.'),
                    Grid::make(2)->schema([
                        Select::make('import_format')->label('Formato de la planilla de lotes')->options(Project::IMPORT_FORMATS)->default('generic'),
                        TextInput::make('import_sheet')->label('Nombre de la hoja en la planilla'),
                    ]),
                ]),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->columns([
                ImageColumn::make('hero_image')->label('')->disk('public')->imageHeight(48),
                TextColumn::make('name')->label('Proyecto')->searchable()->description(fn (Project $p) => $p->location),
                TextColumn::make('type_label')->label('Tipo')->toggleable(),
                TextColumn::make('units_count')->label('Lotes / unidades')->counts('units'),
                ToggleColumn::make('is_published')->label('Publicado'),
                TextColumn::make('updated_at')->label('Actualizado')->since()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([TrashedFilter::make()])
            ->recordActions([
                Action::make('ver')->label('Ver en sitio')->icon(Heroicon::OutlinedArrowTopRightOnSquare)
                    ->url(fn (Project $p) => route('projects.show', $p->slug), shouldOpenInNewTab: true)
                    ->visible(fn (Project $p) => $p->is_published && ! $p->trashed()),
                EditAction::make(),
                DeleteAction::make(),
                RestoreAction::make(),
                ForceDeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [UnitsRelationManager::class];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListProjects::route('/'),
            'create' => CreateProject::route('/create'),
            'edit' => EditProject::route('/{record}/edit'),
        ];
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()->withoutGlobalScopes([SoftDeletingScope::class]);
    }
}
