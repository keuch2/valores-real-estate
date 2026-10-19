<x-layouts.site active="nosotros" :title="$page->meta_title ?: 'Nosotros'" :description="$page->meta_description">
<section class="wrap page-intro">
  <div class="eyebrow">{{ $page->get('eyebrow') }}</div>
  <h1 class="display-1 balance" style="max-width:18ch">{{ $page->get('title') }}</h1>
</section>

<section class="wrap pt-72 two-col align-start">
  <div class="media media-4x5"><img src="{{ media_url($page->get('image')) }}" alt="{{ $page->get('image_alt') }}">{{ render_tag($page->get('image_is_render')) }}</div>
  <div class="prose prose-lg">
    @foreach ($page->get('paragraphs', []) as $paragraph)
    <p>{{ $paragraph['text'] }}</p>
    @endforeach
  </div>
</section>

<section class="wrap section section-last">
  <div class="eyebrow">{{ $page->get('board_eyebrow') }}</div>
  <h2 class="display-3 mb-44">{{ $page->get('board_title') }}</h2>
  <div class="grid-board">
    @foreach ($board as $m)
      <div class="person">
        <div class="person-photo">@if ($m->photo)<img src="{{ media_url($m->photo) }}" alt="{{ $m->name }}">@endif</div>
        <div>
          <div class="person-role">{{ $m->role }}</div>
          <div class="person-name">{{ $m->name }}</div>
          <div class="person-bio">@foreach (paragraphs($m->bio) as $p)<p>{{ $p }}</p>@endforeach</div>
        </div>
      </div>
    @endforeach
  </div>
</section>
</x-layouts.site>
