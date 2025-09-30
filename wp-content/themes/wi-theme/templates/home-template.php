<?php
/**
 * Template Name: Home
 * Description: Minimal Home template sa OUR CLIENTS sekcijom preko plugina WI Clients Marquee
 */

get_header(); ?>

<link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/css/home.css?v=<?php echo filemtime(get_template_directory() . '/css/home.css'); ?>">

<main id="home-main" class="home-main">

  <?php
  // OUR CLIENTS sekcija (shortcode iz plugina)
  echo do_shortcode('[wi_clients_marquee]');
  ?>

</main>

<?php get_footer();
