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
wp_enqueue_style(
  'wi-hero',
  get_stylesheet_directory_uri() . '/css/singleservice.css',
  [],
  filemtime( get_stylesheet_directory() . '/css/singleservice.css' )
);
wp_enqueue_script(
  'wi-hero',
  get_stylesheet_directory_uri() . '/js/singleservice.js',
  [],
  filemtime( get_stylesheet_directory() . '/js/singleservice.js' ),
  true // u footeru
);
get_header();
?>

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
        <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-clipboard w-10 h-10 text-black" aria-hidden="true"><rect width="8" height="4" x="8" y="2" rx="1" ry="1"></rect><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"></path></svg>
      </div>
      <h3>1. PLANEN</h3>
      <p>Gemeinsam definieren wir Ihre Ziele, Zielgruppe und Budget. Wir analysieren die besten Standorte für Ihre Kampagne.</p>
    </div>

    <!-- Kartica 2 -->
    <div class="card">
      <div class="card-icon">
       <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-truck w-10 h-10 text-black" aria-hidden="true"><path d="M14 18V6a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2v11a1 1 0 0 0 1 1h2"></path><path d="M15 18H9"></path><path d="M19 18h2a1 1 0 0 0 1-1v-3.65a1 1 0 0 0-.22-.624l-3.48-4.35A1 1 0 0 0 17.52 8H14"></path><circle cx="17" cy="18" r="2"></circle><circle cx="7" cy="18" r="2"></circle></svg>
      </div>
      <h3>2. PLAKATIEREN</h3>
      <p>Unser erfahrenes Team klebt Ihre Plakate professionell und termingerecht an den ausgewählten Standorten.</p>
    </div>

    <!-- Kartica 3 -->
    <div class="card">
      <div class="card-icon">
        <svg xmlns="http://www.w3.org/2000/svg" class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <path d="M21.801 10A10 10 0 1 1 17 3.335"></path>
          <path d="m9 11 3 3L22 4"></path>
        </svg>
      </div>
      <h3>3. BELEGEN</h3>
      <p>Sie erhalten eine lückenlose Dokumentation mit Fotos aller Plakatierungen als Nachweis für Ihre Kampagne.</p>
    </div>

  </div>
</section>


</main>
<!-- Prefooter CTA -->
<section class="bg-black w-full py-16 mt-16 wi-prefooter-cta">
  <div class="content-container text-center overflow-hidden">
    <h2 class="unbounded-bold text-white mb-8 leading-none whitespace-nowrap wi-prefooter-cta__title">
      BEREIT FÜR MAXIMUM IMPACT?
    </h2>
    <button class="bg-[#ffed00] border-2 border-[#ffed00] rounded-full hover:bg-white hover:border-white transition-all transform hover:scale-105 poppins-extrabold text-black flex items-center justify-center mx-auto wi-prefooter-cta__btn">
      JA
    </button>
  </div>
</section>


<?php get_footer(); ?>
