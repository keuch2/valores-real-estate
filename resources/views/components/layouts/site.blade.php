@props(['active' => '', 'title' => '', 'description' => null, 'scriptData' => [], 'recaptcha' => false])
@php
    $fullTitle = $active === 'inicio' ? 'Valores Real Estate · '.$title : $title.' · Valores Real Estate';
    $spam = app(\App\Settings\SpamSettings::class);
    $loadRecaptcha = $recaptcha && $spam->recaptcha_enabled && $spam->recaptcha_site_key;
@endphp
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>{{ $fullTitle }}</title>
<meta name="description" content="{{ $description ?: config('site.default_description') }}">
<link rel="icon" type="image/png" href="{{ asset('assets/img/general/favicon.png') }}">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin="">
<link rel="stylesheet" href="{{ versioned_asset('assets/css/site.css') }}">
</head>
<body>
<header class="site-header">
  <div class="wrap header-bar">
    <a href="{{ route('home') }}" class="brand"><img src="{{ asset('assets/img/general/logo.png') }}" alt="Valores Real Estate" width="121" height="34"></a>
    <nav class="nav-main" aria-label="Principal">
      @foreach (config('site.nav') as $key => [$label, $route])
        <a href="{{ route($route) }}"@if ($key === $active) class="is-active" aria-current="page"@endif>{{ $label }}</a>
      @endforeach
    </nav>
    <button class="menu-toggle" type="button" aria-expanded="false" aria-controls="nav-mobile">Menú</button>
  </div>
  <nav class="nav-mobile" id="nav-mobile" aria-label="Principal" hidden>
    @foreach (config('site.nav') as $key => [$label, $route])
      <a href="{{ route($route) }}"@if ($key === $active) class="is-active"@endif>{{ $label }}</a>
    @endforeach
  </nav>
</header>
<main>
{{ $slot }}
</main>

<footer class="site-footer">
  <div class="wrap footer-grid">
    <div class="footer-brand">
      <img src="{{ asset('assets/img/general/logo.png') }}" alt="Valores Real Estate" width="114" height="32">
      <p>{{ $general->footer_tagline }}</p>
    </div>
    <div class="footer-col">
      <div class="footer-title">Sitio</div>
      @foreach (config('site.nav') as [$label, $route])<a href="{{ route($route) }}">{{ $label }}</a>@endforeach
    </div>
    <div class="footer-col">
      <div class="footer-title">Contacto</div>
      @foreach ($general->contact_rows as $row)<span>{{ $row['value'] }}</span>@endforeach
    </div>
    <div class="footer-col">
      <div class="footer-title">Redes</div>
      @foreach ($general->socials as $social)<a href="{{ $social['url'] }}"@if (str_starts_with($social['url'], 'http')) target="_blank" rel="noopener"@endif>{{ $social['label'] }}</a>@endforeach
    </div>
  </div>
  <div class="wrap footer-legal">
    <span>© {{ date('Y') }} Valores Real Estate</span><span>Asunción, Paraguay</span>
  </div>
</footer>

<a class="wa-float" href="{{ wa_url((string) $whatsapp->float_message, 'float') }}" target="_blank" rel="noopener">{{ icon('whatsapp', 22) }}<span>{{ $whatsapp->float_label }}</span></a>

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

<script>window.VRE = {!! json_encode($scriptData + ['office' => [$general->office_lat, $general->office_lng], 'recaptcha' => $loadRecaptcha ? $spam->recaptcha_site_key : null], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG) !!};</script>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" crossorigin=""></script>
@if ($loadRecaptcha)
<script src="https://www.google.com/recaptcha/api.js?render={{ urlencode($spam->recaptcha_site_key) }}" async defer></script>
@endif
<script src="{{ versioned_asset('assets/js/site.js') }}"></script>
</body>
</html>
