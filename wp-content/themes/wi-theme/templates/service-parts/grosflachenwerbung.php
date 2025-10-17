<?php
/**
 * Partial: Großflächenwerbung – hero + slider + intro (editabilan iz metaboxa)
 * Vidi: $slug, $svc, $page_title, $page_id
 */

/* 1) Page-specifični CSS (ostavi ako već postoji tvoj fajl) */
$css_path = get_stylesheet_directory() . '/css/service-grossflaechenwerbung.css';
$css_url  = get_stylesheet_directory_uri() . '/css/service-grossflaechenwerbung.css';
if (file_exists($css_path)) { $css_url .= '?v=' . filemtime($css_path); }
echo '<link rel="stylesheet" href="'.esc_url($css_url).'">';

/* 2) Meta: slider + INTRO TEKST iz functions.php (wi_get_gross_meta) */
$meta = function_exists('wi_get_gross_meta') ? wi_get_gross_meta($page_id) : [];
$slides = [];

/* slider slike iz meta */
if (!empty($meta['hero_ids']) && is_array($meta['hero_ids'])) {
  foreach ($meta['hero_ids'] as $aid) {
    $u = wp_get_attachment_image_url((int)$aid, 'full');
    if ($u) $slides[] = $u;
  }
}
/* fallback ako nema ništa */
if (empty($slides)) {
  $slides = [
    'https://picsum.photos/id/1011/1600/900',
    'https://picsum.photos/id/1015/1600/900',
    'https://picsum.photos/id/1020/1600/900',
  ];
}
/* dodaj featured iz plugina ako postoji */
if (!empty($svc['image_url'])) { $slides[] = $svc['image_url']; }
$slides = array_values(array_unique(array_filter($slides)));

$carousel_id = ($slug ?: 'grossflaechenwerbung') . '-hero';

/* 3) NASLOV – i dalje forsiran (kako si hteo) */
$intro_title = "GROßFLÄCHEN\nWERBUNG";

/* 4) INTRO TEKST – sada iz metaboxa (sa fallbackom) */
$intro_text = isset($meta['intro_text']) && $meta['intro_text'] !== ''
  ? $meta['intro_text']
  : 'Plakatwerbung ist eine der effektivsten Formen der Außenwerbung. Mit strategisch platzierten Plakaten erreichen Sie täglich tausende von potenziellen Kunden genau dort, wo sie leben, arbeiten und einkaufen. WERBEINSEL sorgt dafür, dass Ihre Botschaft im Straßenbild unübersehbar wird.';
?>

<section id="<?php echo esc_attr($carousel_id); ?>" class="svc-hero-carousel">
  <div class="svc-hero-carousel__wrap">
    <?php
      echo '<h1 class="svc-intro__title">';
      foreach (preg_split('/\r\n|\r|\n/', $intro_title) as $k => $line) {
        if ($line === '') continue;
        echo '<span>'.esc_html($line).'</span>';
        if ($k < 1) echo '<br>';
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

  <!-- NAV strelice i dots PREMEŠTENE OVDE -->
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

  <div class="svc-hero-carousel__dots" role="tablist" aria-label="Slider Pagination"></div>
</div>

</section>

<!-- INTRO: koristi KORISNIČKI unos iz metaboxa, bez skraćivanja; poštuj nove redove -->
<section class="svc-intro" style="position:relative; z-index:5;">
  <div class="content-container svc-intro__wrap" style="overflow:visible;">
    <div class="svc-intro__lead"
         style="display:block !important; overflow:visible !important; height:auto !important; max-height:none !important; -webkit-line-clamp:unset !important; line-clamp:unset !important; -webkit-box-orient:unset !important; white-space:normal !important; mask-image:none !important; -webkit-mask-image:none !important;">
      <?php
        // dozvoli bezbedan HTML iz metabox-a i sačuvaj nove redove
        echo wp_kses_post( wpautop( $intro_text ) );
      ?>
    </div>
  </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function(){
  const root = document.getElementById('<?php echo esc_js($carousel_id); ?>');
  if (!root) return;

  let slides = Array.from(root.querySelectorAll('.svc-hero-carousel__bg'));
  // Preflight slika
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
