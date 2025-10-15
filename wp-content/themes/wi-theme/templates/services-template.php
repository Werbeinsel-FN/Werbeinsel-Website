<?php
/**
 * Template Name: Services (Werbeinsel)
 * Description: Stranica servisa – čitanje sadržaja iz "Services Content" plugina.
 */
get_header();

/** Učitamo opcije */
$opt = get_option('wi_services_options', []);
$H = [
  'ooh' => !empty($opt['headings']['ooh']) ? $opt['headings']['ooh'] : 'AUF DIE STRAßE',
  'pix' => !empty($opt['headings']['pix']) ? $opt['headings']['pix'] : 'PIXEL & CODE',
  'kle' => !empty($opt['headings']['kle']) ? $opt['headings']['kle'] : 'LASS KLEBEN',
];

function wi_get_group($key, $fallback) {
  $opt = get_option('wi_services_options', []);
  $items = $opt['groups'][$key] ?? [];
  foreach ($fallback as $i => $f) {
    $items[$i]['title'] = isset($items[$i]['title']) && $items[$i]['title'] !== '' ? $items[$i]['title'] : $f['title'];
    $items[$i]['cat']   = isset($items[$i]['cat']) ? (int)$items[$i]['cat'] : 0;
    $items[$i]['image'] = isset($items[$i]['image']) ? (int)$items[$i]['image'] : 0;
    $items[$i]['image_url'] = $items[$i]['image'] ? wp_get_attachment_image_url((int)$items[$i]['image'], 'full') : $f['image'];
    $items[$i]['cat_name']  = $items[$i]['cat'] ? get_cat_name((int)$items[$i]['cat']) : $f['category'];
    // helper: slug od naslova
    $items[$i]['slug'] = sanitize_title( $items[$i]['title'] );
    $items[$i]['group_key'] = $key;
    $items[$i]['index'] = $i;
  }
  return $items;
}

// Fallbackovi (ne menjamo)
$out_of_home_fb = [
  ['title'=>'Plakatwerbung','category'=>'Auf die Straße','image'=>'https://images.unsplash.com/photo-1578662996442-48f60103fc96?w=1200&q=80&auto=format&fit=crop'],
  ['title'=>'Großflächenwerbung','category'=>'Auf die Straße','image'=>'https://images.unsplash.com/photo-1553708881-112abc53fe54?w=1200&q=80&auto=format&fit=crop'],
  ['title'=>'Digital Signage','category'=>'Auf die Straße','image'=>'https://images.unsplash.com/photo-1560472354-b33ff0c44a43?w=1200&q=80&auto=format&fit=crop'],
  ['title'=>'Transit Advertising','category'=>'Auf die Straße','image'=>'https://images.unsplash.com/photo-1544620347-c4fd4a3d5957?w=1200&q=80&auto=format&fit=crop'],
  ['title'=>'Guerilla Marketing','category'=>'Auf die Straße','image'=>'https://images.unsplash.com/photo-1557804506-669a67965ba0?w=1200&q=80&auto=format&fit=crop'],
  ['title'=>'Ambient Advertising','category'=>'Auf die Straße','image'=>'https://images.unsplash.com/photo-1551650975-87deedd944c3?w=1200&q=80&auto=format&fit=crop'],
];
$pixel_code_fb = [
  ['title'=>'Corporate Design','category'=>'Pixel & Code','image'=>'https://images.unsplash.com/photo-1561070791-2526d30994b5?w=1200&q=80&auto=format&fit=crop'],
  ['title'=>'Webentwicklung','category'=>'Pixel & Code','image'=>'https://images.unsplash.com/photo-1467232004584-a241de8bcf5d?w=1200&q=80&auto=format&fit=crop'],
  ['title'=>'Print Design','category'=>'Pixel & Code','image'=>'https://images.unsplash.com/photo-1586281380117-5a60ae2050cc?w=1200&q=80&auto=format&fit=crop'],
  ['title'=>'UI/UX Design','category'=>'Pixel & Code','image'=>'https://images.unsplash.com/photo-1559136555-9303baea8ebd?w=1200&q=80&auto=format&fit=crop'],
];
$lass_kleben_fb = [
  ['title'=>'Neonreklame','category'=>'Lass kleben','image'=>'https://images.unsplash.com/photo-1518709268805-4e9042af2176?w=1200&q=80&auto=format&fit=crop'],
  ['title'=>'LED-Displays','category'=>'Lass kleben','image'=>'https://images.unsplash.com/photo-1551677750-6de7fd4f26aa?w=1200&q=80&auto=format&fit=crop'],
  ['title'=>'Leuchtschriften','category'=>'Lass kleben','image'=>'https://images.unsplash.com/photo-1579952363873-27d3bfad9c0d?w=1200&q=80&auto=format&fit=crop'],
];

$OOH = wi_get_group('out_of_home', $out_of_home_fb);
$PIX = wi_get_group('pixel_code',  $pixel_code_fb);
$KLE = wi_get_group('lass_kleben', $lass_kleben_fb);

