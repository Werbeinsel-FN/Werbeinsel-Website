<?php
/**
 * Template Name: Impressum
 * Description: Template for the Impressum page
 */

get_header(); ?>

<link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/css/impressum.css?v=<?php echo filemtime(get_template_directory() . '/css/home.css'); ?>">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
<link href="https://fonts.googleapis.com/css2?family=Unbounded:wght@400;700;800&display=swap" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;700;800&display=swap" rel="stylesheet">

<main id="impressum-main" class="impressum-main">
<header><h1>Impressum</h1></header>
<section class="impressum-section">
  <div class="company-info">
    <h3>zwetschke GmbH &amp; Co. KG</h3>
    <p>Proviantbachstr. 1 ½<br>
    86153 Augsburg</p>

    <p>Handelsregister: HRA 18731<br>
    Registergericht: Amtsgericht Augsburg</p>
  </div>

  <div class="representation">
    <h3>Vertreten durch:</h3>
    <p>zwetschke Verwaltungs GmbH<br>
    Proviantbachstr. 1 ½<br>
    86153 Augsburg</p>

    <p>Diese vertreten durch:<br>
    Hannes Zwetschke</p>

    <p>Handelsregister: HRB 30246<br>
    Registergericht: Amtsgericht Augsburg</p>
  </div>

  <div class="contact">
    <h3>Kontakt</h3>
    <p>Telefon: +49 821 899 822 11<br>
    E-Mail: <a href="mailto:info@zwetschke.de">info@zwetschke.de</a></p>
  </div>

  <div class="vat-id">
    <h3>Umsatzsteuer-ID</h3>
    <p>Umsatzsteuer-Identifikationsnummer gemäß § 27 a Umsatzsteuergesetz: DE304374864</p>
  </div>

  <div class="editorial">
    <h3>Redaktionell verantwortlich</h3>
    <p>Hannes Zwetschke</p>
  </div>

  <div class="dispute-resolution">
    <h3>Verbraucher­streit­beilegung / Universal­schlichtungs­stelle</h3>
    <p>Wir sind nicht bereit oder verpflichtet, an Streitbeilegungsverfahren vor einer Verbraucherschlichtungsstelle teilzunehmen.</p>
  </div>

  <div class="data-protection">
    <h3>Name und Anschrift des Datenschutzbeauftragten:</h3>
    <p>SECUWING GmbH &amp; Co. KG | Datenschutz Agentur<br>
    Maximilian Hartung<br>
    Frauentorstraße 9<br>
    86152 Augsburg<br>
    Telefon: +49 821 90786458<br>
    E-Mail: <a href="mailto:epost@datenschutz-agentur.de">epost@datenschutz-agentur.de</a></p>
  </div>
</section>
</main>

<?php get_footer();