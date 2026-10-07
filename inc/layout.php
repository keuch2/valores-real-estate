<?php
require __DIR__ . '/data.php';

function e(?string $s): string
{
    return htmlspecialchars((string) $s, ENT_QUOTES, 'UTF-8');
}

function img(string $key, bool $small = false): string
{
    return 'assets/img/' . $key . ($small ? '-sm' : '') . '.jpg';
}

function file_url(string $path): string
{
    return implode('/', array_map('rawurlencode', explode('/', $path)));
}

function project_url(string $id): string
{
    return 'proyecto.php?p=' . rawurlencode($id);
}

function wa_url(string $text = ''): string
{
    return 'https://wa.me/' . WHATSAPP . ($text === '' ? '' : '?text=' . rawurlencode($text));
}

function visit_url(string $projectName): string
{
    return wa_url("Hola, me gustaría agendar una visita a $projectName. Mi día y horario de preferencia es: ");
}

function is_render(string $key): bool
{
    $matches = fn(array $patterns) => (bool) array_filter($patterns, fn($pat) => fnmatch($pat, $key));
    return $matches(RENDERS) && !$matches(NOT_RENDERS);
}

function render_tag(string $key): string
{
    return is_render($key) ? '<span class="render-tag">Imagen ilustrativa (render)</span>' : '';
}

function visible_advisors(): array
{
    return array_values(array_filter(ADVISORS, fn($a) => empty($a['hidden'])));
}

function icon(string $name, int $size = 18): string
{
    $paths = [
        'arrow' => '<path d="M5 12h14M13 6l6 6-6 6"/>',
        'back' => '<path d="M19 12H5M11 6l-6 6 6 6"/>',
        'pin' => '<path d="M12 21s-7-6.2-7-11.5A7 7 0 0 1 19 9.5C19 14.8 12 21 12 21Z"/><circle cx="12" cy="9.5" r="2.5"/>',
        'download' => '<path d="M12 4v11M7 10l5 5 5-5M5 20h14"/>',
        'external' => '<path d="M7 17L17 7M8 7h9v9"/>',
        'check' => '<path d="M5 12.5l4.5 4.5L19 7.5"/>',
        'calendar' => '<rect x="4" y="5" width="16" height="15" rx="2"/><path d="M4 10h16M9 3v4M15 3v4"/>',
        'instagram' => '<rect x="4" y="4" width="16" height="16" rx="4.5"/><circle cx="12" cy="12" r="3.6"/><circle cx="16.8" cy="7.2" r=".6" fill="currentColor"/>',
        'whatsapp' => '<path d="M4 20l1.3-3.9A8 8 0 1 1 8 19.1L4 20Z"/><path d="M9 9.5c.3 2.4 2.1 4.3 4.5 4.8l1-1.2 1.8.8-.4 1.6c-3.6.2-7-3-7.2-6.6l1.6-.4.8 1.8-1.1 1"/>',
    ];
    return '<svg class="icon" width="' . $size . '" height="' . $size . '" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">' . $paths[$name] . '</svg>';
}

function page_header(string $active, string $title, string $description = ''): void
{
    $fullTitle = $active === 'inicio' ? 'Valores Real Estate · ' . $title : $title . ' · Valores Real Estate';
    ?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= e($fullTitle) ?></title>
<meta name="description" content="<?= e($description ?: 'Valores Real Estate desarrolla proyectos residenciales y urbanos en Paraguay.') ?>">
<link rel="icon" type="image/png" href="assets/img/general/favicon.png">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin="">
<link rel="stylesheet" href="assets/css/site.css?v=<?= filemtime(__DIR__ . '/../assets/css/site.css') ?>">
</head>
<body>
<header class="site-header">
  <div class="wrap header-bar">
    <a href="index.php" class="brand"><img src="assets/img/general/logo.png" alt="Valores Real Estate" width="121" height="34"></a>
    <nav class="nav-main" aria-label="Principal">
      <?php foreach (NAV as $key => [$label, $href]): ?>
        <a href="<?= $href ?>"<?= $key === $active ? ' class="is-active" aria-current="page"' : '' ?>><?= e($label) ?></a>
      <?php endforeach; ?>
    </nav>
    <button class="menu-toggle" type="button" aria-expanded="false" aria-controls="nav-mobile">Menú</button>
  </div>
  <nav class="nav-mobile" id="nav-mobile" aria-label="Principal" hidden>
    <?php foreach (NAV as $key => [$label, $href]): ?>
      <a href="<?= $href ?>"<?= $key === $active ? ' class="is-active"' : '' ?>><?= e($label) ?></a>
    <?php endforeach; ?>
  </nav>
</header>
<main>
<?php
}

