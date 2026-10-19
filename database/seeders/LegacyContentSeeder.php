<?php

namespace Database\Seeders;

use App\Models\Advisor;
use App\Models\BoardMember;
use App\Models\News;
use App\Models\Page;
use App\Models\Project;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

/**
 * Carga el contenido del sitio PHP original (legacy/data.php, legacy/lots.php y los textos de las
 * plantillas) y copia sus medios al disco "public". Re-ejecutable: actualiza por slug/clave.
 *
 *   php artisan db:seed --class=LegacyContentSeeder
 */
class LegacyContentSeeder extends Seeder
{
    /** Carpeta de imágenes del sitio original → slug del proyecto. */
    private const IMG_DIRS = ['sol' => 'sol-city', 'paraqvaria' => 'paraqvaria', 'ribera' => 'la-ribera', 'cumbres' => 'cumbres'];

    /** Posición de cada proyecto sobre mapa-paraguay.svg (index.php original). */
    private const MAP_PINS = [
        'la-ribera' => [59.93, 71.77, 'top'],
        'sol-city' => [60.94, 72.45, 'bottom'],
        'cumbres' => [64.22, 72.73, 'right'],
        'paraqvaria' => [76.63, 93.54, 'top'],
    ];

    private const IMPORT = [
        'sol-city' => ['sol_city', 'sol city'],
        'paraqvaria' => ['paraqvaria', 'paraqvaria'],
        'la-ribera' => ['ribera', 'ribera'],
        'cumbres' => ['cumbres', 'cumbres'],
    ];

    private string $media;

    public function run(): void
    {
        require_once __DIR__.'/legacy/data.php';
        require_once __DIR__.'/legacy/lots.php';
        $this->media = __DIR__.'/legacy/media';

        DB::transaction(function () {
            $this->projects();
            $this->news();
            $this->people();
            $this->pages();
        });
    }

    private function projects(): void
    {
        $compare = [];
        foreach (\COMPARE as [$label, $values]) {
            if ($values !== null) {
                $compare[$label] = $values;
            }
        }

        foreach (array_keys(\PROJECTS) as $i => $slug) {
            $p = \PROJECTS[$slug];
            [$mapX, $mapY, $side] = self::MAP_PINS[$slug];
            [$format, $sheet] = self::IMPORT[$slug];

            $project = Project::withTrashed()->updateOrCreate(['slug' => $slug], [
                'name' => $p['name'],
                'full_name' => $p['fullName'],
                'type_label' => $p['type'],
                'location' => $p['loc'],
                'lat' => $p['ll'][0],
                'lng' => $p['ll'][1],
                'kind' => $p['kind'],
                'hero_image' => $this->img($p['img']),
                'hero_is_render' => $this->isRender($p['img']),
                'tagline' => $p['tagline'],
                'short' => $p['short'],
                'body' => $p['p1']."\n\n".$p['p2'],
                'delivery' => $p['delivery'] ?? null,
                'instagram' => $p['ig'] ?? null,
                'brochure_path' => $this->copy('brochures/'.basename($p['brochure']), "projects/$slug/brochure.pdf"),
                'masterplan_image' => isset($p['masterplan']) ? $this->img($p['masterplan']) : null,
                'video_path' => isset($p['video']) ? $this->copy("video/{$p['video']}.mp4", "projects/$slug/video.mp4") : null,
                'video_poster' => isset($p['video']) ? $this->copy("video/{$p['video']}-poster.jpg", "projects/$slug/video-poster.jpg") : null,
                'facts' => array_map(fn ($f) => ['value' => $f[0], 'label' => $f[1]], $p['facts']),
                'amenities_title' => isset($p['amenities']) ? 'Bienestar y confort, todos los días' : null,
                'amenities' => $p['amenities'] ?? null,
                'sports_title' => $p['sports']['title'] ?? null,
                'sports_text' => $p['sports']['text'] ?? null,
                'sports_phones' => $p['sports']['phones'] ?? null,
                'compare_type' => $compare['Tipo'][$i] ?? null,
                'compare_surface' => $compare['Superficie'][$i] ?? null,
                'compare_amenities' => $compare['Amenities'][$i] ?? null,
                'compare_environment' => $compare['Entorno'][$i] ?? null,
                'map_x' => $mapX,
                'map_y' => $mapY,
                'map_label_side' => $side,
                'news_tag' => $p['name'],
                'import_format' => $format,
                'import_sheet' => $sheet,
                'meta_description' => $p['short'],
                'is_published' => true,
                'sort_order' => $p['num'],
                'deleted_at' => null,
            ]);

            $project->images()->delete();
            foreach ($p['gallery'] as $n => $key) {
                $project->images()->create(['path' => $this->img($key), 'is_render' => $this->isRender($key), 'sort_order' => $n]);
            }

            $project->typologies()->delete();
            foreach ($p['tipos'] ?? [] as $n => $t) {
                $project->typologies()->create([
                    'name' => $t['name'],
                    'specs' => array_map(fn ($s) => ['label' => $s[0], 'value' => $s[1]], $t['specs']),
                    'images' => array_map(fn ($k) => ['path' => $this->img($k), 'is_render' => $this->isRender($k)], $t['imgs']),
                    'sort_order' => $n,
                ]);
            }

            $project->units()->delete();
            foreach (\LOTS[$slug] ?? [] as $n => $u) {
                $project->units()->create([
                    'block' => $u['mz'] ?? null,
                    'number' => $u['n'],
                    'floor' => $u['piso'] ?? null,
                    'type' => $u['type'] ?? null,
                    'area_m2' => $u['m2'],
                    'orientation' => $u['ubic'] ?? null,
                    'status' => $u['status'],
                    'sort_order' => $n,
                ]);
            }
        }
    }

