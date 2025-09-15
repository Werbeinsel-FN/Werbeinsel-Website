<?php
/**
 * Template Name: Services (Werbeinsel)
 * Description: Stranica servisa – verno Figmi, žuta/crna pozadina po sekcijama.
 */
get_header();

/** Podaci */
$out_of_home = [
  ['title' => 'Plakatwerbung',       'category' => 'Auf die Straße', 'image' => 'https://images.unsplash.com/photo-1578662996442-48f60103fc96?w=1200&q=80&auto=format&fit=crop'],
  ['title' => 'Großflächenwerbung',  'category' => 'Auf die Straße', 'image' => 'https://images.unsplash.com/photo-1553708881-112abc53fe54?w=1200&q=80&auto=format&fit=crop'],
  ['title' => 'Digital Signage',     'category' => 'Auf die Straße', 'image' => 'https://images.unsplash.com/photo-1560472354-b33ff0c44a43?w=1200&q=80&auto=format&fit=crop'],
  ['title' => 'Transit Advertising', 'category' => 'Auf die Straße', 'image' => 'https://images.unsplash.com/photo-1544620347-c4fd4a3d5957?w=1200&q=80&auto=format&fit=crop'],
  ['title' => 'Guerilla Marketing',  'category' => 'Auf die Straße', 'image' => 'https://images.unsplash.com/photo-1557804506-669a67965ba0?w=1200&q=80&auto=format&fit=crop'],
  ['title' => 'Ambient Advertising', 'category' => 'Auf die Straße', 'image' => 'https://images.unsplash.com/photo-1551650975-87deedd944c3?w=1200&q=80&auto=format&fit=crop'],
];

$werbetechnik = [
  ['title' => 'Neonreklame',     'category' => 'Lass kleben', 'image' => 'https://images.unsplash.com/photo-1518709268805-4e9042af2176?w=1200&q=80&auto=format&fit=crop'],
  ['title' => 'LED-Displays',    'category' => 'Lass kleben', 'image' => 'https://images.unsplash.com/photo-1551677750-6de7fd4f26aa?w=1200&q=80&auto=format&fit=crop'],
  ['title' => 'Leuchtschriften', 'category' => 'Lass kleben', 'image' => 'https://images.unsplash.com/photo-1579952363873-27d3bfad9c0d?w=1200&q=80&auto=format&fit=crop'],
];

$design = [
  ['title' => 'Corporate Design', 'category' => 'Pixel & Code', 'image' => 'https://images.unsplash.com/photo-1561070791-2526d30994b5?w=1200&q=80&auto=format&fit=crop'],
  ['title' => 'Webentwicklung',   'category' => 'Pixel & Code', 'image' => 'https://images.unsplash.com/photo-1467232004584-a241de8bcf5d?w=1200&q=80&auto=format&fit=crop'],
  ['title' => 'Print Design',     'category' => 'Pixel & Code', 'image' => 'https://images.unsplash.com/photo-1586281380117-5a60ae2050cc?w=1200&q=80&auto=format&fit=crop'],
  ['title' => 'UI/UX Design',     'category' => 'Pixel & Code', 'image' => 'https://images.unsplash.com/photo-1559136555-9303baea8ebd?w=1200&q=80&auto=format&fit=crop'],
];

/** Enqueue CSS */
$services_css_path = get_stylesheet_directory() . '/css/services.css';
$services_css_url  = get_stylesheet_directory_uri() . '/css/services.css';
if (file_exists($services_css_path)) {
  $services_css_url .= '?v=' . filemtime($services_css_path);
}
?>
<link rel="stylesheet" href="<?php echo esc_url($services_css_url); ?>">

<main id="services-main" class="svc-page">

  <!-- AUF DIE STRAßE (žuta) -->
  <section class="svc-section svc-section--yellow">
    <div class="svc-container">
      <h2 class="svc-hl svc-hl--black">AUF DIE STRAßE</h2>
      <div class="svc-grid svc-grid--3">
        <?php foreach ($out_of_home as $s): ?>
          <article class="svc-card" role="button" tabindex="0">
            <img class="svc-card__img" src="<?php echo esc_url($s['image']); ?>" alt="<?php echo esc_attr($s['title']); ?>" loading="lazy">
            <div class="svc-card__overlay svc-card__overlay--dark">
              <p class="svc-card__cat"><?php echo esc_html($s['category']); ?></p>
              <h3 class="svc-card__ttl"><?php echo esc_html($s['title']); ?></h3>
            </div>
          </article>
        <?php endforeach; ?>
      </div>
    </div>
  </section>

  <!-- LASS KLEBEN (crna) -->
  <section class="svc-section svc-section--dark">
    <div class="svc-container">
      <h2 class="svc-hl svc-hl--white">LASS KLEBEN</h2>
      <div class="svc-grid svc-grid--3-md">
        <?php foreach ($werbetechnik as $s): ?>
          <article class="svc-card" role="button" tabindex="0">
            <img class="svc-card__img" src="<?php echo esc_url($s['image']); ?>" alt="<?php echo esc_attr($s['title']); ?>" loading="lazy">
            <div class="svc-card__overlay svc-card__overlay--yellow">
              <p class="svc-card__cat svc-card__cat--dark"><?php echo esc_html($s['category']); ?></p>
              <h3 class="svc-card__ttl svc-card__ttl--dark"><?php echo esc_html($s['title']); ?></h3>
            </div>
          </article>
        <?php endforeach; ?>
      </div>
    </div>
  </section>

  <!-- PIXEL & CODE (žuta) -->
  <section class="svc-section svc-section--yellow">
    <div class="svc-container">
      <h2 class="svc-hl svc-hl--black">PIXEL &amp; CODE</h2>
      <div class="svc-grid svc-grid--2cols">
        <div class="svc-grid svc-grid--2">
          <?php foreach (array_slice($design, 0, 2) as $s): ?>
            <article class="svc-card" role="button" tabindex="0">
              <img class="svc-card__img" src="<?php echo esc_url($s['image']); ?>" alt="<?php echo esc_attr($s['title']); ?>" loading="lazy">
              <div class="svc-card__overlay svc-card__overlay--dark">
                <p class="svc-card__cat"><?php echo esc_html($s['category']); ?></p>
                <h3 class="svc-card__ttl"><?php echo esc_html($s['title']); ?></h3>
              </div>
            </article>
          <?php endforeach; ?>
        </div>
        <div class="svc-grid svc-grid--2">
          <?php foreach (array_slice($design, 2) as $s): ?>
            <article class="svc-card" role="button" tabindex="0">
              <img class="svc-card__img" src="<?php echo esc_url($s['image']); ?>" alt="<?php echo esc_attr($s['title']); ?>" loading="lazy">
              <div class="svc-card__overlay svc-card__overlay--dark">
                <p class="svc-card__cat"><?php echo esc_html($s['category']); ?></p>
                <h3 class="svc-card__ttl"><?php echo esc_html($s['title']); ?></h3>
              </div>
            </article>
          <?php endforeach; ?>
        </div>
      </div>
    </div>
  </section>

</main>

<?php get_footer(); ?>
