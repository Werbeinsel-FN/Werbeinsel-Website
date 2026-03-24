<?php
/**
 * Jobs-Template: Metaboxen (Inhalte wie Desktop/sajt – editierbar je Seite).
 */
if (!defined('ABSPATH')) {
  exit;
}

require_once get_template_directory() . '/inc/jobs-data.php';

/**
 * Ein Akkordeon-Stellenblock im Admin (Wiederholer).
 *
 * @param int   $index 0,1,2,…
 * @param array $job   title, tags[], description, tasks (array|string), requirements
 * @param bool  $is_tpl Wenn true: Platzhalter __WI_JI__ für JS-Klonen
 */
function wi_jobs_metabox_render_stelle_row($index, array $job, $is_tpl = false) {
  $ji       = $is_tpl ? '__WI_JI__' : (string) (int) $index;
  $name_pre = $is_tpl ? 'wi_jobs_job[__WI_JI__]' : 'wi_jobs_job[' . (int) $index . ']';
  $title    = isset($job['title']) ? (string) $job['title'] : '';
  $desc     = isset($job['description']) ? (string) $job['description'] : '';
  $tasks    = $job['tasks'] ?? [];
  $req      = $job['requirements'] ?? [];
  if (is_array($tasks)) {
    $tasks = implode("\n", $tasks);
  }
  if (is_array($req)) {
    $req = implode("\n", $req);
  }
  $tags = isset($job['tags']) && is_array($job['tags']) ? $job['tags'] : [];
  while (count($tags) < 3) {
    $tags[] = ['text' => '', 'highlighted' => false];
  }
  ?>
  <div class="wi-jobs-job-block" data-wi-jobs-row="<?php echo esc_attr($ji); ?>" style="border:1px solid #ccc;padding:12px;margin:12px 0;background:#fafafa;border-radius:8px;position:relative;">
    <?php if (!$is_tpl) : ?>
      <p style="margin:0 0 8px;"><strong><?php echo esc_html(sprintf(/* translators: %d: index */ __('Stelle %d', 'wi'), (int) $index + 1)); ?></strong></p>
    <?php else : ?>
      <p style="margin:0 0 8px;"><strong class="wi-jobs-stelle-label"><?php esc_html_e('Neue Stelle', 'wi'); ?></strong></p>
    <?php endif; ?>
    <p><label><?php esc_html_e('Titel', 'wi'); ?></label><br>
    <input type="text" name="<?php echo esc_attr($name_pre); ?>[title]" class="widefat" value="<?php echo esc_attr($title); ?>"></p>
    <?php
    for ($ti = 1; $ti <= 3; $ti++) :
      $tg = $tags[$ti - 1] ?? ['text' => '', 'highlighted' => false];
      $tx = isset($tg['text']) ? (string) $tg['text'] : '';
      $hi = !empty($tg['highlighted']);
      ?>
      <p style="display:flex;align-items:center;gap:12px;flex-wrap:wrap;">
        <label style="flex:1;min-width:200px;"><?php echo esc_html(sprintf(/* translators: %d: tag number */ __('Tag %d', 'wi'), $ti)); ?><br>
        <input type="text" name="<?php echo esc_attr($name_pre); ?>[tag<?php echo (int) $ti; ?>]" class="widefat" value="<?php echo esc_attr($tx); ?>"></label>
        <label><input type="checkbox" name="<?php echo esc_attr($name_pre); ?>[tag<?php echo (int) $ti; ?>_hi]" value="1" <?php checked($hi); ?>> <?php esc_html_e('Gelb hervorheben', 'wi'); ?></label>
      </p>
    <?php endfor; ?>
    <p><label><?php esc_html_e('Kurzbeschreibung', 'wi'); ?></label><br>
    <textarea name="<?php echo esc_attr($name_pre); ?>[desc]" rows="2" class="widefat"><?php echo esc_textarea($desc); ?></textarea></p>
    <p><label><?php esc_html_e('Aufgaben (eine Zeile pro Punkt)', 'wi'); ?></label><br>
    <textarea name="<?php echo esc_attr($name_pre); ?>[tasks]" rows="5" class="widefat"><?php echo esc_textarea((string) $tasks); ?></textarea></p>
    <p><label><?php esc_html_e('Anforderungen (eine Zeile pro Punkt)', 'wi'); ?></label><br>
    <textarea name="<?php echo esc_attr($name_pre); ?>[req]" rows="5" class="widefat"><?php echo esc_textarea((string) $req); ?></textarea></p>
    <?php if (!$is_tpl) : ?>
      <p style="margin:0;"><button type="button" class="button wi-jobs-remove-stelle"><?php esc_html_e('Stelle entfernen', 'wi'); ?></button></p>
    <?php endif; ?>
  </div>
  <?php
}

