<x-layouts.site active="trabaja" :title="$page->meta_title ?: 'Trabaja con nosotros'" :description="$page->meta_description" :recaptcha="true">
<section class="wrap page-intro section-last two-col">
  <div class="stack-28">
    <div class="eyebrow">{{ $page->get('eyebrow') }}</div>
    <h1 class="display-1 balance">{{ $page->get('title') }}</h1>
    <p class="lead-muted">{{ $page->get('lead') }}</p>
    @if ($page->get('image'))
    <div class="media media-16x10 mt-12"><img src="{{ media_url($page->get('image')) }}" alt="">{{ render_tag($page->get('image_is_render')) }}</div>
    @endif
  </div>
  <div class="form-wrap pt-48">
    <x-form-success :title="$page->get('success_title')" :text="$page->get('success_text')" />
    <form class="form" method="post" action="{{ route('careers.store') }}" enctype="multipart/form-data" data-ajax-form @if (session('form_sent')) hidden @endif>
      <x-form-guard action="careers" />
      <label>Nombre y apellido<input name="nombre" required autocomplete="name" value="{{ old('nombre') }}"></label>
      <label>Teléfono<input name="telefono" type="tel" required autocomplete="tel" value="{{ old('telefono') }}"></label>
      <label>Email<input name="email" type="email" required autocomplete="email" value="{{ old('email') }}"></label>
      <label>Área de interés
        <select name="area">@foreach ($page->get('areas', []) as $area)<option>{{ $area['label'] }}</option>@endforeach</select>
      </label>
      <label class="full file-drop">
        <span class="stack-4"><strong data-file-name>Tu currículum</strong><span class="muted small">PDF o Word, máximo 5 MB</span></span>
        <span class="file-cta">Adjuntar CV</span>
        <input type="file" name="cv" accept=".pdf,.doc,.docx" data-file-input>
      </label>
      <label class="full">Mensaje (opcional)<textarea name="mensaje" rows="3">{{ old('mensaje') }}</textarea></label>
      <x-form-errors />
      <button type="submit" class="btn btn-primary btn-lg full-start">Enviar postulación</button>
    </form>
  </div>
</section>
</x-layouts.site>
