<?php
/**
 * Template Name: Home
 * Description: Minimal Home template sa OUR CLIENTS sekcijom preko plugina WI Clients Marquee
 */

get_header(); ?>

<link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/css/home.css?v=<?php echo filemtime(get_template_directory() . '/css/home.css'); ?>">

<?php
// === HERO (na samom vrhu) ===
$video_id = get_post_meta(get_the_ID(), '_wi_header_video_id', true);
$video_url = $video_id ? wp_get_attachment_url($video_id) : '';
$has_thumb = has_post_thumbnail();
$thumb_id  = $has_thumb ? get_post_thumbnail_id(get_the_ID()) : 0;
$thumb_src = $thumb_id ? wp_get_attachment_image_url($thumb_id, 'full') : '';
?>
<main id="home-main" class="home-main">

<header id="home-hero" class="home-hero">
  <div class="home-hero__frame">
    <div class="home-hero__inner">
      <?php if ($video_url) : ?>
        <video class="home-hero__media" src="<?php echo esc_url($video_url); ?>" autoplay muted loop playsinline preload="auto"></video>
      <?php elseif ($thumb_src) : ?>
        <img class="home-hero__media" src="<?php echo esc_url($thumb_src); ?>" alt="" loading="eager" decoding="async">
      <?php else : ?>
        <div class="home-hero__fallback" aria-hidden="true"></div>
      <?php endif; ?>

      <span class="home-hero__overlay" aria-hidden="true"></span>
    </div>
  </div>
</header>

<?php
$intro_title = get_post_meta(get_the_ID(), '_wi_intro_title', true);
$intro_text  = get_post_meta(get_the_ID(), '_wi_intro_text', true);
?>

<?php if ($intro_title || $intro_text): ?>
<section class="content-container text-center" aria-labelledby="home-intro-title">
  <div class="content-inner">
    <?php if ($intro_title): ?>
      <h1 id="home-intro-title" class="intro-title"><?php echo wp_kses_post($intro_title); ?></h1>
    <?php endif; ?>

    <?php if ($intro_text): ?>
      <div class="intro-text">
        <?php echo wpautop(wp_kses_post($intro_text)); ?>
      </div>
    <?php endif; ?>
  </div>
</section>
<?php endif; ?>


<?php
// === SERVICES KARTICE ===
$services_title = get_post_meta(get_the_ID(), '_wi_services_title', true);

// podrazumevani linkovi do sekcija na /services/
$default_targets = [
  1 => home_url('/services/#plakatwerbung'),
  2 => home_url('/services/#lass-kleben'),
  3 => home_url('/services/#pixel-code'),
];

$cards = [];
for ($i=1; $i<=3; $i++){
  $img_id = get_post_meta(get_the_ID(), "_wi_services_{$i}_img_id", true);
  $title  = get_post_meta(get_the_ID(), "_wi_services_{$i}_title", true);
  $custom_link = get_post_meta(get_the_ID(), "_wi_services_{$i}_link", true);

  if ($img_id || $title) {
    $cards[] = [
      'img'   => $img_id ? wp_get_attachment_image_url($img_id, 'xl') : '',
      'title' => $title,
      'href'  => $custom_link ? esc_url($custom_link) : $default_targets[$i],
    ];
  }
}

if ($services_title || !empty($cards)) :
?>
<section class="services">
  <div class="services__container">
    <?php if ($services_title): ?>
      <h2 class="services__title"><?php echo esc_html($services_title); ?></h2>
    <?php endif; ?>

    <?php if (!empty($cards)): ?>
    <div class="services__grid">
      <?php foreach ($cards as $c): ?>
        <a class="service-card" href="<?php echo esc_url($c['href']); ?>">
          <?php if (!empty($c['img'])): ?>
            <img class="service-card__img" src="<?php echo esc_url($c['img']); ?>" alt="">
          <?php endif; ?>
          <div class="service-card__label">
            <span class="service-card__title"><?php echo esc_html($c['title'] ?: 'Service'); ?></span>
          </div>
        </a>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>
  </div>
</section>
<?php endif; ?>


<?php
// === OUR CLIENTS sekcija (shortcode iz plugina)
echo do_shortcode('[wi_clients_marquee]');
?>

<?php
// === CTA sekcija ===

// Pronađi stranicu sa slugom "kontakt" ili "contact"
$wi_contact_url = '';
if ( $p = get_page_by_path( 'kontakt' ) ) {
    $wi_contact_url = get_permalink( $p->ID );
} elseif ( $p = get_page_by_path( 'contact' ) ) {
    $wi_contact_url = get_permalink( $p->ID );
} else {
    $wi_contact_url = home_url( '/kontakt/' );
}

$cta_title = get_post_meta(get_the_ID(), '_wi_cta_title', true);
if (!$cta_title) {
  $cta_title = 'BEREIT FÜR<br>MAXIMUM IMPACT?';
}
?>

<section class="cta">
  <div class="cta__inner">
    <h2 class="cta__title"><?php echo $cta_title; ?></h2>
    <button
      class="cta__btn"
      onclick="window.location.href='<?php echo esc_url( $wi_contact_url ); ?>';"
    >
      JA
    </button>
  </div>
</section>

</main>

<?php get_footer();
