<?php
/**
 * Template Name: Jobs
 * Description: Template for the Jobs page
 */
get_header(); ?>

<main class="jobs-page">
  <section class="intro-section">
    <h1 class="page-title">KARRIERE</h1>
    <p class="intro-text">
      Werde Teil unseres kreativen Teams! Wir suchen talentierte und motivierte Menschen, die mit uns die Zukunft der Werbung gestalten möchten.
    </p>
  </section>

  <section class="job-listings">
    <?php
    $jobs = new WP_Query(array(
        'post_type' => 'job',
        'posts_per_page' => -1
    ));
    if ($jobs->have_posts()) :
        while ($jobs->have_posts()) : $jobs->the_post();
            $standort = get_post_meta(get_the_ID(), '_job_standort', true);
            $arbeitszeit = get_post_meta(get_the_ID(), '_job_arbeitszeit', true);
            $erfahrung = get_post_meta(get_the_ID(), '_job_erfahrung', true);
    ?>
      <article class="job-card">
  <h2><?php the_title(); ?></h2>
  <ul class="job-info">
    <li><strong>Standort:</strong> <?php echo esc_html($standort); ?></li>
    <li><strong>Arbeitszeit:</strong> <?php echo esc_html($arbeitszeit); ?></li>
    <li><strong>Erfahrung:</strong> <?php echo esc_html($erfahrung); ?></li>
  </ul>

  <!-- Job description -->
  <p class="job-description">
    <?php the_content(); ?>
  </p>

  <a href="#" class="btn-apply">JETZT BEWERBEN</a>
</article>
    <?php
        endwhile;
        wp_reset_postdata();
    else :
        echo '<p>Derzeit sind keine Jobs verfügbar.</p>';
    endif;
    ?>
  </section>
</main>


<?php get_footer(); ?>