<?php
/**
 * Plugin Name: WI Contact
 * Description: Custom kontakt forma + “pills” + kontakt info (adresa/email/telefon + mapa).
 * Version: 1.3.4
 * Author: WI
 */

if (!defined('ABSPATH')) exit;

class WI_Contact {
  const OPT_FIELDS = 'wi_contact_fields';
  const OPT_PILLS  = 'wi_contact_pills';
  const OPT_INFO   = 'wi_contact_info';

  public function __construct() {
    add_action('admin_menu',        [$this, 'admin_menu']);
    add_action('admin_init',        [$this, 'register_settings']);
    add_shortcode('wi_contact_form',[$this, 'shortcode_form']);
  }

  /* ---------- Defaults ---------- */

  public static function defaults_fields() {
    return [
      ['name'=>'name','label'=>'Name','type'=>'text','required'=>true],
      ['name'=>'email','label'=>'E-Mail','type'=>'email','required'=>true],
      ['name'=>'company','label'=>'Unternehmen','type'=>'text','required'=>false],
      ['name'=>'phone','label'=>'Telefon','type'=>'tel','required'=>false],
    ];
  }

  public static function defaults_pills() {
    return [
      'services'  => ['title'=>'SERVICES','multiple'=>true,  'items'=>['Außenwerbung','Beschriftung','Grafikdesign','Webdesign']],
      'budget'    => ['title'=>'BUDGET',  'multiple'=>false, 'items'=>['< 5.000€','5.000€ - 15.000€','15.000€ - 50.000€','> 50.000€']],
      'zeitrahmen'=> ['title'=>'ZEITRAHMEN','multiple'=>false,'items'=>['Sofort','Innerhalb 1 Monat','1–3 Monate','> 3 Monate']],
    ];
  }

  public static function defaults_info() {
    return [
      'address_lines' => ["Flughafen 76/3", "88046 Friedrichshafen", "Deutschland"],
      'email' => 'hallo@werbeinsel.de',
      'phone' => '+49 7541 700 57 44',
      'map_mode' => 'address', // address | coords
      'map_address' => 'Flughafen 76/3, 88046 Friedrichshafen, Deutschland',
      'lat'  => '',
      'lng'  => '',
      'zoom' => 15,
      'hl'   => 'de',
    ];
  }

  /* ---------- Helpers: normalization ---------- */

 private static function normalize_fields($arr) {
  $out = [];
  if (!is_array($arr)) return self::defaults_fields();
  foreach ($arr as $f) {
    $name  = isset($f['name'])  ? (is_array($f['name'])  ? reset($f['name'])  : $f['name'])  : '';
    $label = isset($f['label']) ? (is_array($f['label']) ? reset($f['label']) : $f['label']) : '';
    $type  = isset($f['type'])  ? (is_array($f['type'])  ? reset($f['type'])  : $f['type'])  : 'text';

    $type = in_array($type, ['text','email','tel'], true) ? $type : 'text';

    // sanitize i skini sve zvezdice sa kraja (radi i ako ih je više)
    $label = sanitize_text_field($label);
    $label = preg_replace('/\s*\*+$/', '', (string)$label);

    $out[] = [
      'name'     => sanitize_key($name),
      'label'    => $label,
      'type'     => $type,
      'required' => !empty($f['required']),
    ];
  }
  if (!$out) $out = self::defaults_fields();
  return $out;
}


  private static function normalize_pills($p) {
    $def = self::defaults_pills();
    if (!is_array($p) || !$p) return $def;

    foreach ($p as $k => &$g) {
      $g = is_array($g) ? $g : [];
      $g['title']    = sanitize_text_field($g['title'] ?? strtoupper($k));
      $g['multiple'] = !empty($g['multiple']);
      if (isset($g['items']) && !is_array($g['items'])) {
        $g['items'] = preg_split('/\r?\n/', (string)$g['items']);
      }
      // Čistimo unos; entiteti mogu ostati – rešavamo ih pri prikazu.
      $g['items'] = array_values(array_filter(array_map('sanitize_text_field', $g['items'] ?? [])));
    }
    return $p;
  }

