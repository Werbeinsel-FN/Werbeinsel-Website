<?php
/**
 * Plugin Name: Werbeinsel Services Manager
 * Description: Uređivanje sadržaja "Services" stranice (grid). (Varijanta bez "Service Details" modula.)
 * Version: 2.1.1
 * Author: Werbeinsel
 */

if (!defined('ABSPATH')) exit;

class WI_Services_Manager {
  const OPT_KEY_LIST    = 'wi_services_options';
  const CAP             = 'manage_options';
  const PAGE_SLUG       = 'wi-services';

  public function __construct() {
    // Admin
    add_action('admin_menu',            [$this, 'menu']);
    add_action('admin_init',            [$this, 'register']);
    add_action('admin_enqueue_scripts', [$this, 'assets']);

    // Aktivacija/deaktivacija (nema rewrites više)
    register_activation_hook(__FILE__,  [$this, 'activate']);
    register_deactivation_hook(__FILE__,[$this, 'deactivate']);
  }

  public function menu() {
    add_menu_page(
      __('Services Content','wi'),
      __('Services Content','wi'),
      self::CAP,
      self::PAGE_SLUG,
      [$this, 'render_services'],
      'dashicons-layout',
      58
    );
  }

  public function register() {
    register_setting('wi_services_group', self::OPT_KEY_LIST, [$this, 'sanitize_list']);
  }

  private function groups_spec() {
    return [
      'out_of_home' => ['label' => 'AUF DIE STRAßE', 'count' => 6],
      'pixel_code'  => ['label' => 'PIXEL & CODE',   'count' => 4],
      'lass_kleben' => ['label' => 'LASS KLEBEN',    'count' => 3],
    ];
  }