function wi_jobs_delete_legacy_job_meta($post_id) {
  for ($ji = 1; $ji <= 15; $ji++) {
    delete_post_meta($post_id, "_wi_jobs_j{$ji}_title");
    delete_post_meta($post_id, "_wi_jobs_j{$ji}_desc");
    delete_post_meta($post_id, "_wi_jobs_j{$ji}_tasks");
    delete_post_meta($post_id, "_wi_jobs_j{$ji}_req");
    for ($ti = 1; $ti <= 3; $ti++) {
      delete_post_meta($post_id, "_wi_jobs_j{$ji}_tag{$ti}");
      delete_post_meta($post_id, "_wi_jobs_j{$ji}_tag{$ti}_hi");
    }
  }
}

function wi_jobs_metabox_hero_cb($post) {
  if (!wi_is_jobs_template($post->ID)) {
    echo '<p class="description">Nur für die Seite mit Template „Jobs“.</p>';
    return;
  }
  wp_nonce_field('wi_jobs_hero_save', 'wi_jobs_hero_nonce');
  $d = wi_jobs_default_data();
  ?>
  <p><label><strong>Hero-Titel</strong> (Zeilenumbruch = neuer Zeile)</label><br>
  <textarea name="wi_jobs_hero_title" rows="3" class="widefat"><?php echo esc_textarea(get_post_meta($post->ID, '_wi_jobs_hero_title', true) ?: $d['hero_title']); ?></textarea></p>
  <p><label><strong>Hero-Einleitung</strong></label><br>
  <textarea name="wi_jobs_hero_intro" rows="3" class="widefat"><?php echo esc_textarea(get_post_meta($post->ID, '_wi_jobs_hero_intro', true) ?: $d['hero_intro']); ?></textarea></p>
  <p><label><strong>Überschrift offene Stellen</strong></label><br>
  <input type="text" name="wi_jobs_open_title" class="widefat" value="<?php echo esc_attr(get_post_meta($post->ID, '_wi_jobs_open_title', true) ?: $d['open_title']); ?>"></p>
  <?php
}

function wi_jobs_metabox_jobs_cb($post) {
  if (!wi_is_jobs_template($post->ID)) {
    echo '<p class="description">Nur für die Seite mit Template „Jobs“.</p>';
    return;
  }
  wp_nonce_field('wi_jobs_jobs_save', 'wi_jobs_jobs_nonce');
  $jobs = wi_jobs_get_page_data($post->ID)['jobs'];
  ?>
  <p class="description"><?php esc_html_e('Beliebig viele Stellen für die Sektion „HIER IST PLATZ FÜR DICH“. Mindestens eine Zeile mit Titel sollte ausgefüllt sein.', 'wi'); ?></p>
  <div id="wi-jobs-stellen-repeater">
    <?php
    foreach (array_values($jobs) as $i => $job) {
      wi_jobs_metabox_render_stelle_row($i, $job, false);
    }
    ?>
  </div>
  <p>
    <button type="button" class="button button-primary" id="wi-jobs-add-stelle">+ <?php esc_html_e('Stelle hinzufügen', 'wi'); ?></button>
  </p>
  <template id="wi-jobs-stelle-template">
    <?php wi_jobs_metabox_render_stelle_row(0, ['title' => '', 'tags' => [], 'description' => '', 'tasks' => '', 'requirements' => ''], true); ?>
  </template>
  <script>
  (function(){
    var wrap = document.getElementById('wi-jobs-stellen-repeater');
    var tpl  = document.getElementById('wi-jobs-stelle-template');
    var addB = document.getElementById('wi-jobs-add-stelle');
    if (!wrap || !tpl || !addB) return;
    function reindex(){
      var blocks = wrap.querySelectorAll('.wi-jobs-job-block');
      blocks.forEach(function(el, i){
        el.querySelectorAll('input[name], textarea[name]').forEach(function(inp){
          var n = inp.getAttribute('name');
          if (!n) return;
          inp.setAttribute('name', n.replace(/wi_jobs_job\[[^\]]+\]/, 'wi_jobs_job[' + i + ']'));
        });
        var strong = el.querySelector('p strong');
        if (strong && !strong.classList.contains('wi-jobs-stelle-label')) {
          strong.textContent = '<?php echo esc_js(__('Stelle', 'wi')); ?> ' + (i + 1);
        }
      });
    }
    addB.addEventListener('click', function(e){
      e.preventDefault();
      var html = tpl.innerHTML.replace(/__WI_JI__/g, 'new');
      var div = document.createElement('div');
      div.innerHTML = html.trim();
      var block = div.firstElementChild;
      if (block) {
        var rm = document.createElement('p');
        rm.style.margin = '0';
        var btn = document.createElement('button');
        btn.type = 'button';
        btn.className = 'button wi-jobs-remove-stelle';
        btn.textContent = '<?php echo esc_js(__('Stelle entfernen', 'wi')); ?>';
        rm.appendChild(btn);
        block.appendChild(rm);
        wrap.appendChild(block);
        reindex();
      }
    });
    wrap.addEventListener('click', function(e){
      var t = e.target;
      if (!t.classList || !t.classList.contains('wi-jobs-remove-stelle')) return;
      e.preventDefault();
      if (wrap.querySelectorAll('.wi-jobs-job-block').length <= 1) {
        window.alert('<?php echo esc_js(__('Mindestens eine Stelle behalten.', 'wi')); ?>');
        return;
      }
      t.closest('.wi-jobs-job-block').remove();
      reindex();
    });
  })();
  </script>
  <?php
}

