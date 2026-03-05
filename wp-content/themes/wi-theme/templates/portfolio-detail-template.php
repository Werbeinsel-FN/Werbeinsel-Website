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
          <?php foreach ($project['images'] as $img) : ?>
            <div class="wi-portfolio-detail__gallery-item">
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
</script>
<?php get_footer(); ?>
