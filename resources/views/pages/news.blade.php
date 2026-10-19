<x-layouts.site active="noticias" :title="$page->meta_title ?: 'Noticias'" :description="$page->meta_description">
<section class="wrap page-intro">
  <div class="eyebrow">{{ $page->get('intro_eyebrow') }}</div>
  <div class="intro-row">
    <h1 class="display-1">{{ $page->get('intro_title') }}</h1>
    <div class="pill-row" data-news-filters>
      @foreach ($tags as $i => $tag)
        <button type="button" class="pill{{ $i === 0 ? ' is-on' : '' }}" data-filter="{{ $tag }}">{{ $tag }}</button>
      @endforeach
    </div>
  </div>
</section>

<section class="wrap section-last">
  <div class="grid-news" data-news-grid>
    @foreach ($news as $item)<x-news-card :item="$item" :with-cta="true" />@endforeach
  </div>
</section>
</x-layouts.site>
