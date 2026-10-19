<?php

use App\Models\News;
use App\Models\Project;

beforeEach(fn () => seedSite());

it('muestra todas las páginas públicas', function (string $url) {
    $this->get($url)->assertOk();
})->with(['/', '/proyectos', '/nosotros', '/noticias', '/contacto', '/trabaja-con-nosotros', '/investor-pass',
    '/proyectos/sol-city', '/proyectos/cumbres', '/proyectos/la-ribera', '/proyectos/paraqvaria']);

it('no muestra proyectos despublicados', function () {
    Project::where('slug', 'cumbres')->update(['is_published' => false]);

    $this->get('/proyectos/cumbres')->assertNotFound();
    $this->get('/')->assertDontSee('San Bernardino, Cordillera');
    $this->get('/proyectos')->assertDontSee('/proyectos/cumbres');
});

it('redirige las URLs del sitio original', function (string $from, string $to) {
    $this->get($from)->assertStatus(301)->assertRedirect($to);
})->with([
    ['/proyecto.php?p=cumbres', '/proyectos/cumbres'],
    ['/proyecto.php?p=inexistente', '/proyectos'],
    ['/nosotros.php', '/nosotros'],
    ['/trabaja-con-nosotros.php', '/trabaja-con-nosotros'],
]);

it('redirige el slug anterior cuando un proyecto cambia de URL', function () {
    Project::where('slug', 'sol-city')->first()->update(['slug' => 'sol-city-asuncion']);

    $this->get('/proyectos/sol-city')->assertStatus(301)->assertRedirect('/proyectos/sol-city-asuncion');
});

it('muestra la leyenda de render y los lotes reales', function () {
    $this->get('/proyectos/la-ribera')
        ->assertSee('Imagen ilustrativa (render)')
        ->assertSee('Se muestran los lotes disponibles.');
});

it('publica notas originales con el HTML sanitizado', function () {
    News::create([
        'kind' => 'article', 'title' => 'Nota propia', 'slug' => 'nota-propia', 'image' => 'news/x.jpg',
        'body' => '<p>Hola <strong>mundo</strong></p><script>alert(1)</script><a href="javascript:alert(1)">x</a>',
        'published_at' => now()->subMinute(),
    ]);

    $this->get('/noticias/nota-propia')->assertOk()
        ->assertSee('<strong>mundo</strong>', false)
        ->assertDontSee('<script>alert(1)</script>', false)
        ->assertDontSee('javascript:', false);
    $this->get('/noticias')->assertSee('/noticias/nota-propia');
});
