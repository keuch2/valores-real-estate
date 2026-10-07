<?php
require __DIR__ . '/inc/layout.php';

$id = $_GET['p'] ?? '';
if (!isset(PROJECTS[$id])) {
    header('Location: proyectos.php');
    exit;
}
$p = PROJECTS[$id];
$gallery = array_map(fn($k) => img($k), $p['gallery']);
$imageKeys = array_merge($p['gallery'], ...array_column($p['tipos'] ?? [], 'imgs'));
$renders = array_values(array_map(fn($k) => img($k), array_filter($imageKeys, 'is_render')));
page_header('proyectos', $p['fullName'], $p['short']);
?>
<section class="proj-hero">
  <img src="<?= img($p['img']) ?>" alt="" class="hero-img">
  <div class="proj-hero-shade"></div>
  <?= render_tag($p['img']) ?>
  <div class="wrap proj-hero-inner">
    <a class="back-pill" href="proyectos.php"><?= icon('back', 16) ?>Todos los proyectos</a>
    <div class="stack-18">
      <div class="eyebrow-line"><?= e($p['type']) ?></div>
      <h1 class="proj-title"><?= e($p['fullName']) ?></h1>
      <div class="proj-loc"><?= e($p['loc']) ?><?php if (!empty($p['delivery'])): ?> · Entrega <?= e(mb_strtolower($p['delivery'])) ?><?php endif; ?></div>
    </div>
  </div>
</section>

<section class="facts">
  <div class="wrap facts-grid">
    <?php foreach ($p['facts'] as [$v, $k]): ?>
      <div class="fact"><div class="fact-v"><?= e($v) ?></div><div class="muted small"><?= e($k) ?></div></div>
    <?php endforeach; ?>
  </div>
</section>

<section class="wrap section two-col">
  <div>
    <div class="eyebrow">El proyecto</div>
    <h2 class="display-2 balance"><?= e($p['tagline']) ?></h2>
  </div>
  <div class="prose">
    <p><?= e($p['p1']) ?></p>
    <p><?= e($p['p2']) ?></p>
    <div class="btn-row">
      <a class="btn btn-primary" href="<?= visit_url($p['fullName']) ?>" target="_blank" rel="noopener"><?= icon('calendar', 17) ?>Agendá tu visita</a>
      <a class="btn btn-outline" href="#consulta" data-scroll>Consultar por este proyecto</a>
      <a class="btn btn-outline" href="<?= file_url($p['brochure']) ?>" target="_blank" rel="noopener"><?= icon('download', 17) ?>Descargar brochure (PDF)</a>
    </div>
    <?php if (!empty($p['ig'])): ?>
      <a class="ig-link" href="https://www.instagram.com/<?= e($p['ig']) ?>" target="_blank" rel="noopener"><?= icon('instagram', 18) ?>Seguí a <?= e($p['name']) ?> en Instagram: @<?= e($p['ig']) ?></a>
    <?php endif; ?>
  </div>
</section>

<?php if (!empty($p['video'])): ?>
<section class="wrap section">
  <div class="eyebrow">Video</div>
  <h2 class="display-3 mb-40">Conocé <?= e($p['name']) ?></h2>
  <div class="video-wrap" data-video>
    <video class="proj-video" controls preload="metadata" playsinline poster="assets/video/<?= e($p['video']) ?>-poster.jpg">
      <source src="assets/video/<?= e($p['video']) ?>.mp4" type="video/mp4">
    </video>
    <button type="button" class="video-play" aria-label="Reproducir video de <?= e($p['name']) ?>"><span class="video-play-icon"></span>Ver video</button>
  </div>
</section>
<?php endif; ?>

