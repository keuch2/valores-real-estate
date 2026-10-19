<?php

namespace App\Imports;

use App\Models\Project;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use RuntimeException;

/**
 * Lee la planilla de lotes/unidades del cliente y devuelve sólo datos publicables
 * (manzana, número, piso, tipo, superficie, ubicación, estado). Los precios y cuentas de la
 * planilla se ignoran: no hay campo donde guardarlos.
 */
class LotSheetImporter
{
    public const GENERIC_HEADERS = ['Manzana', 'Lote', 'Superficie', 'Tipo', 'Piso', 'Ubicación', 'Estado'];

    /** @return array<int, array{block: ?string, number: string, floor: ?int, type: ?string, area_m2: ?float, orientation: ?string, status: string}> */
    public function parse(string $file, string $format, ?string $sheet = null): array
    {
        $book = IOFactory::createReaderForFile($file)->setReadDataOnly(true)->load($file);
        $ws = $sheet ? $book->getSheetByName($sheet) : $book->getActiveSheet();
        if (! $ws) {
            $names = implode(', ', $book->getSheetNames());
            throw new RuntimeException("La planilla no tiene una hoja llamada \"$sheet\". Hojas disponibles: $names.");
        }

        $rows = match ($format) {
            'cumbres' => $this->cumbres($ws),
            'paraqvaria' => $this->paraqvaria($ws),
            'ribera' => $this->ribera($ws),
            'sol_city' => $this->solCity($ws),
            default => $this->generic($ws),
        };
        if (! $rows) {
            throw new RuntimeException('No se encontraron lotes ni unidades en la hoja. Revisá el formato elegido en el proyecto.');
        }

        return $rows;
    }

    /** Reemplaza todos los lotes/unidades del proyecto. */
    public function replace(Project $project, array $rows): void
    {
        DB::transaction(function () use ($project, $rows) {
            $project->units()->delete();
            foreach (array_values($rows) as $i => $row) {
                $project->units()->create($row + ['sort_order' => $i]);
            }
        });
    }

    public static function summary(array $rows): array
    {
        return collect($rows)->countBy('status')->all();
    }

    private function cumbres(Worksheet $ws): array
    {
        $out = [];
        for ($r = 7, $max = $ws->getHighestRow(); $r <= $max; $r++) {
            [$mz, $lote] = [$this->v($ws, 4, $r), $this->v($ws, 5, $r)];
            if ($mz === null || $lote === null) {
                continue;
            }
            $sold = false;
            for ($c = 6; $c <= 12; $c++) {
                $sold = $sold || mb_strtolower(trim((string) $this->v($ws, $c, $r))) === 'vendido';
            }
            $out[] = $this->row($mz, $lote, area: $sold ? null : $this->v($ws, 8, $r), status: $sold ? 'vendido' : 'disponible');
        }

        return $out;
    }

    private function paraqvaria(Worksheet $ws): array
    {
        $out = [];
        for ($r = 3, $max = $ws->getHighestRow(); $r <= $max; $r++) {
            [$mz, $tipo, $lote] = [$this->v($ws, 3, $r), $this->v($ws, 4, $r), $this->v($ws, 5, $r)];
            if ($mz === null || $lote === null) {
                continue;
            }
            $estado = mb_strtolower(trim((string) $this->v($ws, 1, $r)));
            $status = ['vendido' => 'vendido', '' => 'disponible'][$estado] ?? 'nodisponible';
            $out[] = $this->row($mz, $lote, type: ucfirst(mb_strtolower(trim((string) $tipo))), area: $this->v($ws, 6, $r), status: $status);
        }

        return $out;
    }

    /** La hoja de La Ribera lista sólo lotes disponibles; la manzana está una vez por grupo (la numeración reinicia en cada grupo). */
    private function ribera(Worksheet $ws): array
    {
        $max = $ws->getHighestRow();
        $groups = [];
        $current = [];
        $prev = null;
        for ($r = 3; $r <= $max; $r++) {
            $lote = $this->v($ws, 2, $r);
            if (is_numeric($lote) && $this->v($ws, 3, $r)) {
                if ($prev !== null && $lote < $prev && $current) {
                    $groups[] = $current;
                    $current = [];
                }
                $current[] = $r;
                $prev = $lote;
            }
        }
        if ($current) {
            $groups[] = $current;
        }

        $out = [];
        foreach ($groups as $rows) {
            $mz = null;
            foreach ($rows as $r) {
                $mz ??= $this->v($ws, 1, $r);
            }
            foreach ($rows as $r) {
                $out[] = $this->row($mz, $this->v($ws, 2, $r), type: 'Residencial', area: $this->v($ws, 3, $r), status: 'disponible');
            }
        }

        // Alta densidad: manzanas M1–M3 con código de lote (M1, MT1…) en la columna B.
        $mz = null;
        for ($r = 3; $r <= $max; $r++) {
            [$a, $b] = [$this->v($ws, 1, $r), $this->v($ws, 2, $r)];
            if (is_string($a) && preg_match('/^M\d+$/', $a)) {
                $mz = $a;
            }
            if ($mz && is_string($b) && str_starts_with($b, 'M')) {
                $out[] = $this->row($mz, $b, type: 'Alta densidad', area: $this->v($ws, 3, $r), status: 'disponible');
            }
        }

        return $out;
    }