/** CSS */
$services_css_path = get_stylesheet_directory() . '/css/services.css';
$services_css_url  = get_stylesheet_directory_uri() . '/css/services.css';
if (file_exists($services_css_path)) { $services_css_url .= '?v=' . filemtime($services_css_path); }

/** Helper: URL ka detail stranici */
if (!function_exists('wi_service_detail_url')) {
  function wi_service_detail_url($slug){
    $slug = sanitize_title($slug);

    // 1) postoji li WP stranica sa tim slugom?
    $page = get_page_by_path($slug);
    if ($page) {
      return get_permalink($page);
    }

    // 2) (opciono) ručna mapa ako ima izuzetaka u nazivima
    $map = [
      // 'grossflachenwerbung' => 'grossflaechenwerbung',
    ];
    if (isset($map[$slug])) {
      $mapped = get_page_by_path($map[$slug]);
      if ($mapped) return get_permalink($mapped);
    }

    // 3) fallback: /{slug}/
    return home_url('/service/' . $slug . '/');
  }
}
?>
<link rel="stylesheet" href="<?php echo esc_url($services_css_url); ?>">

<main id="services-main" class="svc-page">

  <!-- AUF DIE STRAßE (žuta) -->
  <section class="svc-section svc-section--yellow">
    <div class="svc-container content-container">
      <h2 class="svc-hl svc-hl--black"><?php echo esc_html($H['ooh']); ?></h2>
      <div class="svc-grid svc-grid--3">
        <?php foreach ($OOH as $s): ?>
          <a class="svc-card" href="<?php echo esc_url( wi_service_detail_url($s['slug']) ); ?>">
            <img class="svc-card__img" src="<?php echo esc_url($s['image_url']); ?>" alt="<?php echo esc_attr($s['title']); ?>" loading="lazy">
            <div class="svc-card__overlay svc-card__overlay--dark">
              <p class="svc-card__cat"><?php echo esc_html($s['cat_name']); ?></p>
              <h3 class="svc-card__ttl"><?php echo esc_html($s['title']); ?></h3>
            </div>
          </a>
        <?php endforeach; ?>
      </div>
    </div>
  </section>

  <!-- PIXEL & CODE (crna, u sredini) -->
  <section class="svc-section svc-section--dark">
    <div class="svc-container content-container">
      <h2 class="svc-hl svc-hl--white"><?php echo esc_html($H['pix']); ?></h2>
      <div class="svc-grid svc-grid--2cols">
        <div class="svc-grid svc-grid--2">
          <?php foreach (array_slice($PIX, 0, 2) as $s): ?>
            <a class="svc-card" href="<?php echo esc_url( wi_service_detail_url($s['slug']) ); ?>">
              <img class="svc-card__img" src="<?php echo esc_url($s['image_url']); ?>" alt="<?php echo esc_attr($s['title']); ?>" loading="lazy">
              <div class="svc-card__overlay svc-card__overlay--yellow">
                <p class="svc-card__cat svc-card__cat--dark"><?php echo esc_html($s['cat_name']); ?></p>
                <h3 class="svc-card__ttl svc-card__ttl--dark"><?php echo esc_html($s['title']); ?></h3>
              </div>
            </a>
          <?php endforeach; ?>
        </div>
        <div class="svc-grid svc-grid--2">
          <?php foreach (array_slice($PIX, 2) as $s): ?>
            <a class="svc-card" href="<?php echo esc_url( wi_service_detail_url($s['slug']) ); ?>">
              <img class="svc-card__img" src="<?php echo esc_url($s['image_url']); ?>" alt="<?php echo esc_attr($s['title']); ?>" loading="lazy">
              <div class="svc-card__overlay svc-card__overlay--yellow">
                <p class="svc-card__cat svc-card__cat--dark"><?php echo esc_html($s['cat_name']); ?></p>
                <h3 class="svc-card__ttl svc-card__ttl--dark"><?php echo esc_html($s['title']); ?></h3>
              </div>
            </a>
          <?php endforeach; ?>
        </div>
      </div>
    </div>
  </section>

  <!-- LASS KLEBEN (žuta, na dnu) -->
  <section class="svc-section svc-section--yellow">
    <div class="svc-container content-container">
      <h2 class="svc-hl svc-hl--black"><?php echo esc_html($H['kle']); ?></h2>
      <div class="svc-grid svc-grid--3-md">
        <?php foreach ($KLE as $s): ?>
          <a class="svc-card" href="<?php echo esc_url( wi_service_detail_url($s['slug']) ); ?>">
            <img class="svc-card__img" src="<?php echo esc_url($s['image_url']); ?>" alt="<?php echo esc_attr($s['title']); ?>" loading="lazy">
            <div class="svc-card__overlay svc-card__overlay--dark">
              <p class="svc-card__cat"><?php echo esc_html($s['cat_name']); ?></p>
              <h3 class="svc-card__ttl"><?php echo esc_html($s['title']); ?></h3>
            </div>
          </a>
        <?php endforeach; ?>
      </div>
    </div>
  </section>

</main>

<?php get_footer(); ?>
