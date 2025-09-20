<?php
/**
 * Plugin Name: Werbeinsel Impressum Manager
 * Description: Uređivanje sadržaja Impressum stranice: h3 naslovi + paragrafi po blokovima.
 * Version: 1.0.0
 * Author: Werbeinsel
 */

if (!defined('ABSPATH')) exit;

class WI_Impressum_Manager {
  const OPT_KEY   = 'wi_impressum_options';
  const CAP       = 'manage_options';
  const PAGE_SLUG = 'wi-impressum';

  public function __construct() {
    add_action('admin_menu', [$this, 'menu']);
    add_action('admin_init', [$this, 'register']);
    add_action('admin_enqueue_scripts', [$this, 'assets']);
    // helper za theme
    add_shortcode('wi_impressum_dump', [$this, 'shortcode_dump']); // (opciono) za test
  }

  public function menu() {
    add_menu_page(
      __('Impressum Content','wi'),
      __('Impressum Content','wi'),
      self::CAP,
      self::PAGE_SLUG,
      [$this, 'render'],
      'dashicons-media-text',
      59
    );
  }

  public function register() {
    register_setting('wi_impressum_group', self::OPT_KEY, [$this, 'sanitize']);
  }

  /** Specifikacija 3 bloka (ključ => label) */
  private function sections_spec() {
    return [
      'tmg'   => 'ANGABEN GEMÄSS § 5 TMG',
      'kontakt' => 'KONTAKT',
      'ustid' => 'UMSATZSTEUER-ID',
    ];
  }

  /** Podrazumevani sadržaj – biće učitan ako nema ničeg u opcijama */
  private function defaults() {
    return [
      'tmg' => [
        'title' => 'ANGABEN GEMÄSS § 5 TMG',
        'paras' => ['WERBEINSEL','Flughafen 76/3','88046 Friedrichshafen','Deutschland'],
      ],
      'kontakt' => [
        'title' => 'KONTAKT',
        'paras' => ['Telefon: +49 7541 700 57 44','E-Mail: hallo@werbeinsel.de'],
      ],
      'ustid' => [
        'title' => 'UMSATZSTEUER-ID',
        'paras' => ['Umsatzsteuer-Identifikationsnummer gemäß § 27 a Umsatzsteuergesetz:','DE322482204'],
      ],
    ];
  }

