@php($labels = $page->get('compare_labels', []))
<x-layouts.site active="proyectos" :title="$page->meta_title ?: 'Proyectos'" :description="$page->meta_description" :script-data="['projects' => $pins]">
<section class="wrap page-intro">
  <div class="eyebrow">{{ $page->get('intro_eyebrow') }}</div>
  <div class="intro-row">
    <h1 class="display-1" style="max-width:14ch">{{ $page->get('intro_title') }}</h1>
    <p class="lead-muted">{{ $page->get('intro_lead') }}</p>
  </div>
</section>

<section class="wrap">
  <div class="grid-projects">
    @foreach ($projects as $p)
      <a class="project-card" href="{{ $p->url() }}">
        <div class="media media-4x3">
          <img src="{{ media_url($p->hero_image, true) }}" alt="{{ $p->name }}" loading="lazy">{{ render_tag($p->hero_is_render) }}
          <span class="chip">{{ $p->type_label }}</span>
        </div>
        <div class="project-card-foot">
          <div class="stack-8">
            <h2 class="card-title card-title-lg">{{ $p->name }}</h2>
            <div class="with-icon muted">{{ icon('pin', 16) }}{{ $p->location }}</div>
            <p class="muted body-sm">{{ $p->short }}</p>
          </div>
          <span class="round-arrow round-arrow-solid">{{ icon('arrow') }}</span>
        </div>
      </a>
    @endforeach
    @if ($page->get('soon_enabled'))
    <div class="project-card is-soon">
      <div class="media media-4x3 soon-box"><span class="chip chip-accent">{{ $page->get('soon_chip') }}</span></div>
      <div class="stack-8">
        <h2 class="card-title card-title-lg">{{ $page->get('soon_title') }}</h2>
        <div class="faint">{{ $page->get('soon_text') }}</div>
      </div>
    </div>
    @endif
  </div>
</section>

<section class="wrap section">
  <div class="eyebrow">{{ $page->get('map_eyebrow') }}</div>
  <h2 class="display-3">{{ $page->get('map_title') }}</h2>
  <div class="vmap" data-vmap="all"></div>
</section>

<section class="wrap section section-last">
  <div class="eyebrow">{{ $page->get('compare_eyebrow') }}</div>
  <h2 class="display-3">{{ $page->get('compare_title') }}</h2>
  <div class="compare-scroll">
    <table class="compare">
      <thead>
        <tr>
          <th></th>
          @foreach ($projects as $p)<th><a href="{{ $p->url() }}">{{ $p->name }}</a></th>@endforeach
        </tr>
      </thead>
      <tbody>
        @foreach (['location' => 'location', 'type' => 'compare_type', 'surface' => 'compare_surface', 'amenities' => 'compare_amenities', 'environment' => 'compare_environment'] as $key => $field)
          <tr>
            <th scope="row">{{ $labels[$key] ?? '' }}</th>
            @foreach ($projects as $p)<td>{{ $p->{$field} }}</td>@endforeach
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</section>
</x-layouts.site>