  /** Admin assets — ostavljeno samo za glavnu stranicu (grid) */
  public function assets($hook) {
    $is_ours = (isset($_GET['page']) && $_GET['page'] === self::PAGE_SLUG);
    if (!$is_ours) return;

    wp_enqueue_media();

    add_action('admin_print_footer_scripts', function () { ?>
      <style>
        .wi-wrap .card{background:#fff;border:1px solid #e5e7eb;border-radius:12px;padding:18px 16px;margin:16px 0}
        .wi-wrap h2{margin:8px 0 14px}
        .wi-grid{display:grid;gap:16px;grid-template-columns:repeat(auto-fit,minmax(260px,1fr))}
        .wi-grid-3{display:grid;gap:16px;grid-template-columns:repeat(auto-fit,minmax(320px,1fr))}
        .wi-item{border:1px dashed #d1d5db;border-radius:12px;padding:12px;background:#fafafa}
        .wi-row{display:flex;gap:12px;align-items:flex-start}
        .wi-row > *:not(img){flex:1}
        .wi-img{width:140px;height:88px;object-fit:cover;border-radius:8px;background:#eee;border:1px solid #e5e7eb}
        .wi-col{display:grid;gap:8px}
        .wi-wrap label{display:block;font-weight:600;margin-bottom:4px}
        .wi-wrap .regular-text, .wi-wrap textarea{width:100%;max-width:100%}
        .wi-wrap select{width:100%}
        .muted{opacity:.7;font-size:12px}
        @media (max-width: 960px){ .wi-row{flex-direction:column} .wi-img{width:100%;height:160px} }
      </style>
      <script>
        (function(){
          function openMedia(cb){
            if (!window.wp || !wp.media) return;
            const frame = wp.media({ title:'Odaberi datoteku', button:{ text:'Koristi' }, multiple:false });
            frame.on('select', function(){
              const att = frame.state().get('selection').first().toJSON();
              cb(att);
            });
            frame.open();
          }
          function triggerChange(el){
            ['input','change'].forEach(ev => el && el.dispatchEvent(new Event(ev, {bubbles:true})));
          }

          document.addEventListener('click', function(e){
            const gridPick = e.target.closest('.wi-upload');
            const gridRm   = e.target.closest('.wi-remove');
            if (gridPick){
              e.preventDefault();
              const box   = gridPick.closest('.wi-item');
              const input = box.querySelector('.wi-image-id');
              const img   = box.querySelector('.wi-img');
              openMedia(function(att){
                input.value = att.id || '';
                img.src     = (att.sizes && att.sizes.medium ? att.sizes.medium.url : att.url) || '';
                triggerChange(input);
              });
              return;
            }
            if (gridRm){
              e.preventDefault();
              const box   = gridRm.closest('.wi-item');
              const input = box.querySelector('.wi-image-id');
              const img   = box.querySelector('.wi-img');
              input.value = '';
              img.src     = '';
              triggerChange(input);
              return;
            }
          });
        })();
      </script>
    <?php });
  }

  /* --------------------- Services Content (grid) --------------------- */

  public function render_services() {
    if (!current_user_can(self::CAP)) return;

    $opt    = get_option(self::OPT_KEY_LIST, []);
    $heads  = $opt['headings'] ?? ['ooh'=>'AUF DIE STRAßE','pix'=>'PIXEL & CODE','kle'=>'LASS KLEBEN'];
    $groups = $opt['groups']   ?? [];
    $cats   = get_categories(['hide_empty'=>false]);
    ?>
    <div class="wrap wi-wrap">
      <h1>Services Content</h1>
      <form method="post" action="options.php">
        <?php settings_fields('wi_services_group'); ?>

        <div class="card">
          <h2><?php _e('Glavni naslovi','wi'); ?></h2>
          <div class="wi-grid">
            <div><label>AUF DIE STRAßE</label>
              <input type="text" name="<?php echo self::OPT_KEY_LIST; ?>[headings][ooh]" class="regular-text" value="<?php echo esc_attr($heads['ooh'] ?? ''); ?>">
            </div>
            <div><label>PIXEL &amp; CODE</label>
              <input type="text" name="<?php echo self::OPT_KEY_LIST; ?>[headings][pix]" class="regular-text" value="<?php echo esc_attr($heads['pix'] ?? ''); ?>">
            </div>
            <div><label>LASS KLEBEN</label>
              <input type="text" name="<?php echo self::OPT_KEY_LIST; ?>[headings][kle]" class="regular-text" value="<?php echo esc_attr($heads['kle'] ?? ''); ?>">
            </div>
          </div>
        </div>

        <?php foreach ($this->groups_spec() as $key => $spec): ?>
          <div class="card">
            <h2><?php echo esc_html($spec['label']); ?></h2>
            <div class="wi-grid-3">
              <?php
              for ($i=0; $i<$spec['count']; $i++):
                $item = $groups[$key][$i] ?? ['title'=>'','cat'=>0,'image'=>0];
                $img  = $item['image'] ? wp_get_attachment_image_url((int)$item['image'], 'medium') : '';
              ?>
                <div class="wi-item">
                  <div style="font-weight:700;margin-bottom:8px">Kartica <?php echo ($i+1); ?></div>
                  <div class="wi-col">
                    <div>
                      <label>Naziv</label>
                      <input type="text"
                        name="<?php echo self::OPT_KEY_LIST; ?>[groups][<?php echo esc_attr($key); ?>][<?php echo $i; ?>][title]"
                        value="<?php echo esc_attr($item['title']); ?>" class="regular-text">
                      <div class="muted">Permalink: <code>/services/<?php echo esc_html( sanitize_title($item['title'] ?: 'example') ); ?>/</code></div>
                    </div>
                    <div>
                      <label>Kategorija (WP post kategorije)</label>
                      <select name="<?php echo self::OPT_KEY_LIST; ?>[groups][<?php echo esc_attr($key); ?>][<?php echo $i; ?>][cat]">
                        <option value="0">— bez kategorije —</option>
                        <?php foreach ($cats as $c): ?>
                          <option value="<?php echo (int)$c->term_id; ?>" <?php selected((int)$item['cat'], (int)$c->term_id); ?>>
                            <?php echo esc_html($c->name); ?>
                          </option>
                        <?php endforeach; ?>
                      </select>
                    </div>
                    <div class="wi-row">
                      <img class="wi-img" src="<?php echo esc_url($img ?: ''); ?>" alt="">
                      <input type="hidden" class="wi-image-id"
                        name="<?php echo self::OPT_KEY_LIST; ?>[groups][<?php echo esc_attr($key); ?>][<?php echo $i; ?>][image]"
                        value="<?php echo esc_attr($item['image']); ?>">
                      <div>
                        <button type="button" class="button wi-upload">Odaberi sliku</button>
                        <button type="button" class="button wi-remove">Ukloni</button>
                      </div>
                    </div>
                  </div>
                </div>
              <?php endfor; ?>
            </div>
          </div>
        <?php endforeach; ?>

        <?php submit_button(); ?>
      </form>
    </div>
    <?php
  }

  public function sanitize_list($input) {
    $defaults = ['ooh'=>'AUF DIE STRAßE','pix'=>'PIXEL & CODE','kle'=>'LASS KLEBEN'];

    $out = ['headings'=>[],'groups'=>[]];
    foreach ($defaults as $k=>$def) {
      $val = isset($input['headings'][$k]) ? trim(wp_unslash($input['headings'][$k])) : '';
      $out['headings'][$k] = $val !== '' ? sanitize_text_field($val) : $def;
    }

    $spec = $this->groups_spec();
    foreach ($spec as $key => $s) {
      $out['groups'][$key] = [];
      for ($i=0; $i<$s['count']; $i++) {
        $item  = $input['groups'][$key][$i] ?? [];
        $title = isset($item['title']) ? sanitize_text_field($item['title']) : '';
        $cat   = isset($item['cat'])   ? absint($item['cat']) : 0;
        $img   = isset($item['image']) ? absint($item['image']) : 0;
        $out['groups'][$key][$i] = ['title'=>$title,'cat'=>$cat,'image'=>$img];
      }
    }
    return $out;
  }

  /* --------------------- Data helper (bez detalja) --------------------- */

  public static function get_service_data_by_slug($slug) {
    $slug   = sanitize_title($slug);
    $list   = get_option(self::OPT_KEY_LIST, []);
    $groups = $list['groups'] ?? [];

    $found = ['title'=>'','image_url'=>'','category'=>''];
    foreach ($groups as $group_key => $items) {
      foreach ((array)$items as $it) {
        if (empty($it['title'])) continue;
        if (sanitize_title($it['title']) === $slug) {
          $found['title']     = $it['title'];
          $found['image_url'] = $it['image'] ? wp_get_attachment_image_url((int)$it['image'], 'full') : '';
          $found['category']  = $it['cat'] ? get_cat_name((int)$it['cat']) : '';
          break 2;
        }
      }
    }
    // Nema više OPT_KEY_DETAILS ni detalja
    return [$found, []];
  }

  public function activate(){ /* nema rewrite pravila, samo flush da bude čisto */ flush_rewrite_rules(false); }
  public function deactivate(){ flush_rewrite_rules(false); }
}

new WI_Services_Manager();
