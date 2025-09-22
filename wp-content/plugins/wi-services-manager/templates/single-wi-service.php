<?php
// *** NIKAKVA deklaracija klase ovdje! Samo render. ***
if (!defined('ABSPATH')) exit;

$slug = get_query_var( WI_Services_Manager::QV_TAG );
list($svc, $det) = WI_Services_Manager::get_service_data_by_slug($slug);

// fallback
$slides = $det['slides'] ?? [
  ['h1'=>'PLAKATWERBUNG','h2'=>'Authentische Plakatwerbung','text'=>'So sieht professionelle Straßenwerbung in der Praxis aus','bg_type'=>'image','bg_url'=>''],
  ['h1'=>'PLAKATWERBUNG','h2'=>'Professionelle Außenwerbung','text'=>'Ihre Marke im städtischen Umfeld präsentieren','bg_type'=>'image','bg_url'=>''],
  ['h1'=>'PLAKATWERBUNG','h2'=>'Großformat-Werbung','text'=>'Maximale Aufmerksamkeit durch beeindruckende Größe','bg_type'=>'image','bg_url'=>''],
  ['h1'=>'PLAKATWERBUNG','h2'=>'Strategische Platzierung','text'=>'An hochfrequentierten Verkehrsknotenpunkten','bg_type'=>'image','bg_url'=>''],
];
$below = $det['below'] ?? ['title'=>'IHRE BOTSCHAFT AUF DIE STRAßE','text'=>'Plakatwerbung ist eine der effektivsten Formen der Außenwerbung...'];

get_header();
?>
<style>
/* ----- Layout / Container ----- */
.content-container{max-width:1400px;margin:0 auto;padding:0 16px}
.mt-16{margin-top:4rem}
.unbounded-bold{font-family:system-ui, sans-serif;font-weight:800}
.poppins{font-family:system-ui, sans-serif}
.poppins-bold{font-family:system-ui, sans-serif;font-weight:700}

/* === Full-bleed wrap sa TAČNO 30px spoljnog lufta i savršenim centrom === */
.wi-hero-wrap{
  position:relative;
  display:flex; align-items:center; gap:8px;
  height:50vh; min-height:400px;

  /* ključ: poravnanje na viewport, simetričan padding */
  width:100vw;
  margin-left:calc(50% - 50vw);  /* centriraj prema viewportu */
  padding-left:30px;
  padding-right:30px;
  box-sizing:border-box;
}
@media (min-width:640px){
  .wi-hero-wrap{ height:60vh; min-height:500px; }
}
@media (min-width:1024px){
  .wi-hero-wrap{ height:70vh; }
}

/* Na malim ekranima može manji luft (npr. 16px) */
@media (max-width:480px){
  .wi-hero-wrap{
    padding-left:16px;
    padding-right:16px;
  }
}


/* bočni thumbovi */
.wi-side{
  display:none;
}
@media (min-width:768px){
  .wi-side{ display:block; width:12%; height:100%; overflow:hidden; border-radius:20px; opacity:.6; cursor:pointer; transition:opacity .2s }
  .wi-side:hover{ opacity:.8 }
}
@media (min-width:1024px){
  .wi-side{ width:16%; border-radius:40px }
}
.wi-side img{ width:100%; height:100%; object-fit:cover; display:block }

/* ----- Hero / Slider (sada je u sredini, sužen) ----- */
.wi-hero{
  position:relative; height:100%;
  overflow:hidden; border-radius:20px;
  flex:1;
}
@media (min-width:1024px){ .wi-hero{ border-radius:40px } }

.wi-slide{position:absolute; inset:0; opacity:0; transition:opacity 1s; border-radius:inherit; overflow:hidden}
.wi-slide.is-active{opacity:1}
.wi-slide .wi-dim{position:absolute; inset:0; background:rgba(0,0,0,.4)}
.wi-hero img,.wi-hero video{width:100%; height:100%; object-fit:cover; display:block}

/* Središnji tekst unutar slajdera */
.wi-center{
  position:absolute; inset:0;
  display:flex; flex-direction:column; /* vertikalni stack */
  align-items:center; justify-content:center;
  text-align:center; color:#fff; padding:0 16px;
}


