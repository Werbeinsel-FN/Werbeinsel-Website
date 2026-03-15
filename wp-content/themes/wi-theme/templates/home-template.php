<?php
/**
 * Template Name: Home
 * Description: Home template – dizajn iz Desktop/sajt (Hero, About, Services, Portfolio, Clients, Testimonials, CTA)
 */

get_header(); ?>

<link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/css/home.css?v=<?php echo filemtime(get_template_directory() . '/css/home.css'); ?>">

<?php
// === HERO ===
$hero_bg_id = get_post_meta(get_the_ID(), '_wi_hero_bg_image_id', true);
if (!$hero_bg_id && has_post_thumbnail()) $hero_bg_id = get_post_thumbnail_id(get_the_ID());
$hero_bg_url = $hero_bg_id ? wp_get_attachment_image_url($hero_bg_id, 'full') : '';
$hero_title = get_post_meta(get_the_ID(), '_wi_hero_title', true) ?: "Ihre Werbung.<br>Unser<br>Handwerk.";
$hero_subtitle = get_post_meta(get_the_ID(), '_wi_hero_subtitle', true) ?: "Plakat. Folie. Digital.<br>Sichtbarkeit für Marken in der Region.";
$hero_btn1_text = get_post_meta(get_the_ID(), '_wi_hero_btn1_text', true) ?: 'Projekt anfragen';
$hero_btn1_link = get_post_meta(get_the_ID(), '_wi_hero_btn1_link', true);
if (!$hero_btn1_link) {
    $cp = get_page_by_path('kontakt') ?: get_page_by_path('contact');
    $hero_btn1_link = $cp ? get_permalink($cp->ID) : home_url('/kontakt/');
}
$hero_btn2_text = get_post_meta(get_the_ID(), '_wi_hero_btn2_text', true) ?: 'Services ansehen';
$hero_btn2_link = get_post_meta(get_the_ID(), '_wi_hero_btn2_link', true) ?: '#services';
$hero_fallback = 'https://images.unsplash.com/photo-1763168555657-00921f695ffd?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&q=80&w=1920';
?>
<main id="home-main" class="home-main">

<header id="home-hero" class="home-hero home-hero--design">
  <div class="home-hero__bg" style="background-image: linear-gradient(to right, rgba(0,0,0,0.1), rgba(0,0,0,0.1)), url('<?php echo esc_attr(esc_url($hero_bg_url ?: $hero_fallback)); ?>');"></div>
  <div class="home-hero__container">
    <div class="home-hero__content">
      <div class="home-hero__box">
        <h1 class="home-hero__title"><?php echo wp_kses_post($hero_title); ?></h1>
        <p class="home-hero__subtitle"><?php echo wp_kses_post(nl2br($hero_subtitle)); ?></p>
        <div class="home-hero__buttons">
          <a href="<?php echo esc_url($hero_btn1_link); ?>" class="home-hero__btn home-hero__btn--primary"><?php echo esc_html($hero_btn1_text); ?></a>
          <a href="<?php echo esc_url($hero_btn2_link); ?>" class="home-hero__btn home-hero__btn--secondary"><?php echo esc_html($hero_btn2_text); ?></a>
        </div>
      </div>
    </div>
  </div>
</header>

<?php
// === ABOUT ===
$about_title = get_post_meta(get_the_ID(), '_wi_about_title', true);
$about_para1 = get_post_meta(get_the_ID(), '_wi_about_para1', true);
$about_para2 = get_post_meta(get_the_ID(), '_wi_about_para2', true);
if ($about_title === '') $about_title = "Ihre Agentur für<br>klassische Werbung";
if ($about_para1 === '') $about_para1 = "WERBEINSEL steht für klare Kommunikation<br>und starke visuelle Präsenz.";
if ($about_para2 === '') $about_para2 = "Wir entwickeln, produzieren und montieren<br>klassische Außenwerbung – regional verankert,<br>professionell umgesetzt.";
if ($about_title || $about_para1 || $about_para2):
?>
<section id="about" class="home-about">
  <div class="home-section__container">
    <div class="home-about__inner">
      <?php if ($about_title): ?><h2 class="home-about__title"><?php echo wp_kses_post(nl2br($about_title)); ?></h2><?php endif; ?>
      <?php if ($about_para1): ?><p class="home-about__para"><?php echo wp_kses_post(nl2br($about_para1)); ?></p><?php endif; ?>
      <?php if ($about_para2): ?><p class="home-about__para home-about__para--mt"><?php echo wp_kses_post(nl2br($about_para2)); ?></p><?php endif; ?>
    </div>
  </div>
