<?php
/**
 * Plugin Name: WI Clients Marquee
 * Description: Naizmenične crno/belo pilule sa logotipovima (3 reda). Admin: lista logotipa. Shortcode: [wi_clients_marquee].
 * Version: 1.1.0
 * Author: Werbeinsel
 */

if (!defined('ABSPATH')) exit;

class WI_Clients_Marquee {
  const OPT_KEY     = 'wi_clients_marquee_items';
  const MENU_SLUG   = 'wi-clients-marquee';
  const ROWS        = 3;   // broj redova
  const ROW_CAP     = 8;   // minimalno komada po redu (pre dupliranja za seamless)

  public function __construct(){
    // Admin
    add_action('admin_menu',              [$this,'admin_menu']);
    add_action('admin_init',              [$this,'register_setting']);
    add_action('admin_enqueue_scripts',   [$this,'admin_assets']);

    // Front
    add_shortcode('wi_clients_marquee',   [$this,'shortcode']);
    add_action('wp_head',                 [$this,'print_front_css']); // inline CSS zbog jednostavnosti
  }

  /* ================= ADMIN ================= */

  public function admin_menu(){
    add_menu_page(
      __('Clients Marquee','wi'),
      __('Clients Marquee','wi'),
      'manage_options',
      self::MENU_SLUG,
      [$this,'render_admin_page'],
      'dashicons-slides',
      58
    );
  }

  public function register_setting(){
    register_setting('wi_clients_marquee_group', self::OPT_KEY, [
      'type'              => 'array',
      'sanitize_callback' => [$this,'sanitize_items'],
      'default'           => [],
    ]);
  }

  public function sanitize_items($val){
    $out = [];
    if (is_array($val)) {
      foreach ($val as $row){
        $id  = isset($row['image_id']) ? intval($row['image_id']) : 0;
        $alt = isset($row['alt']) ? sanitize_text_field($row['alt']) : '';
        // ignorisemo stare 'type' vrednosti – prelazna kompatibilnost
        $out[] = ['image_id'=>$id, 'alt'=>$alt];
      }
    }
    return $out;
  }

