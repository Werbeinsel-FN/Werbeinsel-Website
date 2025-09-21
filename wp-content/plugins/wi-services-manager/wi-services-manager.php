<?php
/**
 * Plugin Name: Werbeinsel Services Manager
 * Description: Uređivanje sadržaja "Services" stranice + detalja servisa (slider i sadržaj ispod).
 * Version: 2.0.2
 * Author: Werbeinsel
 */

if (!defined('ABSPATH')) exit;

class WI_Services_Manager {
  const OPT_KEY_LIST    = 'wi_services_options';
  const OPT_KEY_DETAILS = 'wi_service_details';
  const CAP             = 'manage_options';
  const PAGE_SLUG       = 'wi-services';
  const PAGE_SLUG_DET   = 'wi-service-details';
  const QV_TAG          = 'wi_service';

  public function __construct() {
    add_action('admin_menu',              [$this, 'menu']);
    add_action('admin_init',              [$this, 'register']);
    add_action('admin_enqueue_scripts',   [$this, 'assets']);

    add_action('init',                    [$this, 'rewrites']);
    add_filter('query_vars',              [$this, 'query_vars']);
    add_filter('template_include',        [$this, 'template_loader']);

    register_activation_hook(__FILE__,    [$this, 'activate']);
    register_deactivation_hook(__FILE__,  [$this, 'deactivate']);
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
    add_submenu_page(
      self::PAGE_SLUG,
      __('Service Details','wi'),
      __('Service Details','wi'),
      self::CAP,
      self::PAGE_SLUG_DET,
      [$this, 'render_details']
    );
  }

  public function register() {
    register_setting('wi_services_group',         self::OPT_KEY_LIST,    [$this, 'sanitize_list']);
    register_setting('wi_service_details_group',  self::OPT_KEY_DETAILS, [$this, 'sanitize_details']);
  }

  private function groups_spec() {
    return [
      'out_of_home' => ['label' => 'AUF DIE STRAßE', 'count' => 6],
      'pixel_code'  => ['label' => 'PIXEL & CODE',   'count' => 4],
      'lass_kleben' => ['label' => 'LASS KLEBEN',    'count' => 3],
    ];
  }

  /** Defaults used when nothing is saved yet (titles only) */
  private function defaults_map() {
    return [
      'out_of_home' => [
        ['title'=>'Plakatwerbung'],
        ['title'=>'Großflächenwerbung'],
        ['title'=>'Digital Signage'],
        ['title'=>'Transit Advertising'],
        ['title'=>'Guerilla Marketing'],
        ['title'=>'Ambient Advertising'],
      ],
      'pixel_code' => [
        ['title'=>'Corporate Design'],
        ['title'=>'Webentwicklung'],
        ['title'=>'Print Design'],
        ['title'=>'UI/UX Design'],
      ],
      'lass_kleben' => [
        ['title'=>'Neonreklame'],
        ['title'=>'LED-Displays'],
        ['title'=>'Leuchtschriften'],
      ],
    ];
  }

