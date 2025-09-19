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

<main id="impressum-main">

    <!-- Naslov -->
    <section class="content-container text-center">
        <h1 class="unbounded-bold">IMPRESSUM</h1>
    </section>

    <!-- Sadržaj -->
    <section class="content-container">
        <div class="content-wrapper">

            <!-- Angaben gemäß § 5 TMG -->
            <div class="impressum-block">
                <h3>ANGABEN GEMÄSS § 5 TMG</h3>
                <div class="poppins">
                    <p>AGENCY GmbH</p>
                    <p>Musterstraße 123</p>
                    <p>12345 Berlin</p>
                    <p>Deutschland</p>
                </div>
            </div>

            <!-- Kontakt -->
            <div class="impressum-block">
                <h3>KONTAKT</h3>
                <div class="poppins">
                    <p>Telefon: +49 30 123 456 789</p>
                    <p>E-Mail: info@agency.com</p>
                </div>
            </div>

            <!-- Umsatzsteuer-ID -->
            <div class="impressum-block">
                <h3>UMSATZSTEUER-ID</h3>
                <div class="poppins">
                    <p>Umsatzsteuer-Identifikationsnummer gemäß § 27 a Umsatzsteuergesetz:</p>
                    <p>DE123456789</p>
                </div>
            </div>

        </div>
    </section>

</main>

<?php get_footer(); ?>
