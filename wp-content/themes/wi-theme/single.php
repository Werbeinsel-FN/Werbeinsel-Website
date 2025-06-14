<?php
get_header();
?>
<main>
	<div class="post-main-wrapper">
		<?php
		if (have_posts()) :
			while (have_posts()) : the_post(); ?>
				<div class="post-title-wrapper">            
					<?php the_title('<h1>', '</h1>'); ?>
				</div>
				<div class="post-thumbnail-wrapper">            
					<?php the_post_thumbnail('large', ['class' => 'post-thumbnail', 'alt' => get_the_title()]); ?>
				</div>	
				<div class="post-content-wrapper">				
					<?php the_content(); ?>
				</div>
			<?php
			endwhile;
		else :
			echo '<p>' . __('Keine Inhalte gefunden.', 'wi-theme') . '</p>';
		endif;
		?>
	</div>
</main>	

<?php
get_footer();
?>
