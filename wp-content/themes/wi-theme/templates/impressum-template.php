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
  <section class="content-container text-center">
    <h1 class="unbounded-bold"><?php the_title(); ?></h1>
  </section>

  <section class="content-container mt-16">
    <div class="max-w-6xl mx-auto">
      <div class="space-y-12">
        <?php
          while (have_posts()) { the_post();
            the_content();
          }
        ?>
      </div>
    </div>
  </section>
</main>

<?php get_footer(); ?>
