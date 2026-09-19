<?php
/**
 * Template Name: Kontakt (Next + WP form, AJAX submit)
 */
get_header();

$next_url       = content_url('uploads/next-contact/index.html?v=' . filemtime(__DIR__ . '/contact-template.php'));
$form_html      = do_shortcode('[wi_contact_form]');
$plugin_css_url = plugins_url('assets/front.css', WP_PLUGIN_DIR . '/wi-contact/wi-contact.php');
$theme_contact_css_url = get_template_directory_uri() . '/css/contact.css?v=' . filemtime(get_template_directory() . '/css/contact.css');

/* NOVO: uzmi reCAPTCHA v3 SITE KEY iz opcija plugina (SECRET NE IDE U JS) */
$site_key       = get_option('wi_contact_recaptcha_site', '');

$pills = null;

if (class_exists('WI_Contact') && method_exists('WI_Contact', 'get_pills_decoded')) {
  // ✅ uzmi dekodirane vrednosti iz plugina
  $pills = WI_Contact::get_pills_decoded();
} else {
  // fallback: uzmi raw iz option i dekodiraj ručno
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
  } else {
    // ručno dekodiraj HTML entitete (ako helper ne postoji)
    foreach ($pills as $k => $g) {
      if (isset($pills[$k]['title'])) {
        $pills[$k]['title'] = wp_specialchars_decode($pills[$k]['title'], ENT_QUOTES);
      }
      if (!empty($pills[$k]['items']) && is_array($pills[$k]['items'])) {
        $pills[$k]['items'] = array_map(function($s){
          return wp_specialchars_decode($s, ENT_QUOTES);
        }, $pills[$k]['items']);
      }
    }
  }
}

?>
<style>
  /* Full-bleed iFrame kao ranije */
  #contact-main{--bleed:calc(50vw - 50%);width:calc(100% + 2*var(--bleed));
    margin-left:calc(-1*var(--bleed));margin-right:calc(-1*var(--bleed)); 
    padding:0!important;overflow:visible!important; }
  .contact-main-container{margin:0!important;padding:0!important;}
  #wi-next-contact{border-radius:0!important;display:block;width:100%;border:0;}
  .site,.site-main,.content-area,.entry-content{overflow:visible!important;}
  #wi-contact-info-wrap{
  --bleed:calc(50vw - 50%);
  width:calc(100% + 2*var(--bleed));
  margin-left:calc(-1*var(--bleed));
  margin-right:calc(-1*var(--bleed));
  padding:0!important;
  
}
</style>

<main id="contact-main" class="contact-main">
  <div class="contact-main-container full-bleed">
    <iframe
      id="wi-next-contact"
      src="<?php echo esc_url($next_url); ?>"
      style="width:100%;border:0;display:block;overflow:hidden;height:1px;min-height:400px;"
      scrolling="no"
      loading="lazy"
      referrerpolicy="no-referrer"
      allow="clipboard-write"
    ></iframe>
  </div>
</main>
<div id="wi-contact-info-wrap">
  <section class="wi-contact-info">
    <div class="info-container">
      <h2 class="wi-contact-info-headline">Direkt Kontakt<br>aufnehmen</h2>
      <?php echo do_shortcode('[wi_contact_info]'); ?>
    </div>
  </section>
</div>

