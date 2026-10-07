<?php
require __DIR__ . '/inc/layout.php';
page_header('proyectos', 'Proyectos', 'Sol City, Paraqvaria, La Ribera y Cumbres de San Bernardino: los desarrollos de Valores Real Estate.');
?>
<section class="wrap page-intro">
  <div class="eyebrow">Proyectos</div>
  <div class="intro-row">
    <h1 class="display-1" style="max-width:14ch">Desarrollos con visión de futuro</h1>
    <p class="lead-muted">Cuatro proyectos activos entre residencias, barrios cerrados y nuevas centralidades urbanas.</p>
  </div>
</section>

<section class="wrap">
  <div class="grid-projects">
    <?php foreach (PROJECTS as $id => $p): ?>
      <a class="project-card" href="<?= project_url($id) ?>">
        <div class="media media-4x3">
          <img src="<?= img($p['img'], true) ?>" alt="<?= e($p['name']) ?>" loading="lazy"><?= render_tag($p['img']) ?>
          <span class="chip"><?= e($p['type']) ?></span>
        </div>
        <div class="project-card-foot">
          <div class="stack-8">
            <h2 class="card-title card-title-lg"><?= e($p['name']) ?></h2>
            <div class="with-icon muted"><?= icon('pin', 16) ?><?= e($p['loc']) ?></div>
            <p class="muted body-sm"><?= e($p['short']) ?></p>
          </div>
          <span class="round-arrow round-arrow-solid"><?= icon('arrow') ?></span>
        </div>
      </a>
    <?php endforeach; ?>
    <div class="project-card is-soon">
      <div class="media media-4x3 soon-box"><span class="chip chip-accent">Próximamente</span></div>
      <div class="stack-8">
        <h2 class="card-title card-title-lg">Nuevo proyecto</h2>
        <div class="faint">Más información muy pronto.</div>
      </div>
    </div>
  </div>
</section>

<section class="wrap section">
  <div class="eyebrow">Mapa</div>
  <h2 class="display-3">Ubicación de los proyectos</h2>
  <div class="vmap" data-vmap="all"></div>
</section>

<section class="wrap section section-last">
  <div class="eyebrow">Comparador</div>
  <h2 class="display-3">Compará los proyectos</h2>
  <div class="compare-scroll">
    <table class="compare">
      <thead>
        <tr>
          <th></th>
          <?php foreach (PROJECTS as $id => $p): ?><th><a href="<?= project_url($id) ?>"><?= e($p['name']) ?></a></th><?php endforeach; ?>
        </tr>
      </thead>
      <tbody>
        <?php foreach (COMPARE as [$label, $vals]): $vals ??= array_column(PROJECTS, 'loc'); ?>
          <tr>
            <th scope="row"><?= e($label) ?></th>
            <?php foreach ($vals as $v): ?><td><?= e($v) ?></td><?php endforeach; ?>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</section>
<?php
$pins = [];
foreach (PROJECTS as $id => $p) $pins[] = ['num' => $p['num'], 'name' => $p['name'], 'll' => $p['ll'], 'url' => project_url($id)];
page_footer(['projects' => $pins]);
