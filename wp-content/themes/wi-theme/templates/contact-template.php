<?php
/**
 * Template Name: Kontakt (Next + WP form + Danke overlay)
 */
get_header();

/** URL ka statičkom Next prikazu u uploads */
$next_url = content_url('uploads/next-contact/index.html?v=9');

/** HTML WP forme iz plugina (shortcode) */
$form_html = do_shortcode('[wi_contact_form]');

/** CSS plugina koji stilizuje formu (ubacujemo ga u <head> iFrame-a) */
$plugin_css_url = plugins_url('assets/front.css', WP_PLUGIN_DIR . '/wi-contact/wi-contact.php');

/** Konfiguracija kolona dugmadi (services/budget/zeitrahmen) iz plugina */
$pills = get_option('wi_contact_pills');
if (!is_array($pills) || empty($pills)) {
  if (class_exists('WI_Contact') && method_exists('WI_Contact','defaults_pills')) {
    $pills = WI_Contact::defaults_pills();
  } else {
    $pills = [
      'services'  => ['title'=>'SERVICES','multiple'=>true,  'items'=>['Außenwerbung','Beschriftung','Grafikdesign','Webdesign']],
      'budget'    => ['title'=>'BUDGET',  'multiple'=>false, 'items'=>['< 5.000€','5.000€ - 15.000€','15.000€ - 50.000€','> 50.000€']],
      'zeitrahmen'=> ['title'=>'ZEITRAHMEN','multiple'=>false,'items'=>['Sofort','Innerhalb 1 Monat','1–3 Monate','> 3 Monate']],
    ];
  }
}
?>
<style>
  /* Full-bleed za main wrapper (kao ranije) */
  #contact-main { --bleed: calc(50vw - 50%); width: calc(100% + 2 * var(--bleed));
    margin-left: calc(-1 * var(--bleed)); margin-right: calc(-1 * var(--bleed));
    padding:0 !important; overflow:visible !important; }
  .contact-main-container { margin:0 !important; padding:0 !important; }
  #wi-next-contact { border-radius:0 !important; display:block; width:100%; border:0; }
  .site, .site-main, .content-area, .entry-content { overflow:visible !important; }
</style>

<main id="contact-main" class="contact-main">
  <div class="contact-main-container full-bleed">
    <iframe
      id="wi-next-contact"
      src="<?php echo esc_url($next_url); ?>"
      style="width:100%; border:0; display:block; overflow:hidden; height:1px; min-height:400px;"
      scrolling="no"
      loading="lazy"
      referrerpolicy="no-referrer"
      allow="clipboard-write"
    ></iframe>
  </div>
</main>

