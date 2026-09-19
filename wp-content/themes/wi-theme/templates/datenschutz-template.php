<?php
/* Template Name: Datenschutz */
get_header(); ?>

<link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/css/datenschutz.css?v=<?php echo filemtime(get_template_directory() . '/css/datenschutz.css'); ?>">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

<main id="datenschutz-main" class="datenschutz-main">
  <div class="datenschutz-wrapper">
    <section class="content-container text-center">
      <h1 class="datenschutz-title"><?php the_title(); ?></h1>
    </section>

    <section class="content-container mt-16">
      <div class="inner-container">
        <?php
          while (have_posts()) { the_post();
            the_content();
          }
        ?>
      </div>
    </section>
  </div>
</main>

<?php get_footer(); ?>
