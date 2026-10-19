<?php

namespace App\Filament\Resources\FormSubmissions;

use App\Filament\Resources\FormSubmissions\Pages\ListFormSubmissions;
use App\Filament\Resources\FormSubmissions\Pages\ViewFormSubmission;
use App\Models\FormSubmission;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ViewAction;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Storage;
use UnitEnum;

class FormSubmissionResource extends Resource
{
    protected static ?string $model = FormSubmission::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedInboxArrowDown;

    protected static string|UnitEnum|null $navigationGroup = 'Formularios';

    protected static ?string $navigationLabel = 'Mensajes recibidos';

    protected static ?string $modelLabel = 'mensaje';

    protected static ?string $pluralModelLabel = 'mensajes recibidos';

    protected static ?string $recordTitleAttribute = 'name';

    public static function canCreate(): bool
    {
        return false;
    }

    public static function getNavigationBadge(): ?string
    {
        $unread = FormSubmission::whereNull('read_at')->count();

        return $unread ? (string) $unread : null;
    }

    public static function getNavigationBadgeTooltip(): ?string
    {
        return 'Sin leer';
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Datos del mensaje')->columnSpanFull()->columns(3)->schema([
                TextEntry::make('type')->label('Formulario')->formatStateUsing(fn ($state) => FormSubmission::TYPES[$state] ?? $state)->badge(),
                TextEntry::make('created_at')->label('Recibido')->dateTime('d/m/Y H:i'),
                TextEntry::make('project.full_name')->label('Proyecto')->placeholder('—'),
                TextEntry::make('name')->label('Nombre'),
                TextEntry::make('email')->label('Email')->copyable()->url(fn ($state) => $state ? "mailto:$state" : null),
                TextEntry::make('phone')->label('Teléfono')->copyable()->placeholder('—'),
                TextEntry::make('country')->label('País')->placeholder('—')->visible(fn (FormSubmission $r) => $r->type === 'investor'),
                TextEntry::make('modality')->label('Modalidad')->placeholder('—')->visible(fn (FormSubmission $r) => $r->type === 'investor'),
                TextEntry::make('area')->label('Área de interés')->placeholder('—')->visible(fn (FormSubmission $r) => $r->type === 'careers'),
                TextEntry::make('cv_original_name')->label('CV')->placeholder('Sin CV')->visible(fn (FormSubmission $r) => $r->type === 'careers'),
                TextEntry::make('message')->label('Mensaje')->placeholder('—')->columnSpanFull()->prose(),
            ]),
            Section::make('Envío y verificación')->columnSpanFull()->columns(3)->collapsed()->schema([
                TextEntry::make('mail_sent_at')->label('Aviso por email')->dateTime('d/m/Y H:i')
                    ->placeholder(fn (FormSubmission $r) => $r->mail_error ? 'Falló el envío' : 'No enviado'),
                TextEntry::make('mail_error')->label('Error del email')->placeholder('—')->columnSpan(2),
                TextEntry::make('recaptcha_score')->label('Puntaje reCAPTCHA')->placeholder('—'),
                TextEntry::make('is_spam_suspect')->label('Posible spam')->formatStateUsing(fn ($state) => $state ? 'Sí' : 'No'),
                TextEntry::make('ip')->label('IP'),
                TextEntry::make('whatsapp_url')->label('Mensaje de WhatsApp generado')->placeholder('—')->columnSpanFull()
                    ->formatStateUsing(function (?string $state) {
                        parse_str((string) parse_url((string) $state, PHP_URL_QUERY), $query);

                        return $query['text'] ?? $state;
                    }),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->recordClasses(fn (FormSubmission $r) => $r->read_at ? null : 'font-semibold')
            ->columns([
                IconColumn::make('read_at')->label('')->boolean()->getStateUsing(fn (FormSubmission $r) => (bool) $r->read_at)
                    ->trueIcon(Heroicon::OutlinedEnvelopeOpen)->falseIcon(Heroicon::Envelope)->trueColor('gray')->falseColor('primary'),
                TextColumn::make('created_at')->label('Fecha')->dateTime('d/m/Y H:i')->sortable(),
                TextColumn::make('type')->label('Formulario')->formatStateUsing(fn ($state) => FormSubmission::TYPES[$state] ?? $state)->badge(),
                TextColumn::make('name')->label('Nombre')->searchable()->description(fn (FormSubmission $r) => $r->project?->name),
                TextColumn::make('email')->label('Email')->searchable(),
                TextColumn::make('phone')->label('Teléfono')->toggleable(),
                IconColumn::make('cv_path')->label('CV')->icon(fn ($state) => $state ? Heroicon::OutlinedPaperClip : null)->color('gray')->toggleable(),
                IconColumn::make('is_spam_suspect')->label('Spam?')->icon(fn ($state) => $state ? Heroicon::OutlinedExclamationTriangle : null)
                    ->color('danger')->tooltip(fn ($state) => $state ? 'Posible spam' : null)->toggleable(),
            ])
            ->filters([
                SelectFilter::make('project_id')->label('Proyecto')->relationship('project', 'name'),
                TernaryFilter::make('read_at')->label('Leídos')->nullable()->trueLabel('Leídos')->falseLabel('Sin leer'),
                TernaryFilter::make('is_spam_suspect')->label('Posible spam'),
            ])
            ->recordActions([
                ViewAction::make(),
                self::downloadCvAction(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    BulkAction::make('markRead')->label('Marcar como leídos')->icon(Heroicon::OutlinedEnvelopeOpen)
                        ->action(fn (Collection $records) => FormSubmission::whereKey($records->modelKeys())->update(['read_at' => now()]))
                        ->deselectRecordsAfterCompletion(),
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function downloadCvAction(): Action
    {
        return Action::make('cv')->label('Descargar CV')->icon(Heroicon::OutlinedArrowDownTray)
            ->visible(fn (FormSubmission $r) => $r->cv_path && Storage::disk('local')->exists($r->cv_path))
            ->action(fn (FormSubmission $r) => Storage::disk('local')->download($r->cv_path, $r->cv_original_name ?: basename($r->cv_path)));
    }

    public static function getPages(): array
    {
        return [
            'index' => ListFormSubmissions::route('/'),
            'view' => ViewFormSubmission::route('/{record}'),
        ];
    }
}
