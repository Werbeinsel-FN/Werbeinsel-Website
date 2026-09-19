<?php
/**
 * Home Page Sections - Metaboxes za About, Services, Portfolio, Clients, Testimonials, CTA
 * Dizajn iz Desktop/sajt - sve sekcije editabilne u WordPress
 */

if (!defined('ABSPATH')) exit;

// === ABOUT ===
function wi_home_about_cb($post) {
    $tpl = (string) get_page_template_slug($post->ID);
    if (!$tpl || strpos($tpl, 'home') === false) { echo '<p class="description">Nur für Home-Template.</p>'; return; }
    wp_nonce_field('wi_home_about_save', 'wi_home_about_nonce');
    $title = get_post_meta($post->ID, '_wi_about_title', true);
    $para1 = get_post_meta($post->ID, '_wi_about_para1', true);
    $para2 = get_post_meta($post->ID, '_wi_about_para2', true);
    ?>
    <p><label><strong>Überschrift</strong></label><br>
    <textarea name="wi_about_title" rows="2" class="widefat" placeholder="Ihre Agentur für klassische Werbung"><?php echo esc_textarea($title); ?></textarea></p>
    <p><label><strong>Absatz 1</strong></label><br>
    <textarea name="wi_about_para1" rows="2" class="widefat" placeholder="WERBEINSEL steht für klare Kommunikation..."><?php echo esc_textarea($para1); ?></textarea></p>
    <p><label><strong>Absatz 2</strong></label><br>
    <textarea name="wi_about_para2" rows="3" class="widefat" placeholder="Wir entwickeln, produzieren..."><?php echo esc_textarea($para2); ?></textarea></p>
    <?php
}

// === SERVICES (4 kartice) ===
function wi_home_services_design_cb($post) {
    $tpl = (string) get_page_template_slug($post->ID);
    if (!$tpl || strpos($tpl, 'home') === false) { echo '<p class="description">Nur für Home-Template.</p>'; return; }
    wp_nonce_field('wi_home_services_design_save', 'wi_home_services_design_nonce');
    $title_raw = get_post_meta($post->ID, '_wi_services_title', true);
    $t_trim = trim((string) $title_raw);
    $title = ($t_trim === '' || in_array(strtolower($t_trim), ['services', 'servicesa'], true)) ? 'Was wir machen' : $title_raw;
    $svc = get_page_by_path('services') ?: get_page_by_path('leistungen');
    $svc_base = $svc ? get_permalink($svc) : home_url('/services/');
    $default_links = [$svc_base . '#plakatwerbung', $svc_base . '#lass-kleben', $svc_base . '#pixel-code', $svc_base . '#print-design'];
    ?>
    <p><label><strong>Sektions-Titel</strong></label><br>
    <input type="text" name="wi_services_title" class="widefat" value="<?php echo esc_attr($title); ?>" placeholder="Was wir machen"></p>
    <?php
        $def_card_titles = ['Plakatwerbung', 'Folierung & Beschriftung', 'Digitale Werbemittel', 'Drucksachen'];
        $bad_values = ['servicea', 'servicesa', 'services', 'service'];
        for ($i = 1; $i <= 4; $i++):
        $img_id = get_post_meta($post->ID, "_wi_services_{$i}_img_id", true);
        $img_url = $img_id ? wp_get_attachment_image_url($img_id, 'medium') : '';
        $card_title_raw = get_post_meta($post->ID, "_wi_services_{$i}_title", true);
        $card_title = ($i <= 3 && in_array(strtolower(trim((string) $card_title_raw)), $bad_values, true)) ? $def_card_titles[$i-1] : ($card_title_raw ?: ($def_card_titles[$i-1] ?? ''));
        $card_desc = get_post_meta($post->ID, "_wi_services_{$i}_desc", true);
        $card_link = get_post_meta($post->ID, "_wi_services_{$i}_link", true) ?: ($default_links[$i-1] ?? '');
    ?>
    <div style="border:1px solid #ddd;padding:12px;margin:12px 0;background:#fafafa;border-radius:8px;">
        <h4>Karte <?php echo $i; ?></h4>
        <p><img id="wi_services_<?php echo $i; ?>_prev" src="<?php echo esc_url($img_url); ?>" style="max-width:120px;max-height:80px;object-fit:cover;display:<?php echo $img_url ? 'block' : 'none'; ?>" alt=""><br>
        <button type="button" class="button wi-pick-svc" data-slot="<?php echo $i; ?>">Bild wählen</button>
        <button type="button" class="button wi-clear-svc" data-slot="<?php echo $i; ?>">Entfernen</button>
        <input type="hidden" id="wi_services_<?php echo $i; ?>_img_id" name="wi_services_<?php echo $i; ?>_img_id" value="<?php echo esc_attr($img_id); ?>"></p>
        <p><label>Titel</label><br><input type="text" name="wi_services_<?php echo $i; ?>_title" class="widefat" value="<?php echo esc_attr($card_title); ?>" placeholder="<?php echo esc_attr($def_card_titles[$i-1] ?? 'Plakatwerbung'); ?>"></p>
        <p><label>Beschreibung</label><br><input type="text" name="wi_services_<?php echo $i; ?>_desc" class="widefat" value="<?php echo esc_attr($card_desc); ?>" placeholder="Auffällig. Präsent. Wirkungsvoll."></p>
        <p><label>Link</label><br><input type="url" name="wi_services_<?php echo $i; ?>_link" class="widefat" value="<?php echo esc_attr($card_link); ?>"></p>
    </div>
    <?php endfor; ?>
    <script>
    (function($){$(function(){var f;$('.wi-pick-svc').on('click',function(){var s=$(this).data('slot');if(f)f.close();f=wp.media({title:'Bild',button:{text:'Verwenden'},library:{type:'image'},multiple:false});f.on('select',function(){var a=f.state().get('selection').first().toJSON();$('#wi_services_'+s+'_img_id').val(a.id);$('#wi_services_'+s+'_prev').attr('src',a.sizes&&a.sizes.medium?a.sizes.medium.url:a.url).show();});f.open();});$('.wi-clear-svc').on('click',function(){var s=$(this).data('slot');$('#wi_services_'+s+'_img_id').val('');$('#wi_services_'+s+'_prev').attr('src','').hide();});});})(jQuery);
    </script>
    <?php
}