function wi_jobs_metabox_process_cb($post) {
  if (!wi_is_jobs_template($post->ID)) {
    echo '<p class="description">Nur für die Seite mit Template „Jobs“.</p>';
    return;
  }
  wp_nonce_field('wi_jobs_process_save', 'wi_jobs_process_nonce');
  $d = wi_jobs_default_data();
  ?>
  <p><label><strong>Sektions-Titel</strong></label><br>
  <input type="text" name="wi_jobs_process_title" class="widefat" value="<?php echo esc_attr(get_post_meta($post->ID, '_wi_jobs_process_title', true) ?: $d['process_title']); ?>"></p>
  <?php
  foreach ([1, 2, 3, 4] as $si) {
    $s = $d['steps'][$si - 1];
    ?>
    <div style="border:1px dashed #999;padding:10px;margin:10px 0;">
      <strong>Schritt <?php echo (int) $si; ?></strong>
      <p><label>Nummer (z.B. 01)</label><input type="text" name="wi_jobs_s<?php echo (int) $si; ?>_num" class="widefat" value="<?php echo esc_attr(get_post_meta($post->ID, "_wi_jobs_s{$si}_num", true) ?: $s['num']); ?>"></p>
      <p><label>Titel</label><input type="text" name="wi_jobs_s<?php echo (int) $si; ?>_title" class="widefat" value="<?php echo esc_attr(get_post_meta($post->ID, "_wi_jobs_s{$si}_title", true) ?: $s['title']); ?>"></p>
      <p><label>Text</label><textarea name="wi_jobs_s<?php echo (int) $si; ?>_text" rows="2" class="widefat"><?php echo esc_textarea(get_post_meta($post->ID, "_wi_jobs_s{$si}_text", true) ?: $s['text']); ?></textarea></p>
    </div>
    <?php
  }
}

function wi_jobs_metabox_form_cb($post) {
  if (!wi_is_jobs_template($post->ID)) {
    echo '<p class="description">Nur für die Seite mit Template „Jobs“.</p>';
    return;
  }
  wp_nonce_field('wi_jobs_form_save', 'wi_jobs_form_nonce');
  $d = wi_jobs_default_data();
  ?>
  <p><label><strong>Formular-Überschrift</strong></label><br>
  <input type="text" name="wi_jobs_form_title" class="widefat" value="<?php echo esc_attr(get_post_meta($post->ID, '_wi_jobs_form_title', true) ?: $d['form_title']); ?>"></p>
  <p><label><strong>Formular-Untertitel</strong></label><br>
  <textarea name="wi_jobs_form_subtitle" rows="2" class="widefat"><?php echo esc_textarea(get_post_meta($post->ID, '_wi_jobs_form_subtitle', true) ?: $d['form_subtitle']); ?></textarea></p>
  <p><label><strong>Positions-Buttons</strong> (eine Zeile pro Button)</label><br>
  <textarea name="wi_jobs_positions_lines" rows="5" class="widefat"><?php echo esc_textarea(get_post_meta($post->ID, '_wi_jobs_positions_lines', true) ?: $d['positions_lines']); ?></textarea></p>
  <p><label><strong>„Verfügbar ab“-Buttons</strong> (eine Zeile pro Button)</label><br>
  <textarea name="wi_jobs_dates_lines" rows="4" class="widefat"><?php echo esc_textarea(get_post_meta($post->ID, '_wi_jobs_dates_lines', true) ?: $d['dates_lines']); ?></textarea></p>
  <?php
}

