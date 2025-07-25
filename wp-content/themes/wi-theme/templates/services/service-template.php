<?php
/* Template Name: Single Service */

get_header(); // Lade den Header

if (have_posts()) : while (have_posts()) : the_post();
?>

<div class="service-main-wrapper">
	
	<div class="service-title-wrapper">
		<h2 class="service-title">
			<?php the_title(); ?>
		</h2>
	</div>
	
	<div class="service-content-wrapper">
		<div class="service-thumbnail-wrapper">
			<?php
			if (has_post_thumbnail()) {
				$full_image_url = get_the_post_thumbnail_url(get_the_ID(), 'full');
				echo '<img src="' . esc_url($full_image_url) . '" alt="">';
			}
			?>			
		</div>	
	</div>
</div>

<?php 
endwhile; endif;
get_footer(); // Lade den Footer