<style id="wi-contact-info-inline">
  /* Bereich (schwarzer Hintergrund); unteres Padding 32px */
  #wi-contact-info-wrap section,
  #wi-contact-info-wrap .wi-contact-info{
    background:#000 !important;
    color:#fff !important;
    border-top:none !important;
    padding-top:4.8rem !important;
    padding-bottom:6rem !important;
    margin-top:0 !important;
  }
  @media (min-width:768px){
    #wi-contact-info-wrap section,
    #wi-contact-info-wrap .wi-contact-info{ padding-top:7.2rem !important; padding-bottom:7rem !important; }
  }
  @media (min-width:1024px){
    #wi-contact-info-wrap section,
    #wi-contact-info-wrap .wi-contact-info{ padding-top:9.6rem !important; padding-bottom:8rem !important; }
  }

  /* Container širina + bočni padovi +20% */
  #wi-contact-info-wrap .info-container,
  #wi-contact-info-wrap .content-container{
    max-width:2136px !important;
    margin-inline:auto !important;
    padding-inline:6vw !important;
  }

  /* Grid: mobil 1×4 → tablet 2×2 → desktop 4×1 */
  #wi-contact-info-wrap .wi-ci__row,
  #wi-contact-info-wrap .info-grid,
  #wi-contact-info-wrap .cf-row,
  #wi-contact-info-wrap .grid{
    display:grid !important;
    grid-template-columns:1fr !important;
    gap:6rem !important;
    text-align:center !important;
    max-width: 1680px !important;
    margin-inline: auto !important;
  }
  /* Tablet: 2×2 */
  @media (min-width:600px){
    #wi-contact-info-wrap .wi-ci__row,
    #wi-contact-info-wrap .info-grid,
    #wi-contact-info-wrap .cf-row,
    #wi-contact-info-wrap .grid{
      grid-template-columns:repeat(2,1fr) !important;
      gap:6rem !important;
    }
  }
  /* Desktop: 4×1 – još veći razmak između TELEFON, E-MAIL, WHATSAPP, ADRESSE */
  @media (min-width:1200px){
    #wi-contact-info-wrap .wi-ci__row,
    #wi-contact-info-wrap .info-grid,
    #wi-contact-info-wrap .cf-row,
    #wi-contact-info-wrap .grid{
      grid-template-columns:repeat(4,1fr) !important;
      gap:5.5rem 4.5rem !important;
    }
  }

  /* Stavka – kolona centrirana, ikona gore */
  #wi-contact-info-wrap .wi-ci__item,
  #wi-contact-info-wrap .info-item,
  #wi-contact-info-wrap .cf-item{
    display:flex !important;
    flex-direction:column !important;
    align-items:center !important;
    gap:1.2rem !important;
  }
  #wi-contact-info-wrap .wi-ci__item .wi-ci__icon-wrap{ margin-bottom: 0.3rem !important; }

  /* Headline „Direkt Kontakt aufnehmen“ – bez velikih slova, kao u sajt */
  #wi-contact-info-wrap .wi-contact-info .wi-ci{ padding-top: 0 !important; }
  #wi-contact-info-wrap .wi-contact-info-headline{
    font-family: var(--font-unbounded,"Unbounded",system-ui,sans-serif) !important;
    font-size: clamp(43px,6vw,67px) !important;
    font-weight: 800 !important;
    line-height: 1.0 !important;
    letter-spacing: -0.01em !important;
    color: #ffed00 !important;
    text-align: center !important;
    margin: 0 0 3.6rem !important;
    text-transform: none !important;
  }
  @media (min-width:1024px){ #wi-contact-info-wrap .wi-contact-info-headline{ margin-bottom: 4.8rem !important; } }

  /* Ikona u žutom krugu +20% */
  #wi-contact-info-wrap .wi-ci__icon-wrap{
    width: 96px !important;
    height: 96px !important;
    border-radius: 9999px !important;
    background: #FFED00 !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    flex-shrink: 0 !important;
  }
  #wi-contact-info-wrap .wi-ci__icon-wrap .wi-ci__icon{
    width: 38px !important;
    height: 38px !important;
    color: #000 !important;
  }
  /* Deblje ikone (kao WhatsApp) – stroke 2.5px */
  #wi-contact-info-wrap .wi-ci__icon-wrap .wi-ci__icon *{ fill: none !important; stroke: #000 !important; stroke-width: 2.5 !important; stroke-linecap: round !important; stroke-linejoin: round !important; }
  /* WhatsApp – zeleni krug, bela ikona */
  #wi-contact-info-wrap .wi-ci__icon-wrap--whatsapp{
    background: #25D366 !important;
  }
  #wi-contact-info-wrap .wi-ci__icon-wrap--whatsapp .wi-ci__icon,
  #wi-contact-info-wrap .wi-ci__icon-wrap--whatsapp .wi-ci__icon *{ fill: #fff !important; stroke: #fff !important; color: #fff !important; }
  /* Button "Chat starten" +20% */
  #wi-contact-info-wrap .wi-ci__whatsapp-btn{
    display: inline-block !important;
    background: #25D366 !important;
    color: #fff !important;
    font-family: var(--font-poppins,"Poppins",system-ui,sans-serif) !important;
    font-size: clamp(19px,1.8vw,22px) !important;
    font-weight: 600 !important;
    padding: 0.9rem 1.8rem !important;
    border-radius: 9999px !important;
    text-decoration: none !important;
    margin-top: 0.3rem !important;
    transition: opacity 0.2s !important;
  }
  #wi-contact-info-wrap .wi-ci__whatsapp-btn:hover{ opacity: 0.9 !important; }

  /* Überschrift des Eintrags (TELEFON, E-MAIL, ADRESSE) +20% */
  #wi-contact-info-wrap .wi-ci__title,
  #wi-contact-info-wrap .info-title,
  #wi-contact-info-wrap .cf-title,
  #wi-contact-info-wrap .wi-ci__item h3{
    font-family: var(--font-unbounded,"Unbounded",system-ui,sans-serif) !important;
    font-weight: 800 !important;
    text-transform: uppercase !important;
    letter-spacing: 0.05em !important;
    font-size: clamp(19px,1.92vw,22px) !important;
    line-height: 1.05 !important;
    margin: 0 !important;
    color: #FFED00 !important;
  }

  /* Text darunter (weiß, Poppins) +20% */
  #wi-contact-info-wrap .wi-ci__text,
  #wi-contact-info-wrap .info-text,
  #wi-contact-info-wrap .cf-text,
  #wi-contact-info-wrap .wi-ci__item p,
  #wi-contact-info-wrap .wi-ci__item a.wi-ci__text{
    font-family: var(--font-poppins,"Poppins",system-ui,sans-serif) !important;
    font-size: clamp(22px,2.16vw,26px) !important;
    font-weight: 500 !important;
    line-height: 1.35 !important;
    margin: 0 !important;
    color: #fff !important;
  }

  /* Links */
  #wi-contact-info-wrap .info-link,
  #wi-contact-info-wrap .cf-link,
  #wi-contact-info-wrap a{
    color:inherit !important;
    text-decoration:none !important;
  }
  #wi-contact-info-wrap .info-link:hover,
  #wi-contact-info-wrap .cf-link:hover,
  #wi-contact-info-wrap a:hover{
    color:#ffed00 !important;
  }

  /* Gelben Abstand zum Footer und Paddings aus main entfernen */
  main.pb-10{ padding-bottom:0 !important; }
  #wi-contact-info-wrap{ margin-bottom:0 !important; }
  #wi-contact-info-wrap + *{ margin-top:0 !important; }
