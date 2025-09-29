<?php
/**
 * Template Name: Kontakt (Next + WP form, AJAX submit)
 */
get_header();

$next_url       = content_url('uploads/next-contact/index.html?v=11');
$form_html      = do_shortcode('[wi_contact_form]');
$plugin_css_url = plugins_url('assets/front.css', WP_PLUGIN_DIR . '/wi-contact/wi-contact.php');

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
    padding:0!important;overflow:visible!important;}
  .contact-main-container{margin:0!important;padding:0!important;}
  #wi-next-contact{border-radius:0!important;display:block;width:100%;border:0;}
  .site,.site-main,.content-area,.entry-content{overflow:visible!important;}
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

<script>
(function(){
  const iframe    = document.getElementById('wi-next-contact');
  const CSS_URL   = <?php echo wp_json_encode($plugin_css_url.'?v=8'); ?>;
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
    if(!doc.getElementById('wi-contact-front-css')){
      const l=doc.createElement('link'); l.id='wi-contact-front-css'; l.rel='stylesheet'; l.href=CSS_URL; doc.head.appendChild(l);
    }
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
      `; doc.head.appendChild(st);
    }
  }
  function isThreeColGrid(el){if(!el)return false;const cs=getComputedStyle(el);const cols=(cs.gridTemplateColumns||'').split(' ').filter(Boolean).length;return cols>=3||el.className.includes('md:grid-cols-3');}
  function renderPills(doc){
    let root=doc.getElementById('wp-pills-root');
    if(!root){const slot=doc.getElementById('wp-form-slot');let x=slot?slot.nextElementSibling:null;while(x && !isThreeColGrid(x)) x=x.nextElementSibling;root=x||null;}
    if(!root)return;
    root.innerHTML='';
    Object.keys(PILLS).forEach(key=>{
      const group=PILLS[key]; const col=doc.createElement('div');
      const h=doc.createElement('label'); h.className='block text-3xl unbounded-bold text-black mb-6 text-center'; h.textContent=group.title||key.toUpperCase(); col.appendChild(h);
      const box=doc.createElement('div'); box.className='space-y-2';
      (group.items||[]).forEach(item=>{
        const btn=doc.createElement('button'); btn.type='button';
        btn.className='wi-pill w-full p-4 rounded-[40px] text-center transition-all poppins font-bold text-lg bg-black text-white border-2 border-black hover:bg-neutral-800';
        btn.textContent=item; btn.dataset.group=key; btn.dataset.value=item;
        btn.addEventListener('click',()=>{ if(group.multiple){btn.classList.toggle('wi-pill-active');}else{box.querySelectorAll('button').forEach(b=>b.classList.remove('wi-pill-active')); btn.classList.add('wi-pill-active');} syncHidden(doc); fitFromDOM(); });
        box.appendChild(btn);
      });
      col.appendChild(box); root.appendChild(col);
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

  async function submitWithRecaptcha(doc){
    const frm = doc.querySelector('.wi-contact-form');
    if (!frm) return;

    // nativna validacija prvo
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

    // ako nema site key ili grecaptcha – pošalji bez tokena (kao do sada)
    if (!SITE_KEY) { submitAjax(doc); return; }

    const w = iframe.contentWindow;
    const tokenInput = doc.getElementById('wi_recaptcha_token');

    function fail(msg){
      console.warn(msg || 'reCAPTCHA nije dostupna');
      alert('reCAPTCHA je blokirana ili nije učitana. Dozvoli Google skripte i pokušaj ponovo.');
    }

    if (w && w.grecaptcha && w.grecaptcha.execute) {
      try {
        await new Promise((resolve) => w.grecaptcha.ready(resolve));
        const token = await w.grecaptcha.execute(SITE_KEY, {action: 'contact'});
        if (tokenInput) tokenInput.value = token;
        submitAjax(doc);
      } catch (e) {
        fail('reCAPTCHA execute error'); 
      }
    } else {
      // ako kasni učitavanje, probaj kratko kasnije
      setTimeout(() => {
        if (w && w.grecaptcha && w.grecaptcha.execute) submitWithRecaptcha(doc);
        else fail('reCAPTCHA nije učitana');
      }, 600);
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
  }
  function showError(){
    const d=iframe.contentDocument||iframe.contentWindow?.document; if(!d)return; injectAssets(d);
    const o=ensureDanke(d); o.classList.add('wi-error');
    const t=o.querySelector('.wi-danke-title'); if(t) t.textContent='UPS!';
    const s=o.querySelector('.wi-danke-sub'); if(s) s.textContent='Greška pri slanju. Pokušajte ponovo.';
    bringIntoView(); fitFromDOM();
    setTimeout(()=>o.remove(),5000);
  }

  /* -------- AJAX submit (sa redirect detekcijom) -------- */
  async function submitAjax(doc){
    const frm = doc.querySelector('.wi-contact-form');
    if (!frm) return;

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

    try {
      syncHidden(doc);

      const fd = new FormData(frm);
      if (!fd.get('action')) fd.set('action', 'wi_contact_submit');

      await fetch(POST_URL, {
        method: 'POST',
        body: fd,
        credentials: 'include',
        mode: 'no-cors'
      });

      showDanke();

      try {
        frm.reset();
        clearLooseFields(doc);
        doc.querySelectorAll('.wi-pill.wi-pill-active').forEach(b => b.classList.remove('wi-pill-active'));
        syncHidden(doc);
      } catch {}

      fitFromDOM();
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

    /* NOVO: učitaj reCAPTCHA skriptu u IFRAME dokument */
    loadRecaptchaIntoIframe(doc);

    renderPills(doc); wireCTA(doc); installDelegation(doc); syncHidden(doc);
    fitFromDOM();

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

<?php get_footer(); ?>
