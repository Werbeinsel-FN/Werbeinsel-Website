<?php
/**
 * Partial: Großflächenwerbung – hero (naslov + slider) + intro tekst (FORCIRAN)
 * Vidi promenljive iz parent template-a: $slug, $svc, $page_title, $page_id
 */

/* 1) Poseban CSS samo za ovu stranicu (možeš dodati kasnije) */
$css_path = get_stylesheet_directory() . '/css/service-grossflaechenwerbung.css';
$css_url  = get_stylesheet_directory_uri() . '/css/service-grossflaechenwerbung.css';
if (file_exists($css_path)) { $css_url .= '?v=' . filemtime($css_path); }
echo '<link rel="stylesheet" href="'.esc_url($css_url).'">';

/* 2) Meta iz admina – samo radi slidera; za INTRO NASLOV I TEKST ispod slidera ne koristimo meta */
if (function_exists('wi_get_gross_meta')) {
  $meta = wi_get_gross_meta($page_id);
} elseif (function_exists('wi_get_plakat_meta')) {
  $meta = wi_get_plakat_meta($page_id);
} else {
  $meta = [];
}

/* 3) Slike za slider (meta['hero_ids']) + fallback + dodaj $svc['image_url'] ako postoji */
$slides = [];
if (!empty($meta['hero_ids']) && is_array($meta['hero_ids'])) {
  foreach ($meta['hero_ids'] as $aid) {
    $u = wp_get_attachment_image_url((int)$aid, 'full');
    if ($u) $slides[] = $u;
  }
}
if (empty($slides)) {
  $slides = [
    'https://picsum.photos/id/1011/1600/900',
    'https://picsum.photos/id/1015/1600/900',
    'https://picsum.photos/id/1020/1600/900',
  ];
}
if (!empty($svc['image_url'])) { $slides[] = $svc['image_url']; }
$slides = array_values(array_unique(array_filter($slides)));

$carousel_id = ($slug ?: 'grossflaechenwerbung') . '-hero';

/* 4) NASLOV – uvek forsiramo za ovu stranicu */
$intro_title = "GROßFLÄCHEN\nWERBUNG";

/* 5) INTRO TEKST – uvek forsiramo TAČNO OVO (ignorišemo meta['intro_text']) */
$intro_text = 'Plakatwerbung ist eine der effektivsten Formen der Außenwerbung. Mit strategisch platzierten Plakaten erreichen Sie täglich tausende von potenziellen Kunden genau dort, wo sie leben, arbeiten und einkaufen. WERBEINSEL sorgt dafür, dass Ihre Botschaft im Straßenbild unübersehbar wird.';
?>

<!-- HERO (naslov + slider) -->
<section id="<?php echo esc_attr($carousel_id); ?>" class="svc-hero-carousel">
  <div class="svc-hero-carousel__wrap">

    <?php
    // H1 (linija po linija)
    echo '<h1 class="svc-intro__title">';
    $title_lines = preg_split('/\r\n|\r|\n/', $intro_title);
    foreach ($title_lines as $k => $line) {
      if ($line === '') continue;
      echo '<span>' . esc_html($line) . '</span>';
      if ($k < count($title_lines) - 1) echo '<br>';
    }
    echo '</h1>';
    ?>

    <div class="svc-hero-carousel__viewport">
      <?php foreach ($slides as $i => $url): ?>
        <div
          class="svc-hero-carousel__bg<?php echo $i === 0 ? ' is-active' : ''; ?>"
          style="background-image:url('<?php echo esc_url($url); ?>');"
          role="img"
          aria-label="Großflächenwerbung Slide <?php echo (int)$i+1; ?>">
        </div>
      <?php endforeach; ?>

      <button class="svc-hero-carousel__nav svc-hero-carousel__nav--prev" aria-label="Vorheriges Bild">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="svc-hero-carousel__icon" aria-hidden="true">
          <path d="m15 18-6-6 6-6" fill="none" stroke="currentColor" stroke-width="4" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
      </button>
      <button class="svc-hero-carousel__nav svc-hero-carousel__nav--next" aria-label="Nächstes Bild">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="svc-hero-carousel__icon" aria-hidden="true">
          <path d="m9 18 6-6-6-6" fill="none" stroke="currentColor" stroke-width="4" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
      </button>

      <div class="svc-hero-carousel__dots" role="tablist" aria-label="Slider Pagination">
        <?php foreach ($slides as $i => $_): ?>
          <button class="svc-hero-carousel__dot<?php echo $i === 0 ? ' is-active' : ''; ?>" data-index="<?php echo (int)$i; ?>" aria-label="Gehe zu Slide <?php echo (int)$i+1; ?>" role="tab"></button>
        <?php endforeach; ?>
      </div>
    </div>
  </div>
</section>

<!-- INTRO (tekst ispod slajdera) -->
<section class="svc-intro">
  <div class="content-container svc-intro__wrap">
    <p class="svc-intro__lead" data-len="<?php echo esc_attr( mb_strlen($intro_text, 'UTF-8') ); ?>">
      <?php echo esc_html($intro_text); ?>
    </p>
  </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function(){
  const root = document.getElementById('<?php echo esc_js($carousel_id); ?>');
  if (!root) return;

  let slides = Array.from(root.querySelectorAll('.svc-hero-carousel__bg'));

  // Preflight: ukloni slike koje ne mogu da se učitaju
  Promise.all(slides.map(el => new Promise(resolve => {
    const url = el.style.backgroundImage.replace(/^url\(["']?/, '').replace(/["']?\)$/, '');
    const img = new Image();
    img.onload  = () => resolve({el, ok:true});
    img.onerror = () => resolve({el, ok:false});
    img.src = url;
  }))).then(results => {
    results.filter(r => !r.ok).forEach(r => r.el.remove());
    slides = Array.from(root.querySelectorAll('.svc-hero-carousel__bg'));

    const dotsWrap = root.querySelector('.svc-hero-carousel__dots');
    if (dotsWrap) {
      dotsWrap.innerHTML = slides.map((_, i) =>
        `<button class="svc-hero-carousel__dot${i===0?' is-active':''}" data-index="${i}" role="tab" aria-label="Gehe zu Slide ${i+1}"></button>`
      ).join('');
    }
    const dots = root.querySelectorAll('.svc-hero-carousel__dot');
    const prev = root.querySelector('.svc-hero-carousel__nav--prev');
    const next = root.querySelector('.svc-hero-carousel__nav--next');

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
    root.addEventListener('mouseenter', ()=> clearInterval(t));
    root.addEventListener('mouseleave', ()=> t = setInterval(()=> show(i+1), 6000));
  });
});
</script>
