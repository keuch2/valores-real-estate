<?php

namespace App\Filament\Resources\Projects\RelationManagers;

use App\Imports\LotSheetImporter;
use App\Models\Project;
use App\Models\ProjectUnit;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Throwable;

class UnitsRelationManager extends RelationManager
{
    protected static string $relationship = 'units';

    protected static ?string $title = 'Lotes y unidades';

    protected static ?string $modelLabel = 'lote / unidad';

    protected static ?string $pluralModelLabel = 'lotes y unidades';

    public static function canViewForRecord(Model $ownerRecord, string $pageClass): bool
    {
        return $ownerRecord->kind !== 'none';
    }

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Grid::make(3)->schema([
                TextInput::make('block')->label('Manzana')->maxLength(20),
                TextInput::make('number')->label('Lote / unidad')->required()->maxLength(20),
                TextInput::make('floor')->label('Piso')->numeric()->minValue(0),
                TextInput::make('type')->label('Tipo / tipología')->maxLength(120),
                TextInput::make('area_m2')->label('Superficie (m²)')->numeric()->minValue(0),
                TextInput::make('orientation')->label('Ubicación')->placeholder('Frente / Contrafrente'),
                Select::make('status')->label('Estado')->options(ProjectUnit::STATUSES)->required()->default('disponible'),
            ]),
        ]);
    }

    public function table(Table $table): Table
    {
        $isUnits = $this->getOwnerRecord()->kind === 'units';

        return $table
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->paginated([25, 50, 100, 'all'])
            ->defaultPaginationPageOption(50)
            ->columns([
                TextColumn::make('block')->label('Manzana')->searchable()->sortable()->hidden($isUnits),
                TextColumn::make('number')->label($isUnits ? 'Unidad' : 'Lote')->searchable()->sortable(),
                TextColumn::make('floor')->label('Piso')->sortable()->visible($isUnits),
                TextColumn::make('type')->label('Tipo')->toggleable(),
                TextColumn::make('area_m2')->label('Superficie')->numeric(2, ',', '.')->suffix(' m²')->sortable(),
                TextColumn::make('orientation')->label('Ubicación')->visible($isUnits),
                SelectColumn::make('status')->label('Estado')->options(ProjectUnit::STATUSES)->selectablePlaceholder(false),
            ])
            ->filters([
                SelectFilter::make('status')->label('Estado')->options(ProjectUnit::STATUSES),
                SelectFilter::make('block')->label('Manzana')->hidden($isUnits)
                    ->options(fn () => ProjectUnit::where('project_id', $this->getOwnerRecord()->id)->whereNotNull('block')
                        ->distinct()->orderBy('block')->pluck('block', 'block')->all()),
            ])
            ->headerActions([
                $this->importAction(),
                CreateAction::make()->label('Agregar'),
            ])
            ->recordActions([EditAction::make(), DeleteAction::make()])
            ->toolbarActions([
                BulkActionGroup::make([
                    BulkAction::make('status')->label('Cambiar estado')->icon(Heroicon::OutlinedTag)
                        ->schema([Select::make('status')->label('Nuevo estado')->options(ProjectUnit::STATUSES)->required()])
                        ->action(fn (Collection $records, array $data) => ProjectUnit::whereKey($records->modelKeys())->update(['status' => $data['status']]))
                        ->deselectRecordsAfterCompletion(),
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    private function importAction(): Action
    {
        return Action::make('import')
            ->label('Importar planilla')
            ->icon(Heroicon::OutlinedArrowUpTray)
            ->color('gray')
            ->modalHeading('Importar lotes y unidades desde Excel')
            ->modalDescription(function () {
                $p = $this->getOwnerRecord();
                $format = Project::IMPORT_FORMATS[$p->import_format] ?? $p->import_format;

                return "Se reemplazan todos los lotes/unidades de este proyecto. Formato: $format · Hoja: "
                    .($p->import_sheet ?: 'la primera de la planilla').'. Los precios de la planilla no se importan ni se guardan.';
            })
            ->schema([
                FileUpload::make('file')->label('Planilla (.xlsx)')->required()
                    ->disk('local')->directory('imports')->visibility('private')
                    ->acceptedFileTypes(['application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'])
                    ->maxSize(20 * 1024),
            ])
            ->modalSubmitActionLabel('Importar')
            ->action(function (array $data, LotSheetImporter $importer) {
                $project = $this->getOwnerRecord();
                $disk = Storage::disk('local');
                try {
                    $rows = $importer->parse($disk->path($data['file']), $project->import_format, $project->import_sheet);
                    $before = $project->units()->count();
                    $importer->replace($project, $rows);
                    $summary = collect(LotSheetImporter::summary($rows))
                        ->map(fn ($n, $s) => (ProjectUnit::STATUSES[$s] ?? $s).": $n")->implode(' · ');
                    Notification::make()->success()->title('Planilla importada')
                        ->body("Antes: $before · Ahora: ".count($rows)." ($summary)")->send();
                } catch (Throwable $e) {
                    Notification::make()->danger()->title('No se pudo importar')->body($e->getMessage())->persistent()->send();
                } finally {
                    $disk->delete($data['file']); // la planilla tiene precios y cuentas: no se conserva
                }
            });
    }
}