function wi_jobs_metabox_close_cb($post) {
  if (!wi_is_jobs_template($post->ID)) {
    echo '<p class="description">Nur für die Seite mit Template „Jobs“.</p>';
    return;
  }
  wp_nonce_field('wi_jobs_close_save', 'wi_jobs_close_nonce');
  $d = wi_jobs_default_data();
  ?>
  <p><label><strong>Abschluss-Titel</strong></label><br>
  <input type="text" name="wi_jobs_close_title" class="widefat" value="<?php echo esc_attr(get_post_meta($post->ID, '_wi_jobs_close_title', true) ?: $d['close_title']); ?>"></p>
  <p><label><strong>Abschluss-Text</strong></label><br>
  <textarea name="wi_jobs_close_text" rows="4" class="widefat"><?php echo esc_textarea(get_post_meta($post->ID, '_wi_jobs_close_text', true) ?: $d['close_text']); ?></textarea></p>
  <?php
}

function wi_register_jobs_metaboxes($post) {
  if (!$post instanceof WP_Post) {
    return;
  }
  if (!wi_is_jobs_template($post->ID)) {
    return;
  }
  add_meta_box('wi_jobs_hero', __('Jobs: Hero & Stellenliste', 'wi'), 'wi_jobs_metabox_hero_cb', 'page', 'normal', 'high');
  add_meta_box('wi_jobs_positions', __('Jobs: Stellen (Akkordeon, beliebig viele)', 'wi'), 'wi_jobs_metabox_jobs_cb', 'page', 'normal', 'high');
  add_meta_box('wi_jobs_process', __('Jobs: Bewerbungsprozess (4 Schritte)', 'wi'), 'wi_jobs_metabox_process_cb', 'page', 'normal', 'high');
  add_meta_box('wi_jobs_form', __('Jobs: Formular-Texte & Buttons', 'wi'), 'wi_jobs_metabox_form_cb', 'page', 'normal', 'high');
  add_meta_box('wi_jobs_close', __('Jobs: Abschluss-Sektion', 'wi'), 'wi_jobs_metabox_close_cb', 'page', 'normal', 'high');
}
add_action('add_meta_boxes_page', 'wi_register_jobs_metaboxes');

