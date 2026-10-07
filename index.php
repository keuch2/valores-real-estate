<?php
require __DIR__ . '/inc/layout.php';
page_header('inicio', 'Desarrollamos lugares que crecen en valor');

// Posición de cada proyecto sobre mapa-paraguay.svg (% del ancho/alto) y lado de la etiqueta.
$mapPins = [
    'la-ribera' => [59.93, 71.77, 'top'],
    'sol-city' => [60.94, 72.45, 'bottom'],
    'cumbres' => [64.22, 72.73, 'right'],
    'paraqvaria' => [76.63, 93.54, 'top'],
];
?>
<section class="hero">
  <img src="<?= img('general/hero-cumbres') ?>" alt="" class="hero-img">
  <div class="hero-shade"></div>
  <div class="wrap hero-inner">
    <h1 class="hero-title">Desarrollamos lugares que crecen en valor.</h1>
    <div class="hero-foot">
      <p>Proyectos residenciales, urbanos y de inversión en distintas regiones del Paraguay.</p>
      <a class="btn btn-accent btn-lg" href="proyectos.php">Ver proyectos <?= icon('arrow') ?></a>
    </div>
  </div>
</section>

<section class="wrap section">
  <?php section_head('Dónde estamos', 'Nuestros proyectos en el Paraguay', '<p class="lead-muted">Seleccioná un punto en el mapa para conocer cada desarrollo.</p>'); ?>
  <div class="py-map">
    <div class="py-map-canvas">
      <div class="py-map-frame">
        <img src="assets/img/general/mapa-paraguay.svg" alt="Mapa del Paraguay">
        <span class="py-map-label">Paraguay</span>
        <?php foreach ($mapPins as $id => [$x, $y, $side]): $p = PROJECTS[$id]; ?>
          <span class="py-dot" style="left:<?= $x ?>%;top:<?= $y ?>%"></span>
          <a class="py-pin py-pin-<?= $side ?>" style="left:<?= $x ?>%;top:<?= $y ?>%" href="<?= project_url($id) ?>"><span class="py-pin-tip"></span><span class="py-pin-label"><?= e($p['name']) ?></span></a>
        <?php endforeach; ?>
      </div>
    </div>
    <div class="py-map-list">
      <?php foreach (PROJECTS as $id => $p): ?>
        <a href="<?= project_url($id) ?>">
          <span class="num-badge"><?= $p['num'] ?></span>
          <span class="py-map-item"><span class="py-map-name"><?= e($p['name']) ?></span><span class="muted"><?= e($p['loc']) ?></span></span>
        </a>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<section class="wrap section">
  <?php section_head('Proyectos activos', 'Nuestros desarrollos', link_more('proyectos.php', 'Ver todos los proyectos')); ?>
  <div class="grid-cards">
    <?php foreach (PROJECTS as $id => $p): ?>
      <a class="project-card" href="<?= project_url($id) ?>">
        <div class="media media-3x4">
          <img src="<?= img($p['img'], true) ?>" alt="<?= e($p['name']) ?>" loading="lazy"><?= render_tag($p['img']) ?>
          <span class="chip"><?= e($p['type']) ?></span>
        </div>
        <div class="project-card-foot">
          <div>
            <h3 class="card-title"><?= e($p['name']) ?></h3>
            <div class="muted small"><?= e($p['loc']) ?></div>
          </div>
          <span class="round-arrow"><?= icon('arrow', 16) ?></span>
        </div>
      </a>
    <?php endforeach; ?>
  </div>
</section>

<section class="wrap section section-last">
  <?php section_head('Prensa', 'Últimas noticias', link_more('noticias.php', 'Ver todas')); ?>
  <div class="grid-news grid-news-home">
    <?php foreach (array_slice(NEWS, 0, 3) as $a) news_card($a); ?>
  </div>
</section>
<?php page_footer(); ?>