// === PORTFOLIO (6 projekata) ===
function wi_home_portfolio_cb($post) {
    $tpl = (string) get_page_template_slug($post->ID);
    if (!$tpl || strpos($tpl, 'home') === false) { echo '<p class="description">Nur für Home-Template.</p>'; return; }
    wp_nonce_field('wi_home_portfolio_save', 'wi_home_portfolio_nonce');
    $title = get_post_meta($post->ID, '_wi_portfolio_title', true);
    $subtitle = get_post_meta($post->ID, '_wi_portfolio_subtitle', true);
    $btn_text = get_post_meta($post->ID, '_wi_portfolio_btn_text', true);
    $btn_link = get_post_meta($post->ID, '_wi_portfolio_btn_link', true);
    $contact_url = '';
    if ($p = get_page_by_path('kontakt')) $contact_url = get_permalink($p->ID);
    elseif ($p = get_page_by_path('contact')) $contact_url = get_permalink($p->ID);
    else $contact_url = home_url('/kontakt/');
    ?>
    <p><label><strong>Titel</strong></label><br><textarea name="wi_portfolio_title" rows="2" class="widefat" placeholder="Unsere Arbeiten"><?php echo esc_textarea($title); ?></textarea></p>
    <p><label><strong>Untertitel</strong></label><br><textarea name="wi_portfolio_subtitle" rows="2" class="widefat" placeholder="Von Fahrzeugbeschriftung bis Großflächenplakat..."><?php echo esc_textarea($subtitle); ?></textarea></p>
    <p><label><strong>Button-Text</strong></label><br><input type="text" name="wi_portfolio_btn_text" class="widefat" value="<?php echo esc_attr($btn_text); ?>" placeholder="Ihr Projekt starten"></p>
    <p><label><strong>Button-Link</strong></label><br><input type="url" name="wi_portfolio_btn_link" class="widefat" value="<?php echo esc_attr($btn_link ?: $contact_url); ?>"></p>
    <hr><h4>Projekte (6)</h4>
    <?php
        $port_cat_uc_to_title = ['BESCHRIFTUNG' => 'Beschriftung', 'PLAKATIERUNG' => 'Plakatierung', 'AUSSENWERBUNG' => 'Außenwerbung'];
        for ($i = 1; $i <= 6; $i++):
        $img_id = get_post_meta($post->ID, "_wi_portfolio_{$i}_img_id", true);
        $img_url = $img_id ? wp_get_attachment_image_url($img_id, 'medium') : '';
        $proj_title = get_post_meta($post->ID, "_wi_portfolio_{$i}_title", true);
        $proj_cat_raw = get_post_meta($post->ID, "_wi_portfolio_{$i}_category", true);
        $proj_cat = ($proj_cat_raw && isset($port_cat_uc_to_title[strtoupper(trim($proj_cat_raw))])) ? $port_cat_uc_to_title[strtoupper(trim($proj_cat_raw))] : $proj_cat_raw;
        $proj_link = get_post_meta($post->ID, "_wi_portfolio_{$i}_link", true);
    ?>
    <div style="border:1px solid #ddd;padding:12px;margin:8px 0;background:#f9f9f9;border-radius:6px;">
        <strong>Projekt <?php echo $i; ?></strong>
        <p><img id="wi_port_<?php echo $i; ?>_prev" src="<?php echo esc_url($img_url); ?>" style="max-width:100px;height:75px;object-fit:cover;display:<?php echo $img_url ? 'inline' : 'none'; ?>"><br>
        <button type="button" class="button wi-pick-port" data-slot="<?php echo $i; ?>">Bild</button>
        <button type="button" class="button wi-clear-port" data-slot="<?php echo $i; ?>">x</button>
        <input type="hidden" id="wi_portfolio_<?php echo $i; ?>_img_id" name="wi_portfolio_<?php echo $i; ?>_img_id" value="<?php echo esc_attr($img_id); ?>"></p>
        <p><input type="text" name="wi_portfolio_<?php echo $i; ?>_title" class="widefat" value="<?php echo esc_attr($proj_title); ?>" placeholder="Titel"></p>
        <p><input type="text" name="wi_portfolio_<?php echo $i; ?>_category" class="widefat" value="<?php echo esc_attr($proj_cat); ?>" placeholder="Kategorie (z.B. Beschriftung)"></p>
        <p><input type="url" name="wi_portfolio_<?php echo $i; ?>_link" class="widefat" value="<?php echo esc_attr($proj_link); ?>" placeholder="Link (optional)"></p>
    </div>
    <?php endfor; ?>
    <script>
    (function($){$(function(){var f;$('.wi-pick-port').on('click',function(){var s=$(this).data('slot');if(f)f.close();f=wp.media({title:'Bild',button:{text:'OK'},library:{type:'image'},multiple:false});f.on('select',function(){var a=f.state().get('selection').first().toJSON();$('#wi_portfolio_'+s+'_img_id').val(a.id);$('#wi_port_'+s+'_prev').attr('src',a.sizes&&a.sizes.medium?a.sizes.medium.url:a.url).show();});f.open();});$('.wi-clear-port').on('click',function(){var s=$(this).data('slot');$('#wi_portfolio_'+s+'_img_id').val('');$('#wi_port_'+s+'_prev').hide();});});})(jQuery);
    </script>
    <?php
}