/* Tipografija u hero-u (srazmerno smanjena) */
.wi-h1{
  font-size:clamp(22px, 3.6vw, 60px);   /* manji nego pre */
  line-height:1.1;
  margin-bottom:8px;
  letter-spacing:-.01em;
  font-weight:800;
}
.wi-h2{
  font-size:clamp(12px, 1.8vw, 42px);   /* manji podnaslov */
  line-height:1.2;
  margin-bottom:6px;
  font-weight:700;
}

.wi-p { font-size:clamp(13px, 1.6vw, 22px); line-height:1.55; max-width:760px; margin:0 auto }
@media (max-width:580px){
  .wi-h1{
    font-size:18px !important;
    line-height:1.1;
  }
  .wi-h2{
    font-size:14px !important;
    line-height:1.2;
  }
  .wi-p{
    font-size:12px !important;
    line-height:1.4;
  }
}
/* Strelice = PRAVI krugovi */
.wi-arrow{
  position:absolute; top:50%; transform:translateY(-50%);
  width:48px; height:48px ;            /* fiksna širina/visina */
  border-radius:50%;                  /* krug */
  background:#ffed00; border:none; cursor:pointer;
  display:flex; align-items:center; justify-content:center;
  line-height:1; font-size:18px;      /* veličina simbola */
  box-shadow:0 2px 10px rgba(0,0,0,.25);
  z-index:5;                          /* iznad slajda */
  padding:0;                          /* bez paddinga! */
}
.wi-arrow:hover{ background:#fff; }
.wi-arrow--left{ left:8px; }
.wi-arrow--right{ right:8px; }
@media (min-width:640px){
  .wi-arrow{ width:56px; height:56px; font-size:20px; }
  .wi-arrow--left{ left:16px; }
  .wi-arrow--right{ right:16px; }
}
@media (min-width:1024px){
  .wi-arrow{ width:44px; height:44px; font-size:22px; }
  .wi-arrow--left{ left:32px; }
  .wi-arrow--right{ right:32px; }
}

/* Dotovi = krugovi pri dnu */
.wi-dots{position:absolute; left:50%; transform:translateX(-50%); bottom:14px; display:flex; gap:10px}
.wi-dot{
  width:12px; height:12px; aspect-ratio:1/1;
  border-radius:50%;
  box-sizing:border-box; border:2px solid rgba(255,255,255,.85);
  background:transparent; cursor:pointer; display:inline-block;
  padding:0; appearance:none; -webkit-appearance:none; -moz-appearance:none; line-height:0;
}
.wi-dot.is-active{background:#ffed00; border-color:#ffed00}

/* ----- Ispod slidera: naslov + paragraf ----- */
.wi-below-h2{
  color:#000; line-height:1.06; letter-spacing:-.01em; text-wrap:balance;
  font-size:clamp(28px, 4.5vw, 60px) !important;
  margin:0 0 32px !important;
}
@media (min-width:900px){
  .wi-below-p{
    color:#000 !important;
    font-size:20px !important;
    line-height:1.6 !important;
    max-width:900px !important;
    margin:0 auto !important;
  }
}
@media (min-width:900px){
  .content-container .max-w-4xl h2.text-\[60px\].unbounded-bold{
    font-size:60px !important;
    margin-bottom:22px !important;
    color:#000 !important;
  }
}

/* ----- Responsive fine tune ----- */
@media (max-width:820px){
  .wi-h1{font-size:32px}
  .wi-h2{font-size:18px}
  .wi-p {font-size:14px}
}
@media (max-width:600px){
  .wi-dots{bottom:12px}
}

/* ===== HARD OVERRIDE: paragraf ispod slidera ===== */
section.content-container.mt-16 .max-w-4xl.text-center p,
section.content-container.mt-16 .max-w-4xl p {
  font-family: "Poppins", system-ui, sans-serif !important;
  font-size: 20px !important;
  line-height: 1.6 !important;
  color: #000 !important;
  max-width: 900px !important;
  margin: 0 auto !important;
  letter-spacing: 0 !important;
}
/* =========================
   Sekcija: 3 kompaktne kartice
   ========================= */
.cards-section {
  margin-top: 100px;
  margin-bottom: 60px;
}

.cards-grid {
  display: grid;
  grid-template-columns: 1fr;   /* mobilno: 1 kolona */
  gap: 20px;
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 16px;
}

/* Kartica */
.card {
  background: #000;
  border: 2px solid #333;
  border-radius: 20px;
  padding: 48px;
  text-align: center;
  transition: border-color 0.3s ease;
}
.card:hover {
  border-color: #ffed00;
}

/* Ikonica */
.card-icon {
  width: 80px;
  height: 80px;
  background: #ffed00;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  margin: 0 auto 24px;
}
.card-icon .icon {
  width: 40px;
  height: 40px;
  stroke: #000;
}

/* Naslov */
.card h3 {
  font-family: "Poppins", system-ui, sans-serif !important;
  font-weight: 700 !important;
  font-size: 24px !important;     /* fiksno smanjeno */
  line-height: 1.3 !important;
  color: #fff !important;
  margin: 0 0 16px !important;
  text-transform: none !important;
  letter-spacing: normal !important;
}

/* Tekst */
.card p {
  font-family: "Poppins", system-ui, sans-serif !important;
  font-size: 16px !important;
  line-height: 1.5 !important;
  color: #fff !important;
  margin: 0 auto !important;
  max-width: 280px !important;
}

/* Desktop: 3 kartice u redu */
@media (min-width: 768px) {
  .cards-grid {
    grid-template-columns: repeat(3, 1fr);
    gap: 24px;
  }
}

</style>

<main class="content-container" style="padding-top:40px;padding-bottom:40px">
  <section class="content-container">
    <!-- Wrap sa bočnim thumbnailovima -->
    <div class="wi-hero-wrap">
      <!-- Leva: PRETHODNA -->
      <div class="wi-side wi-side--prev" aria-label="Previous slide (thumbnail)">
        <img id="wi-prev-thumb" src="" alt="Previous image">
      </div>

      <!-- Centralni suženi slider -->
      <div class="wi-hero" id="wi-hero">
        <?php foreach ($slides as $i => $s):
          $h1 = esc_html($s['h1'] ?? '');
          $h2 = esc_html($s['h2'] ?? '');
          $tx = wp_kses_post($s['text'] ?? '');
          $bg_type = ($s['bg_type'] ?? 'image') === 'video' ? 'video' : 'image';
          $bg_url  = esc_url($s['bg_url'] ?? '');
        ?>
        <div class="wi-slide<?php echo $i===0 ? ' is-active' : ''; ?>" data-bgurl="<?php echo $bg_url; ?>">
          <?php if ($bg_type === 'video' && $bg_url): ?>
            <video src="<?php echo $bg_url; ?>" autoplay muted loop playsinline></video>
          <?php else: ?>
            <?php if ($bg_url): ?>
              <img src="<?php echo $bg_url; ?>" alt="">
            <?php else: ?>
              <div style="background:#444;width:100%;height:100%"></div>
            <?php endif; ?>
          <?php endif; ?>
          <div class="wi-dim"></div>
          <div class="wi-center">
            <div class="text-center text-white max-w-4xl px-8">
              <?php if ($h1): ?><h1 class="wi-h1 unbounded-bold"><?php echo $h1; ?></h1><?php endif; ?>
              <?php if ($h2): ?><h2 class="wi-h2 poppins-bold"><?php echo $h2; ?></h2><?php endif; ?>
              <?php if ($tx): ?><p class="wi-p poppins"><?php echo $tx; ?></p><?php endif; ?>
            </div>
          </div>
        </div>
        <?php endforeach; ?>

        <button class="wi-arrow wi-arrow--left" aria-label="Prev">
          &#8249;
        </button>
        <button class="wi-arrow wi-arrow--right" aria-label="Next">
          &#8250;
        </button>

        <div class="wi-dots">
          <?php foreach ($slides as $i => $_): ?>
            <button class="wi-dot<?php echo $i===0 ? ' is-active' : ''; ?>" data-index="<?php echo (int)$i; ?>"></button>
          <?php endforeach; ?>
        </div>
      </div>

      <!-- Desna: SLEDEĆA -->
      <div class="wi-side wi-side--next" aria-label="Next slide (thumbnail)">
        <img id="wi-next-thumb" src="" alt="Next image">
      </div>
    </div>
  </section>

  <section class="content-container mt-16">
    <div class="max-w-4xl mx-auto text-center" style="margin:0 auto;max-width:64rem">
      <?php if (!empty($below['title'])): ?>
        <h2 class="wi-below-h2 unbounded-bold"><?php echo esc_html($below['title']); ?></h2>
      <?php endif; ?>
      <?php if (!empty($below['text'])): ?>
        <p class="wi-below-p poppins">
          <?php echo wp_kses_post( wpautop($below['text']) ); ?>
        </p>
      <?php endif; ?>
    </div>
  </section>
  <!-- === Dodatna sekcija sa 3 kartice === -->
<section class="content-container cards-section">
  <div class="cards-grid">
    
    <!-- Kartica 1 -->
    <div class="card">
      <div class="card-icon">
        <svg xmlns="http://www.w3.org/2000/svg" class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <circle cx="12" cy="12" r="10"></circle>
          <circle cx="12" cy="12" r="6"></circle>
          <circle cx="12" cy="12" r="2"></circle>
        </svg>
      </div>
      <h3>Zielgenaue Platzierung</h3>
      <p>Strategische Standorte für maximale Reichweite und optimale Zielgruppenerreichung</p>
    </div>

    <!-- Kartica 2 -->
    <div class="card">
      <div class="card-icon">
        <svg xmlns="http://www.w3.org/2000/svg" class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <path d="M12 6v6l4 2"></path>
          <circle cx="12" cy="12" r="10"></circle>
        </svg>
      </div>
      <h3>24/7 Sichtbarkeit</h3>
      <p>Ihre Werbung arbeitet rund um die Uhr für Sie, ohne Unterbrechung oder Ausfallzeiten</p>
    </div>

    <!-- Kartica 3 -->
    <div class="card">
      <div class="card-icon">
        <svg xmlns="http://www.w3.org/2000/svg" class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <path d="M21.801 10A10 10 0 1 1 17 3.335"></path>
          <path d="m9 11 3 3L22 4"></path>
        </svg>
      </div>
      <h3>Messbare Erfolge</h3>
      <p>Wir dokumentieren und messen Ihre Kampagnenerfolge mit detaillierten Reports</p>
    </div>

  </div>
</section>


</main>

<script>
(function(){
  const hero  = document.getElementById('wi-hero');
  if(!hero) return;

  const slides = Array.from(hero.querySelectorAll('.wi-slide'));
  const dots   = Array.from(hero.querySelectorAll('.wi-dot'));
  const prevBt = hero.querySelector('.wi-arrow--left');
  const nextBt = hero.querySelector('.wi-arrow--right');

  // bočni thumbnailovi
  const prevThumb = document.getElementById('wi-prev-thumb');
  const nextThumb = document.getElementById('wi-next-thumb');
  const leftSide  = document.querySelector('.wi-side--prev');
  const rightSide = document.querySelector('.wi-side--next');

  let idx = 0, timer = null;

  function getBG(i){
    const s = slides[i];
    if (!s) return '';
    // prioritet: data-bgurl (postavljeno iz PHP-a); fallback: pronađi <img> src
    const data = s.getAttribute('data-bgurl') || '';
    if (data) return data;
    const img = s.querySelector('img');
    return img ? img.getAttribute('src') : '';
  }

  function updateThumbs(){
    if (!slides.length) return;
    const pi = (idx - 1 + slides.length) % slides.length;
    const ni = (idx + 1) % slides.length;
    const psrc = getBG(pi);
    const nsrc = getBG(ni);
    if (prevThumb && psrc) prevThumb.src = psrc;
    if (nextThumb && nsrc) nextThumb.src = nsrc;
  }

  function show(i){
    slides[idx].classList.remove('is-active');
    dots[idx].classList.remove('is-active');
    idx = (i + slides.length) % slides.length;
    slides[idx].classList.add('is-active');
    dots[idx].classList.add('is-active');
    updateThumbs();
  }

  function auto(){
    clearInterval(timer);
    timer = setInterval(()=> show(idx+1), 6000);
  }

  // Kontrole
  prevBt.addEventListener('click', ()=>{ show(idx-1); auto(); });
  nextBt.addEventListener('click', ()=>{ show(idx+1); auto(); });
  dots.forEach(d => d.addEventListener('click', ()=>{ show(parseInt(d.dataset.index,10)); auto(); }));

  // Klik na bočne thumbove (ponašaju se kao prev/next)
  if (leftSide)  leftSide.addEventListener('click', ()=>{ show(idx-1); auto(); });
  if (rightSide) rightSide.addEventListener('click', ()=>{ show(idx+1); auto(); });

  // Init
  updateThumbs();
  auto();
})();
</script>

<?php get_footer(); ?>
