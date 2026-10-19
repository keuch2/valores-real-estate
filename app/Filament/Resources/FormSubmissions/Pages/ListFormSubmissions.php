<?php

namespace App\Filament\Resources\FormSubmissions\Pages;

use App\Filament\Resources\FormSubmissions\FormSubmissionResource;
use App\Models\FormSubmission;
use Filament\Actions\Action;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Support\Icons\Heroicon;
use Illuminate\Database\Eloquent\Builder;

class ListFormSubmissions extends ListRecords
{
    protected static string $resource = FormSubmissionResource::class;

    public function getTabs(): array
    {
        $tabs = ['todos' => Tab::make('Todos')];
        foreach (FormSubmission::TYPES as $type => $label) {
            $tabs[$type] = Tab::make($label)
                ->modifyQueryUsing(fn (Builder $query) => $query->where('type', $type))
                ->badge(fn () => FormSubmission::where('type', $type)->whereNull('read_at')->count() ?: null);
        }

        return $tabs;
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('csv')->label('Exportar CSV')->icon(Heroicon::OutlinedArrowDownTray)->color('gray')
                ->action(function () {
                    $type = $this->activeTab && $this->activeTab !== 'todos' ? $this->activeTab : null;

                    return response()->streamDownload(function () use ($type) {
                        $out = fopen('php://output', 'w');
                        fwrite($out, "\xEF\xBB\xBF"); // BOM para que Excel lea los acentos
                        fputcsv($out, ['Fecha', 'Formulario', 'Proyecto', 'Nombre', 'Email', 'Teléfono', 'País', 'Área', 'Modalidad', 'Mensaje', 'CV', 'Posible spam']);
                        FormSubmission::with('project')->when($type, fn ($q) => $q->where('type', $type))
                            ->orderByDesc('created_at')->each(function (FormSubmission $s) use ($out) {
                                fputcsv($out, [
                                    $s->created_at->format('d/m/Y H:i'), FormSubmission::TYPES[$s->type] ?? $s->type, $s->project?->name,
                                    $s->name, $s->email, $s->phone, $s->country, $s->area, $s->modality, $s->message,
                                    $s->cv_original_name, $s->is_spam_suspect ? 'Sí' : 'No',
                                ]);
                            });
                        fclose($out);
                    }, 'mensajes-'.($type ?? 'todos').'-'.now()->format('Y-m-d').'.csv', ['Content-Type' => 'text/csv; charset=UTF-8']);
                }),
        ];
    }
}