// === CLIENTS (naslov i podnaslov; logotipi iz plugina Clients Marquee) ===
function wi_home_clients_cb($post) {
    $tpl = (string) get_page_template_slug($post->ID);
    if (!$tpl || strpos($tpl, 'home') === false) { echo '<p class="description">Nur für Home-Template.</p>'; return; }
    wp_nonce_field('wi_home_clients_save', 'wi_home_clients_nonce');
    $title = get_post_meta($post->ID, '_wi_clients_title', true);
    $subtitle = get_post_meta($post->ID, '_wi_clients_subtitle', true);
    ?>
    <p><label><strong>Titel</strong></label><br><input type="text" name="wi_clients_title" class="widefat" value="<?php echo esc_attr($title); ?>" placeholder="OUR CLIENTS"></p>
    <p><label><strong>Untertitel</strong></label><br><textarea name="wi_clients_subtitle" rows="2" class="widefat" placeholder="Von Kultur bis Industrie..."><?php echo esc_textarea($subtitle); ?></textarea></p>
    <p class="description">Die Logos in den drei Reihen werden im Menü <strong>Clients Marquee</strong> verwaltet (Bilder wählen, Reihenfolge festlegen). Die Logos werden automatisch in Schwarz-Weiß dargestellt; auf schwarzer Pille wird das Logo invertiert (weiß), auf weißer Pille dunkel.</p>
    <?php
}