add_action('save_post_page', function ($post_id) {
  if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
    return;
  }
  if (!current_user_can('edit_page', $post_id)) {
    return;
  }
  if (!wi_is_jobs_template($post_id)) {
    return;
  }

  if (isset($_POST['wi_jobs_hero_nonce']) && wp_verify_nonce(wp_unslash($_POST['wi_jobs_hero_nonce']), 'wi_jobs_hero_save')) {
    if (isset($_POST['wi_jobs_hero_title'])) {
      update_post_meta($post_id, '_wi_jobs_hero_title', sanitize_textarea_field(wp_unslash($_POST['wi_jobs_hero_title'])));
    }
    if (isset($_POST['wi_jobs_hero_intro'])) {
      update_post_meta($post_id, '_wi_jobs_hero_intro', sanitize_textarea_field(wp_unslash($_POST['wi_jobs_hero_intro'])));
    }
    if (isset($_POST['wi_jobs_open_title'])) {
      update_post_meta($post_id, '_wi_jobs_open_title', sanitize_text_field(wp_unslash($_POST['wi_jobs_open_title'])));
    }
  }

  if (isset($_POST['wi_jobs_jobs_nonce']) && wp_verify_nonce(wp_unslash($_POST['wi_jobs_jobs_nonce']), 'wi_jobs_jobs_save')) {
    $split_lines = static function ($text) {
      $lines = preg_split('/\r\n|\r|\n/', (string) $text);
      $lines = array_map('trim', $lines);
      return array_values(array_filter($lines, static function ($x) {
        return $x !== '';
      }));
    };
    $list = [];
    if (!empty($_POST['wi_jobs_job']) && is_array($_POST['wi_jobs_job'])) {
      foreach (wp_unslash($_POST['wi_jobs_job']) as $row) {
        if (!is_array($row)) {
          continue;
        }
        $title = isset($row['title']) ? sanitize_text_field($row['title']) : '';
        if ($title === '') {
          continue;
        }
        $tags = [];
        for ($t = 1; $t <= 3; $t++) {
          $tx = isset($row["tag{$t}"]) ? sanitize_text_field($row["tag{$t}"]) : '';
          if ($tx === '') {
            continue;
          }
          $tags[] = [
            'text'        => $tx,
            'highlighted' => !empty($row["tag{$t}_hi"]),
          ];
        }
        $tasks_raw = isset($row['tasks']) ? sanitize_textarea_field($row['tasks']) : '';
        $req_raw   = isset($row['req']) ? sanitize_textarea_field($row['req']) : '';
        $list[]    = [
          'title'        => $title,
          'tags'         => $tags,
          'description'  => isset($row['desc']) ? sanitize_textarea_field($row['desc']) : '',
          'tasks'        => $split_lines($tasks_raw),
          'requirements' => $split_lines($req_raw),
        ];
      }
    }
    if (!empty($list)) {
      update_post_meta($post_id, '_wi_jobs_list', wp_json_encode($list));
      wi_jobs_delete_legacy_job_meta($post_id);
    } else {
      delete_post_meta($post_id, '_wi_jobs_list');
    }
  }

  if (isset($_POST['wi_jobs_process_nonce']) && wp_verify_nonce(wp_unslash($_POST['wi_jobs_process_nonce']), 'wi_jobs_process_save')) {
    if (isset($_POST['wi_jobs_process_title'])) {
      update_post_meta($post_id, '_wi_jobs_process_title', sanitize_text_field(wp_unslash($_POST['wi_jobs_process_title'])));
    }
    foreach ([1, 2, 3, 4] as $si) {
      if (isset($_POST["wi_jobs_s{$si}_num"])) {
        update_post_meta($post_id, "_wi_jobs_s{$si}_num", sanitize_text_field(wp_unslash($_POST["wi_jobs_s{$si}_num"])));
      }
      if (isset($_POST["wi_jobs_s{$si}_title"])) {
        update_post_meta($post_id, "_wi_jobs_s{$si}_title", sanitize_text_field(wp_unslash($_POST["wi_jobs_s{$si}_title"])));
      }
      if (isset($_POST["wi_jobs_s{$si}_text"])) {
        update_post_meta($post_id, "_wi_jobs_s{$si}_text", sanitize_textarea_field(wp_unslash($_POST["wi_jobs_s{$si}_text"])));
      }
    }
  }

  if (isset($_POST['wi_jobs_form_nonce']) && wp_verify_nonce(wp_unslash($_POST['wi_jobs_form_nonce']), 'wi_jobs_form_save')) {
    foreach (['_wi_jobs_form_title' => 'wi_jobs_form_title', '_wi_jobs_form_subtitle' => 'wi_jobs_form_subtitle', '_wi_jobs_positions_lines' => 'wi_jobs_positions_lines', '_wi_jobs_dates_lines' => 'wi_jobs_dates_lines'] as $meta => $post_key) {
      if (isset($_POST[$post_key])) {
        $v = wp_unslash($_POST[$post_key]);
        if ($post_key === 'wi_jobs_form_title') {
          update_post_meta($post_id, $meta, sanitize_text_field($v));
        } else {
          update_post_meta($post_id, $meta, sanitize_textarea_field($v));
        }
      }
    }
  }

  if (isset($_POST['wi_jobs_close_nonce']) && wp_verify_nonce(wp_unslash($_POST['wi_jobs_close_nonce']), 'wi_jobs_close_save')) {
    if (isset($_POST['wi_jobs_close_title'])) {
      update_post_meta($post_id, '_wi_jobs_close_title', sanitize_text_field(wp_unslash($_POST['wi_jobs_close_title'])));
    }
    if (isset($_POST['wi_jobs_close_text'])) {
      update_post_meta($post_id, '_wi_jobs_close_text', sanitize_textarea_field(wp_unslash($_POST['wi_jobs_close_text'])));
    }
  }
}, 15);
