@php
    $styles = ['blue' => ['', 'tag-blue'], 'amber' => [' mode-card-accent', 'tag-amber'], 'info' => [' mode-card-info', 'tag-soft']];
    $modes = $page->get('modes', []);
@endphp
<x-layouts.site active="investor" :title="$page->meta_title ?: 'Investor Pass'" :description="$page->meta_description" :recaptcha="true">
<section class="wrap page-intro two-col align-end">
  <div>
    <div class="eyebrow">{{ $page->get('eyebrow') }}</div>
    <h1 class="display-1 balance">{{ $page->get('title') }}</h1>
  </div>
  <div class="stack-24">
    <p class="lead">{{ $page->get('lead') }}</p>
    <a class="btn btn-primary btn-lg self-start" href="#solicitud" data-scroll>{{ $page->get('cta') }}</a>
  </div>
</section>

@if ($page->get('banner'))
<section class="wrap pt-72">
  <div class="media banner"><img src="{{ media_url($page->get('banner')) }}" alt="">{{ render_tag($page->get('banner_is_render')) }}</div>
</section>
@endif

<section class="wrap section">
  <div class="eyebrow">{{ $page->get('modes_eyebrow') }}</div>
  <h2 class="display-3 mb-44">{{ $page->get('modes_title') }}</h2>
  <div class="{{ count($modes) > 2 ? 'grid-3' : 'grid-2' }}">
    @foreach ($modes as $mode)
      @php([$cardClass, $tagClass] = $styles[$mode['style'] ?? 'blue'] ?? $styles['blue'])
    <div class="mode-card{{ $cardClass }}">
      <div class="mode-head"><span class="kicker">{{ $mode['kicker'] }}</span>@if (! empty($mode['tag']))<span class="tag {{ $tagClass }}">{{ $mode['tag'] }}</span>@endif</div>
      <div class="mode-amount">{{ $mode['amount'] }}</div>
      <p class="muted">{{ fill_template($mode['text'] ?? '', ['proyectos' => $projectNames]) }}</p>
      @if (! empty($mode['link_url']))
      <a class="link-more self-start" href="{{ $mode['link_url'] }}" target="_blank" rel="noopener">{{ $mode['link_label'] ?: 'Más información' }} {{ icon('external', 14) }}</a>
      @endif
    </div>
    @endforeach
  </div>
</section>

<section class="wrap section">
  <div class="eyebrow">{{ $page->get('steps_eyebrow') }}</div>
  <h2 class="display-3 mb-44">{{ $page->get('steps_title') }}</h2>
  <div class="steps">
    @foreach ($page->get('steps', []) as $step)
      <div class="step"><span class="step-n">{{ $step['number'] }}</span><h3>{{ $step['title'] }}</h3><p class="muted">{{ $step['text'] }}</p></div>
    @endforeach
  </div>
</section>

<section class="wrap section section-last two-col" id="solicitud">
  <div>
    <h2 class="display-2">{{ $page->get('form_title') }}</h2>
    <p class="lead-muted">{{ $page->get('form_lead') }}</p>
  </div>
  <div class="form-wrap">
    <x-form-success :title="$page->get('success_title')" :text="$page->get('success_text')">
      <a class="btn btn-primary btn-sm" href="{{ session('whatsapp_url', '#') }}" target="_blank" rel="noopener" data-whatsapp-link @unless (session('whatsapp_url')) hidden @endunless>{{ icon('whatsapp', 17) }}Abrir WhatsApp</a>
    </x-form-success>
    <form class="form" method="post" action="{{ route('investor.store') }}" data-ajax-form @if (session('form_sent')) hidden @endif>
      <x-form-guard action="investor" />
      <label>Nombre<input name="nombre" required autocomplete="name" value="{{ old('nombre') }}"></label>
      <label>País de residencia<input name="pais" required autocomplete="country-name" value="{{ old('pais') }}"></label>
      <label>Email<input name="email" type="email" required autocomplete="email" value="{{ old('email') }}"></label>
      <label>Teléfono<input name="telefono" type="tel" autocomplete="tel" value="{{ old('telefono') }}"></label>
      <label class="full">Modalidad de interés
        <select name="modalidad">@foreach ($page->get('modality_options', []) as $option)<option>{{ $option }}</option>@endforeach</select>
      </label>
      <x-form-errors />
      <button type="submit" class="btn btn-primary btn-lg full-start">{{ icon('whatsapp', 18) }}{{ $page->get('submit_label') }}</button>
    </form>
  </div>
</section>
</x-layouts.site>
