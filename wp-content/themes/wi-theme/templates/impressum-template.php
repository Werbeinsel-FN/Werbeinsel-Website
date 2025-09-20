<?php
/**
 * Template Name: Impressum
 * Description: Template for the Impressum page
 */

get_header(); ?>

<link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/css/impressum.css?v=<?php echo filemtime(get_template_directory() . '/css/impressum.css'); ?>">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
<link href="https://fonts.googleapis.com/css2?family=Unbounded:wght@400;700;800&display=swap" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;700;800&display=swap" rel="stylesheet">

<?php
// Lokalne defaulte koristimo ako plugin nije aktivan ili opcije nisu popunjene
function wi_impressum_defaults() {
  return [
    'tmg' => [
      'title' => 'ANGABEN GEMÄSS § 5 TMG',
      'paras' => ['WERBEINSEL','Flughafen 76/3','88046 Friedrichshafen','Deutschland'],
    ],
    'kontakt' => [
      'title' => 'KONTAKT',
      'paras' => ['Telefon: +49 7541 700 57 44','E-Mail: hallo@werbeinsel.de'],
    ],
    'ustid' => [
      'title' => 'UMSATZSTEUER-ID',
      'paras' => ['Umsatzsteuer-Identifikationsnummer gemäß § 27 a Umsatzsteuergesetz:','DE322482204'],
    ],
  ];
}

// Učitaj podatke iz plugina ako postoji, inače defaulte
$defaults = wi_impressum_defaults();
if (class_exists('WI_Impressum_Manager')) {
  $data = WI_Impressum_Manager::get_data();
  // osiguraj da svaka sekcija postoji i ima paras; ako ne, dopuni defaultima
  foreach (['tmg','kontakt','ustid'] as $k) {
    if (empty($data[$k]) || !is_array($data[$k])) { $data[$k] = $defaults[$k]; }
    if (empty($data[$k]['title'])) { $data[$k]['title'] = $defaults[$k]['title']; }
    if (empty($data[$k]['paras']) || !is_array($data[$k]['paras'])) { $data[$k]['paras'] = $defaults[$k]['paras']; }
  }
} else {
  $data = $defaults;
}
?>

<main id="impressum-main">

  <!-- Naslov (ostaje) -->
  <section class="content-container text-center">
    <h1 class="unbounded-bold">IMPRESSUM</h1>
  </section>

  <!-- Glavni deo -->
  <section class="content-container mt-16">
    <div class="max-w-6xl mx-auto">
      <div class="space-y-12">

        <!-- Angaben gemäß § 5 TMG -->
        <div class="text-center">
          <h3 class="text-3xl poppins-bold text-black mb-6">
            <?php echo esc_html($data['tmg']['title']); ?>
          </h3>
          <div class="poppins text-lg text-black space-y-3">
            <?php foreach ($data['tmg']['paras'] as $i => $p): ?>
              <p class="<?php echo $i === 0 ? 'font-semibold' : ''; ?>"><?php echo esc_html($p); ?></p>
            <?php endforeach; ?>
          </div>
        </div>

        <!-- Kontakt -->
        <div class="text-center">
          <h3 class="text-3xl poppins-bold text-black mb-6">
            <?php echo esc_html($data['kontakt']['title']); ?>
          </h3>
          <div class="poppins text-lg text-black space-y-3">
            <?php foreach ($data['kontakt']['paras'] as $p): ?>
              <p><?php echo esc_html($p); ?></p>
            <?php endforeach; ?>
          </div>
        </div>

        <!-- Umsatzsteuer-ID -->
        <div class="text-center">
          <h3 class="text-3xl poppins-bold text-black mb-6">
            <?php echo esc_html($data['ustid']['title']); ?>
          </h3>
          <div class="poppins text-lg text-black space-y-3">
            <?php foreach ($data['ustid']['paras'] as $p): ?>
              <p><?php echo esc_html($p); ?></p>
            <?php endforeach; ?>
          </div>
        </div>

      </div>
    </div>
  </section>

</main>

<?php get_footer(); ?>
