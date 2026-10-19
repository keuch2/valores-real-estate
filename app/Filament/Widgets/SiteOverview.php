<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\FormSubmissions\FormSubmissionResource;
use App\Filament\Resources\News\NewsResource;
use App\Filament\Resources\Projects\ProjectResource;
use App\Models\FormSubmission;
use App\Models\News;
use App\Models\Project;
use App\Models\ProjectUnit;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class SiteOverview extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        $unread = FormSubmission::whereNull('read_at')->count();

        return [
            Stat::make('Mensajes sin leer', $unread)
                ->description(FormSubmission::where('created_at', '>=', now()->subDays(30))->count().' en los últimos 30 días')
                ->color($unread ? 'warning' : 'gray')
                ->url(FormSubmissionResource::getUrl()),
            Stat::make('Proyectos publicados', Project::where('is_published', true)->count())
                ->description(ProjectUnit::where('status', 'disponible')->count().' lotes/unidades disponibles')
                ->url(ProjectResource::getUrl()),
            Stat::make('Noticias publicadas', News::where('is_published', true)->count())
                ->url(NewsResource::getUrl()),
        ];
    }
}
