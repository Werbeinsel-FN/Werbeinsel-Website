<?php
/* Template Name: Single Service */

get_header(); // Lade den Header

if (have_posts()) : while (have_posts()) : the_post();
?>

<main id="service-main" class="service-main">
	<div class="inner-wrapper">
		<header class="service-title-wrapper">
			<h1 class="service-title">
				<?php the_title(); ?>
			</h1>
		</header>	
		<div class="service-content-wrapper">
			<div class="quote-wrapper">
				<div class="msg-wrapper">
					"Plakatieren ist meine Leidenschaft, die mir Spaß und Freude schafft."
				</div>
				<div class="author-wrapper">
					(Maurizio Lakovara, Plakatierer)
				</div>
			</div>
			<div class="service-thumbnail-wrapper">
				<?php
				if (has_post_thumbnail()) {
					$full_image_url = get_the_post_thumbnail_url(get_the_ID(), 'full');
					echo '<img src="' . esc_url($full_image_url) . '" alt="">';
				}
				?>			
			</div>
		</div>
	<div>
</main>

<?php 
endwhile; endif;
get_footer(); // Lade den Footer