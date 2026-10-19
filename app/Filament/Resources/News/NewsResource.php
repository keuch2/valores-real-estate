<?php

namespace App\Filament\Resources\News;

use App\Filament\Resources\News\Pages\CreateNews;
use App\Filament\Resources\News\Pages\EditNews;
use App\Filament\Resources\News\Pages\ListNews;
use App\Filament\Support\Media;
use App\Models\News;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\ToggleButtons;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use UnitEnum;

class NewsResource extends Resource
{
    protected static ?string $model = News::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedNewspaper;

    protected static string|UnitEnum|null $navigationGroup = 'Contenido';

    protected static ?int $navigationSort = 2;

    protected static ?string $modelLabel = 'noticia';

    protected static ?string $pluralModelLabel = 'noticias';

    protected static ?string $recordTitleAttribute = 'title';

    public static function form(Schema $schema): Schema
    {
        $isArticle = fn (Get $get) => $get('kind') === 'article';

        return $schema->components([
            Section::make()->columnSpanFull()->schema([
                ToggleButtons::make('kind')->label('Tipo de noticia')->options(News::KINDS)->inline()->required()->default('external')->live(),
                TextInput::make('title')->label('Título')->required()->maxLength(300)
                    ->live(onBlur: true)
                    ->afterStateUpdated(function (?string $state, Set $set, Get $get) {
                        if ($get('kind') === 'article' && blank($get('slug'))) {
                            $set('slug', Str::slug(Str::limit((string) $state, 80, '')));
                        }
                    }),
                Grid::make(2)->schema([
                    TextInput::make('external_url')->label('Enlace a la nota')->url()->maxLength(500)
                        ->required(fn (Get $get) => ! $isArticle($get))->visible(fn (Get $get) => ! $isArticle($get)),
                    TextInput::make('slug')->label('URL de la nota')->prefix('/noticias/')->alphaDash()->maxLength(120)
                        ->unique(ignoreRecord: true)->required($isArticle)->visible($isArticle),
                    TextInput::make('source')->label('Medio')->placeholder('Forbes Paraguay')
                        ->helperText(fn (Get $get) => $isArticle($get) ? 'Opcional. Vacío = Valores Real Estate.' : null),
                    Select::make('project_id')->label('Proyecto relacionado')->relationship('project', 'name')->searchable()->preload(),
                    TextInput::make('tag')->label('Etiqueta del filtro')->placeholder('Vacío = nombre del proyecto; ej. Mercado'),
                    DateTimePicker::make('published_at')->label('Fecha de publicación')->default(now())->native(false)->displayFormat('d/m/Y H:i'),
                ]),
                Grid::make(2)->schema([
                    Media::image('image', 'news')->label('Foto destacada')->required(),
                    Toggle::make('image_is_render')->label('La foto es un render')->inline(false),
                ]),
                Textarea::make('excerpt')->label('Bajada')->rows(2)->maxLength(500)->visible($isArticle),
                RichEditor::make('body')->label('Contenido')->visible($isArticle)->required($isArticle)
                    ->fileAttachmentsDisk('public')->fileAttachmentsDirectory('news')->fileAttachmentsVisibility('public')
                    ->toolbarButtons([['bold', 'italic', 'link'], ['h2', 'h3', 'blockquote'], ['bulletList', 'orderedList'], ['attachFiles'], ['undo', 'redo']]),
                Textarea::make('meta_description')->label('Descripción para Google')->rows(2)->maxLength(300)->visible($isArticle),
                Toggle::make('is_published')->label('Publicada')->default(true),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('published_at', 'desc')
            ->columns([
                ImageColumn::make('image')->label('')->disk('public')->imageHeight(44),
                TextColumn::make('title')->label('Título')->searchable()->wrap()->limit(90)
                    ->description(fn (News $n) => $n->isExternal() ? ($n->source ?: 'Enlace externo') : 'Nota original'),
                TextColumn::make('tag')->label('Etiqueta')->state(fn (News $n) => $n->tagLabel())->badge(),
                TextColumn::make('published_at')->label('Fecha')->date('d/m/Y')->sortable(),
                ToggleColumn::make('is_published')->label('Publicada'),
            ])
            ->filters([
                SelectFilter::make('kind')->label('Tipo')->options(News::KINDS),
                SelectFilter::make('project_id')->label('Proyecto')->relationship('project', 'name'),
            ])
            ->recordActions([
                Action::make('ver')->label('Ver')->icon(Heroicon::OutlinedArrowTopRightOnSquare)
                    ->url(fn (News $n) => $n->url(), shouldOpenInNewTab: true),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListNews::route('/'),
            'create' => CreateNews::route('/create'),
            'edit' => EditNews::route('/{record}/edit'),
        ];
    }
}
