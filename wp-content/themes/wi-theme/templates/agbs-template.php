<?php
/**
 * Template Name: AGBs
 * Description: Template for the Allgemeine Geschäftsbedingungen (AGBs) page
 */
get_header(); ?>

<link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/css/agbs.css?v=<?php echo filemtime(get_template_directory() . '/css/agbs.css'); ?>">

<main id="agbs-main">
  <div>
    <!-- HEADER -->
    <section class="content-container text-center">
      <h1 class="text-[48px] sm:text-[64px] md:text-[120px] lg:text-[175px] unbounded-bold text-black mb-8 leading-none">
        <?php the_title(); ?>
      </h1>
    </section>

    <!-- CONTENT -->
    <section class="content-container mt-16">
      <div class="max-w-6xl mx-auto">
        <div class="space-y-8">
          <?php while (have_posts()) { the_post(); the_content(); } ?>
        </div>
      </div>
    </section>
  </div>
</main>

<?php get_footer(); ?>