  /** FRONTEND helper: vrati “pills” sa dekodiranim HTML entitetima (&lt; -> <) */
  public static function get_pills_decoded() {
    $p = self::normalize_pills( get_option(self::OPT_PILLS, self::defaults_pills()) );
    foreach ($p as &$g) {
      $g['title'] = wp_specialchars_decode( $g['title'], ENT_QUOTES );
      $g['items'] = array_map(function($s){
        return wp_specialchars_decode( $s, ENT_QUOTES );
      }, $g['items'] ?? []);
    }
    return $p;
  }

  /* ---------- Admin ---------- */

  public function admin_menu() {
    add_menu_page('WI Contact', 'WI Contact', 'manage_options', 'wi-contact',
      [$this, 'admin_page'], 'dashicons-email', 56);
  }

  public function register_settings() {
    register_setting('wi_contact_group', self::OPT_FIELDS);
    register_setting('wi_contact_group', self::OPT_PILLS);
    register_setting('wi_contact_group', self::OPT_INFO);

    if (!get_option(self::OPT_FIELDS)) update_option(self::OPT_FIELDS, self::defaults_fields());
    if (!get_option(self::OPT_PILLS))  update_option(self::OPT_PILLS,  self::defaults_pills());
    if (!get_option(self::OPT_INFO))   update_option(self::OPT_INFO,   self::defaults_info());
  }

  public function admin_page() {
    if (!current_user_can('manage_options')) return;

    $fields = self::normalize_fields(get_option(self::OPT_FIELDS, self::defaults_fields()));
    $pills  = self::normalize_pills(get_option(self::OPT_PILLS,  self::defaults_pills()));
    $info   = get_option(self::OPT_INFO,   self::defaults_info());
    if (!is_array($info)) $info = self::defaults_info();

    // Save
    if ($_SERVER['REQUEST_METHOD']==='POST' && check_admin_referer('wi_contact_save','wi_contact_nonce')) {
      $safe_fields = self::normalize_fields($_POST['fields'] ?? []);
      update_option(self::OPT_FIELDS, $safe_fields);

      $new_pills = self::normalize_pills($_POST['pills'] ?? []);
      update_option(self::OPT_PILLS, $new_pills);

      $ni = [];
      $ni['address_lines'] = array_values(array_filter(array_map('sanitize_text_field', preg_split('/\r?\n/', $_POST['info']['address_lines'] ?? ""))));
      $ni['email']         = sanitize_text_field($_POST['info']['email'] ?? '');
      $ni['phone']         = sanitize_text_field($_POST['info']['phone'] ?? '');
      $ni['map_mode']      = in_array(($_POST['info']['map_mode'] ?? 'address'), ['address','coords'], true) ? $_POST['info']['map_mode'] : 'address';
      $ni['map_address']   = sanitize_text_field($_POST['info']['map_address'] ?? '');
      $ni['lat']           = sanitize_text_field($_POST['info']['lat'] ?? '');
      $ni['lng']           = sanitize_text_field($_POST['info']['lng'] ?? '');
      $ni['zoom']          = intval($_POST['info']['zoom'] ?? 15);
      $ni['hl']            = sanitize_text_field($_POST['info']['hl'] ?? 'de');

      if (empty($ni['map_address']) && !empty($ni['address_lines'])) {
        $ni['map_address'] = implode(', ', $ni['address_lines']);
      }
      update_option(self::OPT_INFO, $ni);

      $fields = $safe_fields;
      $pills  = $new_pills;
      $info   = $ni;

      echo '<div class="updated notice"><p>Sačuvano.</p></div>';
    }

    ?>
    <div class="wrap">
      <h1>WI Contact</h1>
      <form method="post">
        <?php wp_nonce_field('wi_contact_save','wi_contact_nonce'); ?>

        <h2 style="margin-top:20px;">A) Kontakt polja (forma)</h2>
        <p>Dodaj/izbriši/izmeni polja forme.</p>
        <table class="widefat striped">
          <thead><tr><th>Ime polja (name)</th><th>Label</th><th>Tip</th><th>Required</th><th></th></tr></thead>
          <tbody id="wi-fields-rows">
            <?php foreach ($fields as $i=>$f):
              $val_name  = isset($f['name'])  ? (string)$f['name']  : '';
              $val_label = isset($f['label']) ? (string)$f['label'] : '';
              $val_type  = isset($f['type'])  ? (string)$f['type']  : 'text';
              $val_req   = !empty($f['required']);
            ?>
              <tr>
                <td><input name="fields[<?php echo $i;?>][name]"   value="<?php echo esc_attr($val_name);?>"  class="regular-text" /></td>
                <td><input name="fields[<?php echo $i;?>][label]"  value="<?php echo esc_attr($val_label);?>" class="regular-text" /></td>
                <td>
                  <select name="fields[<?php echo $i;?>][type]">
                    <?php foreach (['text','email','tel'] as $t): ?>
                      <option value="<?php echo $t;?>" <?php selected($val_type,$t);?>><?php echo $t;?></option>
                    <?php endforeach;?>
                  </select>
                </td>
                <td><label><input type="checkbox" name="fields[<?php echo $i;?>][required]" value="1" <?php checked($val_req);?>> Required</label></td>
                <td><button class="button wi-remove-row" type="button">Obriši</button></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
        <p><button type="button" class="button" id="wi-add-field">+ Dodaj polje</button></p>

