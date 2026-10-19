<x-layouts.site active="contacto" :title="$page->meta_title ?: 'Contacto'" :description="$page->meta_description" :recaptcha="true">
<section class="wrap page-intro two-col">
  <div class="stack-28">
    <div class="eyebrow">{{ $page->get('eyebrow') }}</div>
    <h1 class="display-1">{{ $page->get('title') }}</h1>
    <div class="contact-list mt-12">
      @foreach ($general->contact_rows as $row)
        <div class="contact-row"><span class="label-caps">{{ $row['label'] }}</span><span>{{ $row['value'] }}</span></div>
      @endforeach
    </div>
    <a class="btn btn-primary self-start" href="{{ wa_url() }}" target="_blank" rel="noopener">{{ icon('whatsapp', 18) }}{{ $page->get('whatsapp_cta') }}</a>
  </div>
  <div class="form-wrap pt-48">
    <x-form-success :title="$page->get('success_title')" :text="$page->get('success_text')" />
    <form class="form" method="post" action="{{ route('contact.store') }}" data-ajax-form @if (session('form_sent')) hidden @endif>
      <x-form-guard action="contact" />
      <label>Nombre<input name="nombre" required autocomplete="name" value="{{ old('nombre') }}"></label>
      <label>Teléfono<input name="telefono" type="tel" autocomplete="tel" value="{{ old('telefono') }}"></label>
      <label class="full">Email<input name="email" type="email" required autocomplete="email" value="{{ old('email') }}"></label>
      <label class="full">Mensaje<textarea name="mensaje" rows="4" required>{{ old('mensaje') }}</textarea></label>
      <x-form-errors />
      <button type="submit" class="btn btn-primary btn-lg full-start">Enviar mensaje</button>
    </form>
  </div>
</section>

<section class="wrap section">
  <div class="eyebrow">{{ $page->get('advisors_eyebrow') }}</div>
  <h2 class="display-3 mb-40">{{ $page->get('advisors_title') }}</h2>
  <div class="grid-advisors">
    @foreach ($advisors as $a)
      <div class="advisor-card">
        <div class="advisor-id">
          <span class="avatar">{{ collect(explode(' ', $a->name))->map(fn ($w) => mb_substr($w, 0, 1))->implode('') }}</span>
          <div><div class="advisor-name">{{ $a->name }}</div><div class="muted small">{{ $a->role_label }}</div></div>
        </div>
        <div class="advisor-links">
          @if ($a->email)<a href="mailto:{{ $a->email }}" class="break">{{ $a->email }}</a>@endif
          @if ($a->instagram)<a href="https://instagram.com/{{ $a->instagram }}" target="_blank" rel="noopener">{{ '@'.$a->instagram }}</a>@endif
        </div>
      </div>
    @endforeach
  </div>
</section>

<section class="wrap section section-last">
  <div class="vmap vmap-sm" data-vmap="office"></div>
</section>
</x-layouts.site>