    private function solCity(Worksheet $ws): array
    {
        $out = [];
        for ($r = 6, $max = $ws->getHighestRow(); $r <= $max; $r++) {
            [$unit, $piso, $tipo] = [$this->v($ws, 2, $r), $this->v($ws, 3, $r), $this->v($ws, 4, $r)];
            if (! is_numeric($unit) || ! $tipo) {
                continue;
            }
            $ubic = mb_strtolower(trim((string) $this->v($ws, 6, $r)));
            $out[] = $this->row(null, $unit,
                floor: (int) $piso,
                type: str_replace('Dormitorios', 'dormitorios', preg_replace('/\s+/', ' ', trim((string) $tipo))),
                area: $this->v($ws, 5, $r),
                orientation: ['adelante' => 'Frente', 'atrás' => 'Contrafrente', 'atras' => 'Contrafrente'][$ubic] ?? ucfirst($ubic),
                status: mb_strtolower(trim((string) $this->v($ws, 7, $r))) === 'vendido' ? 'vendido' : 'disponible');
        }

        return $out;
    }

    /** Plantilla genérica: primera fila con encabezados Manzana, Lote, Superficie, Tipo, Piso, Ubicación, Estado. */
    private function generic(Worksheet $ws): array
    {
        $cols = [];
        $lastCol = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($ws->getHighestColumn());
        for ($c = 1; $c <= $lastCol; $c++) {
            $cols[mb_strtolower(trim((string) $this->v($ws, $c, 1)))] = $c;
        }
        $col = fn (string $name) => $cols[mb_strtolower($name)] ?? null;
        if (! $col('Lote')) {
            throw new RuntimeException('La hoja debe tener en la primera fila los encabezados: '.implode(', ', self::GENERIC_HEADERS).'.');
        }
        $get = fn (string $name, int $r) => ($c = $col($name)) ? $this->v($ws, $c, $r) : null;

        $out = [];
        for ($r = 2, $max = $ws->getHighestRow(); $r <= $max; $r++) {
            if ($get('Lote', $r) === null) {
                continue;
            }
            $estado = mb_strtolower(trim((string) $get('Estado', $r)));
            $status = match (true) {
                str_starts_with($estado, 'vend') => 'vendido',
                str_starts_with($estado, 'reserv') => 'reservado',
                str_starts_with($estado, 'no') => 'nodisponible',
                default => 'disponible',
            };
            $out[] = $this->row($get('Manzana', $r), $get('Lote', $r),
                floor: is_numeric($get('Piso', $r)) ? (int) $get('Piso', $r) : null,
                type: $get('Tipo', $r), area: $get('Superficie', $r), orientation: $get('Ubicación', $r), status: $status);
        }

        return $out;
    }

    private function row(mixed $block, mixed $number, ?int $floor = null, mixed $type = null, mixed $area = null, mixed $orientation = null, string $status = 'disponible'): array
    {
        return [
            'block' => $block === null || $block === '' ? null : $this->str($block),
            'number' => $this->str($number),
            'floor' => $floor,
            'type' => $type === null || $type === '' ? null : (string) $type,
            'area_m2' => is_numeric($area) ? round((float) $area, 2) : null,
            'orientation' => $orientation === null || $orientation === '' ? null : (string) $orientation,
            'status' => $status,
        ];
    }

    private function str(mixed $value): string
    {
        return is_numeric($value) && floor((float) $value) == $value ? (string) (int) $value : trim((string) $value);
    }

    /** Valor de la celda; en fórmulas usa el último valor calculado guardado en el archivo (como openpyxl data_only). */
    private function v(Worksheet $ws, int $col, int $row): mixed
    {
        $cell = $ws->getCell([$col, $row]);
        $value = $cell->isFormula() ? $cell->getOldCalculatedValue() : $cell->getValue();
        if ($value instanceof \PhpOffice\PhpSpreadsheet\RichText\RichText) {
            $value = $value->getPlainText();
        }

        return $value === '' ? null : $value;
    }
}
