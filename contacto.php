<?php
require __DIR__ . '/inc/layout.php';
page_header('contacto', 'Contacto', 'Contactá a Valores Real Estate y a nuestro equipo de asesores comerciales.');
?>
<section class="wrap page-intro two-col">
  <div class="stack-28">
    <div class="eyebrow">Contacto</div>
    <h1 class="display-1">Estamos para ayudarte.</h1>
    <div class="contact-list mt-12">
      <?php foreach (CONTACT as [$k, $v]): ?>
        <div class="contact-row"><span class="label-caps"><?= e($k) ?></span><span><?= e($v) ?></span></div>
      <?php endforeach; ?>
    </div>
    <a class="btn btn-primary self-start" href="<?= wa_url() ?>" target="_blank" rel="noopener"><?= icon('whatsapp', 18) ?>Escribinos por WhatsApp</a>
  </div>
  <div class="form-wrap pt-48">
    <?php form_success('Mensaje enviado', 'Gracias por escribirnos. Te responderemos a la brevedad.'); ?>
    <form class="form" data-demo-form>
      <label>Nombre<input name="nombre" required autocomplete="name"></label>
      <label>Teléfono<input name="telefono" type="tel" autocomplete="tel"></label>
      <label class="full">Email<input name="email" type="email" required autocomplete="email"></label>
      <label class="full">Mensaje<textarea name="mensaje" rows="4" required></textarea></label>
      <button type="submit" class="btn btn-primary btn-lg full-start">Enviar mensaje</button>
    </form>
  </div>
</section>

<section class="wrap section">
  <div class="eyebrow">Asesores comerciales</div>
  <h2 class="display-3 mb-40">Hablá directo con el equipo</h2>
  <div class="grid-advisors">
    <?php foreach (visible_advisors() as $a): ?>
      <div class="advisor-card">
        <div class="advisor-id">
          <span class="avatar"><?= e(implode('', array_map(fn($w) => mb_substr($w, 0, 1), explode(' ', $a['name'])))) ?></span>
          <div><div class="advisor-name"><?= e($a['name']) ?></div><div class="muted small">Asesora comercial</div></div>
        </div>
        <div class="advisor-links">
          <a href="mailto:<?= e($a['email']) ?>" class="break"><?= e($a['email']) ?></a>
          <?php if ($a['ig']): ?><a href="https://instagram.com/<?= e($a['ig']) ?>" target="_blank" rel="noopener">@<?= e($a['ig']) ?></a><?php endif; ?>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
</section>

<section class="wrap section section-last">
  <div class="vmap vmap-sm" data-vmap="office"></div>
</section>
<?php page_footer(); ?>
