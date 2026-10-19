@props(['title', 'text', 'resetLabel' => ''])
    <div class="form-success"@unless (session('form_sent')) hidden @endunless>
      <span class="check-badge">{{ icon('check', 22) }}</span>
      <h3>{{ $title }}</h3>
      <p>{{ $text }}</p>
      {{ $slot }}
      @if ($resetLabel)<button type="button" class="link-btn" data-form-reset>{{ $resetLabel }}</button>@endif
    </div>