</section>
<?php endif; ?>

<?php
// === SERVICES (4 kartice) ===
$services_title_raw = trim((string) get_post_meta(get_the_ID(), '_wi_services_title', true));
$replace_with_default = in_array(strtolower($services_title_raw), ['services', 'servicesa'], true);
$services_title = ($services_title_raw && !$replace_with_default) ? $services_title_raw : 'Was wir machen';
$svc_page = get_page_by_path('services') ?: get_page_by_path('leistungen');
$svc_base = $svc_page ? get_permalink($svc_page) : home_url('/services/');
$def_links = [$svc_base . '#plakatwerbung', $svc_base . '#lass-kleben', $svc_base . '#pixel-code', $svc_base . '#print-design'];
$def_imgs = [
  'https://images.unsplash.com/photo-1759683935078-385fad4fcd95?w=1080&q=80',
  'https://images.unsplash.com/photo-1751958034607-8328df6932cf?w=1080&q=80',
  'https://images.unsplash.com/photo-1764795850248-97a5e986b242?w=1080&q=80',
  'https://images.unsplash.com/photo-1617380607001-2797ed957a6f?w=1080&q=80'
];
$def_titles = ['Plakatwerbung', 'Folierung & Beschriftung', 'Digitale Werbemittel', 'Drucksachen'];
$def_descs = ['Auffällig. Präsent. Wirkungsvoll.', 'Fahrzeuge. Schaufenster. Fassaden.', 'Screens. Social Media. Online-Kampagnen.', 'Flyer. Broschüren. Geschäftsausstattung.'];
$bad_values = ['servicea', 'servicesa', 'services', 'service'];
$services_cards = [];
for ($i = 1; $i <= 4; $i++) {
  $img_id = get_post_meta(get_the_ID(), "_wi_services_{$i}_img_id", true);
  $img = $img_id ? wp_get_attachment_image_url($img_id, 'large') : $def_imgs[$i-1];
  $title_raw = get_post_meta(get_the_ID(), "_wi_services_{$i}_title", true) ?: $def_titles[$i-1];
  $title = ($i <= 3 && in_array(strtolower(trim((string) $title_raw)), $bad_values, true)) ? $def_titles[$i-1] : $title_raw;
  $desc = get_post_meta(get_the_ID(), "_wi_services_{$i}_desc", true) ?: $def_descs[$i-1];
  $link = get_post_meta(get_the_ID(), "_wi_services_{$i}_link", true) ?: $def_links[$i-1];
  $services_cards[] = ['img' => $img, 'title' => $title, 'desc' => $desc, 'link' => $link];
}
?>
<section id="services" class="home-services">
  <div class="home-section__container">
    <h2 class="home-services__title"><?php echo esc_html($services_title); ?></h2>
    <div class="home-services__grid">
      <?php foreach ($services_cards as $c): ?>
      <a href="<?php echo esc_url($c['link']); ?>" class="home-services__card">
        <div class="home-services__card-bg" style="background-image: linear-gradient(to top, rgba(0,0,0,0.85), rgba(0,0,0,0.2)), url('<?php echo esc_attr(esc_url($c['img'])); ?>');"></div>
        <div class="home-services__card-content">
          <h3 class="home-services__card-title"><?php echo esc_html($c['title']); ?></h3>
          <?php if ($c['desc']): ?><p class="home-services__card-desc"><?php echo esc_html($c['desc']); ?></p><?php endif; ?>
        </div>
      </a>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<?php