  public function assets($hook) {
    if ($hook !== 'toplevel_page_'.self::PAGE_SLUG) return;

    add_action('admin_print_footer_scripts', function () { ?>
      <style>
        .wi-wrap .card{background:#fff;border:1px solid #e5e7eb;border-radius:12px;padding:18px 16px;margin:16px 0}
        .wi-wrap h1{margin:10px 0 16px}
        .wi-wrap h2{margin:6px 0 12px}
        .wi-grid{display:grid;gap:16px;grid-template-columns:repeat(auto-fit,minmax(280px,1fr))}
        .wi-row{display:flex;gap:10px;align-items:center;margin:6px 0}
        .wi-wrap label{display:block;font-weight:600;margin-bottom:6px}
        .wi-wrap .regular-text{width:100%;max-width:100%}
        .wi-paras{margin-top:10px}
        .wi-para{display:flex;gap:8px;align-items:center;margin-bottom:8px}
        .wi-para input{flex:1}
        .wi-ghost{opacity:.55}
      </style>
      <script>
        (function(){
          function addPara(btn){
            const box  = btn.closest('.card');
            const list = box.querySelector('.wi-paras');
            const proto= box.querySelector('.wi-para[data-proto="1"]');
            const clone= proto.cloneNode(true);
            clone.dataset.proto = "0";
            clone.classList.remove('wi-ghost');
            clone.querySelector('input').value = '';
            list.appendChild(clone);
          }
          function removePara(btn){
            const row = btn.closest('.wi-para');
            const list = row.parentElement;
            if(list.querySelectorAll('.wi-para').length>1){
              row.remove();
            } else {
              row.querySelector('input').value='';
            }
          }
          document.addEventListener('click', function(e){
            if(e.target.matches('.wi-add')){ e.preventDefault(); addPara(e.target); }
            if(e.target.matches('.wi-del')){ e.preventDefault(); removePara(e.target); }
          });
        })();
      </script>
    <?php });
  }

  public function render() {
    if (!current_user_can(self::CAP)) return;

    $opt  = get_option(self::OPT_KEY, []);
    $defs = $this->defaults();

    // merge defaults
    $data = wp_parse_args($opt, $defs);

    ?>
    <div class="wrap wi-wrap">
      <h1>Impressum Content</h1>
      <form method="post" action="options.php">
        <?php settings_fields('wi_impressum_group'); ?>

        <?php foreach ($this->sections_spec() as $key => $label): 
          $title = $data[$key]['title'] ?? $defs[$key]['title'];
          $paras = $data[$key]['paras'] ?? $defs[$key]['paras'];
        ?>
          <div class="card" data-section="<?php echo esc_attr($key); ?>">
            <h2><?php echo esc_html($label); ?></h2>

            <div class="wi-grid">
              <div>
                <label>H3 naslov</label>
                <input type="text" class="regular-text"
                  name="<?php echo self::OPT_KEY; ?>[<?php echo esc_attr($key); ?>][title]"
                  value="<?php echo esc_attr($title); ?>">
              </div>
            </div>

            <div class="wi-paras">
              <?php
                // Jedan "proto" red (skriveni šablon za Add) + postojeći redovi
                $nameBase = self::OPT_KEY."[$key][paras]";
                $renderRow = function($value, $proto=false) use ($nameBase){
                  $v = is_string($value)? $value : '';
                  ?>
                  <div class="wi-para <?php echo $proto?'wi-ghost':''; ?>" <?php echo $proto?'data-proto="1" style="display:none"':''; ?>>
                    <input type="text" name="<?php echo esc_attr($nameBase); ?>[]" value="<?php echo esc_attr($v); ?>" class="regular-text" placeholder="Tekst paragrafa">
                    <button class="button button-secondary wi-del" type="button">Ukloni</button>
                  </div>
                <?php };
              ?>
              <?php $renderRow('', true); // proto ?>
              <?php foreach ($paras as $p) { $renderRow($p, false); } ?>
            </div>

            <div class="wi-row">
              <button type="button" class="button button-primary wi-add">+ Dodaj paragraf</button>
            </div>
          </div>
        <?php endforeach; ?>

        <?php submit_button(); ?>
      </form>
    </div>
    <?php
  }

  public function sanitize($input) {
    $defs = $this->defaults();
    $out  = [];

    foreach ($this->sections_spec() as $key => $label) {
      $title = isset($input[$key]['title']) ? sanitize_text_field($input[$key]['title']) : '';
      $paras = isset($input[$key]['paras']) && is_array($input[$key]['paras']) ? $input[$key]['paras'] : [];

      // očisti paragrafe, ukloni prazne
      $clean = [];
      foreach ($paras as $p) {
        $p = trim(wp_unslash($p));
        if ($p !== '') $clean[] = sanitize_text_field($p);
      }
      if (empty($clean)) $clean = $defs[$key]['paras']; // ako obrišu sve, vrati default

      $out[$key] = [
        'title' => ($title !== '' ? $title : $defs[$key]['title']),
        'paras' => $clean
      ];
    }
    return $out;
  }

  /** (opciono) Kratak pregled – za testiranje kroz [wi_impressum_dump] */
  public function shortcode_dump() {
    $opt = get_option(self::OPT_KEY, $this->defaults());
    ob_start();
    echo '<pre style="white-space:pre-wrap;background:#111;color:#0f0;padding:10px;border-radius:8px">';
    print_r($opt);
    echo '</pre>';
    return ob_get_clean();
  }

  /** Helper za temu – vrati uvek popunjene vrednosti (sa defaultima) */
  public static function get_data() {
    $self = new self();
    $defs = $self->defaults();
    $opt  = get_option(self::OPT_KEY, []);
    return wp_parse_args($opt, $defs);
  }
}
new WI_Impressum_Manager();
