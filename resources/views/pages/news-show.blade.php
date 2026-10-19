<x-layouts.site active="noticias" :title="$item->title" :description="$item->meta_description ?: $item->excerpt">
<article class="wrap page-intro article">
  <a class="link-back" href="{{ route('news.index') }}">{{ icon('back', 16) }}Todas las noticias</a>
  <div class="kicker">{{ $item->tagLabel() }}@if ($item->published_at) · {{ $item->published_at->translatedFormat('j \d\e F \d\e Y') }}@endif</div>
  <h1 class="display-2 balance">{{ $item->title }}</h1>
  @if ($item->excerpt)<p class="lead-muted article-lead">{{ $item->excerpt }}</p>@endif
  @if ($item->image)
  <div class="media media-16x10 article-media"><img src="{{ media_url($item->image) }}" alt="">{{ render_tag($item->image_is_render) }}</div>
  @endif
  <div class="article-body prose">{!! $item->body !!}</div>
</article>

@if ($related->isNotEmpty())
<section class="wrap section section-last">
  <x-section-head eyebrow="Noticias" title="Más noticias"><x-link-more :href="route('news.index')">Ver todas</x-link-more></x-section-head>
  <div class="grid-news grid-news-home">
    @foreach ($related as $r)<x-news-card :item="$r" />@endforeach
  </div>
</section>
@endif
</x-layouts.site>
