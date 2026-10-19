<x-layouts.site active="inicio" :title="$page->meta_title ?: 'Desarrollamos lugares que crecen en valor'" :description="$page->meta_description">
<section class="hero">
  <img src="{{ media_url($page->get('hero_image')) }}" alt="" class="hero-img">
  <div class="hero-shade"></div>
  {{ render_tag($page->get('hero_is_render')) }}
  <div class="wrap hero-inner">
    <h1 class="hero-title">{{ $page->get('hero_title') }}</h1>
    <div class="hero-foot">
      <p>{{ $page->get('hero_text') }}</p>
      <a class="btn btn-accent btn-lg" href="{{ route('projects.index') }}">{{ $page->get('hero_cta') }} {{ icon('arrow') }}</a>
    </div>
  </div>
</section>

<section class="wrap section">
  <x-section-head :eyebrow="$page->get('map_eyebrow')" :title="$page->get('map_title')"><p class="lead-muted">{{ $page->get('map_lead') }}</p></x-section-head>
  <div class="py-map">
    <div class="py-map-canvas">
      <div class="py-map-frame">
        <img src="{{ asset('assets/img/general/mapa-paraguay.svg') }}" alt="Mapa del Paraguay">
        <span class="py-map-label">Paraguay</span>
        @foreach ($projects->filter(fn ($p) => $p->map_x !== null && $p->map_y !== null) as $p)
          <span class="py-dot" style="left:{{ $p->map_x }}%;top:{{ $p->map_y }}%"></span>
          <a class="py-pin py-pin-{{ $p->map_label_side }}" style="left:{{ $p->map_x }}%;top:{{ $p->map_y }}%" href="{{ $p->url() }}"><span class="py-pin-tip"></span><span class="py-pin-label">{{ $p->name }}</span></a>
        @endforeach
      </div>
    </div>
    <div class="py-map-list">
      @foreach ($projects as $p)
        <a href="{{ $p->url() }}">
          <span class="num-badge">{{ $loop->iteration }}</span>
          <span class="py-map-item"><span class="py-map-name">{{ $p->name }}</span><span class="muted">{{ $p->location }}</span></span>
        </a>
      @endforeach
    </div>
  </div>
</section>

<section class="wrap section">
  <x-section-head :eyebrow="$page->get('projects_eyebrow')" :title="$page->get('projects_title')"><x-link-more :href="route('projects.index')">{{ $page->get('projects_link') }}</x-link-more></x-section-head>
  <div class="grid-cards">
    @foreach ($projects as $p)
      <a class="project-card" href="{{ $p->url() }}">
        <div class="media media-3x4">
          <img src="{{ media_url($p->hero_image, true) }}" alt="{{ $p->name }}" loading="lazy">{{ render_tag($p->hero_is_render) }}
          <span class="chip">{{ $p->type_label }}</span>
        </div>
        <div class="project-card-foot">
          <div>
            <h3 class="card-title">{{ $p->name }}</h3>
            <div class="muted small">{{ $p->location }}</div>
          </div>
          <span class="round-arrow">{{ icon('arrow', 16) }}</span>
        </div>
      </a>
    @endforeach
  </div>
</section>

<section class="wrap section section-last">
  <x-section-head :eyebrow="$page->get('news_eyebrow')" :title="$page->get('news_title')"><x-link-more :href="route('news.index')">{{ $page->get('news_link') }}</x-link-more></x-section-head>
  <div class="grid-news grid-news-home">
    @foreach ($news as $item)<x-news-card :item="$item" />@endforeach
  </div>
</section>
</x-layouts.site>
