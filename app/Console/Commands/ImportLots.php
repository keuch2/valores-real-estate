<?php

namespace App\Console\Commands;

use App\Imports\LotSheetImporter;
use App\Models\Project;
use Illuminate\Console\Command;

class ImportLots extends Command
{
    protected $signature = 'lots:import {file : Ruta de la planilla .xlsx} {--project=* : Slugs de proyectos (por defecto, todos los que tienen hoja configurada)} {--dry-run : Mostrar el resultado sin guardar} {--json : Con --dry-run, imprimir las filas en JSON}';

    protected $description = 'Importa lotes y unidades desde la planilla del cliente (sin precios)';

    public function handle(LotSheetImporter $importer): int
    {
        $projects = Project::query()->whereNotNull('import_sheet')
            ->when($this->option('project'), fn ($q, $slugs) => $q->whereIn('slug', $slugs))->get();
        $result = [];
        foreach ($projects as $project) {
            $rows = $importer->parse($this->argument('file'), $project->import_format, $project->import_sheet);
            $result[$project->slug] = $rows;
            if (! $this->option('dry-run')) {
                $importer->replace($project, $rows);
            }
            if (! $this->option('json')) {
                $this->line(sprintf('%-12s %4d  %s', $project->slug, count($rows), json_encode(LotSheetImporter::summary($rows))));
            }
        }
        if ($this->option('json')) {
            $this->line(json_encode($result, JSON_UNESCAPED_UNICODE));
        }

        return self::SUCCESS;
    }
}
