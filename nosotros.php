<?php
require __DIR__ . '/inc/layout.php';
page_header('nosotros', 'Nosotros', 'Valores Real Estate desarrolla barrios cerrados, loteamientos, edificaciones residenciales y proyectos de inversión en distintas regiones del Paraguay.');
?>
<section class="wrap page-intro">
  <div class="eyebrow">Nosotros</div>
  <h1 class="display-1 balance" style="max-width:18ch">Proyectos que transforman lugares y generan valor.</h1>
</section>

<section class="wrap pt-72 two-col align-start">
  <div class="media media-4x5"><img src="<?= img('sol/fachada') ?>" alt="Edificio Sol City, de Valores Real Estate"><?= render_tag('sol/fachada') ?></div>
  <div class="prose prose-lg">
    <p>Valores Real Estate nace de una convicción: el mercado inmobiliario paraguayo necesitaba un desarrollador con la solidez, la transparencia y la disciplina propias del mercado financiero. Con esa visión, el desarrollo inmobiliario se incorporó como eje estratégico, dando inicio a una nueva etapa orientada a conectar el mercado de capitales con la economía real y los sectores emergentes del país.</p>
    <p>La respuesta del mercado confirmó esa necesidad. En poco más de cuatro años desarrollamos barrios cerrados, loteamientos, edificaciones residenciales y proyectos de inversión en distintas regiones del Paraguay, acompañando a familias e inversionistas que buscan algo más que un terreno o una propiedad.</p>
    <p>Hoy priorizamos proyectos con potencial de valorización, criterios de sostenibilidad y una gestión patrimonial orientada al largo plazo, para que cada proyecto sea un lugar donde vivir bien y, al mismo tiempo, una buena inversión.</p>
  </div>
</section>

<section class="wrap section section-last">
  <div class="eyebrow">Plana directiva</div>
  <h2 class="display-3 mb-44">Quiénes nos lideran</h2>
  <div class="grid-board">
    <?php foreach (BOARD as $m): ?>
      <div class="person">
        <div class="person-photo"><img src="<?= img($m['photo']) ?>" alt="<?= e($m['name']) ?>"></div>
        <div>
          <div class="person-role"><?= e($m['role']) ?></div>
          <div class="person-name"><?= e($m['name']) ?></div>
          <div class="person-bio"><?php foreach ($m['bio'] as $p): ?><p><?= e($p) ?></p><?php endforeach; ?></div>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
</section>
<?php page_footer(); ?>
