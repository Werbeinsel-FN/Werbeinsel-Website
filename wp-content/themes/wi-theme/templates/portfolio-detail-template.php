<?php
/**
 * Template Name: Portfolio Detail (Projekt aus „Unsere Arbeiten“)
 * Dizajn: Desktop/sajt – PortfolioDetailPage (1 von 6 Projekten)
 */
if (!function_exists('wi_nav_url')) {
  require_once get_template_directory() . '/inc/wi-nav-links.php';
}
require_once get_template_directory() . '/inc/portfolio-data.php';

$slug = get_post_field('post_name', get_the_ID());
$slug_to_slot = [
  'fahrzeugbeschriftung' => 1,
  'schaufenster-werbung' => 2,
  'grossflachenplakat' => 3,
  'leuchtreklame' => 4,
  'city-light-poster' => 5,
  'fassaden-beschriftung' => 6,
];
$slot = isset($slug_to_slot[$slug]) ? $slug_to_slot[$slug] : (int) get_post_meta(get_the_ID(), '_wi_portfolio_slot', true);
$projects = wi_get_portfolio_projects();
$project = ($slot >= 1 && $slot <= 6 && isset($projects[$slot])) ? $projects[$slot] : null;
$arbeiten_url = rtrim(home_url('/'), '/') . '/#arbeiten';

if (!$project) {
  if (!function_exists('wi_nav_url')) {
    require_once get_template_directory() . '/inc/wi-nav-links.php';
  }
  get_header();
  echo '<main class="wi-portfolio-detail"><div class="wi-portfolio-detail__container" style="padding:4rem 1rem;text-align:center;">';
  echo '<h1 style="font-family:Unbounded,sans-serif;margin-bottom:1rem;">Projekt nicht gefunden</h1>';
  echo '<a href="' . esc_url($arbeiten_url) . '" class="wi-portfolio-detail__cta-btn" style="display:inline-block;margin-top:1rem;">Zurück zu Projekten</a>';
  echo '</div></main>';
  get_footer();
  return;
}

