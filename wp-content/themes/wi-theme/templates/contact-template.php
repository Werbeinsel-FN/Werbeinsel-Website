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
  const CSS_URL   = <?php echo wp_json_encode($plugin_css_url . '?v=4'); ?>;
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
    // 1) nadji root gde idu kolone (preporučeno #wp-pills-root; fallback prvi 3-col grid posle #wp-form-slot)
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

  function hookCTA(doc){
    // koristi postojeći CTA "ANFRAGE SENDEN" da pošalje WP formu
    let cta = doc.querySelector('button[type="submit"]');
    if (!cta) {
      const buttons = doc.querySelectorAll('button');
      cta = Array.from(buttons).find(b => /anfrage\s*senden/i.test(b.textContent || ''));
    }
    if (cta) {
      cta.type = 'button';
      cta.addEventListener('click', function(){
        syncHidden(doc);
        const frm = doc.querySelector('.wi-contact-form');
        if (frm && frm.requestSubmit) frm.requestSubmit(); else if (frm) frm.submit();
      });
    }
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

    // 3) iscrtaj kolone dugmadi i poveži CTA
    renderPills(doc);
    hookCTA(doc);
    syncHidden(doc);

    // 4) resize posle injekcije
    fitFromDOM();

    // 5) posmatraj promene visine u iFrame-u (kad korisnik klika)
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

  // dodatni fallbacki
  window.addEventListener('resize', () => requestAnimationFrame(fitFromDOM));
  setTimeout(fitFromDOM, 350);
})();
</script>

<?php get_footer(); ?>
