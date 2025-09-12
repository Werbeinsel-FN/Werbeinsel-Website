<?php
/**
 * Template Name: Kontakt (Next + WP form)
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
  /* Full-bleed wrapper */
  #contact-main {
    --bleed: calc(50vw - 50%);
    width: calc(100% + 2 * var(--bleed));
    margin-left: calc(-1 * var(--bleed));
    margin-right: calc(-1 * var(--bleed));
    padding:0 !important;
    overflow:visible !important;
  }
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
  const CSS_URL   = <?php echo wp_json_encode($plugin_css_url . '?v=6'); ?>;
  const PILLS     = <?php echo wp_json_encode($pills); ?>;

  function injectAssets(doc){
    if (!doc.getElementById('wi-contact-front-css')) {
      const link = doc.createElement('link');
      link.id = 'wi-contact-front-css';
      link.rel = 'stylesheet';
      link.href = CSS_URL;
      doc.head.appendChild(link);
    }
  }

  function isThreeColGrid(el) {
    if (!el) return false;
    const cs = getComputedStyle(el);
    const cols = (cs.gridTemplateColumns || '').split(' ').filter(Boolean).length;
    return cols >= 3 || el.className.includes('md:grid-cols-3');
  }

  function renderPills(doc){
    let root = doc.getElementById('wp-pills-root');
    if (!root) {
      const slot = doc.getElementById('wp-form-slot');
      let x = slot ? slot.nextElementSibling : null;
      while (x && !isThreeColGrid(x)) x = x.nextElementSibling;
      root = x || null;
    }
    if (!root) return;

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
    const frm = doc.querySelector('.wi-contact-form');
    if (!frm) return;
    Object.keys(PILLS).forEach(key => {
      const sel = doc.querySelectorAll('[data-group="'+key+'"].wi-pill-active');
      const values = Array.from(sel).map(b => b.dataset.value);
      const hid = frm.querySelector('input[name="pill_'+key+'"]');
      if (hid) hid.value = values.join(', ');
    });
  }

  function neutralizeOuterForms(doc){
    const forms = doc.querySelectorAll('form:not(.wi-contact-form)');
    forms.forEach(f => {
      f.setAttribute('novalidate','novalidate');
      f.addEventListener('submit', (e) => { e.preventDefault(); e.stopPropagation(); return false; }, true);
    });
  }

  function ensureRefererField(doc){
    const frm = doc.querySelector('.wi-contact-form');
    if (!frm) return;
    let ref = frm.querySelector('input[name="_wp_http_referer"]');
    if (!ref) {
      ref = doc.createElement('input');
      ref.type = 'hidden';
      ref.name = '_wp_http_referer';
      frm.appendChild(ref);
    }
    try { ref.value = doc.location.href; } catch(e) { ref.value = '<?php echo esc_js($next_url); ?>'; }
  }

  /* === Fullscreen "Danke!" sa animiranim thumbs-up, centrirano + auto-hide === */
  function showResultNotice(doc){
    const qs = new URLSearchParams((doc.location && doc.location.search) || '');
    const ok  = qs.get('wi_ok');
    const err = qs.get('wi_error');

    if (ok) {
      // CSS (jednom)
      if (!doc.getElementById('wi-thanks-style')) {
        const st = doc.createElement('style');
        st.id = 'wi-thanks-style';
        st.textContent = `
          @keyframes wi-bob { 0%{transform:translateY(0)} 50%{transform:translateY(-14px)} 100%{transform:translateY(0)} }
          @keyframes wi-fade { to{ opacity:0; visibility:hidden } }
          #wi-thanks-overlay{
            position:fixed; inset:0; background:#ffed00;
            display:flex; flex-direction:column; align-items:center; justify-content:center;
            text-align:center; z-index:2147483647; padding:24px;
          }
          #wi-thanks-overlay.hidden{ animation:wi-fade .4s ease forwards; }
          #wi-thanks-overlay .wi-hand{
            width:clamp(96px,12vw,160px); height:auto; color:#000;
            animation:wi-bob 1.05s ease-in-out 6;
          }
          #wi-thanks-overlay h1{
            margin:20px 0 0; font-weight:900; letter-spacing:1px;
            font-size:clamp(40px,6vw,96px);
          }
          #wi-thanks-overlay p{
            max-width:960px; margin:14px auto 0;
            font-size:clamp(16px,1.8vw,22px); line-height:1.45;
          }
        `;
        doc.head.appendChild(st);
      }

      const old = doc.getElementById('wi-thanks-overlay');
      if (old) old.remove();

      // Thumbs-up SVG (ikonica kao na tvojoj dobroj slici)
      const ov = doc.createElement('div');
      ov.id = 'wi-thanks-overlay';
      ov.setAttribute('role','status');
      ov.setAttribute('aria-live','polite');
      ov.innerHTML = `
        <svg viewBox="0 0 24 24" class="wi-hand" fill="#000" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
          <path d="M2 10h4v12H2zM22 10a2 2 0 0 0-2-2h-6.31l.95-4.57.03-.32A1.5 1.5 0 0 0 13.2 2H12l-4 9v11h9a2 2 0 0 0 2-2l1-7z"/>
        </svg>
        <h1>DANKE!</h1>
        <p>Ihre Anfrage wurde erfolgreich gesendet. Wir melden uns in Kürze bei Ihnen.</p>
      `;
      doc.body.appendChild(ov);

      // skini wi_ok iz URL-a da se overlay ne vraća na refresh
      try {
        const url = new URL(doc.location.href);
        url.searchParams.delete('wi_ok');
        doc.defaultView.history.replaceState({}, '', url.toString());
      } catch(e){}

      // ispravno dovedi parent viewport do iFrame-a (vidi se overlay)
      try { iframe.scrollIntoView({behavior:'smooth', block:'center'}); } catch(e){}

      // prilagodi visinu iFrame-a
      try { parent.postMessage({type:'wi-iframe-height', height: doc.documentElement.clientHeight }, '*'); } catch(e){}

      // auto-hide posle 5s
      setTimeout(() => {
        ov.classList.add('hidden');
        setTimeout(() => {
          ov.remove();
          try {
            parent.postMessage({type:'wi-iframe-height', height: Math.max(doc.body.scrollHeight, doc.documentElement.scrollHeight) }, '*');
          } catch(e){}
        }, 400);
      }, 5000);

      return;
    }

    // === Greška: mali notice ispod forme + scroll u kadar ===
    if (err) {
      const frm = doc.querySelector('.wi-contact-form');
      if (!frm) return;
      const prev = doc.getElementById('wi-contact-notice');
      if (prev) prev.remove();
      const div = doc.createElement('div');
      div.id = 'wi-contact-notice';
      div.className = 'wi-alert err';
      div.style.marginTop = '16px';
      div.style.padding   = '12px 16px';
      div.style.border    = '2px solid #c53030';
      div.style.background= '#fff';
      let msg = err;
      try { msg = decodeURIComponent(msg); } catch(e){}
      try { msg = decodeURIComponent(msg); } catch(e){}
      div.textContent = msg || 'Es gab einen Fehler. Bitte versuchen Sie es erneut.';
      frm.appendChild(div);

      // scroll parenta do iFrame-a i forme u iFrame-u u kadar
      try { iframe.scrollIntoView({behavior:'smooth', block:'center'}); } catch(e){}
      try { frm.scrollIntoView({behavior:'smooth', block:'center'}); } catch(e){}
      try { parent.postMessage({type:'wi-iframe-height', height: Math.max(doc.body.scrollHeight, doc.documentElement.scrollHeight) }, '*'); } catch(e){}
    }
  }

  function hookCTA(doc){
    let cta = doc.querySelector('button[type="submit"]');
    if (!cta) {
      const btns = Array.from(doc.querySelectorAll('button'));
      cta = btns.find(b => /anfrage\s*senden/i.test((b.textContent||'').trim()));
    }
    if (!cta) return;

    cta.type = 'button';
    cta.addEventListener('click', function(ev){
      ev.preventDefault();
      ev.stopImmediatePropagation();
      ev.stopPropagation();

      ensureRefererField(doc);
      syncHidden(doc);

      const frm = doc.querySelector('.wi-contact-form');
      if (!frm) return;
      frm.setAttribute('target','_self');
      frm.setAttribute('method','post');

      if (typeof frm.requestSubmit === 'function') frm.requestSubmit();
      else frm.submit();
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

    // 3) iscrtaj kolone dugmadi
    renderPills(doc);

    // 4) neutralizuj sve spoljne forme i veži CTA da šalje WP formu
    neutralizeOuterForms(doc);
    hookCTA(doc);

    // 5) referer field + prikaži rezultat (ako postoji u URL-u iFrame-a)
    ensureRefererField(doc);
    showResultNotice(doc);

    // 6) upiši vrednosti i resize
    syncHidden(doc);
    fitFromDOM();

    // 7) posmatraj promene visine u iFrame-u
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