$home_url = home_url('/');
$kontakt_url = wi_nav_url('kontakt');
$wi_info = get_option('wi_contact_info', array());
$wi_wa_url = !empty($wi_info['whatsapp_url']) ? esc_url_raw($wi_info['whatsapp_url']) : '';
get_header();
?>
<main class="wi-portfolio-detail">
  <!-- Breadcrumb -->
  <nav class="wi-portfolio-detail__breadcrumb" aria-label="Breadcrumb">
    <div class="wi-portfolio-detail__container">
      <ol class="wi-portfolio-detail__breadcrumb-list">
        <li><a href="<?php echo esc_url($home_url); ?>">Startseite</a></li>
        <li><a href="<?php echo esc_url($arbeiten_url); ?>">Unsere Arbeiten</a></li>
        <li><span aria-current="page"><?php echo esc_html($project['title']); ?></span></li>
      </ol>
    </div>
  </nav>

  <!-- Hero -->
  <section class="wi-portfolio-detail__hero">
    <div class="wi-portfolio-detail__container">
      <div class="wi-portfolio-detail__hero-grid">
        <div class="wi-portfolio-detail__hero-img-wrap">
          <img src="<?php echo esc_url($project['heroImage']); ?>" alt="<?php echo esc_attr($project['title']); ?>" class="wi-portfolio-detail__hero-img" loading="eager">
        </div>
        <div class="wi-portfolio-detail__hero-info">
          <div class="wi-portfolio-detail__badge"><?php echo esc_html($project['category']); ?></div>
          <h1 class="wi-portfolio-detail__title"><?php echo esc_html($project['title']); ?></h1>
          <div class="wi-portfolio-detail__meta-grid">
            <div class="wi-portfolio-detail__meta-box">
              <span class="wi-portfolio-detail__meta-label">Kunde</span>
              <span class="wi-portfolio-detail__meta-value"><?php echo esc_html($project['client']); ?></span>
            </div>
            <div class="wi-portfolio-detail__meta-box">
              <span class="wi-portfolio-detail__meta-label">Jahr</span>
              <span class="wi-portfolio-detail__meta-value"><?php echo esc_html($project['year']); ?></span>
            </div>
            <div class="wi-portfolio-detail__meta-box wi-portfolio-detail__meta-box--full">
              <span class="wi-portfolio-detail__meta-label">Standort</span>
              <span class="wi-portfolio-detail__meta-value"><?php echo esc_html($project['location']); ?></span>
            </div>
          </div>
          <div class="wi-portfolio-detail__services">
            <span class="wi-portfolio-detail__services-label">Leistungen</span>
            <div class="wi-portfolio-detail__services-tags">
              <?php foreach ($project['services'] as $s) : ?>
                <span class="wi-portfolio-detail__service-tag"><?php echo esc_html($s); ?></span>
              <?php endforeach; ?>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Challenge -->
  <section class="wi-portfolio-detail__section wi-portfolio-detail__section--yellow">
    <div class="wi-portfolio-detail__container">
      <div class="wi-portfolio-detail__narrow">
        <h2 class="wi-portfolio-detail__section-title">Die Herausforderung</h2>
        <p class="wi-portfolio-detail__section-text"><?php echo esc_html($project['challenge']); ?></p>
      </div>
    </div>
  </section>

  <!-- Solution -->
  <section class="wi-portfolio-detail__section wi-portfolio-detail__section--black">
    <div class="wi-portfolio-detail__container">
      <div class="wi-portfolio-detail__narrow">
        <h2 class="wi-portfolio-detail__section-title wi-portfolio-detail__section-title--yellow">Die Lösung</h2>
        <p class="wi-portfolio-detail__section-text wi-portfolio-detail__section-text--white"><?php echo esc_html($project['solution']); ?></p>
      </div>
    </div>
  </section>

  <!-- Results -->
  <section class="wi-portfolio-detail__section wi-portfolio-detail__section--white">
    <div class="wi-portfolio-detail__container">
      <h2 class="wi-portfolio-detail__section-title wi-portfolio-detail__section-title--center">Die Ergebnisse</h2>
      <div class="wi-portfolio-detail__results-grid">
        <?php foreach ($project['results'] as $r) : ?>
          <div class="wi-portfolio-detail__result-card">
            <span class="wi-portfolio-detail__result-value"><?php echo esc_html($r['value']); ?></span>
            <span class="wi-portfolio-detail__result-label"><?php echo esc_html($r['label']); ?></span>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  </section>

  <!-- Gallery -->
  <section class="wi-portfolio-detail__section wi-portfolio-detail__section--black">
    <div class="wi-portfolio-detail__container">
      <h2 class="wi-portfolio-detail__section-title wi-portfolio-detail__section-title--yellow wi-portfolio-detail__section-title--center">Weitere Impressionen</h2>
      <div class="wi-portfolio-detail__gallery-wrap">
        <div class="wi-portfolio-detail__gallery" id="wi-gallery-carousel" role="region" aria-label="Galerie">
          <?php foreach ($project['images'] as $idx => $img) : ?>
            <div class="wi-portfolio-detail__gallery-item" data-index="<?php echo (int) $idx; ?>" role="button" tabindex="0" aria-label="<?php echo sprintf(esc_attr__('Bild %d öffnen', 'wi-theme'), $idx + 1); ?>">
              <img src="<?php echo esc_url($img); ?>" alt="<?php echo esc_attr($project['title']); ?>" loading="lazy">
            </div>
          <?php endforeach; ?>
        </div>
        <div class="wi-portfolio-detail__gallery-dots" id="wi-gallery-dots" aria-hidden="true">
          <?php for ($i = 0; $i < count($project['images']); $i++) : ?>
            <button type="button" class="wi-portfolio-detail__gallery-dot<?php echo $i === 0 ? ' wi-portfolio-detail__gallery-dot--active' : ''; ?>" data-index="<?php echo $i; ?>" aria-label="<?php echo sprintf(esc_attr__('Slide %d', 'wi-theme'), $i + 1); ?>"></button>
          <?php endfor; ?>
        </div>
      </div>
    </div>
  </section>

  <!-- Lightbox popup za Weitere Impressionen -->
  <div class="wi-portfolio-detail__lightbox" id="wi-gallery-lightbox" role="dialog" aria-modal="true" aria-label="Galerie vergrößern" hidden>
    <div class="wi-portfolio-detail__lightbox-backdrop" id="wi-lightbox-backdrop"></div>
    <div class="wi-portfolio-detail__lightbox-inner">
      <button type="button" class="wi-portfolio-detail__lightbox-prev" id="wi-lightbox-prev" aria-label="Vorheriges Bild">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M15 18l-6-6 6-6"/></svg>
      </button>
      <div class="wi-portfolio-detail__lightbox-frame">
        <img class="wi-portfolio-detail__lightbox-img" id="wi-lightbox-img" src="" alt="">
      </div>
      <button type="button" class="wi-portfolio-detail__lightbox-next" id="wi-lightbox-next" aria-label="Nächstes Bild">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M9 18l6-6-6-6"/></svg>
      </button>
      <div class="wi-portfolio-detail__lightbox-counter" id="wi-lightbox-counter" aria-live="polite">1/3</div>
    </div>
    <?php if ($wi_wa_url) : ?>
    <div class="wi-portfolio-detail__lightbox-wa-wrap">
      <a href="<?php echo esc_attr($wi_wa_url); ?>" target="_blank" rel="noopener noreferrer" class="wi-floating-wa" aria-label="WhatsApp">
        <span class="wi-floating-wa__icon-wrap">
          <svg class="wi-floating-wa__icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" aria-hidden="true">
            <path fill="currentColor" d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
          </svg>
          <span class="wi-floating-wa__pulse" aria-hidden="true"></span>
        </span>
        <span class="wi-floating-wa__text">WhatsApp uns!</span>
      </a>
    </div>
    <?php endif; ?>
  </div>

  <!-- CTA -->
  <section class="wi-portfolio-detail__section wi-portfolio-detail__section--yellow">
    <div class="wi-portfolio-detail__container">
      <div class="wi-portfolio-detail__cta-inner">
        <h2 class="wi-portfolio-detail__cta-title">Ihr Projekt.<br>Unsere Expertise.</h2>
        <p class="wi-portfolio-detail__cta-text">Lassen Sie uns gemeinsam Ihre Vision verwirklichen –<br>von der ersten Idee bis zur erfolgreichen Kampagne.</p>
        <a href="<?php echo esc_url($kontakt_url); ?>" class="wi-portfolio-detail__cta-btn">Jetzt anfragen</a>
      </div>
    </div>
  </section>