// === PORTFOLIO (6 projekata) ===
$port_title = get_post_meta(get_the_ID(), '_wi_portfolio_title', true) ?: "Unsere<br>Arbeiten";
$port_subtitle = get_post_meta(get_the_ID(), '_wi_portfolio_subtitle', true) ?: "Von Fahrzeugbeschriftung bis Großflächenplakat –<br>Ihre Marke im Mittelpunkt.";
$port_btn = get_post_meta(get_the_ID(), '_wi_portfolio_btn_text', true) ?: 'Ihr Projekt starten';
$port_btn_link = get_post_meta(get_the_ID(), '_wi_portfolio_btn_link', true);
if (!$port_btn_link) {
  $cp = get_page_by_path('kontakt') ?: get_page_by_path('contact');
  $port_btn_link = $cp ? get_permalink($cp->ID) : home_url('/kontakt/');
}
$port_def_imgs = [
  'https://images.unsplash.com/photo-1664314383485-1070ad9dddbc?w=1080&q=80',
  'https://images.unsplash.com/photo-1543949144-a8b18c58a635?w=1080&q=80',
  'https://images.unsplash.com/photo-1769578911474-af7aae3f84e5?w=1080&q=80',
  'https://images.unsplash.com/photo-1568154700421-490b598b1a47?w=1080&q=80',
  'https://images.unsplash.com/photo-1640146459297-eb4f1b084cc6?w=1080&q=80',
  'https://images.unsplash.com/photo-1758862495985-ab1cd2f7d613?w=1080&q=80'
];
$port_def_titles = ['Fahrzeugbeschriftung', 'Schaufenster-Werbung', 'Großflächenplakat', 'Leuchtreklame', 'City-Light-Poster', 'Fassaden-Beschriftung'];
$port_def_cats = ['Beschriftung', 'Beschriftung', 'Plakatierung', 'Außenwerbung', 'Plakatierung', 'Beschriftung'];
$port_detail_slugs = ['fahrzeugbeschriftung', 'schaufenster-werbung', 'grossflachenplakat', 'leuchtreklame', 'city-light-poster', 'fassaden-beschriftung'];
$cat_uc_to_title = ['BESCHRIFTUNG' => 'Beschriftung', 'PLAKATIERUNG' => 'Plakatierung', 'AUSSENWERBUNG' => 'Außenwerbung'];
$port_items = [];
for ($i = 1; $i <= 6; $i++) {
  $img_id = get_post_meta(get_the_ID(), "_wi_portfolio_{$i}_img_id", true);
  $cat_raw = get_post_meta(get_the_ID(), "_wi_portfolio_{$i}_category", true) ?: $port_def_cats[$i-1];
  $category = $cat_uc_to_title[strtoupper(trim((string) $cat_raw))] ?? $cat_raw;
  $link = get_post_meta(get_the_ID(), "_wi_portfolio_{$i}_link", true);
  if (empty($link) && !empty($port_detail_slugs[$i-1])) {
    $detail_page = get_page_by_path($port_detail_slugs[$i-1]);
    if ($detail_page) {
      $link = get_permalink($detail_page);
    }
  }
  $port_items[] = [
    'img' => $img_id ? wp_get_attachment_image_url($img_id, 'large') : $port_def_imgs[$i-1],
    'title' => get_post_meta(get_the_ID(), "_wi_portfolio_{$i}_title", true) ?: $port_def_titles[$i-1],
    'category' => $category,
    'link' => $link
  ];
}
?>
<section id="arbeiten" class="home-portfolio">
  <div class="home-section__container">
    <div class="home-portfolio__header">
      <h2 class="home-portfolio__title"><?php echo wp_kses_post(nl2br($port_title)); ?></h2>
      <p class="home-portfolio__subtitle"><?php echo wp_kses_post(nl2br($port_subtitle)); ?></p>
    </div>
    <div class="home-portfolio__grid">
      <?php foreach ($port_items as $idx => $p):
        $card_tag = $p['link'] ? 'a' : 'div';
        $card_attr = $p['link'] ? ' href="' . esc_url($p['link']) . '"' : '';
      ?>
      <<?php echo $card_tag; ?> class="home-portfolio__card"<?php echo $card_attr; ?>>
        <img class="home-portfolio__img" src="<?php echo esc_url($p['img']); ?>" alt="<?php echo esc_attr($p['title']); ?>" loading="lazy">
        <div class="home-portfolio__overlay">
          <span class="home-portfolio__cat"><?php echo esc_html($p['category']); ?></span>
          <h3 class="home-portfolio__card-title"><?php echo esc_html($p['title']); ?></h3>
        </div>
        <div class="home-portfolio__badge"><span><?php echo esc_html($p['category']); ?></span></div>
      </<?php echo $card_tag; ?>>
      <?php endforeach; ?>
    </div>
    <div class="home-portfolio__cta">
      <a href="<?php echo esc_url($port_btn_link); ?>" class="home-portfolio__btn"><?php echo esc_html($port_btn); ?></a>
    </div>
  </div>