// === TESTIMONIALS (slider) ===
function wi_home_testimonials_cb($post) {
    $tpl = (string) get_page_template_slug($post->ID);
    if (!$tpl || strpos($tpl, 'home') === false) { echo '<p class="description">Nur für Home-Template.</p>'; return; }
    wp_nonce_field('wi_home_testimonials_save', 'wi_home_testimonials_nonce');
    $items = get_post_meta($post->ID, '_wi_testimonials', true);
    if (!is_array($items)) $items = [];
    while (count($items) < 4) $items[] = ['quote' => '', 'company' => '', 'person' => ''];
    ?>
    <p class="description">Testimonials für Slider. Mindestens 1 Eintrag.</p>
    <div id="wi-testimonials-list"><?php
    foreach ($items as $idx => $t):
        $q = isset($t['quote']) ? $t['quote'] : '';
        $c = isset($t['company']) ? $t['company'] : '';
        $p = isset($t['person']) ? $t['person'] : '';
    ?><div class="wi-test-row" style="border:1px solid #ddd;padding:12px;margin:10px 0;background:#fafafa;">
        <strong>#<?php echo $idx+1; ?></strong>
        <p><label>Zitat</label><br><textarea name="wi_test_quote[]" rows="3" class="widefat"><?php echo esc_textarea($q); ?></textarea></p>
        <p><label>Firma</label><br><input type="text" name="wi_test_company[]" class="widefat" value="<?php echo esc_attr($c); ?>"></p>
        <p><label>Person</label><br><input type="text" name="wi_test_person[]" class="widefat" value="<?php echo esc_attr($p); ?>"></p>
    </div><?php endforeach; ?></div>
    <button type="button" class="button" id="wi-add-testimonial">+ Testimonial hinzufügen</button>
    <script>
    (function($){$('#wi-add-testimonial').on('click',function(){var n=$('.wi-test-row').length+1;$('#wi-testimonials-list').append('<div class="wi-test-row" style="border:1px solid #ddd;padding:12px;margin:10px 0;background:#fafafa;"><strong>#'+n+'</strong><p><label>Zitat</label><br><textarea name="wi_test_quote[]" rows="3" class="widefat"></textarea></p><p><label>Firma</label><br><input type="text" name="wi_test_company[]" class="widefat"></p><p><label>Person</label><br><input type="text" name="wi_test_person[]" class="widefat"></p></div>');});})(jQuery);
    </script>
    <?php
}

// === CTA (finalni poziv) ===
function wi_home_cta_design_cb($post) {
    $tpl = (string) get_page_template_slug($post->ID);
    if (!$tpl || strpos($tpl, 'home') === false) { echo '<p class="description">Nur für Home-Template.</p>'; return; }
    wp_nonce_field('wi_home_cta_design_save', 'wi_home_cta_design_nonce');
    $title = get_post_meta($post->ID, '_wi_cta_title', true);
    $subtitle = get_post_meta($post->ID, '_wi_cta_subtitle', true);
    $btn_text = get_post_meta($post->ID, '_wi_cta_btn_text', true);
    $btn_link = get_post_meta($post->ID, '_wi_cta_btn_link', true);
    $contact_url = '';
    if ($p = get_page_by_path('kontakt')) $contact_url = get_permalink($p->ID);
    elseif ($p = get_page_by_path('contact')) $contact_url = get_permalink($p->ID);
    else $contact_url = home_url('/kontakt/');
    ?>
    <p><label><strong>Titel</strong></label><br><textarea name="wi_cta_title" rows="2" class="widefat" placeholder="Bereit für Ihr nächstes Projekt?"><?php echo esc_textarea($title); ?></textarea></p>
    <p><label><strong>Untertitel</strong></label><br><textarea name="wi_cta_subtitle" rows="2" class="widefat" placeholder="Lassen Sie uns über Ihre Werbeziele sprechen..."><?php echo esc_textarea($subtitle); ?></textarea></p>
    <p><label><strong>Button-Text</strong></label><br><input type="text" name="wi_cta_btn_text" class="widefat" value="<?php echo esc_attr($btn_text); ?>" placeholder="JETZT ANFRAGEN"></p>
    <p><label><strong>Button-Link</strong></label><br><input type="url" name="wi_cta_btn_link" class="widefat" value="<?php echo esc_attr($btn_link ?: $contact_url); ?>"></p>
    <?php
}

