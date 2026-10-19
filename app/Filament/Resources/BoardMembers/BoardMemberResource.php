<?php

namespace App\Filament\Resources\BoardMembers;

use App\Filament\Resources\BoardMembers\Pages\CreateBoardMember;
use App\Filament\Resources\BoardMembers\Pages\EditBoardMember;
use App\Filament\Resources\BoardMembers\Pages\ListBoardMembers;
use App\Filament\Support\Media;
use App\Models\BoardMember;
use BackedEnum;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;
use UnitEnum;

class BoardMemberResource extends Resource
{
    protected static ?string $model = BoardMember::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUserGroup;

    protected static string|UnitEnum|null $navigationGroup = 'Contenido';

    protected static ?int $navigationSort = 3;

    protected static ?string $modelLabel = 'directivo';

    protected static ?string $pluralModelLabel = 'plana directiva';

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make()->columnSpanFull()->columns(2)->schema([
                TextInput::make('name')->label('Nombre')->required()->maxLength(160),
                TextInput::make('role')->label('Cargo')->required()->maxLength(160),
                Media::image('photo', 'board')->label('Foto')->imageCropAspectRatio('4:5')->imageEditor(),
                Textarea::make('bio')->label('Descripción')->rows(10)->helperText('Separá los párrafos con una línea en blanco.'),
                Toggle::make('is_visible')->label('Visible en Nosotros')->default(true),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->columns([
                ImageColumn::make('photo')->label('')->disk('public')->circular(),
                TextColumn::make('name')->label('Nombre')->searchable(),
                TextColumn::make('role')->label('Cargo'),
                ToggleColumn::make('is_visible')->label('Visible'),
            ])
            ->recordActions([EditAction::make(), DeleteAction::make()]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListBoardMembers::route('/'),
            'create' => CreateBoardMember::route('/create'),
            'edit' => EditBoardMember::route('/{record}/edit'),
        ];
    }
}