function page_footer(array $scriptData = []): void
{
    ?>
</main>

<footer class="site-footer">
  <div class="wrap footer-grid">
    <div class="footer-brand">
      <img src="assets/img/general/logo.png" alt="Valores Real Estate" width="114" height="32">
      <p>Desarrollamos lugares que crecen en valor.</p>
    </div>
    <div class="footer-col">
      <div class="footer-title">Sitio</div>
      <?php foreach (NAV as [$label, $href]): ?><a href="<?= $href ?>"><?= e($label) ?></a><?php endforeach; ?>
    </div>
    <div class="footer-col">
      <div class="footer-title">Contacto</div>
      <?php foreach (CONTACT as [, $v]): ?><span><?= e($v) ?></span><?php endforeach; ?>
    </div>
    <div class="footer-col">
      <div class="footer-title">Redes</div>
      <?php foreach (SOCIAL as [$label, $href]): ?><a href="<?= e($href) ?>"><?= e($label) ?></a><?php endforeach; ?>
    </div>
  </div>
  <div class="wrap footer-legal">
    <span>© <?= date('Y') ?> Valores Real Estate</span><span>Asunción, Paraguay</span>
  </div>
</footer>

<a class="wa-float" href="<?= wa_url() ?>" target="_blank" rel="noopener"><?= icon('whatsapp', 22) ?><span>Hablar con un asesor</span></a>

<div class="lightbox" id="lightbox" hidden role="dialog" aria-modal="true" aria-label="Visor de imágenes">
  <div class="lb-bar">
    <span class="lb-label"></span>
    <div class="lb-actions">
      <button type="button" data-lb="prev" aria-label="Anterior">‹</button>
      <button type="button" data-lb="next" aria-label="Siguiente">›</button>
      <button type="button" data-lb="out" aria-label="Alejar">−</button>
      <button type="button" data-lb="in" aria-label="Acercar">+</button>
      <button type="button" data-lb="close" class="lb-close" aria-label="Cerrar">✕</button>
    </div>
  </div>
  <div class="lb-stage"><img alt=""></div>
</div>

<script>window.VRE = <?= json_encode($scriptData + ['office' => OFFICE_LL], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>;</script>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" crossorigin=""></script>
<script src="assets/js/site.js?v=<?= filemtime(__DIR__ . '/../assets/js/site.js') ?>"></script>
</body>
</html>
<?php
}

function section_head(string $eyebrow, string $title, string $aside = '', string $tag = 'h2'): void
{
    ?>
    <div class="section-head">
      <div>
        <div class="eyebrow"><?= e($eyebrow) ?></div>
        <<?= $tag ?> class="display-2"><?= e($title) ?></<?= $tag ?>>
      </div>
      <?= $aside ?>
    </div>
<?php
}

function link_more(string $href, string $label): string
{
    return '<a class="link-more" href="' . e($href) . '">' . e($label) . ' ' . icon('arrow', 16) . '</a>';
}

function news_card(array $a, bool $withCta = false): void
{
    ?>
    <a class="news-card" href="<?= e($a['url']) ?>" target="_blank" rel="noopener" data-tag="<?= e($a['tag']) ?>">
      <div class="media media-16x10"><img src="<?= img($a['img'], true) ?>" alt="" loading="lazy"><?= render_tag($a['img']) ?></div>
      <div class="kicker"><?= e($a['source']) ?> · <?= e($a['tag']) ?></div>
      <h3><?= e($a['title']) ?></h3>
      <?php if ($withCta): ?><span class="read-more">Leer nota <?= icon('external', 14) ?></span><?php endif; ?>
    </a>
<?php
}

function form_success(string $title, string $text, string $resetLabel = ''): void
{
    ?>
    <div class="form-success" hidden>
      <span class="check-badge"><?= icon('check', 22) ?></span>
      <h3><?= e($title) ?></h3>
      <p><?= e($text) ?></p>
      <?php if ($resetLabel): ?><button type="button" class="link-btn" data-form-reset><?= e($resetLabel) ?></button><?php endif; ?>
    </div>
<?php
}