    private function news(): void
    {
        foreach (\NEWS as $i => $n) {
            News::updateOrCreate(['external_url' => $n['url']], [
                'kind' => 'external',
                'title' => $n['title'],
                'source' => $n['source'],
                'project_id' => Project::where('slug', $n['pid'])->value('id'),
                'tag' => $n['tag'],
                'image' => $this->img($n['img'], 'news'),
                'image_is_render' => $this->isRender($n['img']),
                // El sitio original no tenía fechas: se asignan en orden descendente para conservar el orden.
                'published_at' => now()->startOfDay()->subDays($i),
                'is_published' => true,
            ]);
        }
    }

    private function people(): void
    {
        foreach (\BOARD as $i => $m) {
            BoardMember::updateOrCreate(['name' => $m['name']], [
                'role' => $m['role'],
                'photo' => $this->copy("img/{$m['photo']}.jpg", 'board/'.basename($m['photo']).'.jpg'),
                'bio' => implode("\n\n", $m['bio']),
                'is_visible' => true,
                'sort_order' => $i,
            ]);
        }

        foreach (\ADVISORS as $i => $a) {
            Advisor::updateOrCreate(['name' => $a['name']], [
                'email' => $a['email'],
                'instagram' => $a['ig'] ?: null,
                'is_visible' => empty($a['hidden']),
                'sort_order' => $i,
            ]);
        }
    }