</section>

<?php
// === CLIENTS (marquee) – podaci iz plugina WI Clients Marquee ===
$clients_title = get_post_meta(get_the_ID(), '_wi_clients_title', true) ?: 'OUR CLIENTS';
$clients_sub = get_post_meta(get_the_ID(), '_wi_clients_subtitle', true) ?: "Von Kultur bis Industrie – Marken,<br>die in der Region sichtbar sein wollen.";

$clients_items = get_option('wi_clients_marquee_items', []);
$clients_row_cap = 8;
$clients_rows_count = 3;
$real = [];
if (is_array($clients_items)) {
  foreach ($clients_items as $it) {
    $id = isset($it['image_id']) ? intval($it['image_id']) : 0;
    if ($id > 0) {
      $real[] = ['image_id' => $id, 'alt' => isset($it['alt']) ? $it['alt'] : 'Client'];
    }
  }
}
// Raspodela po redovima: prvi red 0..7, drugi 8..15, treći 16..23
$rows = array_fill(0, $clients_rows_count, []);
$chunks = array_chunk($real, $clients_row_cap);
for ($r = 0; $r < $clients_rows_count; $r++) {
  $rows[$r] = isset($chunks[$r]) ? $chunks[$r] : [];
}
for ($r = 0; $r < $clients_rows_count; $r++) {
  while (count($rows[$r]) < $clients_row_cap) {
    $rows[$r][] = ['image_id' => 0, 'alt' => 'Client'];
  }
}
?>
<section id="clients" class="home-clients">
  <div class="home-section__container">
    <div class="home-clients__header">
      <h2 class="home-clients__title"><?php echo esc_html($clients_title); ?></h2>
      <p class="home-clients__subtitle"><?php echo wp_kses_post(nl2br($clients_sub)); ?></p>
    </div>
  </div>
  <div class="home-clients__marquee">
    <?php
    foreach ($rows as $ri => $row):
      $dir = $ri === 1 ? 'right' : 'left';
      $dup = array_merge($row, $row, $row, $row, $row, $row);
    ?>
    <div class="home-clients__row">
      <div class="home-clients__track home-clients__track--<?php echo $dir; ?>">
        <?php
        foreach ($dup as $idx => $client):
          $image_id = (int) ($client['image_id'] ?? 0);
          $alt = isset($client['alt']) ? $client['alt'] : 'Client';
          $is_black = ($idx % 2 === 0);
          $var = $is_black ? 'black' : 'white';
          $logo_src = $image_id ? wp_get_attachment_image_url($image_id, 'wi_clients_logo') : '';
        ?>
        <div class="home-clients__pill home-clients__pill--<?php echo esc_attr($var); ?>">
          <?php if ($logo_src): ?>
            <img src="<?php echo esc_url($logo_src); ?>" alt="<?php echo esc_attr($alt); ?>" class="home-clients__pill-logo" decoding="async">
          <?php else: ?>
            <span><?php echo esc_html($alt); ?></span>
          <?php endif; ?>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
    <?php endforeach; ?>
  </div>
</section>

