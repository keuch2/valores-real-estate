@props(['eyebrow', 'title', 'tag' => 'h2'])
    <div class="section-head">
      <div>
        <div class="eyebrow">{{ $eyebrow }}</div>
        <{{ $tag }} class="display-2">{{ $title }}</{{ $tag }}>
      </div>
      {{ $slot }}
    </div>
