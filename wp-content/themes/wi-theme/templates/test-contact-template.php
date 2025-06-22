<?php
/**
 * Template Name: Test Kontakt
 * Description: Template for the contact page
 */

get_header(); ?>

<link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/css/test-contact.css?v=<?php echo filemtime(get_template_directory() . '/css/home.css'); ?>">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/foundation-sites@6.7.5/dist/css/foundation.min.css">
<!-- <link href="https://fonts.googleapis.com/css2?family=Unbounded:wght@400;700;800&display=swap" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;700;800&display=swap" rel="stylesheet"> -->

<main id="test-contact-main" class="test-contact-main">
    <h3 class="contact-main-container headline">Kontakt</h3>
    <?php echo do_shortcode('[wi_form id="30"]'); ?>
</main>

<?php get_footer();