</style>

<script>
(function(){
  const iframe    = document.getElementById('wi-next-contact');
  const CSS_URL   = <?php echo wp_json_encode($plugin_css_url.'?v=8'); ?>;
  const THEME_CSS_URL = <?php echo wp_json_encode($theme_contact_css_url); ?>;
  const FORM_HTML = <?php echo wp_json_encode($form_html); ?>;
  const PILLS     = <?php echo wp_json_encode($pills); ?>;
  const POST_URL  = <?php echo wp_json_encode(admin_url('admin-post.php')); ?>;
  /* NOVO: prosleđen site key iz PHP-a */
  const SITE_KEY  = <?php echo wp_json_encode($site_key); ?>;

  /* -------- autosize -------- */
  let lastH=0, raf=0;
  function setH(h){const c=Math.max(400,Math.ceil(h));if(Math.abs(c-lastH)>3){lastH=c;iframe.style.height=c+'px';}}
  function fitFromDOM(){
    try{const d=iframe.contentWindow&&iframe.contentWindow.document;if(!d)return;
      const h=Math.max(d.body?.scrollHeight||0,d.documentElement?.scrollHeight||0);
      if(h)setH(h);}catch(e){}
  }
  window.addEventListener('message',e=>{
    const d=e.data;
    if(d && d.type==='wi-iframe-height' && typeof d.height==='number'){
      cancelAnimationFrame(raf); raf=requestAnimationFrame(()=>setH(d.height));
    }
  });

  /* -------- helpers -------- */
  function bringIntoView(){
    try{ iframe.scrollIntoView({block:'center',behavior:'smooth'}); }
    catch(_){
      const r=iframe.getBoundingClientRect();
      window.scrollTo({top:r.top + window.pageYOffset - 80, behavior:'smooth'});
    }
  }
function injectAssets(doc){
  // plugin CSS (kao pre)
  if(!doc.getElementById('wi-contact-front-css')){
    const l=doc.createElement('link');
    l.id='wi-contact-front-css';
    l.rel='stylesheet';
    l.href=CSS_URL;
    doc.head.appendChild(l);
  }
  // Svi elementi forme +20% – ubacuje theme u iframe (ne zavisi od cache index.html)
  if(!doc.getElementById('wi-contact-scale-120')){
    const scale=doc.createElement('style');
    scale.id='wi-contact-scale-120';
    scale.textContent=`
      .contact-hero h1{ font-size: clamp(58px, 9.6vw, 106px) !important; margin-bottom: 1.8rem !important; }
      .contact-hero .subline{ font-size: clamp(22px, 2.4vw, 26px) !important; max-width: 840px !important; }
      #wi-contact-root .wi-pills-title{ font-size: clamp(35px, 3.6vw, 46px) !important; margin-bottom: 2.9rem !important; }
      #wp-pills-root .wi-pills-section{ margin-bottom: 4.8rem !important; }
      .contact-form-inner{ max-width: 1400px !important; }
      #wp-pills-root .wi-pills-row{ gap: 0.6rem !important; flex-wrap: nowrap !important; justify-content: center !important; }
      #wp-pills-root .wi-pills-row .wi-pill, #wp-pills-root .wi-pill{ padding: 0.85rem 1.35rem !important; font-size: clamp(15px, 1.4vw, 17px) !important; border-width: 2px !important; white-space: nowrap !important; }
      @media (max-width: 900px){ #wp-pills-root .wi-pills-row{ flex-wrap: wrap !important; } #wp-pills-root .wi-pill{ white-space: normal !important; } }
      #wp-form-slot .wi-contact-form .wi-grid{ gap: 1.8rem !important; margin-bottom: 77px !important; }
      #wp-form-slot .wi-contact-form input, #wp-form-slot .wi-contact-form textarea{ padding: 1.5rem 2.4rem !important; font-size: clamp(19px, 1.8vw, 22px) !important; border-width: 2.5px !important; border-radius: 29px !important; }
      #wp-form-shell .wi-form-extra{ margin-top: 77px !important; margin-bottom: 38px !important; }
      #wp-form-shell .wi-form-extra textarea{ min-height: 240px !important; padding: 1.2rem 1.8rem !important; font-size: clamp(17px, 1.62vw, 19px) !important; border-radius: 29px !important; }
      #wp-form-shell .wi-form-extra textarea::placeholder{ font-size: clamp(17px, 1.62vw, 19px) !important; }
      .wi-privacy-wrap{ gap: 1.2rem !important; margin-top: 2.4rem !important; max-width: 720px !important; }
      .wi-privacy-checkbox{ width: 29px !important; height: 29px !important; }
      .wi-privacy-label, #wp-form-shell .wi-privacy{ font-size: clamp(17px, 1.8vw, 19px) !important; }
      #wp-form-shell .wi-cta-btn{ font-size: clamp(19px, 1.8vw, 24px) !important; padding: 1.8rem 3.6rem !important; }
      #wi-contact-root [class*="grid-cols-[1fr_auto]"] button{ width: 547px !important; height: 96px !important; padding: 0 58px !important; font-size: 36px !important; border-radius: 48px !important; }
      @media (max-width: 640px){ #wi-contact-root [class*="grid-cols-[1fr_auto]"] button{ height: 86px !important; padding: 0 38px !important; font-size: 24px !important; } }
    `;
    doc.head.appendChild(scale);
  }

  // Danke stil (kao pre)
  if(!doc.getElementById('wi-danke-style')){
    const st=doc.createElement('style'); st.id='wi-danke-style';
    st.textContent=`
      .wi-danke-overlay{position:fixed;inset:0;min-height:100vh;display:flex;flex-direction:column;
        align-items:center;justify-content:center;z-index:999999;padding:24px;text-align:center;
        background:#ffed00;color:#000}
      .wi-danke-title{font-weight:800;font-size:clamp(48px,10vw,140px);line-height:.95;margin:18px 0 10px}
      .wi-danke-sub{font-size:clamp(16px,2.6vw,22px)}
      .wi-danke-hand{width:min(28vw,240px);height:auto;display:block;animation:wi-bounce 1.3s ease infinite}
      @keyframes wi-bounce{0%,20%,53%,80%,100%{transform:none}40%,43%{transform:translateY(-16px)}70%{transform:translateY(-9px)}90%{transform:translateY(-4px)}}
      .wi-danke-overlay.wi-error{background:#111;color:#fff}
    `;
    doc.head.appendChild(st);
  }

  // 🔧 VH FIX – spusti documentElement na realnu visinu sadržaja
  if(!doc.getElementById('wi-vh-fix')){
    const fx=doc.createElement('style'); fx.id='wi-vh-fix';
    fx.textContent = `
      html, body, #__next, #wi-contact-root, main{
        min-height:0 !important;
        height:auto !important;
        overflow-x:hidden !important;
      }
      /* ubij sve Tailwind util-e koji forsiraju ekran */
      .min-h-screen, .h-screen { min-height:auto !important; height:auto !important; }
      [class*="min-h-screen"], [class*="h-screen"] { min-height:auto !important; height:auto !important; }
      /* inline stilovi sa vh */
      [style*="vh"]{ min-height:auto !important; height:auto !important; }
    `;
    doc.head.appendChild(fx);
  }

  // sigurnosno – odmah izmeri i “reflow”
  try{
    doc.documentElement.style.scrollBehavior = 'auto';
    // gurka: ako je već postavljen 100vh na root, reset će stupiti tek posle reflow-a
    void doc.body.offsetHeight;
  }catch(_){}
}


  function renderPills(doc){
    let root=doc.getElementById('wp-pills-root');
    if(!root){const slot=doc.getElementById('wp-form-slot');if(slot&&slot.nextElementSibling) root=slot.nextElementSibling;}
    if(!root)return;
    root.innerHTML='';
    root.className='wi-pills-wrapper';
    Object.keys(PILLS).forEach(key=>{
      const group=PILLS[key];
      const section=doc.createElement('div');
      section.className='wi-pills-section';
      const h=doc.createElement('h3');
      h.className='wi-pills-title';
      var raw=(group.title||key.toUpperCase())+'';
      h.innerHTML=raw.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/\n/g,'<br>');
      section.appendChild(h);
      const row=doc.createElement('div');
      row.className='wi-pills-row';
      (group.items||[]).forEach(item=>{
        const btn=doc.createElement('button');
        btn.type='button';
        btn.className='wi-pill';
        btn.textContent=item;
        btn.dataset.group=key;
        btn.dataset.value=item;
        btn.addEventListener('click',function(){
          if(group.multiple){ btn.classList.toggle('wi-pill-active'); }
          else{ row.querySelectorAll('.wi-pill').forEach(function(b){ b.classList.remove('wi-pill-active'); }); btn.classList.add('wi-pill-active'); }
          syncHidden(doc);
          fitFromDOM();
        });
        row.appendChild(btn);
      });
      section.appendChild(row);
      root.appendChild(section);
    });
  }
  function syncHidden(doc){
    const frm=doc.querySelector('.wi-contact-form'); if(!frm)return;
    Object.keys(PILLS).forEach(key=>{
      const sel=doc.querySelectorAll('[data-group="'+key+'"].wi-pill-active');
      const values=Array.from(sel).map(b=>b.dataset.value);
      const hid=frm.querySelector('input[name="pill_'+key+'"]'); if(hid) hid.value=values.join(', ');
    });
  }
  function clearLooseFields(doc) {
    doc.querySelectorAll('textarea').forEach(t => { t.value = ''; });
    doc.querySelectorAll('input, textarea').forEach(el => {
      try { el.dispatchEvent(new Event('input', { bubbles: true })); } catch {}
      try { el.dispatchEvent(new Event('change', { bubbles: true })); } catch {}
    });
  }
// --- Child → Parent: pošalji realnu visinu na osnovu geometrije tela --- //
function startHeightPinger(doc){
  if (doc.__wiPinger) return;           // jednom i nikad više
  doc.__wiPinger = true;

  const w = iframe.contentWindow;

  // Izmeri "stvarno dno" sadržaja: najveći bottom svih direktnih childova <body>
  function measureRealHeight(){
    try{
      const kids = Array.from(doc.body.children);
      let maxBottom = 0;
      for (const el of kids){
        const r = el.getBoundingClientRect();
        if (isFinite(r.bottom)) maxBottom = Math.max(maxBottom, r.bottom);
      }
      // fallback ako telo nema decu (ne bi trebalo, ali za svaki slučaj)
      if (maxBottom < 1) {
        const rB = doc.body.getBoundingClientRect();
        const rH = doc.documentElement.getBoundingClientRect();
        maxBottom = Math.max(rB.bottom || 0, rH.bottom || 0);
      }

      const h = Math.ceil(maxBottom);   // u iFrame-u nema scrolla, pa je ovo realna visina
      // pošalji parentu (slušaš već na 'wi-iframe-height')
      w.parent.postMessage({ type: 'wi-iframe-height', height: h }, '*');
    }catch(_){}
  }

  // Reaguj na sve promene
  try {
    // promene layout-a
    new ResizeObserver(measureRealHeight).observe(doc.documentElement);
    // promene u DOM-u
    new MutationObserver(measureRealHeight).observe(doc.body, {
      childList: true, subtree: true, attributes: true
    });
  } catch(_){}

  // prozorski eventi
  w.addEventListener('load',   measureRealHeight, {passive:true});
  w.addEventListener('resize', measureRealHeight, {passive:true});

  // nekoliko inicijalnih tikova + keep-alive
  setTimeout(measureRealHeight, 50);
  setTimeout(measureRealHeight, 300);
  setTimeout(measureRealHeight, 900);
  setInterval(measureRealHeight, 800);  // safety ping
}

  /* ===== NOVO: reCAPTCHA loader u IFRAME-u + submit helper ===== */
  function loadRecaptchaIntoIframe(doc){
    if (!SITE_KEY) return; // ako nema site key, preskoči
    const w = iframe.contentWindow;
    if (w && w.grecaptcha) return; // već učitano
    const s = doc.createElement('script');
    s.src = 'https://www.google.com/recaptcha/api.js?render=' + encodeURIComponent(SITE_KEY);
    s.async = true; s.defer = true;
    doc.head.appendChild(s);
  }

  function wiSleepIframe(ms) {
    return new Promise(function (res) { setTimeout(res, ms); });
  }

  async function waitForIframeGrecaptcha(win, maxMs) {
    const deadline = Date.now() + (maxMs || 10000);
    while (Date.now() < deadline) {
      if (win && win.grecaptcha && win.grecaptcha.execute) return win.grecaptcha;
      await wiSleepIframe(80);
    }
    return null;
  }

  async function submitWithRecaptcha(doc){
    if (!doc) doc = iframe.contentDocument || (iframe.contentWindow && iframe.contentWindow.document);
    if (!doc) return;
    const frm = doc.querySelector('.wi-contact-form');
    if (!frm) return;

    // 1) Prvo obavezna polja + format emaila (poruke ostaju dok polje nije ispravno)
    function updateFieldError(inp) {
      var errEl = frm.querySelector('.wi-field-error[data-for="' + inp.name + '"]');
      if (!errEl) return false;
      var val = (inp.value || '').trim();
      var msg = '';
      if (inp.hasAttribute('required') || inp.getAttribute('aria-required') === 'true') {
        if (val === '') {
          msg = inp.getAttribute('data-required-msg') || (inp.name + ' ist erforderlich');
        } else if (inp.type === 'email' && !inp.checkValidity()) {
          msg = 'Ungültige E-Mail-Adresse';
        }
      } else if (inp.type === 'email' && val !== '' && !inp.checkValidity()) {
        msg = 'Ungültige E-Mail-Adresse';
      }
      if (msg) {
        inp.classList.add('wi-invalid');
        errEl.textContent = msg;
        errEl.classList.remove('wi-field-error-hidden');
        return true;
      } else {
        inp.classList.remove('wi-invalid');
        errEl.textContent = '';
        errEl.classList.add('wi-field-error-hidden');
        return false;
      }
    }
    const requiredInputs = frm.querySelectorAll('input[required], input[aria-required="true"]');
    var hasInvalid = false;
    requiredInputs.forEach(function(inp) {
      if (updateFieldError(inp)) hasInvalid = true;
    });
    if (hasInvalid) {
      const firstInvalid = frm.querySelector('input.wi-invalid');
      try {
        bringIntoView();
        if (firstInvalid) {
          setTimeout(function() {
            firstInvalid.scrollIntoView({ behavior: 'smooth', block: 'center' });
            firstInvalid.focus({ preventScroll: true });
          }, 250);
        }
      } catch (_) {}
      fitFromDOM();
      return;
    }

    // 2) Zatim checkbox za Datenschutz
    var privacyEl = doc.getElementById('wi-privacy-checkbox');
    var privacyErrWrap = doc.getElementById('wi-privacy-error-wrap');
    if (privacyEl && !privacyEl.checked) {
      if (privacyErrWrap) {
        privacyErrWrap.classList.remove('hidden');
        try {
          privacyErrWrap.scrollIntoView({ behavior: 'smooth', block: 'center' });
        } catch (_) {}
      }
      try { privacyEl.focus(); } catch (_) {}
      fitFromDOM();
      return;
    }

    // 3) Nativna validacija (format email itd.)
    if (!frm.reportValidity()) {
      const firstInvalid = doc.querySelector(':invalid');
      try {
        const r = iframe.getBoundingClientRect();
        window.scrollTo({ top: window.pageYOffset + r.top - 80, behavior: 'smooth' });
        if (firstInvalid) {
          setTimeout(() => {
            firstInvalid.scrollIntoView({ behavior: 'smooth', block: 'center' });
            firstInvalid.focus({ preventScroll: true });
          }, 250);
        }
      } catch {}
      fitFromDOM();
      return;
    }

    // ako nema site key – pošalji bez tokena (server ne proverava reCAPTCHA bez secret)
    if (!SITE_KEY) { submitAjax(doc); return; }

    const w = iframe.contentWindow;
    const tokenInput = doc.getElementById('wi_recaptcha_token');

    function fail(msg){
      if (typeof console !== 'undefined' && console.warn) {
        console.warn(msg || 'reCAPTCHA nije dostupna');
      }
      showError();
    }

    loadRecaptchaIntoIframe(doc);
    let gc = await waitForIframeGrecaptcha(w, 10000);
    if (!gc) {
      loadRecaptchaIntoIframe(doc);
      gc = await waitForIframeGrecaptcha(w, 4000);
    }
    if (!gc) {
      fail('reCAPTCHA nije učitana');
      return;
    }
    try {
      await new Promise((resolve) => gc.ready(resolve));
      const token = await gc.execute(SITE_KEY, {action: 'contact'});
      if (tokenInput) tokenInput.value = token;
      submitAjax(doc);
    } catch (e) {
      fail('reCAPTCHA execute error');
    }
  }
  /* ===== Kraj NOVO ===== */

  /* -------- Danke / Error UI -------- */
  function ensureDanke(doc){
    let o=doc.getElementById('wi-danke'); if(o) return o;
    o=doc.createElement('div'); o.id='wi-danke'; o.className='wi-danke-overlay';
    o.innerHTML=`
      <svg viewBox="0 0 16 16" class="wi-danke-hand" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
        <path d="M6.956 1.745C7.021.81 7.908.087 8.864.325l.261.066c.463.116.874.456 1.012.965.22.816.533 2.511.062 4.51a9.84 9.84 0 0 1 .443-.051c.713-.065 1.669-.072 2.516.21.518.173.994.681 1.2 1.273.184.532.16 1.162-.234 1.733.058.119.103.242.138.363.077.27.113.567.113.856 0 .289-.036.586-.113.856-.039.135-.09.273-.16.404.169.387.107.819-.003 1.148a3.163 3.163 0 0 1-.488.901c.054.152.076.312.076.465 0 .305-.089.625-.253.912C13.1 15.522 12.437 16 11.5 16H8c-.605 0-1.07-.081-1.466-.218a4.82 4.82 0 0 1-.97-.484l-.048-.03c-.504-.307-.999-.609-2.068-.722C2.682 14.464 2 13.846 2 13V9c0-.85.685-1.432 1.357-1.615.849-.232 1.574-.787 2.132-1.41.56-.627.914-1.28 1.039-1.639.199-.575.356-1.539.428-2.59z"/>
      </svg>
      <div class="wi-danke-title">DANKE!</div>
      <div class="wi-danke-sub">Ihre Anfrage wurde erfolgreich gesendet. Wir melden uns in Kürze bei Ihnen.</div>
    `;
    doc.body.appendChild(o); return o;
  }
  function showDanke(){
    const d=iframe.contentDocument||iframe.contentWindow?.document; if(!d)return; injectAssets(d);
    const o=ensureDanke(d); o.classList.remove('wi-error');
    bringIntoView(); fitFromDOM();
    setTimeout(()=>o.remove(),5000);
    try{ startHeightPinger(d); }catch(_){}
  }
  function showError(customSub){
    const d=iframe.contentDocument||iframe.contentWindow?.document; if(!d)return; injectAssets(d);
    const o=ensureDanke(d); o.classList.add('wi-error');
    const t=o.querySelector('.wi-danke-title'); if(t) t.textContent='UPS!';
    const s=o.querySelector('.wi-danke-sub');
    if(s) s.textContent = customSub || 'Fehler beim Senden. Bitte versuchen Sie es erneut.';
    bringIntoView(); fitFromDOM();
    setTimeout(()=>o.remove(),5000);
  }

  /* -------- AJAX submit (sa redirect detekcijom) -------- */
  async function submitAjax(doc){
    const frm = doc.querySelector('.wi-contact-form');
    if (!frm) return;

    try {
      syncHidden(doc);

      const fd = new FormData(frm);
      if (!fd.get('action')) fd.set('action', 'wi_contact_submit');
      var privacyCb = doc.getElementById('wi-privacy-checkbox');
      if (privacyCb) fd.set('wi_privacy', privacyCb.checked ? '1' : '0');

      const response = await fetch(POST_URL, {
        method: 'POST',
        body: fd,
        credentials: 'include'
      });

      const text = await response.text();
      if (response.ok && (text === 'OK' || text.trim() === '')) {
        showDanke();

        try {
          frm.reset();
          clearLooseFields(doc);
          doc.querySelectorAll('.wi-pill.wi-pill-active').forEach(b => b.classList.remove('wi-pill-active'));
          syncHidden(doc);
        } catch {}

        fitFromDOM();
      } else {
        const respTxt = (text || '').trim();
        if (respTxt.indexOf('RECAPTCHA') === 0) {
          showError('reCAPTCHA war nicht erfolgreich. Bitte Google-Skripte / Cookies erlauben oder Seite neu laden.');
        } else {
          showError();
        }
      }
    } catch (err) {
      showError();
    }
  }

  /* -------- CTA hook + delegacija -------- */
  function wireCTA(doc){
    const btn=doc.querySelector('button[type="submit"]');
    if(btn && !btn.dataset.wiWired){
      btn.type='button'; btn.dataset.wiWired='1';
      /* IZMENJENO: sad zovemo submitWithRecaptcha */
      btn.addEventListener('click',(ev)=>{ev.preventDefault(); submitWithRecaptcha(doc);}, true);
    }
  }
  function installDelegation(doc){
    if(doc.__wiDelegation) return; doc.__wiDelegation=true;
    doc.addEventListener('click',(ev)=>{
      const el=ev.target.closest('button,a,[role="button"]'); if(!el) return;
      const txt=(el.textContent||'').trim();
      if(/anfrage\s*senden/i.test(txt)){
        ev.preventDefault(); ev.stopPropagation();
        /* IZMENJENO: sad zovemo submitWithRecaptcha */
        submitWithRecaptcha(doc);
      }
    },true);
    doc.addEventListener('keydown',(ev)=>{
      if(ev.key!=='Enter') return;
      const inForm=ev.target.closest('.wi-contact-form');
      if(inForm){ ev.preventDefault(); /* IZMENJENO */ submitWithRecaptcha(doc); }
    },true);
  }

  /* -------- neutralizuj outer forme + ubaci WP formu -------- */
  function neutralizeOuterForms(doc){
    const forms=doc.querySelectorAll('form:not(.wi-contact-form)');
    forms.forEach(f=>{
      f.setAttribute('novalidate','novalidate');
      f.addEventListener('submit',(e)=>{ e.preventDefault(); e.stopPropagation(); return false; }, true);
    });
  }
  function injectForm(){
    const doc=iframe.contentDocument||iframe.contentWindow?.document; if(!doc) return;
    injectAssets(doc);

    let slot=doc.getElementById('wp-form-slot'); if(!slot) slot=doc.body;
    slot.innerHTML=FORM_HTML;

    const fb=doc.querySelector('.wi-contact-form .wi-submit'); if(fb) fb.style.display='none';

    /* Ažuriranje poruke ispod polja: ostaje dok nije ispravno; za email „Ungültige E-Mail-Adresse“ */
    const frm = doc.querySelector('.wi-contact-form');
    if (frm) {
      function onFieldChange(ev) {
        var inp = ev.target;
        if (!inp || inp.nodeName !== 'INPUT') return;
        var errEl = frm.querySelector('.wi-field-error[data-for="' + inp.name + '"]');
        if (!errEl) return;
        var val = (inp.value || '').trim();
        var msg = '';
        if (inp.hasAttribute('required') || inp.getAttribute('aria-required') === 'true') {
          if (val === '') msg = inp.getAttribute('data-required-msg') || (inp.name + ' ist erforderlich');
          else if (inp.type === 'email' && !inp.checkValidity()) msg = 'Ungültige E-Mail-Adresse';
        } else if (inp.type === 'email' && val !== '' && !inp.checkValidity()) {
          msg = 'Ungültige E-Mail-Adresse';
        }
        if (msg) {
          inp.classList.add('wi-invalid');
          errEl.textContent = msg;
          errEl.classList.remove('wi-field-error-hidden');
        } else {
          inp.classList.remove('wi-invalid');
          errEl.textContent = '';
          errEl.classList.add('wi-field-error-hidden');
        }
      }
      frm.addEventListener('input', onFieldChange, true);
      frm.addEventListener('change', onFieldChange, true);
    }

    /* NOVO: učitaj reCAPTCHA skriptu u IFRAME dokument */
    loadRecaptchaIntoIframe(doc);

    renderPills(doc); wireCTA(doc); installDelegation(doc); syncHidden(doc);
    fitFromDOM();
startHeightPinger(doc);
    try{
      if('ResizeObserver' in window){ new ResizeObserver(()=>fitFromDOM()).observe(doc.documentElement); }
      const mo=new MutationObserver(()=>{ wireCTA(doc); fitFromDOM(); });
      mo.observe(doc.body,{subtree:true,childList:true});
    }catch(e){}
  }

  function handleLoad(){
    try{
      const href=iframe.contentWindow.location.href;
      if(/\/uploads\/next-contact\//.test(href)){
        const doc=iframe.contentDocument||iframe.contentWindow.document;
        neutralizeOuterForms(doc);
        injectForm();
      }
    }catch(e){}
  }

  iframe.addEventListener('load',handleLoad);
  if(iframe.complete) setTimeout(handleLoad,120);
  window.addEventListener('resize',()=>requestAnimationFrame(fitFromDOM));
  setTimeout(fitFromDOM,350);
})();
</script>
<style>
    #wi-contact-info-wrap ~ footer,
  #wi-contact-info-wrap ~ #colophon,
  #wi-contact-info-wrap ~ .site-footer {
    border-top: 0 !important;
    background-image: none !important;
    box-shadow: none !important;
    /* ako postoji 1px gap od teme: povuci footer 1px nagore */
    margin-top: -5px !important;
  }
</style>
<?php get_footer(); ?>