<?php
// === TESTIMONIALS (slider) ===
$testimonials = get_post_meta(get_the_ID(), '_wi_testimonials', true);
if (!is_array($testimonials) || empty($testimonials)) {
  $testimonials = [
    ['quote' => 'Exzellente Standortwahl und perfekte Ausführung. Unsere Markenbekanntheit ist durch die strategisch platzierten Plakate enorm gestiegen. Absolut empfehlenswert!', 'company' => 'Bodensee Events AG', 'person' => 'Sandra Müller'],
    ['quote' => 'Professionell, zuverlässig und kreativ. Die Zusammenarbeit war von Anfang an unkompliziert. Unsere Kampagne hat genau die richtige Zielgruppe erreicht.', 'company' => 'Stadtwerke Regional', 'person' => 'Thomas Weber'],
    ['quote' => 'Die Plakatierung hat unsere Erwartungen übertroffen. Hervorragende Beratung bei der Standortwahl und perfekte Umsetzung innerhalb kürzester Zeit.', 'company' => 'Müller Bäckerei', 'person' => 'Michael Müller'],
    ['quote' => 'Seit Jahren unser verlässlicher Partner für Außenwerbung. Die Qualität stimmt, die Termine werden eingehalten und das Preis-Leistungs-Verhältnis ist top.', 'company' => 'Autohaus Schmidt', 'person' => 'Julia Schmidt']
  ];
}
?>
<section id="testimonials" class="home-testimonials">
  <div class="home-testimonials__inner">
    <div class="home-testimonials__slider">
      <?php foreach ($testimonials as $ti => $t): ?>
      <div class="home-testimonials__slide<?php echo $ti === 0 ? ' is-active' : ''; ?>" data-index="<?php echo $ti; ?>">
        <blockquote class="home-testimonials__quote">"<?php echo esc_html($t['quote'] ?? ''); ?>"</blockquote>
        <div class="home-testimonials__attr">
          <strong><?php echo esc_html($t['company'] ?? ''); ?></strong>
          <span><?php echo esc_html($t['person'] ?? ''); ?></span>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
    <div class="home-testimonials__dots">
      <?php foreach ($testimonials as $ti => $t): ?><button type="button" class="home-testimonials__dot<?php echo $ti === 0 ? ' is-active' : ''; ?>" data-index="<?php echo $ti; ?>" aria-label="Testimonial <?php echo $ti+1; ?>"></button><?php endforeach; ?>
    </div>
  </div>
  <?php if (count($testimonials) > 1): ?>
  <button type="button" class="home-testimonials__arrow home-testimonials__arrow--prev" aria-label="Vorheriges">‹</button>
  <button type="button" class="home-testimonials__arrow home-testimonials__arrow--next" aria-label="Nächstes">›</button>
  <?php endif; ?>
</section>
<script>
(function(){
  var slides=document.querySelectorAll('.home-testimonials__slide');
  var dots=document.querySelectorAll('.home-testimonials__dot');
  var prev=document.querySelector('.home-testimonials__arrow--prev');
  var next=document.querySelector('.home-testimonials__arrow--next');
  var idx=0;
  function go(i){idx=(i+slides.length)%slides.length; slides.forEach(function(s,k){s.classList.toggle('is-active',k===idx)}); dots.forEach(function(d,k){d.classList.toggle('is-active',k===idx)})}
  dots.forEach(function(d,i){d.addEventListener('click',function(){go(i)})});
  if(prev) prev.addEventListener('click',function(){go(idx-1)});
  if(next) next.addEventListener('click',function(){go(idx+1)});
})();
</script>

<?php
// === CTA ===
$cta_title = get_post_meta(get_the_ID(), '_wi_cta_title', true) ?: "Bereit für Ihr<br>nächstes Projekt?";
$cta_sub = get_post_meta(get_the_ID(), '_wi_cta_subtitle', true) ?: "Lassen Sie uns über Ihre Werbeziele sprechen.<br>Gemeinsam machen wir Ihre Marke sichtbar.";
$cta_btn = get_post_meta(get_the_ID(), '_wi_cta_btn_text', true) ?: 'JETZT ANFRAGEN';
$cta_link = get_post_meta(get_the_ID(), '_wi_cta_btn_link', true);
if (!$cta_link) {
  $cp = get_page_by_path('kontakt') ?: get_page_by_path('contact');
  $cta_link = $cp ? get_permalink($cp->ID) : home_url('/kontakt/');
}
?>
<section id="cta" class="home-cta">
  <div class="home-section__container">
    <div class="home-cta__inner">
      <h2 class="home-cta__title"><?php echo wp_kses_post(nl2br($cta_title)); ?></h2>
      <p class="home-cta__subtitle"><?php echo wp_kses_post(nl2br($cta_sub)); ?></p>
      <a href="<?php echo esc_url($cta_link); ?>" class="home-cta__btn"><?php echo esc_html($cta_btn); ?></a>
    </div>
  </div>
</section>

</main>

<?php get_footer(); ?>
