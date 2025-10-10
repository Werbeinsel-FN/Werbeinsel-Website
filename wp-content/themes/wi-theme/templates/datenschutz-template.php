<?php
/**
 * Template Name: Datenschutz
 * Description: Template for the Datenschutz page
 */

get_header(); ?>

<link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/css/datenschutz.css?v=<?php echo filemtime(get_template_directory() . '/css/home.css'); ?>">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
<link href="https://fonts.googleapis.com/css2?family=Unbounded:wght@400;700;800&display=swap" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;700;800&display=swap" rel="stylesheet">

<main id="datenschutz-main" class="datenschutz-main">
  <div class="datenschutz-wrapper">
    <section class="content-container text-center">
      <h1 class="datenschutz-title">DATENSCHUTZ</h1>
    </section>

    <section class="content-container mt-16">
      <div class="inner-container">
        <div class="datenschutz-section">
          <h3 class="section-title">VERANTWORTLICHER</h3>
          <div class="section-content">
            <p>AGENCY GmbH</p>
            <p>Musterstraße 123</p>
            <p>12345 Berlin</p>
            <p>Deutschland</p>
            <p>E-Mail: datenschutz@agency.com</p>
          </div>
        </div>

        <div class="datenschutz-section">
          <h3 class="section-title">ERHEBUNG UND VERARBEITUNG PERSONENBEZOGENER DATEN</h3>
          <p class="section-text">
            Wir erheben und verarbeiten personenbezogene Daten nur, soweit dies zur Erfüllung unserer vertraglichen Pflichten oder zur Wahrung berechtigter Interessen erforderlich ist.
          </p>
        </div>

        <div class="datenschutz-section">
          <h3 class="section-title">IHRE RECHTE</h3>
          <p class="section-text">
            Sie haben das Recht auf Auskunft, Berichtigung, Löschung, Einschränkung der Verarbeitung, Widerspruch und Datenübertragbarkeit.
          </p>
        </div>

        <div class="cookie-box">
          <h4 class="cookie-title">COOKIE-EINSTELLUNGEN</h4>
          <p class="cookie-text">Verwalten Sie Ihre Cookie-Präferenzen und Datenschutzeinstellungen.</p>
          <button class="cookie-button">EINSTELLUNGEN BEARBEITEN</button>
        </div>
      </div>
    </section>
  </div>
</main>


<?php get_footer();