  /** Admin assets */
  public function assets($hook) {
    // Load on both pages robustly (fix for “nothing happens” on click)
    $is_ours = (isset($_GET['page']) && ( $_GET['page'] === self::PAGE_SLUG || $_GET['page'] === self::PAGE_SLUG_DET ));
    if (!$is_ours) return;

    // Ensure media frame exists
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
        .slide-box{border:1px solid #e5e7eb;border-radius:12px;padding:12px;background:#fff}
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
          // Use document-level delegation so it always binds (fix #2)
          document.addEventListener('click', function(e){
            // GRID image
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

            // SLIDER media
            const slidePick = e.target.closest('.wi-slide-upload');
            const slideRm   = e.target.closest('.wi-slide-remove');
            if (slidePick){
              e.preventDefault();
              const wrap = slidePick.closest('.slide-box');
              const idEl  = wrap.querySelector('.wi-slide-bg-id');
              const urlEl = wrap.querySelector('.wi-slide-bg-url');
              openMedia(function(att){
                idEl.value  = att.id || '';
                urlEl.value = att.url || '';
                triggerChange(idEl);
                triggerChange(urlEl);
              });
              return;
            }
            if (slideRm){
              e.preventDefault();
              const wrap = slideRm.closest('.slide-box');
              const idEl  = wrap.querySelector('.wi-slide-bg-id');
              const urlEl = wrap.querySelector('.wi-slide-bg-url');
              idEl.value = '';
              urlEl.value = '';
              triggerChange(idEl);
              triggerChange(urlEl);
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

  /* --------------------- Service Details --------------------- */

  private function all_services_indexed() {
    $saved    = get_option(self::OPT_KEY_LIST, []);
    $groups   = $saved['groups'] ?? [];
    $defaults = $this->defaults_map();
    $spec     = $this->groups_spec();

    // merge saved + defaults to guarantee all slots exist
    foreach ($spec as $gk => $s) {
      for ($i=0; $i<$s['count']; $i++) {
        $title = '';
        if (isset($groups[$gk][$i]['title']) && $groups[$gk][$i]['title'] !== '') {
          $title = $groups[$gk][$i]['title'];
        } elseif (!empty($defaults[$gk][$i]['title'])) {
          $title = $defaults[$gk][$i]['title'];
        }
        if ($title === '') continue;
        // write back so structure exists
        $groups[$gk][$i]['title'] = $title;
      }
    }

    $list = [];
    foreach ($groups as $group_key => $items) {
      foreach ((array)$items as $i => $it) {
        if (empty($it['title'])) continue;
        $slug = sanitize_title($it['title']);
        $list[$slug] = [
          'title' => $it['title'],
          'group' => $group_key,
          'index' => $i,
          'slug'  => $slug,
        ];
      }
    }
    ksort($list);
    return $list;
  }

  public function render_details() {
    if (!current_user_can(self::CAP)) return;

    $services = $this->all_services_indexed();
    // If still empty for any reason, fall back to defaults entirely
    if (!$services) {
      foreach ($this->defaults_map() as $g => $arr) {
        foreach ($arr as $i => $it) {
          if (empty($it['title'])) continue;
          $slug = sanitize_title($it['title']);
          $services[$slug] = ['title'=>$it['title'],'group'=>$g,'index'=>$i,'slug'=>$slug];
        }
      }
      ksort($services);
    }

    $firstKey = $services ? array_key_first($services) : '';
    $selected = isset($_GET['service']) ? sanitize_title(wp_unslash($_GET['service'])) : $firstKey;

    $det = get_option(self::OPT_KEY_DETAILS, []);
    $cur = $det[$selected] ?? [
      'slides' => [
        ['h1'=>'PLAKATWERBUNG','h2'=>'Authentische Plakatwerbung','text'=>'So sieht professionelle Straßenwerbung in der Praxis aus','bg_type'=>'image','bg_id'=>0,'bg_url'=>''],
        ['h1'=>'PLAKATWERBUNG','h2'=>'Professionelle Außenwerbung','text'=>'Ihre Marke im städtischen Umfeld präsentieren','bg_type'=>'image','bg_id'=>0,'bg_url'=>''],
        ['h1'=>'PLAKATWERBUNG','h2'=>'Großformat-Werbung','text'=>'Maximale Aufmerksamkeit durch beeindruckende Größe','bg_type'=>'image','bg_id'=>0,'bg_url'=>''],
        ['h1'=>'PLAKATWERBUNG','h2'=>'Strategische Platzierung','text'=>'An hochfrequentierten Verkehrsknotenpunkten','bg_type'=>'image','bg_id'=>0,'bg_url'=>''],
      ],
      'below' => ['title'=>'IHRE BOTSCHAFT AUF DIE STRAßE','text'=>'Plakatwerbung ist eine der effektivsten Formen der Außenwerbung...']
    ];
    ?>
    <div class="wrap wi-wrap">
      <h1>Service Details</h1>
      <form method="post" action="options.php">
        <?php settings_fields('wi_service_details_group'); ?>
        <input type="hidden" name="<?php echo self::OPT_KEY_DETAILS; ?>[__editing_slug]" value="<?php echo esc_attr($selected); ?>">

        <div class="card">
          <h2>Odaberi servis</h2>
          <select onchange="location.href='?page=<?php echo esc_attr(self::PAGE_SLUG_DET); ?>&service='+this.value">
            <?php foreach($services as $slug => $s): ?>
              <option value="<?php echo esc_attr($slug); ?>" <?php selected($slug, $selected); ?>>
                <?php echo esc_html($s['title']); ?> (<?php echo esc_html($slug); ?>)
              </option>
            <?php endforeach; ?>
          </select>
          <p class="muted">URL: <code><?php echo esc_html( home_url('/services/'.$selected.'/') ); ?></code></p>
        </div>

        <div class="card">
          <h2>Slider (do 4 slajda)</h2>
          <div class="wi-grid">
            <?php for($i=0; $i<4; $i++):
              $sl = $cur['slides'][$i] ?? ['h1'=>'','h2'=>'','text'=>'','bg_type'=>'image','bg_id'=>0,'bg_url'=>''];
            ?>
              <div class="slide-box">
                <strong>Slide <?php echo $i+1; ?></strong>
                <div class="wi-col" style="margin-top:8px">
                  <div>
                    <label>Naslov (H1)</label>
                    <input type="text" class="regular-text" name="<?php echo self::OPT_KEY_DETAILS; ?>[services][<?php echo esc_attr($selected); ?>][slides][<?php echo $i; ?>][h1]" value="<?php echo esc_attr($sl['h1']); ?>">
                  </div>
                  <div>
                    <label>Podnaslov (H2)</label>
                    <input type="text" class="regular-text" name="<?php echo self::OPT_KEY_DETAILS; ?>[services][<?php echo esc_attr($selected); ?>][slides][<?php echo $i; ?>][h2]" value="<?php echo esc_attr($sl['h2']); ?>">
                  </div>
                  <div>
                    <label>Tekst</label>
                    <textarea rows="3" name="<?php echo self::OPT_KEY_DETAILS; ?>[services][<?php echo esc_attr($selected); ?>][slides][<?php echo $i; ?>][text]"><?php echo esc_textarea($sl['text']); ?></textarea>
                  </div>
                  <div>
                    <label>Pozadina</label>
                    <select name="<?php echo self::OPT_KEY_DETAILS; ?>[services][<?php echo esc_attr($selected); ?>][slides][<?php echo $i; ?>][bg_type]">
                      <option value="image" <?php selected(($sl['bg_type']??'image'),'image'); ?>>Slika</option>
                      <option value="video" <?php selected(($sl['bg_type']??'image'),'video'); ?>>Video</option>
                    </select>
                  </div>
                  <div class="wi-row">
                    <div>
                      <label>Media (slika ili video)</label>
                      <input type="hidden" class="wi-slide-bg-id"  name="<?php echo self::OPT_KEY_DETAILS; ?>[services][<?php echo esc_attr($selected); ?>][slides][<?php echo $i; ?>][bg_id]" value="<?php echo esc_attr($sl['bg_id']); ?>">
                      <input type="text" class="wi-slide-bg-url regular-text" name="<?php echo self::OPT_KEY_DETAILS; ?>[services][<?php echo esc_attr($selected); ?>][slides][<?php echo $i; ?>][bg_url]" value="<?php echo esc_attr($sl['bg_url']); ?>" placeholder="URL (automatski se popuni pri odabiru)">
                      <div style="margin-top:6px">
                        <button type="button" class="button wi-slide-upload">Odaberi iz biblioteke</button>
                        <button type="button" class="button wi-slide-remove">Ukloni</button>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            <?php endfor; ?>
          </div>
        </div>

        <div class="card">
          <h2>Sadržaj ispod slidera</h2>
          <div class="wi-grid">
            <div>
              <label>Naslov</label>
              <input type="text" class="regular-text" name="<?php echo self::OPT_KEY_DETAILS; ?>[services][<?php echo esc_attr($selected); ?>][below][title]" value="<?php echo esc_attr($cur['below']['title'] ?? ''); ?>">
            </div>
            <div>
              <label>Tekst</label>
              <textarea rows="5" name="<?php echo self::OPT_KEY_DETAILS; ?>[services][<?php echo esc_attr($selected); ?>][below][text]"><?php echo esc_textarea($cur['below']['text'] ?? ''); ?></textarea>
            </div>
          </div>
        </div>

        <?php submit_button(); ?>
      </form>
    </div>
    <?php
  }

  public function sanitize_details($input) {
    $out = get_option(self::OPT_KEY_DETAILS, []);
    $editing = isset($input['__editing_slug']) ? sanitize_title($input['__editing_slug']) : '';
    if (!$editing) return $out;

    $svcs = $input['services'][$editing] ?? [];
    $slides = [];
    if (isset($svcs['slides']) && is_array($svcs['slides'])) {
      foreach ($svcs['slides'] as $i => $sl) {
        $slides[$i] = [
          'h1'     => sanitize_text_field($sl['h1'] ?? ''),
          'h2'     => sanitize_text_field($sl['h2'] ?? ''),
          'text'   => wp_kses_post($sl['text'] ?? ''),
          'bg_type'=> ($sl['bg_type'] ?? 'image') === 'video' ? 'video' : 'image',
          'bg_id'  => isset($sl['bg_id']) ? absint($sl['bg_id']) : 0,
          'bg_url' => esc_url_raw($sl['bg_url'] ?? ''),
        ];
      }
    }
    $below = [
      'title' => sanitize_text_field($svcs['below']['title'] ?? ''),
      'text'  => wp_kses_post($svcs['below']['text'] ?? ''),
    ];

    if (!isset($out[$editing])) $out[$editing] = [];
    $out[$editing]['slides'] = $slides;
    $out[$editing]['below']  = $below;

    return $out;
  }

  /* --------------------- Frontend routing --------------------- */

  public function rewrites() {
    add_rewrite_tag('%'.self::QV_TAG.'%', '([^&]+)');
    add_rewrite_rule('^services/([^/]+)/?$', 'index.php?'.self::QV_TAG.'=$matches[1]', 'top');
  }
  public function query_vars($vars) { $vars[] = self::QV_TAG; return $vars; }
  public function template_loader($template) {
    $slug = get_query_var(self::QV_TAG);
    if ($slug) {
      $file = plugin_dir_path(__FILE__) . 'templates/single-wi-service.php';
      if (file_exists($file)) return $file;
    }
    return $template;
  }
  public function activate(){ $this->rewrites(); flush_rewrite_rules(false); }
  public function deactivate(){ flush_rewrite_rules(false); }

  /* --------------------- Data helper --------------------- */

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
    $details_all = get_option(self::OPT_KEY_DETAILS, []);
    $details     = $details_all[$slug] ?? [];
    return [$found, $details];
  }
}
new WI_Services_Manager();
