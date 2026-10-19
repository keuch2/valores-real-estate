@php
    $wa = app(\App\Settings\WhatsappSettings::class);
    $visitUrl = wa_url($project->visit_message ?: fill_template($wa->visit_template, ['proyecto' => $project->full_name]), 'visit', $project->whatsapp_number);
    $infoUrl = wa_url($project->info_message ?: fill_template($wa->info_template, ['proyecto' => $project->full_name]), 'info', $project->whatsapp_number);
    $gallery = $project->images->map(fn ($i) => media_url($i->path))->all();
    $renders = $project->images->where('is_render', true)->map(fn ($i) => media_url($i->path))
        ->merge($project->typologies->flatMap(fn ($t) => collect($t->images ?? [])->where('is_render', true)->map(fn ($i) => media_url($i['path']))))
        ->values()->all();
    $lots = $project->units->map(fn ($u) => array_filter([
        'mz' => $u->block, 'n' => $u->number, 'm2' => $u->area_m2, 'type' => $u->type, 'status' => $u->status,
    ], fn ($v) => $v !== null) + ['m2' => null])->values()->all();
    $onlyAvailable = $project->units->isNotEmpty() && $project->units->every(fn ($u) => $u->status === 'disponible');
    $m2 = fn ($v) => number_format((float) $v, 2, ',', '.');
@endphp
<x-layouts.site active="proyectos" :title="$project->full_name" :description="$project->meta_description ?: $project->short" :recaptcha="true"
  :script-data="['project' => ['num' => $number, 'name' => $project->full_name, 'll' => [$project->lat, $project->lng]], 'renders' => $renders]">
<section class="proj-hero">
  <img src="{{ media_url($project->hero_image) }}" alt="" class="hero-img">
  <div class="proj-hero-shade"></div>
  {{ render_tag($project->hero_is_render) }}
  <div class="wrap proj-hero-inner">
    <a class="back-pill" href="{{ route('projects.index') }}">{{ icon('back', 16) }}Todos los proyectos</a>
    <div class="stack-18">
      <div class="eyebrow-line">{{ $project->type_label }}</div>
      <h1 class="proj-title">{{ $project->full_name }}</h1>
      <div class="proj-loc">{{ $project->location }}@if ($project->delivery) · Entrega {{ mb_strtolower($project->delivery) }}@endif</div>
    </div>
  </div>
</section>

@if ($project->facts)
<section class="facts">
  <div class="wrap facts-grid">
    @foreach ($project->facts as $fact)
      <div class="fact"><div class="fact-v">{{ $fact['value'] }}</div><div class="muted small">{{ $fact['label'] }}</div></div>
    @endforeach
  </div>
</section>
@endif

<section class="wrap section two-col">
  <div>
    <div class="eyebrow">El proyecto</div>
    <h2 class="display-2 balance">{{ $project->tagline }}</h2>
  </div>
  <div class="prose">
    @foreach ($project->paragraphs() as $paragraph)
    <p>{{ $paragraph }}</p>
    @endforeach
    <div class="btn-row">
      <a class="btn btn-primary" href="{{ $visitUrl }}" target="_blank" rel="noopener">{{ icon('calendar', 17) }}Agendá tu visita</a>
      <a class="btn btn-outline" href="#consulta" data-scroll>Consultar por este proyecto</a>
      @if ($project->brochure_path)
      <a class="btn btn-outline" href="{{ media_url($project->brochure_path) }}" target="_blank" rel="noopener">{{ icon('download', 17) }}Descargar brochure (PDF)</a>
      @endif
    </div>
    @if ($project->instagram)
      <a class="ig-link" href="https://www.instagram.com/{{ $project->instagram }}" target="_blank" rel="noopener">{{ icon('instagram', 18) }}Seguí a {{ $project->name }} en Instagram: {{ '@'.$project->instagram }}</a>
    @endif
  </div>
</section>

@if ($project->video_path)
<section class="wrap section">
  <div class="eyebrow">Video</div>
  <h2 class="display-3 mb-40">Conocé {{ $project->name }}</h2>
  <div class="video-wrap" data-video>
    <video class="proj-video" controls preload="metadata" playsinline @if ($project->video_poster) poster="{{ media_url($project->video_poster) }}" @endif>
      <source src="{{ media_url($project->video_path) }}" type="video/mp4">
    </video>
    <button type="button" class="video-play" aria-label="Reproducir video de {{ $project->name }}"><span class="video-play-icon"></span>Ver video</button>
  </div>
</section>
@endif