        <hr/>

        <h2>B) “Pills” (Services / Budget / Zeitrahmen)</h2>
        <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:16px;">
          <?php foreach (['services'=>'SERVICES','budget'=>'BUDGET','zeitrahmen'=>'ZEITRAHMEN'] as $key=>$title):
            $g = $pills[$key] ?? ['title'=>$title,'multiple'=>false,'items'=>[]];

            // ↓↓↓ ADMIN PRIKAZ: DEKODUJ ENTITETE PA ESCAPEUJ ZA TEXTAREA
            $items_text = implode("\n", array_map(function($s){
              return wp_specialchars_decode($s, ENT_QUOTES);
            }, $g['items'] ?? []));
          ?>
            <div class="card">
              <h3><?php echo esc_html($title);?></h3>
              <p>Naslov: <input name="pills[<?php echo $key;?>][title]" value="<?php echo esc_attr($g['title']);?>"></p>
              <p><label><input type="checkbox" name="pills[<?php echo $key;?>][multiple]" value="1" <?php checked(!empty($g['multiple']));?>> Dozvoli višestruki izbor</label></p>
              <p>Stavke (po redovima):<br>
                <textarea name="pills[<?php echo $key;?>][items]" rows="6" style="width:100%;"><?php echo esc_textarea($items_text);?></textarea>
              </p>
            </div>
          <?php endforeach;?>
        </div>

        <hr/>

        <h2>C) Kontakt info (kartica pored mape)</h2>
        <table class="form-table">
          <tr>
            <th scope="row">Adresa (po jedna linija)</th>
            <td><textarea name="info[address_lines]" rows="4" class="large-text"><?php echo esc_textarea(implode("\n", $info['address_lines'] ?? []));?></textarea></td>
          </tr>
          <tr>
            <th scope="row">E-mail</th>
            <td><input name="info[email]" class="regular-text" value="<?php echo esc_attr($info['email'] ?? '');?>"></td>
          </tr>
          <tr>
            <th scope="row">Telefon</th>
            <td><input name="info[phone]" class="regular-text" value="<?php echo esc_attr($info['phone'] ?? '');?>"></td>
          </tr>
          <tr>
            <th scope="row">Mapa – izvor</th>
            <td>
              <label><input type="radio" name="info[map_mode]" value="address" <?php checked(($info['map_mode']??'address'),'address');?>> Adresa</label>
              &nbsp;&nbsp;
              <label><input type="radio" name="info[map_mode]" value="coords"  <?php checked(($info['map_mode']??'address'),'coords');?>> Koordinate</label>
            </td>
          </tr>
          <tr>
            <th scope="row">Map address (ako koristiš “Adresa”)</th>
            <td><input name="info[map_address]" class="regular-text" value="<?php echo esc_attr($info['map_address'] ?? '');?>"><br>
              <small>Ostavi prazno da se generiše iz polja Adresa.</small></td>
          </tr>
          <tr>
            <th scope="row">Koordinate (ako koristiš “Koordinate”)</th>
            <td>
              Lat: <input name="info[lat]" size="12" value="<?php echo esc_attr($info['lat'] ?? '');?>"> &nbsp;
              Lng: <input name="info[lng]" size="12" value="<?php echo esc_attr($info['lng'] ?? '');?>"> &nbsp;
              Zoom: <input name="info[zoom]" size="4" value="<?php echo esc_attr($info['zoom'] ?? 15);?>">
            </td>
          </tr>
          <tr>
            <th scope="row">Jezik mape (hl)</th>
            <td><input name="info[hl]" size="6" value="<?php echo esc_attr($info['hl'] ?? 'de');?>"> <small>npr. de, en</small></td>
          </tr>
        </table>

        <?php submit_button('Sačuvaj sve'); ?>
      </form>
    </div>

    <script>
      (function(){
        const tbody = document.getElementById('wi-fields-rows');
        document.getElementById('wi-add-field')?.addEventListener('click', () => {
          const i = tbody.querySelectorAll('tr').length;
          const tr = document.createElement('tr');
          tr.innerHTML =
            '<td><input name="fields['+i+'][name]" class="regular-text"></td>'+
            '<td><input name="fields['+i+'][label]" class="regular-text"></td>'+
            '<td><select name="fields['+i+'][type]"><option>text</option><option>email</option><option>tel</option></select></td>'+
            '<td><label><input type="checkbox" name="fields['+i+'][required]" value="1"> Required</label></td>'+
            '<td><button class="button wi-remove-row" type="button">Obriši</button></td>';
          tbody.appendChild(tr);
        });
        tbody.addEventListener('click', (e) => {
          if (e.target && e.target.classList.contains('wi-remove-row')) {
            e.target.closest('tr').remove();
          }
        });
      })();
    </script>
    <?php
  }

  /* ---------- Shortcode: samo HTML forme (bez submit dugmeta – Next ga koristi) ---------- */
 public function shortcode_form($atts = []) {
  $fields = self::normalize_fields(get_option(self::OPT_FIELDS, self::defaults_fields()));
  ob_start(); ?>
  <form class="wi-contact-form" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" method="post">
    <input type="hidden" name="action" value="wi_contact_submit">

    <div class="wi-grid">
      <?php foreach ($fields as $f):
        $name  = sanitize_key($f['name'] ?? '');
        if (!$name) continue;
        $type  = in_array(($f['type'] ?? 'text'), ['text','email','tel'], true) ? $f['type'] : 'text';
        $label = (string)($f['label'] ?? '');
        // skini eventualne stare zvezdice iz labela
        $label_plain = trim(preg_replace('/\s*\*+$/', '', $label));
        $req   = !empty($f['required']);
        $id    = 'wi_' . $name;
        $ph    = $label_plain . ($req ? ' *' : '');
      ?>
        <!-- VRAĆEN STARI WRAPPER: label oko inputa zbog stilova -->
        <label class="wi-field" for="<?php echo esc_attr($id); ?>">
          <span class="wi-label-text"><?php echo esc_html($label_plain); ?></span>
          <input
            id="<?php echo esc_attr($id); ?>"
            name="<?php echo esc_attr($name); ?>"
            type="<?php echo esc_attr($type); ?>"
            placeholder="<?php echo esc_attr($ph); ?>"
            <?php echo $req ? 'required aria-required="true"' : ''; ?>
          >
        </label>
      <?php endforeach; ?>
    </div>

    <!-- Hidden za pills -->
    <input type="hidden" name="pill_services">
    <input type="hidden" name="pill_budget">
    <input type="hidden" name="pill_zeitrahmen">

    <!-- Fallback submit (skriven u šablonu) -->
    <button type="submit" class="wi-submit">Senden</button>
  </form>
  <?php
  return ob_get_clean();
}


}

new WI_Contact();

/* Napomena: handler za admin_post_wi_contact_submit / nopriv varijante treba da postoji u temi/pluginu. */