<section class="wrap section">
  <div class="section-head">
    <h2 class="display-3">Galería</h2>
    <div class="muted small"><?= count($gallery) ?> imágenes · clic para ampliar</div>
  </div>
  <div class="gallery" data-gallery="<?= e(json_encode($gallery, JSON_UNESCAPED_SLASHES)) ?>" data-label="<?= e($p['fullName']) ?>">
    <?php foreach ($p['gallery'] as $i => $k): ?>
      <button type="button" class="gallery-item<?= $i === 0 ? ' is-wide' : '' ?>" data-index="<?= $i ?>"><img src="<?= img($k, true) ?>" alt="<?= e($p['fullName']) ?> · imagen <?= $i + 1 ?>" loading="lazy"><?= render_tag($k) ?></button>
    <?php endforeach; ?>
  </div>
</section>


<?php if (!empty($p['amenities'])): ?>
<section class="wrap section">
  <div class="eyebrow">Amenities</div>
  <h2 class="display-3 mb-40">Bienestar y confort, todos los días</h2>
  <ul class="amenities">
    <?php foreach ($p['amenities'] as $a): ?><li><?= icon('check', 18) ?><?= e($a) ?></li><?php endforeach; ?>
  </ul>
</section>
<?php endif; ?>

<?php if (!empty($p['sports'])): $sp = $p['sports']; ?>
<section class="wrap section">
  <div class="sports-band">
    <div>
      <div class="eyebrow">Zona deportiva</div>
      <h2 class="display-3"><?= e($sp['title']) ?></h2>
    </div>
    <div class="stack-18">
      <p class="lead-muted"><?= e($sp['text']) ?></p>
      <div>
        <div class="label-caps">Informes e inscripciones</div>
        <div class="sports-phones">
          <?php foreach ($sp['phones'] as $ph): ?><a href="tel:+595<?= e(substr(preg_replace('/\D/', '', $ph), 1)) ?>"><?= e($ph) ?></a><?php endforeach; ?>
        </div>
      </div>
    </div>
  </div>
</section>
<?php endif; ?>

<?php if ($p['kind'] === 'lots'): ?>
<section class="wrap section">
  <div class="section-head">
    <div>
      <div class="eyebrow">Masterplan</div>
      <h2 class="display-3">Plano general del proyecto</h2>
    </div>
    <div class="muted small">Clic para ampliar y hacer zoom</div>
  </div>
  <button type="button" class="masterplan" data-single="<?= img($p['masterplan']) ?>" data-label="Masterplan · <?= e($p['fullName']) ?>">
    <img src="<?= img($p['masterplan'], true) ?>" alt="Masterplan de <?= e($p['fullName']) ?>" loading="lazy">
    <span class="masterplan-hint">Ampliar masterplan</span>
  </button>
</section>

<section class="wrap section" data-lots="<?= e(json_encode(['id' => $id, 'name' => $p['fullName']] + $p['lots'], JSON_UNESCAPED_UNICODE)) ?>">
  <div class="section-head">
    <div>
      <div class="eyebrow">Disponibilidad</div>
      <h2 class="display-3">Lotes disponibles</h2>
    </div>
    <div class="pill-row" data-lot-filters></div>
  </div>
  <div class="lots-layout">
    <div class="lots-map">
      <div class="lots-scroll"><svg viewBox="0 0 1010 560" data-lot-svg></svg></div>
      <div class="zoom-ctrl">
        <button type="button" data-mp="in" aria-label="Acercar">+</button>
        <button type="button" data-mp="out" aria-label="Alejar">−</button>
      </div>
    </div>
    <div class="lots-panel">
      <div class="lot-detail" data-lot-detail hidden></div>
      <div class="lots-row lots-row-head"><span>Lote</span><span>Superficie</span><span>Estado</span></div>
      <div class="lots-list" data-lot-list></div>
    </div>
  </div>
  <p class="note">Plano ilustrativo. Disponibilidad sujeta a confirmación del asesor comercial.</p>
</section>
<?php endif; ?>

