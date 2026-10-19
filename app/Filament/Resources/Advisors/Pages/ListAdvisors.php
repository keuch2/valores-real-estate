<?php

namespace App\Filament\Resources\Advisors\Pages;

use App\Filament\Resources\Advisors\AdvisorResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListAdvisors extends ListRecords
{
    protected static string $resource = AdvisorResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
