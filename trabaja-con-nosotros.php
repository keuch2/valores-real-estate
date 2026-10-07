<?php
require __DIR__ . '/inc/layout.php';
page_header('trabaja', 'Trabaja con nosotros', 'Sumate al equipo de Valores Real Estate.');
?>
<section class="wrap page-intro section-last two-col">
  <div class="stack-28">
    <div class="eyebrow">Trabaja con nosotros</div>
    <h1 class="display-1 balance">Sumate al equipo de Valores Real Estate.</h1>
    <p class="lead-muted">Buscamos personas comprometidas en las áreas comercial, desarrollo de proyectos, administración y marketing. Envianos tu CV y te contactaremos cuando haya una posición acorde a tu perfil.</p>
    <div class="media media-16x10 mt-12"><img src="<?= img('general/trabaja') ?>" alt=""></div>
  </div>
  <div class="form-wrap pt-48">
    <?php form_success('Postulación enviada', 'Gracias por tu interés. Revisaremos tu perfil y te escribiremos.'); ?>
    <form class="form" data-demo-form>
      <label>Nombre y apellido<input name="nombre" required autocomplete="name"></label>
      <label>Teléfono<input name="telefono" type="tel" required autocomplete="tel"></label>
      <label>Email<input name="email" type="email" required autocomplete="email"></label>
      <label>Área de interés
        <select name="area"><option>Comercial / Ventas</option><option>Desarrollo de proyectos</option><option>Administración y finanzas</option><option>Marketing</option><option>Otra</option></select>
      </label>
      <label class="full file-drop">
        <span class="stack-4"><strong data-file-name>Tu currículum</strong><span class="muted small">PDF o Word, máximo 5 MB</span></span>
        <span class="file-cta">Adjuntar CV</span>
        <input type="file" name="cv" accept=".pdf,.doc,.docx" data-file-input>
      </label>
      <label class="full">Mensaje (opcional)<textarea name="mensaje" rows="3"></textarea></label>
      <button type="submit" class="btn btn-primary btn-lg full-start">Enviar postulación</button>
    </form>
  </div>
</section>
<?php page_footer(); ?>
