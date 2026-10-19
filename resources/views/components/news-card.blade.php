@props(['item', 'withCta' => false])
    <a class="news-card" href="{{ $item->url() }}"@if ($item->isExternal()) target="_blank" rel="noopener"@endif data-tag="{{ $item->tagLabel() }}">
      <div class="media media-16x10"><img src="{{ media_url($item->image, true) }}" alt="" loading="lazy">{{ render_tag($item->image_is_render) }}</div>
      <div class="kicker">{{ $item->source ?: 'Valores Real Estate' }} · {{ $item->tagLabel() }}</div>
      <h3>{{ $item->title }}</h3>
      @if ($withCta)<span class="read-more">Leer nota {{ $item->isExternal() ? icon('external', 14) : icon('arrow', 14) }}</span>@endif
    </a>
