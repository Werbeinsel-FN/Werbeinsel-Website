<?php
/**
 * Partial: Corporate Design – hero (naslov + slider) + intro tekst (FORCIRAN)
 * Vidi promenljive iz parent template-a: $slug, $svc, $page_title, $page_id
 */

/* 1) Poseban CSS samo za ovu stranicu */
$css_path = get_stylesheet_directory() . '/css/service-corporate-design.css';
$css_url  = get_stylesheet_directory_uri() . '/css/service-corporate-design.css';
if (file_exists($css_path)) { $css_url .= '?v=' . filemtime($css_path); }
echo '<link rel="stylesheet" href="'.esc_url($css_url).'">';

/* 2) Meta – SAMO za slider (hero_ids). Naslov/intro su forsirani. */
if (function_exists('wi_get_corporate_meta')) {
  $meta = wi_get_corporate_meta($page_id);
} elseif (function_exists('wi_get_plakat_meta')) {
  $meta = wi_get_plakat_meta($page_id);
} else {
  $meta = [];
}

/* 3) Slike za slider */
$slides = [];
if (!empty($meta['hero_ids']) && is_array($meta['hero_ids'])) {
  foreach ($meta['hero_ids'] as $aid) {
    $u = wp_get_attachment_image_url((int)$aid, 'full');
    if ($u) $slides[] = $u;
  }
}
if (empty($slides)) {
  // fallback – zameni realnim vizualima po želji
  $slides = [
    'https://picsum.photos/id/1074/1600/900',
    'https://picsum.photos/id/1070/1600/900',
    'https://picsum.photos/id/1069/1600/900',
  ];
}
if (!empty($svc['image_url'])) { $slides[] = $svc['image_url']; }
$slides = array_values(array_unique(array_filter($slides)));

$carousel_id = ($slug ?: 'corporate-design') . '-hero';

/* 4) NASLOV – uvek forsiran */
$intro_title = "CORPORATE\nDESIGN";

/* 5) INTRO TEKST – uvek forsiran (ignorišemo meta['intro_text']) */
$intro_text = 'Corporate Design macht Ihre Marke sofort erkennbar: Logo, Typografie, Farben, Bildwelt und Layoutregeln greifen nahtlos ineinander und schaffen Konsistenz über alle Kanäle. WERBEINSEL entwickelt und dokumentiert Ihr Erscheinungsbild – von Brand Basics bis zum Styleguide – damit jede Anwendung professionell, einheitlich und wirkungsvoll bleibt.';
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

    <!-- viewport ispod teksta (da ništa ne preklapa intro) -->
    <div class="svc-hero-carousel__viewport" style="z-index:1; pointer-events:none;">
      <?php foreach ($slides as $i => $url): ?>
        <div
          class="svc-hero-carousel__bg<?php echo $i === 0 ? ' is-active' : ''; ?>"
          style="background-image:url('<?php echo esc_url($url); ?>');"
          role="img"
          aria-label="Corporate Design Slide <?php echo (int)$i+1; ?>">
        </div>
      <?php endforeach; ?>
    </div>

    <!-- Navigacija i tačkice iznad viewporta, ostaju klikalni -->
    <button class="svc-hero-carousel__nav svc-hero-carousel__nav--prev" aria-label="Vorheriges Bild" style="z-index:3;">
      <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="svc-hero-carousel__icon" aria-hidden="true">
        <path d="m15 18-6-6 6-6" fill="none" stroke="currentColor" stroke-width="4" stroke-linecap="round" stroke-linejoin="round"/>
      </svg>
    </button>
    <button class="svc-hero-carousel__nav svc-hero-carousel__nav--next" aria-label="Nächstes Bild" style="z-index:3;">
      <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="svc-hero-carousel__icon" aria-hidden="true">
        <path d="m9 18 6-6-6-6" fill="none" stroke="currentColor" stroke-width="4" stroke-linecap="round" stroke-linejoin="round"/>
      </svg>
    </button>

    <div class="svc-hero-carousel__dots" role="tablist" aria-label="Slider Pagination" style="z-index:3;">
      <?php foreach ($slides as $i => $_): ?>
        <button class="svc-hero-carousel__dot<?php echo $i === 0 ? ' is-active' : ''; ?>" data-index="<?php echo (int)$i; ?>" aria-label="Gehe zu Slide <?php echo (int)$i+1; ?>" role="tab"></button>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- INTRO (forsiran + anti-clamp) -->
<section class="svc-intro" style="position:relative; z-index:5;">
  <div class="content-container svc-intro__wrap" style="overflow:visible;">
    <p class="svc-intro__lead"
       data-len="<?php echo esc_attr( mb_strlen($intro_text, 'UTF-8') ); ?>"
       style="display:block !important; overflow:visible !important; height:auto !important; max-height:none !important; -webkit-line-clamp:unset !important; line-clamp:unset !important; -webkit-box-orient:unset !important; white-space:normal !important; mask-image:none !important; -webkit-mask-image:none !important;">
      <?php echo esc_html($intro_text); ?>
    </p>
  </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function(){
  const root = document.getElementById('<?php echo esc_js($carousel_id); ?>');
  if (!root) return;

  // Slider init + preflight
  let slides = Array.from(root.querySelectorAll('.svc-hero-carousel__bg'));
  Promise.all(slides.map(el => new Promise(resolve => {
    const url = el.style.backgroundImage.replace(/^url\(["']?/, '').replace(/["']?\)$/, '');
    const img = new Image();
    img.onload  = () => resolve({el, ok:true});
    img.onerror = () => resolve({el, ok:false});
    img.src = url;
  }))).then(results => {
    results.filter(r => !r.ok).forEach(r => r.el.remove());
    slides = Array.from(root.querySelectorAll('.svc-hero-carousel__bg'));

    const wrap = root.closest('.svc-hero-carousel__wrap');
    const dotsWrap = wrap.querySelector('.svc-hero-carousel__dots');
    if (dotsWrap) {
      dotsWrap.innerHTML = slides.map((_, i) =>
        `<button class="svc-hero-carousel__dot${i===0?' is-active':''}" data-index="${i}" role="tab" aria-label="Gehe zu Slide ${i+1}"></button>`
      ).join('');
    }
    const dots = wrap.querySelectorAll('.svc-hero-carousel__dot');
    const prev = wrap.querySelector('.svc-hero-carousel__nav--prev');
    const next = wrap.querySelector('.svc-hero-carousel__nav--next');

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
    wrap.addEventListener('mouseenter', ()=> clearInterval(t));
    wrap.addEventListener('mouseleave', ()=> t = setInterval(()=> show(i+1), 6000));
  });

  // Anti-clamp guard
  const lead = document.querySelector('.svc-intro__lead');
  if (lead) {
    lead.classList.forEach(c => { if (/clamp|truncate|short|collapsed/i.test(c)) lead.classList.remove(c); });
    ['maxHeight','height','webkitLineClamp','maskImage','webkitMaskImage'].forEach(p => { try { lead.style[p] = ''; } catch(e) {} });
    const wrap = lead.closest('.svc-intro__wrap');
    if (wrap) wrap.style.overflow = 'visible';
    const section = lead.closest('.svc-intro');
    if (section) { section.style.position = 'relative'; section.style.zIndex = 5; }
  }
});
</script>