// === SAVE HANDLERS ===
add_action('save_post_page', function($post_id) {
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (!current_user_can('edit_page', $post_id)) return;

    $allowed_br = ['br' => []];

    // About
    if (isset($_POST['wi_home_about_nonce']) && wp_verify_nonce($_POST['wi_home_about_nonce'], 'wi_home_about_save')) {
        foreach (['_wi_about_title' => 'wi_about_title', '_wi_about_para1' => 'wi_about_para1', '_wi_about_para2' => 'wi_about_para2'] as $meta => $key) {
            $v = isset($_POST[$key]) ? wp_kses($_POST[$key], $allowed_br) : '';
            if ($v) update_post_meta($post_id, $meta, $v); else delete_post_meta($post_id, $meta);
        }
    }

    // Services
    if (isset($_POST['wi_home_services_design_nonce']) && wp_verify_nonce($_POST['wi_home_services_design_nonce'], 'wi_home_services_design_save')) {
        if (isset($_POST['wi_services_title'])) {
            $t = trim(sanitize_text_field($_POST['wi_services_title']));
            $t = ($t === '' || in_array(strtolower($t), ['services', 'servicesa'], true)) ? 'Was wir machen' : $t;
            update_post_meta($post_id, '_wi_services_title', $t);
        }
        $def_card_titles = ['Plakatwerbung', 'Folierung & Beschriftung', 'Digitale Werbemittel', 'Drucksachen'];
        $bad_values = ['servicea', 'servicesa', 'services', 'service'];
        for ($i = 1; $i <= 4; $i++) {
            $img_id = isset($_POST["wi_services_{$i}_img_id"]) ? intval($_POST["wi_services_{$i}_img_id"]) : 0;
            $title = isset($_POST["wi_services_{$i}_title"]) ? sanitize_text_field($_POST["wi_services_{$i}_title"]) : '';
            if ($i <= 3 && in_array(strtolower(trim($title)), $bad_values, true)) $title = $def_card_titles[$i-1];
            $desc = isset($_POST["wi_services_{$i}_desc"]) ? sanitize_text_field($_POST["wi_services_{$i}_desc"]) : '';
            $link = isset($_POST["wi_services_{$i}_link"]) ? esc_url_raw($_POST["wi_services_{$i}_link"]) : '';
            if ($img_id) update_post_meta($post_id, "_wi_services_{$i}_img_id", $img_id); else delete_post_meta($post_id, "_wi_services_{$i}_img_id");
            if ($title) update_post_meta($post_id, "_wi_services_{$i}_title", $title); else delete_post_meta($post_id, "_wi_services_{$i}_title");
            if ($desc) update_post_meta($post_id, "_wi_services_{$i}_desc", $desc); else delete_post_meta($post_id, "_wi_services_{$i}_desc");
            if ($link) update_post_meta($post_id, "_wi_services_{$i}_link", $link); else delete_post_meta($post_id, "_wi_services_{$i}_link");
        }
    }

    // Portfolio
    if (isset($_POST['wi_home_portfolio_nonce']) && wp_verify_nonce($_POST['wi_home_portfolio_nonce'], 'wi_home_portfolio_save')) {
        foreach (['_wi_portfolio_title' => 'wi_portfolio_title', '_wi_portfolio_subtitle' => 'wi_portfolio_subtitle', '_wi_portfolio_btn_text' => 'wi_portfolio_btn_text', '_wi_portfolio_btn_link' => 'wi_portfolio_btn_link'] as $meta => $key) {
            $v = isset($_POST[$key]) ? wp_kses($_POST[$key], $allowed_br) : '';
            if ($v) update_post_meta($post_id, $meta, $v); else delete_post_meta($post_id, $meta);
        }
        $port_cat_uc_to_title = ['BESCHRIFTUNG' => 'Beschriftung', 'PLAKATIERUNG' => 'Plakatierung', 'AUSSENWERBUNG' => 'Außenwerbung'];
        for ($i = 1; $i <= 6; $i++) {
            $img_id = isset($_POST["wi_portfolio_{$i}_img_id"]) ? intval($_POST["wi_portfolio_{$i}_img_id"]) : 0;
            $title = isset($_POST["wi_portfolio_{$i}_title"]) ? sanitize_text_field($_POST["wi_portfolio_{$i}_title"]) : '';
            $cat = isset($_POST["wi_portfolio_{$i}_category"]) ? sanitize_text_field($_POST["wi_portfolio_{$i}_category"]) : '';
            if ($cat && isset($port_cat_uc_to_title[strtoupper(trim($cat))])) $cat = $port_cat_uc_to_title[strtoupper(trim($cat))];
            $link = isset($_POST["wi_portfolio_{$i}_link"]) ? esc_url_raw($_POST["wi_portfolio_{$i}_link"]) : '';
            if ($img_id) update_post_meta($post_id, "_wi_portfolio_{$i}_img_id", $img_id); else delete_post_meta($post_id, "_wi_portfolio_{$i}_img_id");
            if ($title) update_post_meta($post_id, "_wi_portfolio_{$i}_title", $title); else delete_post_meta($post_id, "_wi_portfolio_{$i}_title");
            if ($cat) update_post_meta($post_id, "_wi_portfolio_{$i}_category", $cat); else delete_post_meta($post_id, "_wi_portfolio_{$i}_category");
            if ($link) update_post_meta($post_id, "_wi_portfolio_{$i}_link", $link); else delete_post_meta($post_id, "_wi_portfolio_{$i}_link");
        }
    }

    // Clients (nur Titel und Untertitel; Logos aus Plugin Clients Marquee)
    if (isset($_POST['wi_home_clients_nonce']) && wp_verify_nonce($_POST['wi_home_clients_nonce'], 'wi_home_clients_save')) {
        if (isset($_POST['wi_clients_title'])) update_post_meta($post_id, '_wi_clients_title', sanitize_text_field($_POST['wi_clients_title']));
        if (isset($_POST['wi_clients_subtitle'])) update_post_meta($post_id, '_wi_clients_subtitle', wp_kses($_POST['wi_clients_subtitle'], $allowed_br));
    }

    // Testimonials
    if (isset($_POST['wi_home_testimonials_nonce']) && wp_verify_nonce($_POST['wi_home_testimonials_nonce'], 'wi_home_testimonials_save')) {
        $quotes = isset($_POST['wi_test_quote']) && is_array($_POST['wi_test_quote']) ? $_POST['wi_test_quote'] : [];
        $companies = isset($_POST['wi_test_company']) && is_array($_POST['wi_test_company']) ? $_POST['wi_test_company'] : [];
        $persons = isset($_POST['wi_test_person']) && is_array($_POST['wi_test_person']) ? $_POST['wi_test_person'] : [];
        $out = [];
        foreach ($quotes as $i => $q) {
            $q = wp_kses($q, $allowed_br);
            $c = isset($companies[$i]) ? sanitize_text_field($companies[$i]) : '';
            $p = isset($persons[$i]) ? sanitize_text_field($persons[$i]) : '';
            if ($q || $c || $p) $out[] = ['quote' => $q, 'company' => $c, 'person' => $p];
        }
        update_post_meta($post_id, '_wi_testimonials', $out);
    }

    // CTA
    if (isset($_POST['wi_home_cta_design_nonce']) && wp_verify_nonce($_POST['wi_home_cta_design_nonce'], 'wi_home_cta_design_save')) {
        if (isset($_POST['wi_cta_title'])) update_post_meta($post_id, '_wi_cta_title', wp_kses($_POST['wi_cta_title'], $allowed_br));
        if (isset($_POST['wi_cta_subtitle'])) update_post_meta($post_id, '_wi_cta_subtitle', wp_kses($_POST['wi_cta_subtitle'], $allowed_br));
        if (isset($_POST['wi_cta_btn_text'])) update_post_meta($post_id, '_wi_cta_btn_text', sanitize_text_field($_POST['wi_cta_btn_text']));
        if (isset($_POST['wi_cta_btn_link'])) update_post_meta($post_id, '_wi_cta_btn_link', esc_url_raw($_POST['wi_cta_btn_link']));
    }
});
