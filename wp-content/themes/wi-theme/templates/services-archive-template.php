<?php
/* Template Name: Services */

get_header(); ?>

<div class="services-main-wrapper">
	<div class="services-inner-wrapper">
		<div class="services-title-wrapper">
			<h1 class="services-title">Unsere Leistungen:</h1>		
		</div>
		<?php if (have_posts()) : ?>
			<div class="services-grid">
				<?php while (have_posts()) : the_post(); ?>
					<div class="service-item">						
							<?php if (has_post_thumbnail()) : ?>
								<div class="service-thumbnail">
									<a href="<?php the_permalink(); ?>">
									<?php the_post_thumbnail('full'); ?>
									</a>
								</div>
							<?php endif; ?>
					</div>
				<?php endwhile; ?>
			</div>
		<?php else : ?>
			<p>Keine Services gefunden.</p>
		<?php endif; ?>		
	</div>
</div>

<?php get_footer();