@if ($project->images->isNotEmpty())
<section class="wrap section">
  <div class="section-head">
    <h2 class="display-3">Galería</h2>
    <div class="muted small">{{ count($gallery) }} imágenes · clic para ampliar</div>
  </div>
  <div class="gallery" data-gallery="{{ json_encode($gallery, JSON_UNESCAPED_SLASHES) }}" data-label="{{ $project->full_name }}">
    @foreach ($project->images as $i => $image)
      <button type="button" class="gallery-item{{ $i === 0 ? ' is-wide' : '' }}" data-index="{{ $i }}"><img src="{{ media_url($image->path, true) }}" alt="{{ $image->alt ?: $project->full_name.' · imagen '.($i + 1) }}" loading="lazy">{{ render_tag($image->is_render) }}</button>
    @endforeach
  </div>
</section>
@endif

@if ($project->amenities)
<section class="wrap section">
  <div class="eyebrow">Amenities</div>
  <h2 class="display-3 mb-40">{{ $project->amenities_title ?: 'Amenities' }}</h2>
  <ul class="amenities">
    @foreach ($project->amenities as $amenity)<li>{{ icon('check', 18) }}{{ $amenity }}</li>@endforeach
  </ul>
</section>
@endif

@if ($project->sports_title)
<section class="wrap section">
  <div class="sports-band">
    <div>
      <div class="eyebrow">Zona deportiva</div>
      <h2 class="display-3">{{ $project->sports_title }}</h2>
    </div>
    <div class="stack-18">
      <p class="lead-muted">{{ $project->sports_text }}</p>
      @if ($project->sports_phones)
      <div>
        <div class="label-caps">Informes e inscripciones</div>
        <div class="sports-phones">
          @foreach ($project->sports_phones as $phone)<a href="tel:+595{{ substr(preg_replace('/\D/', '', $phone), 1) }}">{{ $phone }}</a>@endforeach
        </div>
      </div>
      @endif
    </div>
  </div>
</section>
@endif

@if ($project->kind === 'lots' && $project->units->isNotEmpty())
<section class="wrap section" data-lots="{{ json_encode(['name' => $project->full_name, 'lots' => $lots], JSON_UNESCAPED_UNICODE) }}">
  <div class="section-head">
    <div>
      <div class="eyebrow">Masterplan y disponibilidad</div>
      <h2 class="display-3">Lotes de {{ $project->name }}</h2>
    </div>
    <div class="pill-row" data-lot-filters></div>
  </div>
  <div class="lots-layout">
    @if ($project->masterplan_image)
    <button type="button" class="masterplan" data-single="{{ media_url($project->masterplan_image) }}" data-label="Masterplan · {{ $project->full_name }}">
      <img src="{{ media_url($project->masterplan_image, true) }}" alt="Masterplan de {{ $project->full_name }}" loading="lazy">
      <span class="masterplan-hint">Ampliar masterplan</span>
    </button>
    @endif
    <div class="lots-panel">
      <label class="lots-mz">Manzana
        <select data-lot-mz><option value="">Todas</option></select>
      </label>
      <div class="lot-detail" data-lot-detail hidden></div>
      <div class="lots-row lots-row-head"><span>Lote</span><span>Superficie</span><span>Estado</span></div>
      <div class="lots-list" data-lot-list></div>
    </div>
  </div>
  <p class="note">{{ $onlyAvailable ? 'Se muestran los lotes disponibles. ' : '' }}{{ $project->availability_note ?: 'Disponibilidad según la lista vigente, sujeta a confirmación del asesor comercial.' }}</p>
</section>
@endif

