<?php

use App\Imports\LotSheetImporter;
use App\Models\Project;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

function workbook(array $rows, string $sheet = 'Hoja1'): string
{
    $book = new Spreadsheet;
    $book->getActiveSheet()->setTitle($sheet)->fromArray($rows);
    $file = tempnam(sys_get_temp_dir(), 'lots').'.xlsx';
    (new Xlsx($book))->save($file);

    return $file;
}

it('importa la plantilla genérica sin guardar precios', function () {
    $file = workbook([
        ['Manzana', 'Lote', 'Superficie', 'Tipo', 'Precio', 'Estado'],
        ['A', 1, 1500.456, 'Interno', 99999, 'Disponible'],
        ['A', 2, 1600, 'Playa', 120000, 'Vendido'],
        ['B', '3', null, null, null, 'Reservado'],
    ]);

    $rows = (new LotSheetImporter)->parse($file, 'generic');

    expect($rows)->toHaveCount(3)
        ->and($rows[0])->toBe(['block' => 'A', 'number' => '1', 'floor' => null, 'type' => 'Interno', 'area_m2' => 1500.46, 'orientation' => null, 'status' => 'disponible'])
        ->and(array_column($rows, 'status'))->toBe(['disponible', 'vendido', 'reservado'])
        ->and(json_encode($rows))->not->toContain('99999');
});

it('lee el formato de Sol City', function () {
    $file = workbook([
        [], [null, 'LISTA DE PRECIOS'], [], [],
        [null, 'N°', 'Nivel', 'Tipologia', 'Area Propia', 'Ubicación ', 'ESTADO', 'USD M2'],
        [null, 203, 2, '2 Dormitorios ', 131.73, 'Atrás ', 'Disponible ', 2000],
        [null, 502, 5, '3 Dormitorios', 109.85, 'Adelante', 'vendido'],
    ], 'sol city');

    $rows = (new LotSheetImporter)->parse($file, 'sol_city', 'sol city');

    expect($rows[0])->toMatchArray(['number' => '203', 'floor' => 2, 'type' => '2 dormitorios', 'orientation' => 'Contrafrente', 'status' => 'disponible'])
        ->and($rows[1])->toMatchArray(['number' => '502', 'orientation' => 'Frente', 'status' => 'vendido']);
});

it('avisa si la hoja no existe', function () {
    (new LotSheetImporter)->parse(workbook([['Lote'], [1]]), 'generic', 'no-existe');
})->throws(RuntimeException::class, 'Hojas disponibles: Hoja1');

it('reemplaza los lotes del proyecto', function () {
    $project = Project::create(['slug' => 'p', 'name' => 'P', 'full_name' => 'P', 'type_label' => 'Lotes', 'location' => 'X', 'kind' => 'lots']);
    $project->units()->create(['number' => 'viejo', 'status' => 'vendido']);

    $importer = new LotSheetImporter;
    $importer->replace($project, $importer->parse(workbook([['Lote', 'Estado'], [1, 'Disponible'], [2, 'Disponible']]), 'generic'));

    expect($project->units()->pluck('number')->all())->toBe(['1', '2']);
});