<?php if ($p['kind'] === 'units'): ?>
<section class="wrap section" data-tipos>
  <div class="eyebrow">Tipologías</div>
  <div class="section-head">
    <h2 class="display-3">Unidades disponibles</h2>
    <div class="pill-row">
      <?php foreach ($p['tipos'] as $i => $t): ?>
        <button type="button" class="pill<?= $i === 0 ? ' is-on' : '' ?>" data-tipo="<?= $i ?>"><?= e($t['name']) ?></button>
      <?php endforeach; ?>
    </div>
  </div>
  <?php foreach ($p['tipos'] as $i => $t): $imgs = array_map(fn($k) => img($k), $t['imgs']); ?>
    <div class="tipo" data-tipo-panel="<?= $i ?>"<?= $i ? ' hidden' : '' ?>>
      <button type="button" class="tipo-plan" data-gallery-open="<?= e(json_encode($imgs, JSON_UNESCAPED_SLASHES)) ?>" data-label="Planta · <?= e($t['name']) ?>">
        <img src="<?= $imgs[0] ?>" alt="Planta <?= e($t['name']) ?>" loading="lazy">
      </button>
      <div>
        <h3 class="display-4"><?= e($t['name']) ?></h3>
        <?php foreach ($t['specs'] as [$k, $v]): ?>
          <div class="spec"><span class="muted"><?= e($k) ?></span><span><?= e($v) ?></span></div>
        <?php endforeach; ?>
        <a class="btn btn-primary btn-sm mt-28" href="#consulta" data-scroll data-msg="Hola, me interesa la tipología <?= e($t['name']) ?> de Sol City.">Consultar por esta tipología</a>
      </div>
    </div>
  <?php endforeach; ?>
</section>
<?php endif; ?>

<section class="wrap section">
  <div class="eyebrow">Ubicación</div>
  <h2 class="display-3"><?= e($p['loc']) ?></h2>
  <div class="vmap" data-vmap="project"></div>
</section>

<section class="wrap section section-last two-col" id="consulta">
  <div>
    <div class="eyebrow">Consulta</div>
    <h2 class="display-2">Hablemos de <?= e($p['name']) ?></h2>
    <p class="lead-muted mb-36">Dejanos tus datos y un asesor comercial te contactará a la brevedad.</p>
    <div class="btn-row mb-36">
      <a class="btn btn-primary btn-sm" href="<?= wa_url("Hola, me interesa recibir información sobre {$p['fullName']}.") ?>" target="_blank" rel="noopener"><?= icon('whatsapp', 17) ?>Escribinos por WhatsApp</a>
      <a class="btn btn-outline btn-sm" href="<?= visit_url($p['fullName']) ?>" target="_blank" rel="noopener"><?= icon('calendar', 17) ?>Agendá tu visita</a>
    </div>
    <div class="advisor-list">
      <?php foreach (visible_advisors() as $a): ?>
        <div class="advisor-row">
          <div class="stack-3"><strong><?= e($a['name']) ?></strong><span class="muted small">Asesora comercial</span></div>
          <a class="btn-ghost" href="mailto:<?= e($a['email']) ?>">Email</a>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
  <div class="form-wrap">
    <?php form_success('Consulta enviada', 'Gracias. Un asesor de ' . $p['name'] . ' se comunicará con vos.', 'Enviar otra consulta'); ?>
    <form class="form" data-demo-form>
      <input type="hidden" name="proyecto" value="<?= e($p['fullName']) ?>">
      <label>Nombre<input name="nombre" required autocomplete="name"></label>
      <label>Teléfono<input name="telefono" type="tel" required autocomplete="tel"></label>
      <label class="full">Email<input name="email" type="email" required autocomplete="email"></label>
      <label class="full">Mensaje<textarea name="mensaje" rows="4" data-msg-target></textarea></label>
      <button type="submit" class="btn btn-primary btn-lg full-start">Enviar consulta</button>
    </form>
  </div>
</section>
<?php
page_footer(['project' => ['num' => $p['num'], 'name' => $p['fullName'], 'll' => $p['ll']], 'renders' => $renders]);
