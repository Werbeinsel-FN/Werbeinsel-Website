<?php
/* Template Name: Datenschutz */
get_header(); ?>

<link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/css/datenschutz.css?v=<?php echo filemtime(get_template_directory() . '/css/datenschutz.css'); ?>">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
<link href="https://fonts.googleapis.com/css2?family=Unbounded:wght@400;700;800&display=swap" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;700;800&display=swap" rel="stylesheet">

<?php
$data = function_exists('wi_get_datenschutz_data')
    ? wi_get_datenschutz_data(get_the_ID())
    : wi_datenschutz_defaults(); // fallback ako helper nije učitan
?>

<main id="datenschutz-main" class="datenschutz-main">
  <div class="datenschutz-wrapper">
    <section class="content-container text-center">
      <h1 class="datenschutz-title">DATENSCHUTZ</h1> <!-- ostaje fiksno -->
    </section>

    <section class="content-container mt-16">
      <div class="inner-container">

        <div class="datenschutz-section">
          <h3 class="section-title"><?php echo esc_html($data['verantwortlicher']['title']); ?></h3>
          <div class="section-content">
            <?php foreach ($data['verantwortlicher']['paras'] as $p): ?>
              <p><?php echo esc_html($p); ?></p>
            <?php endforeach; ?>
          </div>
        </div>

        <div class="datenschutz-section">
          <h3 class="section-title"><?php echo esc_html($data['erhebung']['title']); ?></h3>
          <p class="section-text"><?php echo esc_html($data['erhebung']['text']); ?></p>
        </div>

        <div class="datenschutz-section">
          <h3 class="section-title"><?php echo esc_html($data['rechte']['title']); ?></h3>
          <p class="section-text"><?php echo esc_html($data['rechte']['text']); ?></p>
        </div>

        <div class="cookie-box">
          <h4 class="cookie-title"><?php echo esc_html($data['cookie']['title']); ?></h4>
          <p class="cookie-text"><?php echo esc_html($data['cookie']['text']); ?></p>
          <button class="cookie-button">EINSTELLUNGEN BEARBEITEN</button> <!-- ostaje fiksno -->
        </div>

      </div>
    </section>
  </div>
</main>

<?php get_footer(); ?>
