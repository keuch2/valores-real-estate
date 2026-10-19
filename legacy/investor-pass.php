<?php
require __DIR__ . '/inc/layout.php';
page_header('investor', 'Investor Pass', 'Residencia permanente en Paraguay a través de la inversión inmobiliaria con Valores Real Estate.');
?>
<section class="wrap page-intro two-col align-end">
  <div>
    <div class="eyebrow">Investor Pass</div>
    <h1 class="display-1 balance">Residencia permanente en Paraguay a través de la inversión inmobiliaria.</h1>
  </div>
  <div class="stack-24">
    <p class="lead">El Investor Pass otorga la residencia permanente a inversionistas extranjeros que canalizan su capital a través de vehículos habilitados. En Valores Real Estate te acompañamos a estructurar la inversión en nuestros proyectos.</p>
    <a class="btn btn-primary btn-lg self-start" href="#solicitud" data-scroll>Quiero más información</a>
  </div>
</section>

<section class="wrap pt-72">
  <div class="media banner"><img src="<?= img('general/investor') ?>" alt=""><?= render_tag('general/investor') ?></div>
</section>

<section class="wrap section">
  <div class="eyebrow">Modalidades</div>
  <h2 class="display-3 mb-44">Tres caminos para invertir</h2>
  <div class="grid-3">
    <div class="mode-card">
      <div class="mode-head"><span class="kicker">Inmobiliaria</span><span class="tag tag-blue">Disponible</span></div>
      <div class="mode-amount">USD 200.000</div>
      <p class="muted">Inversión mínima en proyectos inmobiliarios habilitados: lotes y unidades en Sol City, Paraqvaria, La Ribera y Cumbres.</p>
    </div>
    <div class="mode-card mode-card-accent">
      <div class="mode-head"><span class="kicker">Turismo</span><span class="tag tag-amber">Próximamente</span></div>
      <div class="mode-amount">USD 150.000</div>
      <p class="muted">Inversión en desarrollos con fines turísticos. Estamos preparando las primeras opciones en esta modalidad.</p>
    </div>
    <div class="mode-card mode-card-info">
      <div class="mode-head"><span class="kicker">Mercado de valores</span><span class="tag tag-soft">Información</span></div>
      <div class="mode-amount">USD 200.000</div>
      <p class="muted">Inversión en instrumentos del mercado de valores paraguayo, como bonos, acciones o fondos. Esta modalidad se gestiona a través de una casa de bolsa y no forma parte de la oferta de Valores Real Estate.</p>
      <a class="link-more self-start" href="https://www.valores.com.py/investor-pass" target="_blank" rel="noopener">Ver en Valores Casa de Bolsa <?= icon('external', 14) ?></a>
    </div>
  </div>
</section>

<section class="wrap section">
  <div class="eyebrow">Proceso</div>
  <h2 class="display-3 mb-44">Cómo funciona</h2>
  <div class="steps">
    <?php foreach (INVESTOR_STEPS as [$n, $t, $d]): ?>
      <div class="step"><span class="step-n"><?= $n ?></span><h3><?= e($t) ?></h3><p class="muted"><?= e($d) ?></p></div>
    <?php endforeach; ?>
  </div>
</section>

<section class="wrap section section-last two-col" id="solicitud">
  <div>
    <h2 class="display-2">Empezá tu proceso</h2>
    <p class="lead-muted">Un asesor te explicará los requisitos, plazos y proyectos disponibles para tu inversión.</p>
  </div>
  <div class="form-wrap">
    <?php form_success('Solicitud recibida', 'Te contactaremos por email en las próximas 48 horas hábiles.'); ?>
    <form class="form" data-demo-form>
      <label>Nombre<input name="nombre" required autocomplete="name"></label>
      <label>País de residencia<input name="pais" required autocomplete="country-name"></label>
      <label>Email<input name="email" type="email" required autocomplete="email"></label>
      <label>Teléfono<input name="telefono" type="tel" autocomplete="tel"></label>
      <label class="full">Modalidad de interés
        <select name="modalidad"><option>Inmobiliaria (USD 200.000)</option><option>Turismo (próximamente)</option><option>Aún no lo sé</option></select>
      </label>
      <button type="submit" class="btn btn-primary btn-lg full-start">Solicitar asesoramiento</button>
    </form>
  </div>
</section>
<?php page_footer(); ?>
