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
   <a href="<?php echo site_url('/kontakt'); ?>" 
   class="bg-[#ffed00] border-2 border-[#ffed00] rounded-full hover:bg-white hover:border-white transition-all transform hover:scale-105 poppins-extrabold text-black flex items-center justify-center mx-auto wi-prefooter-cta__btn">
  JA
</a>
  </div>
</section>
<section class="content-container wi-distribution mt-16">
  <div class="wi-dist-box">
    <div class="wi-dist-grid">
      <!-- Levo: tekst -->
      <div class="wi-dist-text">
        <h2 class="wi-dist-title">UNSER VERTEILERGEBIET</h2>
        <div class="wi-dist-copy">
          <p>Wir plakatieren in der gesamten Region Friedrichshafen und Umgebung. Unsere strategisch ausgewählten Standorte garantieren maximale Sichtbarkeit für Ihre Kampagne.</p>
          <p>Mit über 50 Premium-Standorten erreichen Sie täglich tausende von potenziellen Kunden an hochfrequentierten Verkehrsknotenpunkten, Einkaufszentren und zentralen Stadtbereichen.</p>
        </div>
      </div>

      <!-- Desno: samo mapa -->
      <div class="wi-dist-map">
        <div class="wi-map-embed">
          <iframe
            src="https://www.google.com/maps/d/embed?mid=1ir7B-FiRZNb2WciFOsgTqt4zUKkmh8w&ehbc=2E312F"
            loading="lazy"
            referrerpolicy="no-referrer-when-downgrade"
            title="Verteilergebiet Karte">
          </iframe>
        </div>
      </div>
    </div>
  </div>
</section>
<section class="content-container wi-faq">
  <div class="wi-faq-head">
    <h2 class="wi-faq-title">HÄUFIGE FRAGEN</h2>
    <p class="wi-faq-sub">Antworten auf die wichtigsten Fragen zur Plakatwerbung</p>
  </div>

  <div class="wi-faq-list">

    <details class="wi-faq-item">
      <summary class="wi-faq-summary">
        <h3 class="wi-faq-q">Wie lange im Voraus sollte ich meine Plakatwerbung buchen?</h3>
        <svg class="wi-faq-icon" viewBox="0 0 24 24" aria-hidden="true">
          <path class="h" d="M5 12h14" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
          <path class="v" d="M12 5v14" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
        </svg>
      </summary>
      <div class="wi-faq-answer">
        <p>Idealerweise 2–4&nbsp;Wochen im Voraus. Bei größeren Kampagnen empfehlen wir mehr Vorlauf, damit Standorte optimal geplant werden können.</p>
      </div>
    </details>

    <details class="wi-faq-item">
      <summary class="wi-faq-summary">
        <h3 class="wi-faq-q">Welche Plakatgrößen bieten Sie an?</h3>
        <svg class="wi-faq-icon" viewBox="0 0 24 24" aria-hidden="true">
          <path class="h" d="M5 12h14" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
          <path class="v" d="M12 5v14" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
        </svg>
      </summary>
      <div class="wi-faq-answer">
        <p>Gängige Formate sind DIN&nbsp;A1 und DIN&nbsp;A0. Sonderformate sind nach Absprache möglich.</p>
      </div>
    </details>

    <details class="wi-faq-item">
      <summary class="wi-faq-summary">
        <h3 class="wi-faq-q">Erstellen Sie auch das Design für die Plakate?</h3>
        <svg class="wi-faq-icon" viewBox="0 0 24 24" aria-hidden="true">
          <path class="h" d="M5 12h14" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
          <path class="v" d="M12 5v14" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
        </svg>
      </summary>
      <div class="wi-faq-answer">
        <p>Ja. Unser Grafikteam erstellt auf Wunsch ein wirkungsstarkes Layout inkl. Druckdaten.</p>
      </div>
    </details>

    <details class="wi-faq-item">
      <summary class="wi-faq-summary">
        <h3 class="wi-faq-q">Wie wählen Sie die Standorte für meine Plakate aus?</h3>
        <svg class="wi-faq-icon" viewBox="0 0 24 24" aria-hidden="true">
          <path class="h" d="M5 12h14" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
          <path class="v" d="M12 5v14" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
        </svg>
      </summary>
      <div class="wi-faq-answer">
        <p>Auf Basis von Zielgruppe, Frequenz und Sichtachsen wählen wir Premium-Standorte mit hoher Reichweite.</p>
      </div>
    </details>

    <details class="wi-faq-item">
      <summary class="wi-faq-summary">
        <h3 class="wi-faq-q">Was passiert bei schlechtem Wetter oder Vandalismus?</h3>
        <svg class="wi-faq-icon" viewBox="0 0 24 24" aria-hidden="true">
          <path class="h" d="M5 12h14" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
          <path class="v" d="M12 5v14" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
        </svg>
      </summary>
      <div class="wi-faq-answer">
        <p>Wir kontrollieren regelmäßig. Beschädigte Plakate werden nach Absprache zeitnah ersetzt.</p>
      </div>
    </details>

  </div>
</section>



<?php get_footer(); ?>
