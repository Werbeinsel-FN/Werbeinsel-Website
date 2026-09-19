<?php
/**
 * Hero Section Metabox - za Home template
 * Pozadinska slika, naslov, podnaslov i dugmad
 */

function wi_home_hero_section_cb($post) {
    $tpl = (string) get_page_template_slug($post->ID);
    if (!$tpl || strpos($tpl, 'home') === false) {
        echo '<p style="color:#666;">'.esc_html__('Dieser Bereich ist nur auf dem Home-Template verfügbar.', 'wi').'</p>';
        return;
    }
    wp_nonce_field('wi_save_home_hero', 'wi_home_hero_nonce');
    $bg_id = get_post_meta($post->ID, '_wi_hero_bg_image_id', true);
    $bg_url = $bg_id ? wp_get_attachment_image_url($bg_id, 'large') : '';
    $title = get_post_meta($post->ID, '_wi_hero_title', true);
    $subtitle = get_post_meta($post->ID, '_wi_hero_subtitle', true);
    $btn1_text = get_post_meta($post->ID, '_wi_hero_btn1_text', true);
    $btn1_link = get_post_meta($post->ID, '_wi_hero_btn1_link', true);
    $btn2_text = get_post_meta($post->ID, '_wi_hero_btn2_text', true);
    $btn2_link = get_post_meta($post->ID, '_wi_hero_btn2_link', true);
    ?>
    <style>.wi-hero-row{margin:14px 0}.wi-hero-thumb{max-width:200px;max-height:120px;object-fit:cover;border-radius:8px;display:block;background:#eee}.wi-hero-actions{margin-top:8px}</style>
    <div class="wi-hero-row">
        <label><strong><?php esc_html_e('Hintergrundbild', 'wi'); ?></strong></label>
        <img id="wi_hero_bg_preview" class="wi-hero-thumb" src="<?php echo esc_url($bg_url); ?>" alt="" style="<?php echo $bg_url ? '' : 'display:none'; ?>">
        <div class="wi-hero-actions">
            <input type="hidden" id="wi_hero_bg_image_id" name="wi_hero_bg_image_id" value="<?php echo esc_attr($bg_id); ?>">
            <button type="button" class="button" id="wi_hero_bg_select"><?php esc_html_e('Bild auswählen', 'wi'); ?></button>
            <button type="button" class="button" id="wi_hero_bg_clear"><?php esc_html_e('Entfernen', 'wi'); ?></button>
        </div>
        <p class="description"><?php esc_html_e('Hintergrundbild des Hero-Bereichs.', 'wi'); ?></p>
    </div>
    <div class="wi-hero-row">
        <label><strong><?php esc_html_e('Überschrift', 'wi'); ?></strong></label>
        <textarea name="wi_hero_title" id="wi_hero_title" rows="2" class="widefat" placeholder="Ihre Werbung.&#10;Unser&#10;Handwerk."><?php echo esc_textarea($title); ?></textarea>
        <p class="description"><?php esc_html_e('Mit &lt;br&gt; lässt sich ein Zeilenumbruch einfügen.', 'wi'); ?></p>
    </div>
    <div class="wi-hero-row">
        <label><strong><?php esc_html_e('Unterüberschrift', 'wi'); ?></strong></label>
        <textarea name="wi_hero_subtitle" id="wi_hero_subtitle" rows="2" class="widefat" placeholder="Plakat. Folie. Digital."><?php echo esc_textarea($subtitle); ?></textarea>
    </div>
    <div class="wi-hero-row">
        <label><strong><?php esc_html_e('Button 1 (Projekt anfragen)', 'wi'); ?></strong></label>
        <input type="text" name="wi_hero_btn1_text" value="<?php echo esc_attr($btn1_text); ?>" placeholder="Projekt anfragen" class="widefat" style="margin-bottom:6px;">
        <input type="url" name="wi_hero_btn1_link" value="<?php echo esc_attr($btn1_link); ?>" placeholder="<?php echo esc_attr(home_url('/kontakt/')); ?>" class="widefat">
    </div>
    <div class="wi-hero-row">
        <label><strong><?php esc_html_e('Button 2 (Services ansehen)', 'wi'); ?></strong></label>
        <input type="text" name="wi_hero_btn2_text" value="<?php echo esc_attr($btn2_text); ?>" placeholder="Services ansehen" class="widefat" style="margin-bottom:6px;">
        <input type="text" name="wi_hero_btn2_link" value="<?php echo esc_attr($btn2_link); ?>" placeholder="#services" class="widefat">
        <p class="description"><?php esc_html_e('Der Link #services scrollt zum Services-Bereich.', 'wi'); ?></p>
    </div>
    <script>
    (function($){$(function(){var frame;$('#wi_hero_bg_select').on('click',function(e){e.preventDefault();if(frame)frame.close();frame=wp.media({title:'Bild auswählen',button:{text:'Bild übernehmen'},library:{type:'image'},multiple:false});frame.on('select',function(){var att=frame.state().get('selection').first().toJSON();$('#wi_hero_bg_image_id').val(att.id);$('#wi_hero_bg_preview').attr('src',att.sizes&&att.sizes.large?att.sizes.large.url:att.url).show();});frame.open();});$('#wi_hero_bg_clear').on('click',function(){$('#wi_hero_bg_image_id').val('');$('#wi_hero_bg_preview').attr('src','').hide();});});})(jQuery);
    </script>
    <?php
}

add_action('save_post_page', function ($post_id) {
    if (!isset($_POST['wi_home_hero_nonce']) || !wp_verify_nonce($_POST['wi_home_hero_nonce'], 'wi_save_home_hero')) return;
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (!current_user_can('edit_page', $post_id)) return;

    $allowed_br = array('br' => array());
    $bg_id = isset($_POST['wi_hero_bg_image_id']) ? intval($_POST['wi_hero_bg_image_id']) : 0;
    if ($bg_id) update_post_meta($post_id, '_wi_hero_bg_image_id', $bg_id); else delete_post_meta($post_id, '_wi_hero_bg_image_id');

    $title = isset($_POST['wi_hero_title']) ? wp_kses($_POST['wi_hero_title'], $allowed_br) : '';
    $subtitle = isset($_POST['wi_hero_subtitle']) ? wp_kses($_POST['wi_hero_subtitle'], $allowed_br) : '';
    if ($title !== '') update_post_meta($post_id, '_wi_hero_title', $title); else delete_post_meta($post_id, '_wi_hero_title');
    if ($subtitle !== '') update_post_meta($post_id, '_wi_hero_subtitle', $subtitle); else delete_post_meta($post_id, '_wi_hero_subtitle');

    $btn1_text = isset($_POST['wi_hero_btn1_text']) ? sanitize_text_field($_POST['wi_hero_btn1_text']) : '';
    $btn1_link = isset($_POST['wi_hero_btn1_link']) ? esc_url_raw($_POST['wi_hero_btn1_link']) : '';
    $btn2_text = isset($_POST['wi_hero_btn2_text']) ? sanitize_text_field($_POST['wi_hero_btn2_text']) : '';
    $btn2_link = isset($_POST['wi_hero_btn2_link']) ? esc_url_raw($_POST['wi_hero_btn2_link']) : '';
    if ($btn1_text !== '') update_post_meta($post_id, '_wi_hero_btn1_text', $btn1_text); else delete_post_meta($post_id, '_wi_hero_btn1_text');
    if ($btn1_link !== '') update_post_meta($post_id, '_wi_hero_btn1_link', $btn1_link); else delete_post_meta($post_id, '_wi_hero_btn1_link');
    if ($btn2_text !== '') update_post_meta($post_id, '_wi_hero_btn2_text', $btn2_text); else delete_post_meta($post_id, '_wi_hero_btn2_text');
    if ($btn2_link !== '') update_post_meta($post_id, '_wi_hero_btn2_link', $btn2_link); else delete_post_meta($post_id, '_wi_hero_btn2_link');
});
