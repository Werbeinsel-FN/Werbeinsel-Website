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

/* ----- Hero / Slider ----- */
.wi-hero{
  position:relative; height:66vh; min-height:480px;
  overflow:hidden; border-radius:40px;
  margin-left:auto; margin-right:auto;
}
.wi-slide{position:absolute; inset:0; opacity:0; transition:opacity 1s; border-radius:40px; overflow:hidden}
.wi-slide.is-active{opacity:1}
.wi-slide .wi-dim{position:absolute; inset:0; background:rgba(0,0,0,.4)}
.wi-hero img,.wi-hero video{width:100%; height:100%; object-fit:cover; display:block}

/* — desktop: ~50px gutter sa obe strane prema viewportu — */
@media (min-width:1200px){
  .content-container .wi-hero{
    width:calc(100vw - 100px);
    margin-left:calc(50% - 50vw + 50px);
    margin-right:calc(50% - 50vw + 50px);
  }
}

/* Središnji tekst unutar slajdera */
.wi-center{position:absolute; inset:0; display:flex; align-items:center; justify-content:center; text-align:center; color:#fff; padding:0 16px}
.wi-center .text-center{max-width:min(92%,760px); text-wrap:balance}

/* Tipografija u hero-u (srazmerno smanjena) */
.wi-h1{ font-size:clamp(26px, 4.2vw, 56px); line-height:1.05; margin-bottom:12px; letter-spacing:-.01em; font-weight:800 }
.wi-h2{ font-size:clamp(15px, 2.4vw, 24px); line-height:1.12; margin-bottom:8px; font-weight:700 }
.wi-p { font-size:clamp(13px, 1.4vw, 18px); line-height:1.55; max-width:760px; margin:0 auto }

/* Strelice = krugovi */
.wi-arrow{
  position:absolute; top:50%; transform:translateY(-50%);
  width:56px; height:56px; border-radius:50%;
  background:#ffed00; border:none; cursor:pointer;
  display:flex; align-items:center; justify-content:center;
  line-height:0; font-size:20px; box-shadow:0 2px 10px rgba(0,0,0,.25)
}
.wi-arrow:hover{background:#fff}
.wi-arrow--left{left:28px}
.wi-arrow--right{right:28px}

/* Dotovi = pravi krugovi pri dnu */
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
/* Ako koristiš .wi-below-h2 / .wi-below-p varijantu: */
.wi-below-h2{
  color:#000; line-height:1.06; letter-spacing:-.01em; text-wrap:balance;
  font-size:clamp(28px, 4.5vw, 60px) !important;  /* veći naslov */
  margin:0 0 32px !important;
}
/* Desktop “obavezno” 20px, crno, lep line-height – sa !important kako si tražio */
@media (min-width:900px){
  .wi-below-p{
    color:#000 !important;
    font-size:20px !important;
    line-height:1.6 !important;
    max-width:900px !important;
    margin:0 auto !important;
  }
}
/* Ako još u markupu postoji stari Tailwind h2 (text-[60px]) – override ga: */
@media (min-width:900px){
  .content-container .max-w-4xl h2.text-\[60px\].unbounded-bold{
    font-size:60px !important;
    margin-bottom:22px !important;
    color:#000 !important;
  }
}

/* ----- Responsive ----- */
@media (max-width:820px){
  .wi-hero{height:58vh; min-height:400px}
  .wi-h1{font-size:32px}
  .wi-h2{font-size:18px}
  .wi-p {font-size:14px}
}
@media (max-width:600px){
  .wi-dots{bottom:12px}
}
@media (max-width:480px){
  .wi-arrow{width:40px; height:40px; font-size:16px}
  .wi-arrow--left{left:12px}
  .wi-arrow--right{right:12px}
}
/* ===== HARD OVERRIDE: paragraf ispod slidera ===== */
section.content-container.mt-16 .max-w-4xl.text-center p,
section.content-container.mt-16 .max-w-4xl p {
  font-family: "Poppins", system-ui, sans-serif !important;
  font-size: 20px !important;        /* traženo: 20px na desktopu */
  line-height: 1.6 !important;
  color: #000 !important;
  max-width: 900px !important;
  margin: 0 auto !important;          /* skida onih 25px margine */
  letter-spacing: 0 !important;
}
</style>



<main class="content-container" style="padding-top:40px;padding-bottom:40px">
  <section class="content-container">
    <div class="wi-hero" id="wi-hero">
      <?php foreach ($slides as $i => $s):
        $h1 = esc_html($s['h1'] ?? '');
        $h2 = esc_html($s['h2'] ?? '');
        $tx = wp_kses_post($s['text'] ?? '');
        $bg_type = ($s['bg_type'] ?? 'image') === 'video' ? 'video' : 'image';
        $bg_url  = esc_url($s['bg_url'] ?? '');
      ?>
      <div class="wi-slide<?php echo $i===0 ? ' is-active' : ''; ?>">
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

      <button class="wi-arrow wi-arrow--left" aria-label="Prev">&#8249;</button>
      <button class="wi-arrow wi-arrow--right" aria-label="Next">&#8250;</button>

      <div class="wi-dots">
        <?php foreach ($slides as $i => $_): ?>
          <button class="wi-dot<?php echo $i===0 ? ' is-active' : ''; ?>" data-index="<?php echo (int)$i; ?>"></button>
        <?php endforeach; ?>
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

</main>

<script>
(function(){
  const root = document.getElementById('wi-hero');
  if(!root) return;
  const slides = Array.from(root.querySelectorAll('.wi-slide'));
  const dots   = Array.from(root.querySelectorAll('.wi-dot'));
  const prev   = root.querySelector('.wi-arrow--left');
  const next   = root.querySelector('.wi-arrow--right');
  let idx = 0, timer = null;
  function show(i){
    slides[idx].classList.remove('is-active');
    dots[idx].classList.remove('is-active');
    idx = (i + slides.length) % slides.length;
    slides[idx].classList.add('is-active');
    dots[idx].classList.add('is-active');
  }
  function auto(){ clearInterval(timer); timer = setInterval(()=> show(idx+1), 6000); }
  prev.addEventListener('click', ()=>{ show(idx-1); auto(); });
  next.addEventListener('click', ()=>{ show(idx+1); auto(); });
  dots.forEach(d => d.addEventListener('click', ()=>{ show(parseInt(d.dataset.index,10)); auto(); }));
  auto();
})();
</script>

<?php get_footer(); ?>
