<?php

use Database\Seeders\LegacyContentSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

pest()->extend(Tests\TestCase::class)->use(RefreshDatabase::class)->in('Feature');

/** Contenido real del sitio (los medios ya copiados en storage se reutilizan). */
function seedSite(): void
{
    test()->seed(LegacyContentSeeder::class);
}

/** Campos anti-spam válidos (formulario abierto hace 10 segundos). */
function guard(string $action): array
{
    return ['_ts' => encrypt(now()->subSeconds(10)->timestamp), '_form_action' => $action];
}
