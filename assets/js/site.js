(function () {
  'use strict';
  const $ = (sel, root = document) => root.querySelector(sel);
  const $$ = (sel, root = document) => Array.from(root.querySelectorAll(sel));
  const esc = s => String(s).replace(/[&<>"']/g, c => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));
  const fmt = n => n.toLocaleString('es-PY');

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

  /* ---------- lots (plano ilustrativo) ---------- */
  const ST = {
    disponible: { fill: '#CFE1E8', dot: '#2F6880', label: 'Disponible' },
    reservado: { fill: '#FCE7B0', dot: '#F5A800', label: 'Reservado' },
    vendido: { fill: '#DDE1E3', dot: '#9AA7AD', label: 'Vendido' },
  };
  function genLots(cfg) {
    let seed = 0;
    for (const ch of cfg.id) seed = (seed * 31 + ch.charCodeAt(0)) % 2147483646;
    seed += 1;
    const rnd = () => (seed = seed * 16807 % 2147483647) / 2147483647;
    const out = [];
    const bw = 290, bh = 110, gx = 30, gy = 34, x0 = 40, y0 = 30, per = cfg.perRow, lw = bw / per;
    let n = 1, blk = 0;
    for (let r = 0; r < 3; r++) for (let c = 0; c < 3; c++) {
      if (r === 1 && c === 1) continue;
      blk++;
      const bx = x0 + c * (bw + gx), by = y0 + r * (bh + gy);
      for (let row = 0; row < 2; row++) for (let i = 0; i < per; i++) {
        const s = rnd();
        const m2 = Math.round((cfg.range[0] + rnd() * (cfg.range[1] - cfg.range[0])) / 10) * 10;
        out.push({ n, block: blk, x: bx + i * lw, y: by + row * bh / 2, w: lw, h: bh / 2, m2, status: s < 0.5 ? 'disponible' : s < 0.68 ? 'reservado' : 'vendido' });
        n++;
      }
    }
    return out;
  }
  $$('[data-lots]').forEach(root => {
    const cfg = JSON.parse(root.dataset.lots);
    const lots = genLots(cfg);
    const svg = $('[data-lot-svg]', root);
    const list = $('[data-lot-list]', root);
    const detail = $('[data-lot-detail]', root);
    const filters = $('[data-lot-filters]', root);
    const fontSize = cfg.perRow > 12 ? 8 : 9;
    const state = { filter: 'todos', sel: null, zoom: 1 };

    const counts = { todos: lots.length };
    lots.forEach(l => { counts[l.status] = (counts[l.status] || 0) + 1; });
    filters.innerHTML = ['todos', 'disponible', 'reservado', 'vendido'].map(k =>
      `<button type="button" class="pill" data-f="${k}"><span class="swatch" style="background:${k === 'todos' ? '#fff' : ST[k].fill}"></span>${k === 'todos' ? 'Todos' : ST[k].label} · ${counts[k] || 0}</button>`
    ).join('');

    function render() {
      const sel = lots.find(l => l.n === state.sel);
      $$('[data-f]', filters).forEach(b => b.classList.toggle('is-on', b.dataset.f === state.filter));

      svg.innerHTML =
        '<rect width="1010" height="560" fill="#FAFBFB"/>' +
        '<path d="M0 478 C 140 456, 260 500, 420 482 S 720 452, 860 476 S 980 470, 1010 466 L1010 560 L0 560 Z" fill="#DCEAF0"/>' +
        `<text x="505" y="528" text-anchor="middle" font-size="15" fill="#2F6880" font-family="Montserrat" letter-spacing="3">${esc(cfg.water)}</text>` +
        '<rect x="360" y="174" width="290" height="110" rx="4" fill="#E4EFE6"/>' +
        `<text x="505" y="234" text-anchor="middle" font-size="14" fill="#4E7A5A" font-family="Montserrat" letter-spacing="2">${esc(cfg.center)}</text>` +
        lots.map(l => {
          const on = l.n === state.sel;
          const dim = state.filter !== 'todos' && l.status !== state.filter;
          return `<g data-n="${l.n}" opacity="${dim ? 0.25 : 1}"><rect x="${l.x}" y="${l.y}" width="${l.w}" height="${l.h}" fill="${on ? '#2F6880' : ST[l.status].fill}" stroke="${on ? '#16262E' : '#fff'}" stroke-width="${on ? 2 : 1.5}"><title>Lote ${l.n} · ${fmt(l.m2)} m² · ${ST[l.status].label}</title></rect>` +
            `<text x="${l.x + l.w / 2}" y="${l.y + l.h / 2 + 3}" text-anchor="middle" font-size="${fontSize}" fill="${on ? '#fff' : '#3C4C54'}" font-family="Montserrat" pointer-events="none">${l.n}</text></g>`;
        }).join('');
      svg.style.width = svg.style.minWidth = Math.round(state.zoom * 100) + '%';

      list.innerHTML = lots.filter(l => state.filter === 'todos' || l.status === state.filter).map(l =>
        `<button type="button" class="lots-row${l.n === state.sel ? ' is-on' : ''}" data-n="${l.n}"><span>N.º ${l.n}</span><span>${fmt(l.m2)} m²</span><span class="lot-status"><i style="background:${ST[l.status].dot}"></i>${ST[l.status].label}</span></button>`
      ).join('');

      detail.hidden = !sel;
      if (sel) {
        detail.innerHTML =
          `<div class="lot-detail-head"><span>Lote ${sel.n}</span><span class="status-tag" style="background:${ST[sel.status].fill}">${ST[sel.status].label}</span></div>` +
          `<div>Superficie: <strong>${fmt(sel.m2)} m²</strong> · Manzana ${sel.block}</div>` +
          `<a class="btn btn-primary btn-sm" href="#consulta" data-scroll data-msg="${esc(`Hola, me interesa el lote ${sel.n} (${fmt(sel.m2)} m²) de ${cfg.name}.`)}">Consultar por este lote</a>`;
      }
    }

    function pick(n) {
      state.sel = n;
      render();
      const row = $(`.lots-row[data-n="${n}"]`, list);
      if (row) list.scrollTop = row.offsetTop - list.offsetTop - list.clientHeight / 2 + row.offsetHeight / 2;
    }

    filters.addEventListener('click', e => {
      const b = e.target.closest('[data-f]');
      if (b) { state.filter = b.dataset.f; render(); }
    });
    svg.addEventListener('click', e => {
      const g = e.target.closest('[data-n]');
      if (g) pick(Number(g.dataset.n));
    });
    list.addEventListener('click', e => {
      const b = e.target.closest('[data-n]');
      if (b) { state.sel = Number(b.dataset.n); render(); }
    });
    $$('[data-mp]', root).forEach(b => b.addEventListener('click', () => {
      state.zoom = b.dataset.mp === 'in' ? Math.min(3, state.zoom + 0.5) : Math.max(1, state.zoom - 0.5);
      render();
    }));
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