</main>
<script>
(function() {
  var carousel = document.getElementById('wi-gallery-carousel');
  var dotsWrap = document.getElementById('wi-gallery-dots');
  if (!carousel || !dotsWrap) return;
  var dots = dotsWrap.querySelectorAll('.wi-portfolio-detail__gallery-dot');
  var items = carousel.querySelectorAll('.wi-portfolio-detail__gallery-item');
  if (dots.length === 0 || items.length === 0) return;

  function updateDots() {
    var scrollLeft = carousel.scrollLeft;
    var itemW = items[0] ? items[0].offsetWidth : carousel.offsetWidth;
    if (!itemW) return;
    var index = Math.round(scrollLeft / itemW);
    index = Math.max(0, Math.min(index, dots.length - 1));
    dots.forEach(function(dot, i) {
      dot.classList.toggle('wi-portfolio-detail__gallery-dot--active', i === index);
    });
  }

  function scrollToSlide(i) {
    var itemW = items[0] ? items[0].offsetWidth : carousel.offsetWidth;
    carousel.scrollTo({ left: itemW * i, behavior: 'smooth' });
  }

  carousel.addEventListener('scroll', function() { updateDots(); });

  dots.forEach(function(dot, i) {
    dot.addEventListener('click', function() { scrollToSlide(i); });
  });

  updateDots();
})();

(function() {
  var lightbox = document.getElementById('wi-gallery-lightbox');
  var backdrop = document.getElementById('wi-lightbox-backdrop');
  var lightboxImg = document.getElementById('wi-lightbox-img');
  var lightboxPrev = document.getElementById('wi-lightbox-prev');
  var lightboxNext = document.getElementById('wi-lightbox-next');
  var counterEl = document.getElementById('wi-lightbox-counter');
  var gallery = document.getElementById('wi-gallery-carousel');
  if (!lightbox || !gallery) return;
  var items = gallery.querySelectorAll('.wi-portfolio-detail__gallery-item');
  var total = items.length;
  var currentIndex = 0;

  function getImageSrc(index) {
    var item = items[index];
    var img = item ? item.querySelector('img') : null;
    return img ? img.src : '';
  }

  function openLightbox(index) {
    currentIndex = Math.max(0, Math.min(index, total - 1));
    lightboxImg.src = getImageSrc(currentIndex);
    counterEl.textContent = (currentIndex + 1) + '/' + total;
    lightbox.removeAttribute('hidden');
    document.body.style.overflow = 'hidden';
    lightboxPrev.focus();
  }

  function closeLightbox() {
    lightbox.setAttribute('hidden', '');
    document.body.style.overflow = '';
  }

  function goPrev() {
    currentIndex = currentIndex <= 0 ? total - 1 : currentIndex - 1;
    lightboxImg.src = getImageSrc(currentIndex);
    counterEl.textContent = (currentIndex + 1) + '/' + total;
  }

  function goNext() {
    currentIndex = currentIndex >= total - 1 ? 0 : currentIndex + 1;
    lightboxImg.src = getImageSrc(currentIndex);
    counterEl.textContent = (currentIndex + 1) + '/' + total;
  }

  items.forEach(function(item, i) {
    item.addEventListener('click', function() { openLightbox(i); });
    item.addEventListener('keydown', function(e) {
      if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); openLightbox(i); }
    });
  });
  backdrop.addEventListener('click', closeLightbox);
  lightboxPrev.addEventListener('click', goPrev);
  lightboxNext.addEventListener('click', goNext);
  lightbox.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') closeLightbox();
    if (e.key === 'ArrowLeft') goPrev();
    if (e.key === 'ArrowRight') goNext();
  });
})();
</script>
<?php get_footer(); ?>
