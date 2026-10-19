<?php

namespace App\Filament\Resources\Advisors;

use App\Filament\Resources\Advisors\Pages\CreateAdvisor;
use App\Filament\Resources\Advisors\Pages\EditAdvisor;
use App\Filament\Resources\Advisors\Pages\ListAdvisors;
use App\Models\Advisor;
use BackedEnum;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;
use UnitEnum;

class AdvisorResource extends Resource
{
    protected static ?string $model = Advisor::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedIdentification;

    protected static string|UnitEnum|null $navigationGroup = 'Contenido';

    protected static ?int $navigationSort = 4;

    protected static ?string $modelLabel = 'asesora';

    protected static ?string $pluralModelLabel = 'asesoras comerciales';

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make()->columnSpanFull()->columns(2)
                ->description('Los teléfonos personales no se publican: todos los botones de WhatsApp usan el número institucional.')
                ->schema([
                    TextInput::make('name')->label('Nombre')->required()->maxLength(120),
                    TextInput::make('role_label')->label('Cargo')->default('Asesora comercial')->required()->maxLength(120),
                    TextInput::make('email')->label('Email')->email()->maxLength(160),
                    TextInput::make('instagram')->label('Instagram')->prefix('@')->maxLength(80),
                    Toggle::make('is_visible')->label('Visible en el sitio')->default(true),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->columns([
                TextColumn::make('name')->label('Nombre')->searchable(),
                TextColumn::make('email')->label('Email'),
                TextColumn::make('instagram')->label('Instagram')->prefix('@'),
                ToggleColumn::make('is_visible')->label('Visible'),
            ])
            ->recordActions([EditAction::make(), DeleteAction::make()]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAdvisors::route('/'),
            'create' => CreateAdvisor::route('/create'),
            'edit' => EditAdvisor::route('/{record}/edit'),
        ];
    }
}
