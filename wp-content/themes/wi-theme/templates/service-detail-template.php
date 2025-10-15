<?php
/**
 * Template Name: Service Detail (Werbeinsel)
 * Description: Detaljna stranica servisa – jedan template, per-slug partials.
 */

get_header();

/* ---------- CSS ---------- */
$css_path = get_stylesheet_directory() . '/css/service-detail.css';
$css_url  = get_stylesheet_directory_uri() . '/css/service-detail.css';
if (file_exists($css_path)) { $css_url .= '?v=' . filemtime($css_path); }
echo '<link rel="stylesheet" href="'.esc_url($css_url).'">';

/* ---------- Osnovne promenljive (MORAJU pre bilo kakvog include/HTML) ---------- */
$page_id    = get_queried_object_id();                      // sigurnije od get_the_ID() u nekim kontekstima
$slug       = get_post_field('post_name', $page_id) ?: '';  // npr. 'plakatwerbung'
$page_title = get_the_title($page_id);

$svc = ['title'=>'','image_url'=>'','category'=>''];        // UVEK inicijalizuj!

// 1) Pokušaj iz plugina (grid)
if (class_exists('WI_Services_Manager') && $slug) {
  list($found, $details) = WI_Services_Manager::get_service_data_by_slug($slug);
  if (!empty($found['title']))     $svc['title']     = $found['title'];
  if (!empty($found['image_url'])) $svc['image_url'] = $found['image_url'];
  if (!empty($found['category']))  $svc['category']  = $found['category'];
}

// 2) Fallback na featured image stranice
if (!$svc['image_url']) {
  $thumb_id = get_post_thumbnail_id($page_id);
  if ($thumb_id) {
    $src = wp_get_attachment_image_url($thumb_id, 'full');
    if ($src) $svc['image_url'] = $src;
  }
}

// 3) Poslednji fallback (stavi svoj fajl ako želiš)
if (!$svc['image_url']) {
  $svc['image_url'] = get_stylesheet_directory_uri() . '/img/hero-placeholder.jpg'; // napravi ovaj fajl po želji
}

// mala pomoć za debug u HTML komentaru:
echo "\n<!-- service-detail debug: slug={$slug}, image_url=" . esc_html($svc['image_url']) . " -->\n";
?>

<main class="svc-detail">
  <?php
  // Loader per-slug partiala: templates/service-parts/{slug}.php
  $partial_path = $slug ? locate_template('templates/service-parts/' . $slug . '.php') : '';

  if ($partial_path) {
    // Partial vidi $slug, $svc, $page_title, $page_id iz OVOG scope-a.
    require $partial_path;
  } else {
    // Fallback (ako nema partial-a za taj slug)
    ?>
    <section class="svc-detail__hero" style="background-image:url('<?php echo esc_url($svc['image_url']); ?>')">
      <div class="svc-detail__hero__overlay">
        <?php if (!empty($svc['category'])): ?>
          <p class="svc-detail__cat"><?php echo esc_html($svc['category']); ?></p>
        <?php endif; ?>
        <h1 class="svc-detail__title"><?php echo esc_html($page_title); ?></h1>
      </div>
    </section>

    <section class="svc-detail__content content-container">
      <?php while (have_posts()) : the_post(); the_content(); endwhile; ?>
    </section>
    <?php
  }
  ?>
</main>

<?php get_footer(); ?>