    private function pages(): void
    {
        $page = fn (string $key, ?string $title, string $description, array $content) => Page::updateOrCreate(
            ['key' => $key],
            ['meta_title' => $title, 'meta_description' => $description, 'content' => $content],
        );

        $page('home', 'Desarrollamos lugares que crecen en valor', 'Valores Real Estate desarrolla proyectos residenciales y urbanos en Paraguay.', [
            'hero_image' => $this->copy('img/general/hero-cumbres.jpg', 'pages/home-hero.jpg'),
            'hero_is_render' => false,
            'hero_title' => 'Desarrollamos lugares que crecen en valor.',
            'hero_text' => 'Proyectos residenciales, urbanos y de inversión en distintas regiones del Paraguay.',
            'hero_cta' => 'Ver proyectos',
            'map_eyebrow' => 'Dónde estamos',
            'map_title' => 'Nuestros proyectos en el Paraguay',
            'map_lead' => 'Seleccioná un punto en el mapa para conocer cada desarrollo.',
            'projects_eyebrow' => 'Proyectos activos',
            'projects_title' => 'Nuestros desarrollos',
            'projects_link' => 'Ver todos los proyectos',
            'news_eyebrow' => 'Prensa',
            'news_title' => 'Últimas noticias',
            'news_link' => 'Ver todas',
            'news_count' => 3,
        ]);

        $page('proyectos', 'Proyectos', 'Los desarrollos de Valores Real Estate en Paraguay.', [
            'intro_eyebrow' => 'Proyectos',
            'intro_title' => 'Desarrollos con visión de futuro',
            'intro_lead' => 'Proyectos activos entre residencias, barrios cerrados y nuevas centralidades urbanas.',
            'soon_enabled' => true,
            'soon_chip' => 'Próximamente',
            'soon_title' => 'Nuevo proyecto',
            'soon_text' => 'Más información muy pronto.',
            'map_eyebrow' => 'Mapa',
            'map_title' => 'Ubicación de los proyectos',
            'compare_eyebrow' => 'Comparador',
            'compare_title' => 'Compará los proyectos',
            'compare_labels' => [
                'location' => 'Ubicación',
                'type' => 'Tipo',
                'surface' => 'Superficie',
                'amenities' => 'Amenities',
                'environment' => 'Entorno',
            ],
        ]);

        $page('noticias', 'Noticias', 'Valores Real Estate en los medios.', [
            'intro_eyebrow' => 'Noticias',
            'intro_title' => 'En los medios',
        ]);

        $page('nosotros', 'Nosotros', 'Valores Real Estate desarrolla barrios cerrados, loteamientos, edificaciones residenciales y proyectos de inversión en distintas regiones del Paraguay.', [
            'eyebrow' => 'Nosotros',
            'title' => 'Proyectos que transforman lugares y generan valor.',
            'image' => $this->copy('img/sol/fachada.jpg', 'pages/nosotros.jpg'),
            'image_alt' => 'Edificio Sol City, de Valores Real Estate',
            'image_is_render' => true,
            'paragraphs' => array_map(fn ($t) => ['text' => $t], [
                'Valores Real Estate nace de una convicción: el mercado inmobiliario paraguayo necesitaba un desarrollador con la solidez, la transparencia y la disciplina propias del mercado financiero. Con esa visión, el desarrollo inmobiliario se incorporó como eje estratégico, dando inicio a una nueva etapa orientada a conectar el mercado de capitales con la economía real y los sectores emergentes del país.',
                'La respuesta del mercado confirmó esa necesidad. En poco más de cuatro años desarrollamos barrios cerrados, loteamientos, edificaciones residenciales y proyectos de inversión en distintas regiones del Paraguay, acompañando a familias e inversionistas que buscan algo más que un terreno o una propiedad.',
                'Hoy priorizamos proyectos con potencial de valorización, criterios de sostenibilidad y una gestión patrimonial orientada al largo plazo, para que cada proyecto sea un lugar donde vivir bien y, al mismo tiempo, una buena inversión.',
            ]),
            'board_eyebrow' => 'Plana directiva',
            'board_title' => 'Quiénes nos lideran',
        ]);

        $page('trabaja', 'Trabaja con nosotros', 'Sumate al equipo de Valores Real Estate.', [
            'eyebrow' => 'Trabaja con nosotros',
            'title' => 'Sumate al equipo de Valores Real Estate.',
            'lead' => 'Buscamos personas comprometidas en las áreas comercial, desarrollo de proyectos, administración y marketing. Envianos tu CV y te contactaremos cuando haya una posición acorde a tu perfil.',
            'image' => $this->copy('img/general/trabaja.jpg', 'pages/trabaja.jpg'),
            'image_is_render' => false,
            'areas' => array_map(fn ($t) => ['label' => $t], ['Comercial / Ventas', 'Desarrollo de proyectos', 'Administración y finanzas', 'Marketing', 'Otra']),
            'success_title' => 'Postulación enviada',
            'success_text' => 'Gracias por tu interés. Revisaremos tu perfil y te escribiremos.',
        ]);

        $page('contacto', 'Contacto', 'Contactá a Valores Real Estate y a nuestro equipo de asesores comerciales.', [
            'eyebrow' => 'Contacto',
            'title' => 'Estamos para ayudarte.',
            'whatsapp_cta' => 'Escribinos por WhatsApp',
            'success_title' => 'Mensaje enviado',
            'success_text' => 'Gracias por escribirnos. Te responderemos a la brevedad.',
            'advisors_eyebrow' => 'Asesores comerciales',
            'advisors_title' => 'Hablá directo con el equipo',
        ]);

        $page('investor', 'Investor Pass', 'Residencia permanente en Paraguay a través de la inversión inmobiliaria con Valores Real Estate.', [
            'eyebrow' => 'Investor Pass',
            'title' => 'Residencia permanente en Paraguay a través de la inversión inmobiliaria.',
            'lead' => 'El Investor Pass otorga la residencia permanente a inversionistas extranjeros que canalizan su capital a través de vehículos habilitados. En Valores Real Estate te acompañamos a estructurar la inversión en nuestros proyectos.',
            'cta' => 'Quiero más información',
            'banner' => $this->copy('img/general/investor.jpg', 'pages/investor.jpg'),
            'banner_is_render' => true,
            'modes_eyebrow' => 'Modalidades',
            'modes_title' => 'Tres caminos para invertir',
            'modes' => [
                ['kicker' => 'Inmobiliaria', 'tag' => 'Disponible', 'style' => 'blue', 'amount' => 'USD 200.000',
                    'text' => 'Inversión mínima en proyectos inmobiliarios habilitados: lotes y unidades en {proyectos}.', 'link_label' => null, 'link_url' => null],
                ['kicker' => 'Turismo', 'tag' => 'Próximamente', 'style' => 'amber', 'amount' => 'USD 150.000',
                    'text' => 'Inversión en desarrollos con fines turísticos. Estamos preparando las primeras opciones en esta modalidad.', 'link_label' => null, 'link_url' => null],
                ['kicker' => 'Mercado de valores', 'tag' => 'Información', 'style' => 'info', 'amount' => 'USD 200.000',
                    'text' => 'Inversión en instrumentos del mercado de valores paraguayo, como bonos, acciones o fondos. Esta modalidad se gestiona a través de una casa de bolsa y no forma parte de la oferta de Valores Real Estate.',
                    'link_label' => 'Ver en Valores Casa de Bolsa', 'link_url' => 'https://www.valores.com.py/investor-pass'],
            ],
            'steps_eyebrow' => 'Proceso',
            'steps_title' => 'Cómo funciona',
            'steps' => array_map(fn ($s) => ['number' => $s[0], 'title' => $s[1], 'text' => $s[2]], \INVESTOR_STEPS),
            'form_title' => 'Empezá tu proceso',
            'form_lead' => 'Completá tus datos y continuá la conversación con un asesor por WhatsApp.',
            'modality_options' => array_map(fn ($t) => ['label' => $t], ['Inmobiliaria (USD 200.000)', 'Turismo (próximamente)', 'Aún no lo sé']),
            'submit_label' => 'Continuar por WhatsApp',
            'success_title' => 'Solicitud recibida',
            'success_text' => 'Abrimos WhatsApp para que continúes la conversación con un asesor.',
        ]);
    }

    /** Copia una imagen del sitio original (clave "sol/fachada") y su variante -sm. Devuelve la ruta en el disco public. */
    private function img(string $key, string $dir = 'projects'): string
    {
        [$folder, $name] = explode('/', $key, 2);
        $target = $dir === 'news'
            ? 'news/'.str_replace('/', '-', $key).'.jpg'
            : 'projects/'.(self::IMG_DIRS[$folder] ?? $folder)."/$name.jpg";
        $this->copy("img/$key.jpg", $target);
        if (is_file("{$this->media}/img/$key-sm.jpg")) {
            $this->copy("img/$key-sm.jpg", preg_replace('/\.jpg$/', '-sm.jpg', $target));
        }

        return $target;
    }

    private function copy(string $source, string $target): string
    {
        $disk = Storage::disk('public');
        if (! $disk->exists($target)) {
            $disk->put($target, File::get("{$this->media}/$source"));
        }

        return $target;
    }

    private function isRender(string $key): bool
    {
        $matches = fn (array $patterns) => (bool) array_filter($patterns, fn ($pat) => fnmatch($pat, $key));

        return $matches(\RENDERS) && ! $matches(\NOT_RENDERS);
    }
}
