<?php
get_header();
?>
<div class="content">
    <?php
    if (have_posts()) :
        while (have_posts()) : the_post();
            /*the_title('<h1>', '</h1>');*/
            the_content();
        endwhile;
    else :
        echo '<p>' . __('Keine Inhalte gefunden.', 'wi-theme') . '</p>';
    endif;
    ?>
</div>
<?php
get_footer();
?>