@if ($project->kind === 'units')
<section class="wrap section" data-tipos>
  @if ($project->typologies->isNotEmpty())
  <div class="eyebrow">Tipologías</div>
  <div class="section-head">
    <h2 class="display-3">Unidades disponibles</h2>
    <div class="pill-row">
      @foreach ($project->typologies as $i => $t)
        <button type="button" class="pill{{ $i === 0 ? ' is-on' : '' }}" data-tipo="{{ $i }}">{{ $t->name }}</button>
      @endforeach
    </div>
  </div>
  @foreach ($project->typologies as $i => $t)
    @php($imgs = collect($t->images ?? [])->map(fn ($img) => media_url($img['path']))->all())
    <div class="tipo" data-tipo-panel="{{ $i }}"@if ($i) hidden @endif>
      @if ($imgs)
      <button type="button" class="tipo-plan" data-gallery-open="{{ json_encode($imgs, JSON_UNESCAPED_SLASHES) }}" data-label="Planta · {{ $t->name }}">
        <img src="{{ $imgs[0] }}" alt="Planta {{ $t->name }}" loading="lazy">
      </button>
      @endif
      <div>
        <h3 class="display-4">{{ $t->name }}</h3>
        @foreach ($t->specs ?? [] as $spec)
          <div class="spec"><span class="muted">{{ $spec['label'] }}</span><span>{{ $spec['value'] }}</span></div>
        @endforeach
        <a class="btn btn-primary btn-sm mt-28" href="#consulta" data-scroll data-msg="Hola, me interesa la tipología {{ $t->name }} de {{ $project->name }}.">Consultar por esta tipología</a>
      </div>
    </div>
  @endforeach
  @endif

  @if ($project->units->isNotEmpty())
  <div class="units">
    <h3 class="display-4">Disponibilidad de unidades</h3>
    <div class="units-scroll">
      <table class="units-table">
        <thead><tr><th>Unidad</th><th>Piso</th><th>Tipología</th><th>Superficie</th><th>Ubicación</th><th>Estado</th><th></th></tr></thead>
        <tbody>
          @foreach ($project->units as $u)
            @php($free = $u->status === 'disponible')
            <tr @unless ($free) class="is-sold" @endunless>
              <td><strong>{{ $u->number }}</strong></td><td>{{ $u->floor }}</td><td>{{ ucfirst((string) $u->type) }}</td>
              <td>@if ($u->area_m2){{ $m2($u->area_m2) }} m²@endif</td><td>{{ $u->orientation }}</td>
              <td><span class="lot-status"><i class="dot-{{ $u->status }}"></i>{{ \App\Models\ProjectUnit::STATUSES[$u->status] ?? $u->status }}</span></td>
              <td>@if ($free)<a class="btn-ghost" href="#consulta" data-scroll data-msg="Hola, me interesa la unidad {{ $u->number }} ({{ $u->type }}{{ $u->area_m2 ? ', '.$m2($u->area_m2).' m²' : '' }}) de {{ $project->name }}.">Consultar</a>@endif</td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
    @if ($project->availability_note)<p class="note">{{ $project->availability_note }}</p>@endif
  </div>
  @endif
</section>
@endif

@if ($project->lat !== null)
<section class="wrap section">
  <div class="eyebrow">Ubicación</div>
  <h2 class="display-3">{{ $project->location }}</h2>
  <div class="vmap" data-vmap="project"></div>
</section>
@endif

<section class="wrap section section-last two-col" id="consulta">
  <div>
    <div class="eyebrow">Consulta</div>
    <h2 class="display-2">Hablemos de {{ $project->name }}</h2>
    <p class="lead-muted mb-36">Dejanos tus datos y un asesor comercial te contactará a la brevedad.</p>
    <div class="btn-row mb-36">
      <a class="btn btn-primary btn-sm" href="{{ $infoUrl }}" target="_blank" rel="noopener">{{ icon('whatsapp', 17) }}Escribinos por WhatsApp</a>
      <a class="btn btn-outline btn-sm" href="{{ $visitUrl }}" target="_blank" rel="noopener">{{ icon('calendar', 17) }}Agendá tu visita</a>
    </div>
    <div class="advisor-list">
      @foreach ($advisors as $a)
        <div class="advisor-row">
          <div class="stack-3"><strong>{{ $a->name }}</strong><span class="muted small">{{ $a->role_label }}</span></div>
          @if ($a->email)<a class="btn-ghost" href="mailto:{{ $a->email }}">Email</a>@endif
        </div>
      @endforeach
    </div>
  </div>
  <div class="form-wrap">
    <x-form-success title="Consulta enviada" :text="'Gracias. Un asesor de '.$project->name.' se comunicará con vos.'" reset-label="Enviar otra consulta" />
    <form class="form" method="post" action="{{ route('projects.inquiry', $project->slug) }}" data-ajax-form @if (session('form_sent')) hidden @endif>
      <x-form-guard action="project" />
      <label>Nombre<input name="nombre" required autocomplete="name" value="{{ old('nombre') }}"></label>
      <label>Teléfono<input name="telefono" type="tel" required autocomplete="tel" value="{{ old('telefono') }}"></label>
      <label class="full">Email<input name="email" type="email" required autocomplete="email" value="{{ old('email') }}"></label>
      <label class="full">Mensaje<textarea name="mensaje" rows="4" data-msg-target>{{ old('mensaje') }}</textarea></label>
      <x-form-errors />
      <button type="submit" class="btn btn-primary btn-lg full-start">Enviar consulta</button>
    </form>
  </div>
</section>
</x-layouts.site>
