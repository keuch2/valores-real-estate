<?php

namespace App\Filament\Resources\Users;

use App\Filament\Resources\Users\Pages\CreateUser;
use App\Filament\Resources\Users\Pages\EditUser;
use App\Filament\Resources\Users\Pages\ListUsers;
use App\Models\User;
use BackedEnum;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Hash;
use UnitEnum;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUsers;

    protected static string|UnitEnum|null $navigationGroup = 'Configuración';

    protected static ?int $navigationSort = 10;

    protected static ?string $modelLabel = 'usuario';

    protected static ?string $pluralModelLabel = 'usuarios';

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make()->columnSpanFull()->columns(2)->schema([
                TextInput::make('name')->label('Nombre')->required()->maxLength(120),
                TextInput::make('email')->label('Email')->email()->required()->unique(ignoreRecord: true)->maxLength(160),
                TextInput::make('password')->label('Contraseña')->password()->revealable()
                    ->rule(\Illuminate\Validation\Rules\Password::min(10)->letters()->numbers())
                    ->required(fn (string $operation) => $operation === 'create')
                    ->dehydrated(fn (?string $state) => filled($state))
                    ->dehydrateStateUsing(fn (string $state) => Hash::make($state))
                    ->helperText(fn (string $operation) => $operation === 'edit' ? 'Dejala vacía para no cambiarla.' : 'Mínimo 10 caracteres, con letras y números.'),
                Toggle::make('is_active')->label('Activo (puede ingresar al CMS)')->default(true)
                    ->disabled(fn (?User $record) => $record?->is(auth()->user()))
                    ->helperText(fn (?User $record) => $record?->is(auth()->user()) ? 'No podés desactivar tu propio usuario.' : null),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label('Nombre')->searchable(),
                TextColumn::make('email')->label('Email')->searchable(),
                IconColumn::make('is_active')->label('Activo')->boolean(),
                TextColumn::make('created_at')->label('Creado')->date('d/m/Y'),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make()->hidden(fn (User $record) => $record->is(auth()->user())
                    || ($record->is_active && User::where('is_active', true)->count() <= 1)),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListUsers::route('/'),
            'create' => CreateUser::route('/create'),
            'edit' => EditUser::route('/{record}/edit'),
        ];
    }
}