<script>
(function () {
  const iframe = document.getElementById('wi-next-contact');

  /* --------- Autosize visine (postMessage + fallback) --------- */
  let lastH = 0, raf = 0;
  function setH(h) {
    const clamped = Math.max(400, Math.ceil(h));
    if (Math.abs(clamped - lastH) > 3) {
      lastH = clamped;
      iframe.style.height = clamped + 'px';
    }
  }
  function fitFromDOM() {
    try {
      const doc = iframe.contentWindow && iframe.contentWindow.document;
      if (!doc) return;
      const h = Math.max(
        doc.body ? doc.body.scrollHeight : 0,
        doc.documentElement ? doc.documentElement.scrollHeight : 0
      );
      if (h) setH(h);
    } catch (e) { /* cross-origin? ignoriši */ }
  }
  window.addEventListener('message', function (e) {
    const d = e.data;
    if (d && d.type === 'wi-iframe-height' && typeof d.height === 'number') {
      cancelAnimationFrame(raf);
      raf = requestAnimationFrame(() => setH(d.height));
    }
  }, false);

  /* --------- WP forma + pills iz plugina --------- */
  const FORM_HTML = <?php echo wp_json_encode($form_html); ?>;
  const CSS_URL   = <?php echo wp_json_encode($plugin_css_url . '?v=7'); ?>;
  const PILLS     = <?php echo wp_json_encode($pills); ?>;

  function injectAssets(doc){
    if (!doc.getElementById('wi-contact-front-css')) {
      const link = doc.createElement('link');
      link.id = 'wi-contact-front-css';
      link.rel = 'stylesheet';
      link.href = CSS_URL;
      doc.head.appendChild(link);
    }

    // Danke overlay CSS (jednom)
    if (!doc.getElementById('wi-danke-css')) {
      const s = doc.createElement('style');
      s.id = 'wi-danke-css';
      s.textContent = `
      @keyframes wi-bob {
        0%{ transform: translateY(0) }
        50%{ transform: translateY(-10px) }
        100%{ transform: translateY(0) }
      }
      .wi-danke-overlay{
        position:fixed; inset:0; background:#ffed00; z-index:999999;
        display:none; align-items:center; justify-content:center; flex-direction:column;
        text-align:center; padding:24px;
      }
      .wi-danke-hand{ width:min(22vw,180px); height:auto; animation: wi-bob 2s ease-in-out infinite; color:#000; }
      .wi-danke-title{ font-family: var(--font-unbounded, inherit); font-weight:800;
        font-size:clamp(38px,7vw,130px); line-height:1; margin:20px 0 12px; color:#000; }
      .wi-danke-sub{ font-family: var(--font-poppins, inherit); font-size:clamp(14px,2.3vw,24px); color:#111; max-width:1100px; }
      .wi-danke-overlay *{ box-sizing:border-box }
      `;
      doc.head.appendChild(s);
    }
  }

  function isThreeColGrid(el) {
    if (!el) return false;
    const cs = getComputedStyle(el);
    const cols = (cs.gridTemplateColumns || '').split(' ').filter(Boolean).length;
    return cols >= 3 || el.className.includes('md:grid-cols-3');
  }

  function renderPills(doc){
    // 1) nađi root gde idu kolone (#wp-pills-root; fallback prvi 3-col grid posle #wp-form-slot)
    let root = doc.getElementById('wp-pills-root');
    if (!root) {
      const slot = doc.getElementById('wp-form-slot');
      let x = slot ? slot.nextElementSibling : null;
      while (x && !isThreeColGrid(x)) x = x.nextElementSibling;
      root = x || null;
    }
    if (!root) return;

    // 2) iscrtaj tri kolone iz PILLS
    root.innerHTML = '';
    Object.keys(PILLS).forEach(key => {
      const group = PILLS[key];
      const col = doc.createElement('div');

      const h = doc.createElement('label');
      h.className = 'block text-3xl unbounded-bold text-black mb-6 text-center';
      h.textContent = group.title || key.toUpperCase();
      col.appendChild(h);

      const box = doc.createElement('div');
      box.className = 'space-y-2';
      (group.items || []).forEach(item => {
        const btn = doc.createElement('button');
        btn.type = 'button';
        btn.className = 'wi-pill w-full p-4 rounded-[40px] text-center transition-all poppins font-bold text-lg bg-black text-white border-2 border-black hover:bg-neutral-800';
        btn.textContent = item;
        btn.dataset.group = key;
        btn.dataset.value = item;
        btn.addEventListener('click', () => {
          if (group.multiple) {
            btn.classList.toggle('wi-pill-active');
          } else {
            box.querySelectorAll('button').forEach(b => b.classList.remove('wi-pill-active'));
            btn.classList.add('wi-pill-active');
          }
          syncHidden(doc);
          fitFromDOM();
        });
        box.appendChild(btn);
      });
      col.appendChild(box);
      root.appendChild(col);
    });
  }

  function syncHidden(doc){
    // upisuje izabrane vrednosti u hidden inpute unutar .wi-contact-form
    const frm = doc.querySelector('.wi-contact-form');
    if (!frm) return;
    Object.keys(PILLS).forEach(key => {
      const sel = doc.querySelectorAll('[data-group="'+key+'"].wi-pill-active');
      const values = Array.from(sel).map(b => b.dataset.value);
      const hid = frm.querySelector('input[name="pill_'+key+'"]');
      if (hid) hid.value = values.join(', ');
    });
  }

  // ——— Danke overlay (create/show/hide) ———
  function ensureDanke(doc){
    let o = doc.getElementById('wi-danke');
    if (o) return o;

    o = doc.createElement('div');
    o.id = 'wi-danke';
    o.className = 'wi-danke-overlay';
    o.innerHTML = `
      <svg viewBox="0 0 64 64" class="wi-danke-hand" fill="currentColor" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
        <path d="M36 6c-1.7 0-3 1.3-3 3v10l-6.1-.1c-1.6 0-3 1.4-2.9 3l.6 8.7c0 1.6 1.4 3 3 3H34v8c0 1.7 1.3 3 3 3h6c3.3 0 5-2.7 5-6v-16c0-1.7-1.3-3-3-3h-9V9c0-1.7-1.3-3-3-3z"></path>
      </svg>
      <div class="wi-danke-title">DANKE!</div>
      <div class="wi-danke-sub">
        Ihre Anfrage wurde erfolgreich gesendet. Wir melden uns in Kürze bei Ihnen.
      </div>
    `;
    doc.body.appendChild(o);
    return o;
  }
  function showDanke(doc){
    const o = ensureDanke(doc);
    o.style.display = 'flex';
    // zaključa skrol dok je overlay otvoren
    const prev = doc.body.style.overflow;
    doc.body.dataset.wiPrevOverflow = prev || '';
    doc.body.style.overflow = 'hidden';
    setTimeout(() => hideDanke(doc), 5000);
  }
  function hideDanke(doc){
    const o = doc.getElementById('wi-danke');
    if (o) o.style.display = 'none';
    if (doc.body.dataset.wiPrevOverflow !== undefined) {
      doc.body.style.overflow = doc.body.dataset.wiPrevOverflow;
      delete doc.body.dataset.wiPrevOverflow;
    }
    fitFromDOM();
  }

  function neutralizeOuterForms(doc){
    // Ako Next HTML slučajno ima sopstveni <form>, ugasi ga
    const forms = doc.querySelectorAll('form:not(.wi-contact-form)');
    forms.forEach(f => {
      f.setAttribute('novalidate','novalidate');
      f.addEventListener('submit', (e) => { e.preventDefault(); e.stopPropagation(); return false; }, true);
    });
  }

  // ❶ Submit handler forme (centralno mesto za slanje i Danke)
  function attachFormHandler(doc){
    const frm = doc.querySelector('.wi-contact-form');
    if (!frm || frm.dataset.wiHandled) return;
    frm.dataset.wiHandled = '1';

    frm.addEventListener('submit', async function(e){
      e.preventDefault(); e.stopPropagation();

      syncHidden(doc);

      // HTML5 validacija
      if (typeof frm.reportValidity === 'function' && !frm.reportValidity()) {
        const bad = frm.querySelector(':invalid');
        if (bad && bad.scrollIntoView) bad.scrollIntoView({behavior:'smooth', block:'center'});
        return false;
      }

      try {
        const fd = new FormData(frm);
        const res = await fetch(frm.action, {
          method: 'POST',
          body: fd,
          credentials: 'same-origin',
        });

        // uspeh: 2xx/3xx
        if (res.ok || (res.status >= 300 && res.status < 400)) {
          frm.reset();
          doc.querySelectorAll('.wi-pill.wi-pill-active').forEach(b => b.classList.remove('wi-pill-active'));
          syncHidden(doc);
          showDanke(doc);
        } else {
          alert('Greška pri slanju. Pokušajte ponovo.');
        }
      } catch(err){
        console.error(err);
        alert('Greška pri slanju. Proverite internet vezu.');
      }
      return false;
    }, true);
  }

  // ❷ CTA dugme iz Next-a – samo poziva requestSubmit na našoj formi
  function hookCTA(doc){
    let cta = doc.querySelector('button[type="submit"]');
    if (!cta) {
      const btns = Array.from(doc.querySelectorAll('button'));
      cta = btns.find(b => /anfrage\s*senden/i.test((b.textContent||'').trim()));
    }
    if (!cta) return;

    cta.type = 'button'; // sprečava default submit
    cta.addEventListener('click', function(ev){
      ev.preventDefault(); ev.stopImmediatePropagation(); ev.stopPropagation();
      const frm = doc.querySelector('.wi-contact-form');
      if (frm && typeof frm.requestSubmit === 'function') frm.requestSubmit();
      else if (frm) frm.dispatchEvent(new Event('submit', {cancelable:true, bubbles:true}));
    }, true);
  }

  function injectForm(){
    const doc = iframe.contentDocument || (iframe.contentWindow && iframe.contentWindow.document);
    if (!doc) return;

    injectAssets(doc);

    // 1) ubaci WP formu u slot (ili na kraj body-ja)
    let slot = doc.getElementById('wp-form-slot');
    if (!slot) slot = doc.body;
    slot.innerHTML = FORM_HTML;

    // 2) sakrij plugin dugme – koristimo Next CTA
    const submitBtn = doc.querySelector('.wi-contact-form .wi-submit');
    if (submitBtn) submitBtn.style.display = 'none';

    // 3) iscrtaj kolone dugmadi i veži submit/CTA
    renderPills(doc);
    neutralizeOuterForms(doc);
    attachFormHandler(doc); // << ključna linija
    hookCTA(doc);
    syncHidden(doc);

    // 4) resize posle injekcije
    fitFromDOM();

    // 5) posmatraj promene visine u iFrame-u (klikovi itd.)
    try {
      if ('ResizeObserver' in window) {
        const ro = new ResizeObserver(() => fitFromDOM());
        ro.observe(doc.documentElement);
      }
      const mo = new MutationObserver(() => fitFromDOM());
      mo.observe(doc.body, {subtree:true, childList:true});
    } catch (e) {}
  }

  iframe.addEventListener('load', injectForm);
  if (iframe.complete) setTimeout(injectForm, 120);

  window.addEventListener('resize', () => requestAnimationFrame(fitFromDOM));
  setTimeout(fitFromDOM, 350);
})();
</script>

<?php get_footer(); ?>