  public function admin_assets($hook){
    if ($hook !== 'toplevel_page_'.self::MENU_SLUG) return;
    wp_enqueue_media();
    wp_enqueue_script('wi-cm-admin', plugin_dir_url(__FILE__).'wi-cm-admin.js', ['jquery'], '1.1.0', true);
    wp_add_inline_style('wp-admin', '
      .wi-cm-table{width:100%;border-collapse:collapse;margin-top:16px}
      .wi-cm-table th,.wi-cm-table td{border:1px solid #ccd0d4;padding:8px;vertical-align:top}
      .wi-cm-row-actions{display:flex;gap:8px}
      .wi-cm-thumb{width:140px;height:70px;object-fit:contain;background:#f6f7f7;border:1px solid #ccd0d4;border-radius:4px}
      .wi-cm-controls{display:flex;flex-wrap:wrap;gap:12px;align-items:center}
      .wi-cm-alt{min-width:260px}
      .description{color:#555}
    ');
  }

  public function render_admin_page(){
    if (!current_user_can('manage_options')) return;
    $items = get_option(self::OPT_KEY, []);
    ?>
    <div class="wrap">
      <h1>Clients Marquee</h1>
      <p class="description">
        Dodaj logoe koji će se prikazivati u 3 reda. Redovi se pune <b>naizmenično</b> (bez ponavljanja istog logotipa u više redova).
        Boje pilula se automatski smenjuju (crna/bela), a logo se prilagođava kontrastu.
      </p>

      <form method="post" action="options.php">
        <?php settings_fields('wi_clients_marquee_group'); ?>

        <table class="wi-cm-table" id="wi-cm-table">
          <thead>
            <tr>
              <th>Logo</th>
              <th>ALT tekst</th>
              <th style="width:220px">Akcije</th>
            </tr>
          </thead>
          <tbody id="wi-cm-rows">
            <?php if(!empty($items)): foreach($items as $i=>$row):
              $id  = !empty($row['image_id']) ? intval($row['image_id']) : 0;
              $alt = !empty($row['alt']) ? esc_attr($row['alt']) : '';
              $src = $id ? wp_get_attachment_image_url($id, 'medium') : '';
            ?>
            <tr class="wi-cm-row">
              <td>
                <div class="wi-cm-controls">
                  <img class="wi-cm-thumb" src="<?php echo esc_url($src); ?>" alt="">
                  <input type="hidden" class="wi-cm-image-id" name="<?php echo self::OPT_KEY; ?>[<?php echo $i; ?>][image_id]" value="<?php echo $id; ?>">
                  <button type="button" class="button wi-cm-pick">Odaberi</button>
                  <button type="button" class="button wi-cm-clear">Ukloni</button>
                </div>
              </td>
              <td>
                <input type="text" class="regular-text wi-cm-alt" name="<?php echo self::OPT_KEY; ?>[<?php echo $i; ?>][alt]" value="<?php echo $alt; ?>" placeholder="ALT (npr. Naziv klijenta)">
              </td>
              <td>
                <div class="wi-cm-row-actions">
                  <button type="button" class="button button-secondary wi-cm-up">Gore</button>
                  <button type="button" class="button button-secondary wi-cm-down">Dole</button>
                  <button type="button" class="button button-link-delete wi-cm-remove">Obriši</button>
                </div>
              </td>
            </tr>
            <?php endforeach; endif; ?>
          </tbody>
        </table>

        <p>
          <button type="button" class="button button-primary" id="wi-cm-add">+ Dodaj logo</button>
        </p>

        <?php submit_button(); ?>
      </form>

      <!-- Template red (skriven) -->
      <table style="display:none">
        <tbody>
          <tr id="wi-cm-template">
            <td>
              <div class="wi-cm-controls">
                <img class="wi-cm-thumb" src="" alt="">
                <input type="hidden" class="wi-cm-image-id" name="<?php echo self::OPT_KEY; ?>[IDX][image_id]" value="0">
                <button type="button" class="button wi-cm-pick">Odaberi</button>
                <button type="button" class="button wi-cm-clear">Ukloni</button>
              </div>
            </td>
            <td>
              <input type="text" class="regular-text wi-cm-alt" name="<?php echo self::OPT_KEY; ?>[IDX][alt]" value="" placeholder="ALT (npr. Naziv klijenta)">
            </td>
            <td>
              <div class="wi-cm-row-actions">
                <button type="button" class="button button-secondary wi-cm-up">Gore</button>
                <button type="button" class="button button-secondary wi-cm-down">Dole</button>
                <button type="button" class="button button-link-delete wi-cm-remove">Obriši</button>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
    <?php
  }

  /* ================= FRONT (SHORTCODE) ================= */

  public function shortcode($atts){
    $items = get_option(self::OPT_KEY, []);

    // 1) filtriramo samo uploadovane logoe (image_id > 0)
    $real = [];
    if (is_array($items)) {
      foreach ($items as $it){
        $id = isset($it['image_id']) ? intval($it['image_id']) : 0;
        if ($id > 0) {
          $real[] = ['image_id'=>$id, 'alt'=> isset($it['alt']) ? $it['alt'] : 'Client'];
        }
      }
    }

    // 2) raspodela bez ponavljanja po redovima (round-robin)
    $rows = array_fill(0, self::ROWS, []);
    $i = 0;
    foreach ($real as $logo) {
      $rows[$i % self::ROWS][] = $logo;
      $i++;
    }

    // 3) popuni svaku listu do minimalnog kapaciteta default (može da se ponavlja)
    for ($r=0; $r<self::ROWS; $r++){
      while (count($rows[$r]) < self::ROW_CAP) {
        $rows[$r][] = ['image_id'=>0, 'alt'=>'Client'];
      }
    }

    // helper: render jedne pilule (pozadina auto, logo auto)
    $render_pill = function($is_black, $image_id, $alt){
      $bg_class = $is_black ? 'is-black' : 'is-white';
      $style_color = $is_black ? 'color:#fff;' : 'color:#000;';
      $html_logo = '';

      if ($image_id) {
        $src = wp_get_attachment_image_url($image_id, 'large');
        if ($src) {
          $html_logo = '<img src="'.esc_url($src).'" alt="'.esc_attr($alt).'" class="pill-logo pill-logo--img" loading="lazy" decoding="async">';
        }
      }
      if (!$html_logo) {
        // default – inline SVG sa currentColor
        $html_logo = '<svg class="pill-logo pill-logo--svg" viewBox="0 0 640 200" role="img" aria-label="'.esc_attr($alt).'" xmlns="http://www.w3.org/2000/svg">
          <title>'.esc_html($alt).'</title>
          <path fill="currentColor" d="M60 150 L95 50 L130 150 L155 150 L195 50 L230 150 L205 150 L180 85 L155 150 L130 150 L105 85 L80 150 Z"/>
          <text x="260" y="132" font-family="system-ui,Segoe UI,Arial,sans-serif" font-weight="800" font-size="72" fill="currentColor">WERBEINSEL</text>
        </svg>';
      }

      return '<div class="pill '.$bg_class.'" style="'.$style_color.'">'.$html_logo.'</div>';
    };

    // helper: jedna traka sa naizmeničnim bojama (kreće crnom)
    $track = function($logos, $dir_class) use ($render_pill){
      $html = '<div class="clients-track '.$dir_class.'">';
      for ($dup=0; $dup<2; $dup++) { // dupliramo radi seamless loop-a
        $idx = 0;
        foreach ($logos as $logo){
          $is_black = ($idx % 2 === 0); // crna, bela, crna, ...
          $html .= $render_pill($is_black, intval($logo['image_id']), $logo['alt']);
          $idx++;
        }
      }
      $html .= '</div>';
      return $html;
    };

    ob_start(); ?>
    <section class="clients">
      <div class="container">
        <h2 class="clients-title">OUR CLIENTS</h2>
      </div>
      <div class="clients-rows">
        <div class="clients-row"><?php echo $track($rows[0], 'clients-track--left'); ?></div>
        <div class="clients-row"><?php echo $track($rows[1], 'clients-track--right'); ?></div>
        <div class="clients-row"><?php echo $track($rows[2], 'clients-track--left'); ?></div>
      </div>
    </section>
    <?php
    return ob_get_clean();
  }

  public function print_front_css(){
    ?>
    <style id="wi-clients-marquee-css">
      :root{
        --container-max:1780px;--container-pad:32px;--row-gap:64px;--pill-gap:64px;
        --pill-w:384px;--pill-h:192px;--pill-radius:9999px;--track-speed:30s;
      }
      .container{max-width:var(--container-max);margin-inline:auto;padding-inline:var(--container-pad)}
      .clients{margin:4rem 0}
      .clients-title{font:800 clamp(1.5rem,2rem + 1vw,3.5rem)/1.1 system-ui,sans-serif;text-align:center;margin:0 0 4rem;white-space:nowrap}
      .clients-rows{overflow:hidden;display:grid;gap:var(--row-gap)}
      .clients-row{overflow:hidden}
      .clients-track{display:flex;align-items:center;gap:var(--pill-gap);will-change:transform}
      @keyframes wi-left{0%{transform:translate3d(0,0,0)}100%{transform:translate3d(-50%,0,0)}}
      @keyframes wi-right{0%{transform:translate3d(-50%,0,0)}100%{transform:translate3d(0,0,0)}}
      .clients-track--left{animation:wi-left var(--track-speed) linear infinite}
      .clients-track--right{animation:wi-right var(--track-speed) linear infinite}
      .pill{flex:0 0 auto;width:var(--pill-w);height:var(--pill-h);border-radius:var(--pill-radius);display:flex;align-items:center;justify-content:center}
      .pill.is-black{background:#000;border:none}
      .pill.is-white{background:#fff;border:2px solid #000}
      .pill-logo{max-width:70%;max-height:70%;width:auto;height:auto;display:block;object-fit:contain}
      /* Kontrast: samo za uploadovane slike na crnoj piluli */
      .pill.is-black img.pill-logo--img{filter: invert(1) brightness(1.2) contrast(1.05)}
      .pill.is-white img.pill-logo--img{filter:none}
      @media (max-width:1200px){:root{--pill-w:320px;--pill-h:160px;--pill-gap:48px;--track-speed:26s}}
      @media (max-width:900px){:root{--pill-w:260px;--pill-h:130px;--pill-gap:32px;--row-gap:40px;--track-speed:22s}}
      @media (max-width:600px){:root{--pill-w:220px;--pill-h:110px;--pill-gap:24px;--track-speed:18s}.clients-title{margin-bottom:2.5rem}}
    </style>
    <?php
  }
}
new WI_Clients_Marquee();

/* ============ ADMIN JS ============ */
add_action('admin_footer', function(){
  $screen = get_current_screen();
  if (!$screen || $screen->id !== 'toplevel_page_'.WI_Clients_Marquee::MENU_SLUG) return; ?>
  <script>
  (function($){
    function reindex(){
      $('#wi-cm-rows .wi-cm-row').each(function(i,row){
        $(row).find('[name]').each(function(){
          const name = $(this).attr('name');
          const newName = name.replace(/\[\d+\]/,'['+i+']').replace(/\[IDX\]/,'['+i+']');
          $(this).attr('name', newName);
        });
      });
    }
    function pickMedia($row){
      const frame = wp.media({title:'Odaberi logo', button:{text:'Use'}, multiple:false});
      frame.on('select', function(){
        const att = frame.state().get('selection').first().toJSON();
        const url = (att.sizes && (att.sizes.medium || att.sizes.full).url) || att.url;
        $row.find('.wi-cm-image-id').val(att.id);
        $row.find('.wi-cm-thumb').attr('src', url);
      });
      frame.open();
    }
    $('#wi-cm-add').on('click', function(){
      const $tpl = $('#wi-cm-template').clone().removeAttr('id').addClass('wi-cm-row').show();
      $('#wi-cm-rows').append($tpl);
      reindex();
    });
    $('#wi-cm-rows').on('click','.wi-cm-remove', function(){ $(this).closest('.wi-cm-row').remove(); reindex(); });
    $('#wi-cm-rows').on('click','.wi-cm-up', function(){ const $r=$(this).closest('.wi-cm-row'); $r.prev().before($r); reindex(); });
    $('#wi-cm-rows').on('click','.wi-cm-down', function(){ const $r=$(this).closest('.wi-cm-row'); $r.next().after($r); reindex(); });
    $('#wi-cm-rows').on('click','.wi-cm-pick', function(){ pickMedia($(this).closest('.wi-cm-row')); });
    $('#wi-cm-rows').on('click','.wi-cm-clear', function(){ const $r=$(this).closest('.wi-cm-row'); $r.find('.wi-cm-image-id').val('0'); $r.find('.wi-cm-thumb').attr('src',''); });
  })(jQuery);
  </script>
<?php });
