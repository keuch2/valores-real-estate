(function () {
  'use strict';
  const $ = (sel, root = document) => root.querySelector(sel);
  const $$ = (sel, root = document) => Array.from(root.querySelectorAll(sel));
  const esc = s => String(s).replace(/[&<>"']/g, c => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));

  /* ---------- mobile menu ---------- */
  const toggle = $('.menu-toggle');
  const mobileNav = $('#nav-mobile');
  if (toggle && mobileNav) {
    toggle.addEventListener('click', () => {
      const open = mobileNav.hidden;
      mobileNav.hidden = !open;
      toggle.setAttribute('aria-expanded', String(open));
      toggle.textContent = open ? 'Cerrar' : 'Menú';
    });
  }

  /* ---------- smooth scroll (+ prefilled message) ---------- */
  function scrollToEl(el) {
    const header = $('.site-header');
    const top = el.getBoundingClientRect().top + window.scrollY - (header ? header.offsetHeight : 0) - 16;
    window.scrollTo({ top, behavior: 'smooth' });
  }
  function prefill(msg) {
    const target = $('[data-msg-target]');
    if (!target || !msg) return;
    const form = target.closest('form');
    const wrap = form && form.parentElement;
    if (wrap && form.hidden) resetForm(wrap);
    target.value = msg;
  }
  document.addEventListener('click', e => {
    const a = e.target.closest('a[data-scroll]');
    if (!a) return;
    const el = document.querySelector(a.getAttribute('href'));
    if (!el) return;
    e.preventDefault();
    prefill(a.dataset.msg);
    scrollToEl(el);
  });

  /* ---------- demo forms ---------- */
  function resetForm(wrap) {
    const form = $('form', wrap);
    form.reset();
    form.hidden = false;
    $('.form-success', wrap).hidden = true;
    const fileName = $('[data-file-name]', wrap);
    if (fileName) fileName.textContent = 'Tu currículum';
  }
  $$('form[data-demo-form]').forEach(form => {
    const wrap = form.parentElement;
    form.addEventListener('submit', e => {
      e.preventDefault();
      form.hidden = true;
      $('.form-success', wrap).hidden = false;
    });
    const reset = $('[data-form-reset]', wrap);
    if (reset) reset.addEventListener('click', () => resetForm(wrap));
  });
  $$('[data-file-input]').forEach(input => {
    input.addEventListener('change', () => {
      const name = input.files[0] ? input.files[0].name : 'Tu currículum';
      $('[data-file-name]', input.closest('label')).textContent = name;
    });
  });

  /* ---------- lightbox ---------- */
  const lb = $('#lightbox');
  const lbImg = lb && $('.lb-stage img', lb);
  const lbState = { list: [], i: 0, label: '', zoom: 1 };
  function lbRender() {
    lbImg.src = lbState.list[lbState.i];
    const renders = (window.VRE && window.VRE.renders) || [];
    $('.lb-label', lb).textContent = (lbState.list.length > 1
      ? `${lbState.label} · ${lbState.i + 1} / ${lbState.list.length}`
      : lbState.label) + (renders.includes(lbState.list[lbState.i]) ? ' · Imagen ilustrativa (render)' : '');
    lb.classList.toggle('is-single', lbState.list.length < 2);
    lbApplyZoom();
  }
  function lbApplyZoom() {
    const zoomed = lbState.zoom > 1;
    lb.classList.toggle('is-zoomed', zoomed);
    lbImg.style.width = zoomed ? Math.round(lbState.zoom * 100) + '%' : '';
  }
  function lbOpen(list, i, label) {
    Object.assign(lbState, { list, i, label, zoom: 1 });
    lbRender();
    lb.hidden = false;
    document.body.classList.add('no-scroll');
    $('.lb-close', lb).focus();
  }
  function lbClose() {
    lb.hidden = true;
    document.body.classList.remove('no-scroll');
  }
  function lbStep(d) {
    lbState.i = (lbState.i + d + lbState.list.length) % lbState.list.length;
    lbState.zoom = 1;
    lbRender();
  }
  if (lb) {
    const actions = {
      prev: () => lbStep(-1),
      next: () => lbStep(1),
      in: () => { lbState.zoom = Math.min(4, lbState.zoom + 0.75); lbApplyZoom(); },
      out: () => { lbState.zoom = Math.max(1, lbState.zoom - 0.75); lbApplyZoom(); },
      close: lbClose,
    };
    lb.addEventListener('click', e => {
      const btn = e.target.closest('[data-lb]');
      if (btn) actions[btn.dataset.lb]();
      else if (e.target.classList.contains('lb-stage')) lbClose();
    });
    document.addEventListener('keydown', e => {
      if (lb.hidden) return;
      if (e.key === 'Escape') lbClose();
      if (e.key === 'ArrowLeft' && lbState.list.length > 1) lbStep(-1);
      if (e.key === 'ArrowRight' && lbState.list.length > 1) lbStep(1);
    });
    $$('[data-gallery]').forEach(g => {
      const list = JSON.parse(g.dataset.gallery);
      g.addEventListener('click', e => {
        const item = e.target.closest('[data-index]');
        if (item) lbOpen(list, Number(item.dataset.index), g.dataset.label);
      });
    });
    $$('[data-gallery-open]').forEach(b => b.addEventListener('click', () => lbOpen(JSON.parse(b.dataset.galleryOpen), 0, b.dataset.label)));
    $$('[data-single]').forEach(b => b.addEventListener('click', () => lbOpen([b.dataset.single], 0, b.dataset.label)));
  }

  /* ---------- video de proyecto ---------- */
  $$('[data-video]').forEach(wrap => {
    const video = $('video', wrap);
    $('.video-play', wrap).addEventListener('click', () => video.play());
    video.addEventListener('play', () => wrap.classList.add('is-playing'));
  });

  /* ---------- tipologías ---------- */
  const tipos = $('[data-tipos]');
  if (tipos) {
    tipos.addEventListener('click', e => {
      const btn = e.target.closest('[data-tipo]');
      if (!btn) return;
      $$('[data-tipo]', tipos).forEach(b => b.classList.toggle('is-on', b === btn));
      $$('[data-tipo-panel]', tipos).forEach(p => { p.hidden = p.dataset.tipoPanel !== btn.dataset.tipo; });
    });
  }

  /* ---------- news filters ---------- */
  const newsFilters = $('[data-news-filters]');
  if (newsFilters) {
    newsFilters.addEventListener('click', e => {
      const btn = e.target.closest('[data-filter]');
      if (!btn) return;
      const f = btn.dataset.filter;
      $$('[data-filter]', newsFilters).forEach(b => b.classList.toggle('is-on', b === btn));
      $$('[data-news-grid] .news-card').forEach(c => { c.hidden = f !== 'Todas' && c.dataset.tag !== f; });
    });
  }

  /* ---------- lotes (datos de la lista vigente) ---------- */
  const ST = {
    disponible: { fill: '#CFE1E8', dot: '#2F6880', label: 'Disponible' },
    reservado: { fill: '#FCE7B0', dot: '#F5A800', label: 'Reservado' },
    vendido: { fill: '#DDE1E3', dot: '#9AA7AD', label: 'Vendido' },
    nodisponible: { fill: '#EEF1F2', dot: '#C9D2D6', label: 'No disponible' },
  };
  const m2 = v => v ? `${v.toLocaleString('es-PY', { maximumFractionDigits: 2 })} m²` : '—';
  $$('[data-lots]').forEach(root => {
    const cfg = JSON.parse(root.dataset.lots);
    const lots = cfg.lots.map((l, i) => Object.assign({ id: i }, l));
    const list = $('[data-lot-list]', root);
    const detail = $('[data-lot-detail]', root);
    const filters = $('[data-lot-filters]', root);
    const mzSelect = $('[data-lot-mz]', root);
    const counts = { todos: lots.length };
    lots.forEach(l => { counts[l.status] = (counts[l.status] || 0) + 1; });
    const state = { filter: counts.disponible ? 'disponible' : 'todos', mz: '', sel: null };
    const keys = ['todos'].concat(Object.keys(ST).filter(k => counts[k]));
    filters.innerHTML = keys.length > 2 ? keys.map(k =>
      `<button type="button" class="pill" data-f="${k}"><span class="swatch" style="background:${k === 'todos' ? '#fff' : ST[k].fill}"></span>${k === 'todos' ? 'Todos' : ST[k].label} · ${counts[k]}</button>`
    ).join('') : `<span class="muted small">${counts.disponible || 0} lotes disponibles</span>`;
    [...new Set(lots.map(l => l.mz))].forEach(mz => mzSelect.add(new Option(mz, mz)));

    const name = l => `Mz. ${l.mz} · Lote ${l.n}`;
    function render() {
      $$('[data-f]', filters).forEach(b => b.classList.toggle('is-on', b.dataset.f === state.filter));
      list.innerHTML = lots.filter(l => (state.filter === 'todos' || l.status === state.filter) && (!state.mz || l.mz === state.mz)).map(l =>
        `<button type="button" class="lots-row${l.id === state.sel ? ' is-on' : ''}" data-id="${l.id}"><span>${esc(name(l))}${l.type ? `<small>${esc(l.type)}</small>` : ''}</span><span>${m2(l.m2)}</span><span class="lot-status"><i style="background:${ST[l.status].dot}"></i>${ST[l.status].label}</span></button>`
      ).join('') || '<div class="lots-empty">No hay lotes con este filtro.</div>';

      const sel = lots.find(l => l.id === state.sel);
      detail.hidden = !sel;
      if (sel) {
        detail.innerHTML =
          `<div class="lot-detail-head"><span>${esc(name(sel))}</span><span class="status-tag" style="background:${ST[sel.status].fill}">${ST[sel.status].label}</span></div>` +
          `<div>Superficie: <strong>${m2(sel.m2)}</strong>${sel.type ? ` · ${esc(sel.type)}` : ''}</div>` +
          (sel.status === 'disponible' ? `<a class="btn btn-primary btn-sm" href="#consulta" data-scroll data-msg="${esc(`Hola, me interesa el lote ${sel.n} de la manzana ${sel.mz} (${m2(sel.m2)}) de ${cfg.name}.`)}">Consultar por este lote</a>` : '');
      }
    }

    filters.addEventListener('click', e => {
      const b = e.target.closest('[data-f]');
      if (b) { state.filter = b.dataset.f; render(); }
    });
    mzSelect.addEventListener('change', () => { state.mz = mzSelect.value; render(); });
    list.addEventListener('click', e => {
      const b = e.target.closest('[data-id]');
      if (b) { state.sel = Number(b.dataset.id); render(); }
    });
    render();
  });

  /* ---------- leaflet maps ---------- */
  function initMaps() {
    const L = window.L;
    const data = window.VRE || {};
    if (!L) return;
    const pin = (label, num) => L.divIcon({
      className: '',
      iconSize: null,
      html: `<div class="vpin"><span class="vpin-num">${esc(num)}</span>${label ? `<span class="vpin-label">${esc(label)}</span>` : ''}</div>`,
    });
    $$('[data-vmap]').forEach(el => {
      const map = L.map(el, { scrollWheelZoom: false });
      L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { attribution: '© OpenStreetMap', maxZoom: 19 }).addTo(map);
      const kind = el.dataset.vmap;
      if (kind === 'all' && data.projects) {
        data.projects.forEach(p => {
          L.marker(p.ll, { icon: pin('', p.num), title: p.name }).addTo(map).on('click', () => { window.location.href = p.url; });
        });
        map.fitBounds(data.projects.map(p => p.ll), { padding: [80, 80] });
      } else if (kind === 'project' && data.project) {
        L.marker(data.project.ll, { icon: pin(data.project.name, data.project.num) }).addTo(map);
        map.setView(data.project.ll, 13);
      } else if (kind === 'office') {
        L.marker(data.office, { icon: pin('Valores Real Estate', '•') }).addTo(map);
        map.setView(data.office, 15);
      }
    });
  }
  if (document.readyState === 'complete') initMaps();
  else window.addEventListener('load', initMaps);
})();
