<?php
/**
 * Partial: Transit Advertising – hero (naslov + slider) + intro (editabilno)
 * Vidi: $slug, $svc, $page_title, $page_id
 */

/* Page-specific CSS (opciono) */
$css_path = get_stylesheet_directory() . '/css/service-transit-advertising.css';
$css_url  = get_stylesheet_directory_uri() . '/css/service-transit-advertising.css';
if (file_exists($css_path)) { $css_url .= '?v=' . filemtime($css_path); }
echo '<link rel="stylesheet" href="'.esc_url($css_url).'">';

/* Meta (hero_ids + intro_text) */
$meta = function_exists('wi_get_transit_meta') ? wi_get_transit_meta($page_id) : [];

/* Slike za slider */
$slides = [];
if (!empty($meta['hero_ids']) && is_array($meta['hero_ids'])) {
  foreach ($meta['hero_ids'] as $aid) {
    $u = wp_get_attachment_image_url((int)$aid, 'full');
    if ($u) $slides[] = $u;
  }
}
if (empty($slides)) {
  $slides = [
    'https://picsum.photos/id/1200/1600/900',
    'https://picsum.photos/id/1201/1600/900',
    'https://picsum.photos/id/1202/1600/900',
  ];
}
if (!empty($svc['image_url'])) { $slides[] = $svc['image_url']; }
$slides = array_values(array_unique(array_filter($slides)));

$carousel_id = ($slug ?: 'transit-advertising') . '-hero';

/* Naslov (forsiran na 2 reda) */
$intro_title = "TRANSIT\nADVERTISING";

/* Intro tekst iz metabox-a + fallback (prikaz bez skraćivanja) */
$intro_text = !empty($meta['intro_text'])
  ? $meta['intro_text']
  : 'Transit Advertising bringt Ihre Botschaft in Bewegung – auf Bussen, Straßenbahnen und Zügen. WERBEINSEL konzipiert, produziert und montiert großflächige Folierungen und Teilflächen inklusive Genehmigungen, Logistik und Wartung. Maximale Sichtbarkeit im Alltag – dort, wo Menschen unterwegs sind.';
?>

<section id="<?php echo esc_attr($carousel_id); ?>" class="svc-hero-carousel">
  <div class="svc-hero-carousel__wrap">

    <!-- H1 naslov -->
    <h1 class="svc-intro__title">
      <?php
        $lines = preg_split('/\r\n|\r|\n/', $intro_title);
        foreach ($lines as $k => $line) {
          if ($line === '') continue;
          echo '<span>'.esc_html($line).'</span>';
          if ($k < count($lines)-1) echo '<br>';
        }
      ?>
    </h1>

    <!-- VIEWPORT: nav + dots unutra, da su strelice centrirane po visini -->
    <div class="svc-hero-carousel__viewport">
      <?php foreach ($slides as $i => $url): ?>
        <div
          class="svc-hero-carousel__bg<?php echo $i === 0 ? ' is-active' : ''; ?>"
          style="background-image:url('<?php echo esc_url($url); ?>');"
          role="img"
          aria-label="Transit Advertising Slide <?php echo (int)$i+1; ?>">
        </div>
      <?php endforeach; ?>

      <!-- Strelice (izgled preuzimaš iz globalnog CSS-a, ostaje identičan) -->
      <button class="svc-hero-carousel__nav svc-hero-carousel__nav--prev" aria-label="Vorheriges Bild">
        <svg viewBox="0 0 24 24" class="svc-hero-carousel__icon" aria-hidden="true">
          <path d="m15 18-6-6 6-6" fill="none" stroke="currentColor" stroke-width="4" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
      </button>
      <button class="svc-hero-carousel__nav svc-hero-carousel__nav--next" aria-label="Nächstes Bild">
        <svg viewBox="0 0 24 24" class="svc-hero-carousel__icon" aria-hidden="true">
          <path d="m9 18 6-6-6-6" fill="none" stroke="currentColor" stroke-width="4" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
      </button>

      <!-- Tačkice -->
      <div class="svc-hero-carousel__dots" role="tablist" aria-label="Slider Pagination"></div>
    </div>
  </div>
</section>

<!-- INTRO: ceo tekst iz metabox-a, sačuvani novi redovi -->
<section class="svc-intro">
  <div class="content-container svc-intro__wrap">
    <div class="svc-intro__lead">
      <?php echo wp_kses_post( wpautop( $intro_text ) ); ?>
    </div>
  </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function(){
  const root = document.getElementById('<?php echo esc_js($carousel_id); ?>');
  if (!root) return;

  let slides = Array.from(root.querySelectorAll('.svc-hero-carousel__bg'));

  // Preflight: ukloni nevalidne slike → kreiraj dots
  Promise.all(slides.map(el => new Promise(resolve => {
    const url = el.style.backgroundImage.replace(/^url\(["']?/, '').replace(/["']?\)$/, '');
    const img = new Image();
    img.onload  = () => resolve({el, ok:true});
    img.onerror = () => resolve({el, ok:false});
    img.src = url;
  }))).then(results => {
    results.filter(r => !r.ok).forEach(r => r.el.remove());
    slides = Array.from(root.querySelectorAll('.svc-hero-carousel__bg'));

    const viewport = root.querySelector('.svc-hero-carousel__viewport');
    const dotsWrap = viewport.querySelector('.svc-hero-carousel__dots');
    if (dotsWrap) {
      dotsWrap.innerHTML = slides.map((_, i) =>
        `<button class="svc-hero-carousel__dot${i===0?' is-active':''}" data-index="${i}" role="tab" aria-label="Gehe zu Slide ${i+1}"></button>`
      ).join('');
    }

    const dots = viewport.querySelectorAll('.svc-hero-carousel__dot');
    const prev = viewport.querySelector('.svc-hero-carousel__nav--prev');
    const next = viewport.querySelector('.svc-hero-carousel__nav--next');

    slides.forEach((el, k)=> el.classList.toggle('is-active', k===0));

    let i = 0, n = slides.length;
    function show(idx){
      if (!n) return;
      i = (idx + n) % n;
      slides.forEach((el, k)=> el.classList.toggle('is-active', k===i));
      dots.forEach((el, k)=> el.classList.toggle('is-active',  k===i));
    }

    prev && prev.addEventListener('click', ()=> show(i-1));
    next && next.addEventListener('click', ()=> show(i+1));
    dots.forEach(d => d.addEventListener('click', ()=> show(parseInt(d.dataset.index||'0',10))));

    let t = setInterval(()=> show(i+1), 6000);
    viewport.addEventListener('mouseenter', ()=> clearInterval(t));
    viewport.addEventListener('mouseleave', ()=> t = setInterval(()=> show(i+1), 6000));
  });
});
</script>
