<?php

namespace App\Filament\Resources\FormSubmissions\Pages;

use App\Filament\Resources\FormSubmissions\FormSubmissionResource;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\ViewRecord;
use Filament\Support\Icons\Heroicon;

class ViewFormSubmission extends ViewRecord
{
    protected static string $resource = FormSubmissionResource::class;

    public function mount(int|string $record): void
    {
        parent::mount($record);
        if (! $this->record->read_at) {
            $this->record->update(['read_at' => now()]);
        }
    }

    protected function getHeaderActions(): array
    {
        return [
            FormSubmissionResource::downloadCvAction(),
            Action::make('reply')->label('Responder por email')->icon(Heroicon::OutlinedEnvelope)
                ->url(fn () => 'mailto:'.$this->record->email)->visible(fn () => (bool) $this->record->email),
            Action::make('unread')->label('Marcar como no leído')->color('gray')
                ->action(function () {
                    $this->record->update(['read_at' => null]);
                    $this->redirect(FormSubmissionResource::getUrl());
                }),
            DeleteAction::make(),
        ];
    }
}
