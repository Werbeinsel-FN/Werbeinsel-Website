<?php
/**
 * Plugin Name: WI Contact
 * Description: Custom kontakt forma + “pills” + kontakt info (adresa/email/telefon).
 * Version: 1.3.6
 * Author: WI
 */

if (!defined('ABSPATH')) exit;

class WI_Contact {
  const OPT_FIELDS = 'wi_contact_fields';
  const OPT_PILLS  = 'wi_contact_pills';
  const OPT_INFO   = 'wi_contact_info';
  const OPT_RECAPTCHA_SITE   = 'wi_contact_recaptcha_site';
  const OPT_RECAPTCHA_SECRET = 'wi_contact_recaptcha_secret';

  public function __construct() {
    // Admin
    add_action('admin_menu', [$this, 'admin_menu']);
    add_action('admin_init', [$this, 'register_settings']);

    // Front
    add_shortcode('wi_contact_form',  [$this, 'shortcode_form']);
    add_shortcode('wi_contact_info',  [$this, 'shortcode_info']);
    add_action('wp_head',             [$this, 'print_info_css']); // CSS za [wi_contact_info]

    // Submit
    add_action('admin_post_wi_contact_submit',        [$this, 'handle_submit']);
    add_action('admin_post_nopriv_wi_contact_submit', [$this, 'handle_submit']);
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
      'services'   => ['title'=>'Services','items'=>["Plakatwerbung","Grafik","Fotografie"]],
      'budget'     => ['title'=>'Budget','items'=>["<50€","50-200€","200-1000€",">1000€"]],
      'zeitrahmen' => ['title'=>'Zeitrahmen','items'=>["Schnell","2-4 Wochen","1-2 Monate","Später"]],
    ];
  }

  public static function defaults_info() {
    // Legacy map polja ostaju u opciji (radi kompatibilnosti), ali se ne koriste.
    return [
      'address_lines' => [],
      'email' => '',
      'phone' => '',
      'map_mode' => 'address',
      'map_address' => '',
      'lat'  => '',
      'lng'  => '',
      'zoom' => 15,
      'hl'   => 'de',
    ];
  }

  /* ---------- Helpers ---------- */

  private static function normalize_fields($arr) {
    $out = [];
    if (!is_array($arr)) return self::defaults_fields();
    foreach ($arr as $f) {
      $name  = isset($f['name'])  ? (is_array($f['name'])  ? reset($f['name'])  : $f['name'])  : '';
      $label = isset($f['label']) ? (is_array($f['label']) ? reset($f['label']) : $f['label']) : '';
      $type  = isset($f['type'])  ? (is_array($f['type']) ? reset($f['type']) : $f['type']) : 'text';
      $required = !empty($f['required']);
      if (!$name) continue;
      $out[] = ['name'=>$name,'label'=>$label,'type'=>$type,'required'=>$required];
    }
    return $out;
  }

  private static function normalize_pills($p) {
    if (!is_array($p)) return self::defaults_pills();
    foreach ($p as $k => &$g) {
      $g = is_array($g) ? $g : [];
      $g['title']    = sanitize_text_field($g['title'] ?? strtoupper($k));
      $g['multiple'] = !empty($g['multiple']);
      if (isset($g['items']) && !is_array($g['items'])) {
        $g['items'] = preg_split('/\r?\n/', (string)$g['items']);
      }
      $g['items'] = array_values(array_filter(array_map('sanitize_text_field', $g['items'] ?? [])));
    }
    return $p;
  }

  /** FRONTEND helper: pills sa dekodiranim HTML entitetima */
  public static function get_pills_decoded() {
    $p = self::normalize_pills( get_option(self::OPT_PILLS, self::defaults_pills()) );
    foreach ($p as &$g) {
      $g['title'] = wp_specialchars_decode( $g['title'], ENT_QUOTES );
      $g['items'] = array_map(function($s){ return wp_specialchars_decode($s, ENT_QUOTES); }, $g['items'] ?? []);
    }
    return $p;
  }

  /* ---------- Admin ---------- */

  public function admin_menu() {
    add_menu_page('WI Contact', 'WI Contact', 'manage_options', 'wi-contact',
      [$this, 'admin_page'], 'dashicons-email', 56);
  }

  public function register_settings() {
    register_setting('wi_contact_options', self::OPT_FIELDS);
    register_setting('wi_contact_options', self::OPT_PILLS);
    register_setting('wi_contact_options', self::OPT_INFO);
    register_setting('wi_contact_options', self::OPT_RECAPTCHA_SITE);
    register_setting('wi_contact_options', self::OPT_RECAPTCHA_SECRET);
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

      // Ažuriramo samo adresu/email/telefon (mapa je uklonjena iz UI-ja)
      $prev = get_option(self::OPT_INFO, self::defaults_info());
      $ni = is_array($prev) ? $prev : self::defaults_info();
      $ni['address_lines'] = array_values(array_filter(array_map('sanitize_text_field', preg_split('/\r?\n/', $_POST['info']['address_lines'] ?? ""))));
      $ni['email']         = sanitize_text_field($_POST['info']['email'] ?? '');
      $ni['phone']         = sanitize_text_field($_POST['info']['phone'] ?? '');
      update_option(self::OPT_INFO, $ni);

      // reCAPTCHA (opciono)
      $rec_site = sanitize_text_field($_POST['recaptcha_site_key'] ?? '');
      $rec_sec  = sanitize_text_field($_POST['recaptcha_secret_key'] ?? '');
      update_option(self::OPT_RECAPTCHA_SITE, $rec_site);
      update_option(self::OPT_RECAPTCHA_SECRET, $rec_sec);

      $fields = $safe_fields;
      $pills  = $new_pills;
      $info   = $ni;
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
              $val_name  = (string)($f['name'] ?? '');
              $val_label = (string)($f['label'] ?? '');
              $val_type  = (string)($f['type'] ?? 'text');
              $val_req   = !empty($f['required']);
            ?>
              <tr>
                <td><input name="fields[<?php echo $i;?>][name]" value="<?php echo esc_attr($val_name);?>"  class="regular-text" /></td>
                <td><input name="fields[<?php echo $i;?>][label]" value="<?php echo esc_attr($val_label);?>" class="regular-text" /></td>
                <td>
                  <select name="fields[<?php echo $i;?>][type]">
                    <?php foreach (['text','email','tel'] as $tt): ?>
                      <option value="<?php echo esc_attr($tt); ?>" <?php selected($val_type,$tt); ?>><?php echo esc_html($tt); ?></option>
                    <?php endforeach; ?>
                  </select>
                </td>
                <td><input type="checkbox" name="fields[<?php echo $i;?>][required]" <?php checked($val_req); ?>></td>
                <td><a href="#" class="wi-remove-row">Remove</a></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
        <p><a href="#" id="wi-add-field">+ Add field</a></p>

        <h2 style="margin-top:20px;">B) Pills (usluge / budžet / vremenski okvir)</h2>
        <p>Uredi listu predefinisanih stavki (jedan po liniji).</p>
        <table class="widefat striped">
          <thead><tr><th style="width:220px">Grupa</th><th>Stavke</th></tr></thead>
          <tbody>
            <?php foreach ($pills as $k=>$g): ?>
              <tr>
                <th style="vertical-align: top;"><?php echo esc_html($g['title']); ?></th>
                <td><textarea name="pills[<?php echo esc_attr($k); ?>][items]" rows="6" style="width:100%"><?php echo esc_textarea(implode("\n",$g['items'] ?? [])); ?></textarea></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>

        <h2 style="margin-top:20px;">C) Kontakt info</h2>
        <table class="widefat striped">
          <tr>
            <th style="width:220px">Email koji prima</th>
            <td><input name="info[email]" class="regular-text" value="<?php echo esc_attr($info['email'] ?? ''); ?>"></td>
          </tr>
          <tr>
            <th>Telefon</th>
            <td><input name="info[phone]" class="regular-text" value="<?php echo esc_attr($info['phone'] ?? ''); ?>"></td>
          </tr>
          <tr>
            <th>Adresa (linije)</th>
            <td><textarea name="info[address_lines]" rows="6" style="width:100%"><?php echo esc_textarea(implode("\n",$info['address_lines'] ?? [])); ?></textarea></td>
          </tr>
        </table>

        <h2 style="margin-top:20px;">D) reCAPTCHA v3 (opcionalno)</h2>
        <p>Unesi Google reCAPTCHA v3 site i secret ključeve. Ako ostane prazno, forma neće koristiti reCAPTCHA.</p>
        <table class="widefat striped">
          <tr>
            <th style="width:220px">Site key</th>
            <td><input name="recaptcha_site_key" class="regular-text" value="<?php echo esc_attr(get_option(self::OPT_RECAPTCHA_SITE, '')); ?>"></td>
          </tr>
          <tr>
            <th>Secret key</th>
            <td><input name="recaptcha_secret_key" class="regular-text" value="<?php echo esc_attr(get_option(self::OPT_RECAPTCHA_SECRET, '')); ?>"></td>
          </tr>
        </table>

        <?php submit_button('Sačuvaj sve'); ?>
      </form>
    </div>
    <style>
      #wi-fields-rows input[type=text], #wi-fields-rows input[type=email], #wi-fields-rows select{width:100%;}
      .wi-remove-row{color:#a00;}
    </style>
    <script>
      (function(){
        var add = document.getElementById('wi-add-field');
        if (add) {
          add.addEventListener('click', function(e){
            e.preventDefault();
            var tbody = document.getElementById('wi-fields-rows');
            var idx = tbody.children.length;
            var tr = document.createElement('tr');
            tr.innerHTML =
              '<td><input name="fields['+idx+'][name]" class="regular-text"></td>'+
              '<td><input name="fields['+idx+'][label]" class="regular-text"></td>'+
              '<td><select name="fields['+idx+'][type]"><option value="text">text</option><option value="email">email</option><option value="tel">tel</option></select></td>'+
              '<td><input type="checkbox" name="fields['+idx+'][required]"></td>'+
              '<td><a href="#" class="wi-remove-row">Remove</a></td>';
            tbody.appendChild(tr);
          });
        }
        document.addEventListener('click', function(e){
          if (e.target && e.target.matches('.wi-remove-row')) {
            e.preventDefault();
            var tr = e.target.closest('tr');
            if (tr) tr.parentNode.removeChild(tr);
          }
        });
      })();
    </script>
    <?php
  }

  /* ---------- FRONT CSS za [wi_contact_info] ---------- */
  public function print_info_css(){
    ?>
    <style id="wi-contact-info-css">
      /* container širina */
      .wi-ci__container{max-width:1780px;margin-inline:auto;padding-inline:5vw;}
      /* crna sekcija sa žutim gornjim borderom */
      .wi-ci{background:#000;color:#fff;border-top:8px solid #ffed00;padding:4rem 0;margin:0;}
      /* grid 1->3 kolone */
      .wi-ci__row{display:grid;grid-template-columns:1fr;gap:3rem;text-align:center}
      @media (min-width:900px){ .wi-ci__row{grid-template-columns:repeat(3,1fr);gap:4rem} }
      /* item */
      .wi-ci__item{display:flex;flex-direction:column;align-items:center;gap:1.25rem}
      /* ikone – žute linije, bez popune */
      .wi-ci__icon{width:96px;height:96px;display:block;color:#ffed00}
      .wi-ci__icon, .wi-ci__icon *{fill:none;stroke:currentColor;stroke-width:2.5;stroke-linecap:round;stroke-linejoin:round}
      /* tipografija */
      .wi-ci__title{font-family:var(--font-poppins,"Poppins",system-ui,-apple-system,"Segoe UI",Roboto,"Helvetica Neue",Arial,sans-serif) ;font-weight:800;text-transform:uppercase;letter-spacing:.5px;font-size:clamp(20px,2.4vw,34px);line-height:1.05;margin:12px 0 0}
      .wi-ci__text{font-family:var(--font-poppins,"Poppins",system-ui,-apple-system,"Segoe UI",Roboto,"Helvetica Neue",Arial,sans-serif);font-size:clamp(20px,2.4vw,34px);line-height:1.35;margin:6px 0 0}
      .wi-ci__link{color:#fff;text-decoration:none}
      .wi-ci__link:hover{color:#ffed00}
      /* sigurnosno: ukloni donji luft od tema main-a */
      main.pb-10{padding-bottom:0!important}
    </style>
    <?php
  }

  /* ---------- Shortcodes ---------- */

  // Kontakt info (adresa / e-mail / telefon) – izolovan markup bez Tailwind-a
  public function shortcode_info($atts = []) {
    $info  = get_option(self::OPT_INFO, self::defaults_info());
    $email = sanitize_email($info['email'] ?? '');
    $phone = trim((string)($info['phone'] ?? ''));
    $addr  = array_filter(array_map('trim', $info['address_lines'] ?? []));
    $tel_href = preg_replace('/[^\d\+]/', '', $phone);

    ob_start(); ?>
    <section class="wi-ci">
      <div class="wi-ci__container">
        <div class="wi-ci__row">
          <?php if (!empty($addr)): ?>
          <div class="wi-ci__item">
            <svg class="wi-ci__icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" aria-hidden="true">
              <path d="M20 10c0 4.993-5.539 10.193-7.399 11.799a1 1 0 0 1-1.202 0C9.539 20.193 4 14.993 4 10a8 8 0 0 1 16 0"></path>
              <circle cx="12" cy="10" r="3"></circle>
            </svg>
            <h3 class="wi-ci__title">ADRESSE</h3>
            <p class="wi-ci__text"><?php echo implode('<br>', array_map('esc_html', $addr)); ?></p>
          </div>
          <?php endif; ?>

          <?php if ($email): ?>
          <div class="wi-ci__item">
            <svg class="wi-ci__icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" aria-hidden="true">
              <path d="m22 7-8.991 5.727a2 2 0 0 1-2.009 0L2 7"></path>
              <rect x="2" y="4" width="20" height="16" rx="2"></rect>
            </svg>
            <h3 class="wi-ci__title">E-MAIL</h3>
            <a class="wi-ci__text wi-ci__link" href="mailto:<?php echo esc_attr($email); ?>"><?php echo esc_html($email); ?></a>
          </div>
          <?php endif; ?>

          <?php if ($phone): ?>
          <div class="wi-ci__item">
            <svg class="wi-ci__icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" aria-hidden="true">
              <path d="M13.832 16.568a1 1 0 0 0 1.213-.303l.355-.465A2 2 0 0 1 17 15h3a2 2 0 0 1 2 2v3a2 2 0 0 1-2 2A18 18 0 0 1 2 4a2 2 0 0 1 2-2h3a2 2 0 0 1 2 2v3a2 2 0 0 1-.8 1.6l-.468.351a1 1 0 0 0-.292 1.233 14 14 0 0 0 6.392 6.384"></path>
            </svg>
            <h3 class="wi-ci__title">TELEFON</h3>
            <a class="wi-ci__text wi-ci__link" href="tel:<?php echo esc_attr($tel_href); ?>"><?php echo esc_html($phone); ?></a>
          </div>
          <?php endif; ?>
        </div>
      </div>
    </section>
    <?php
    return ob_get_clean();
  }

  // Forma
  public function shortcode_form($atts = []) {
    $fields = self::normalize_fields(get_option(self::OPT_FIELDS, self::defaults_fields()));
    ob_start(); ?>
    <form class="wi-contact-form" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" method="post">
      <?php $rec_site = get_option(self::OPT_RECAPTCHA_SITE, ''); if ($rec_site): ?>
      <input type="hidden" name="wi_recaptcha_token" id="wi_recaptcha_token" value="">
      <script src="https://www.google.com/recaptcha/api.js?render=<?php echo esc_attr($rec_site); ?>"></script>
      <script>
      document.addEventListener('DOMContentLoaded', function(){
        if (typeof grecaptcha !== 'undefined') {
          grecaptcha.ready(function(){
            var form = document.querySelector('.wi-contact-form');
            if (!form) return;
            form.addEventListener('submit', function(evt){
              evt.preventDefault();
              grecaptcha.execute('<?php echo esc_js($rec_site); ?>', {action: 'contact'}).then(function(token){
                var inp = document.getElementById('wi_recaptcha_token');
                if (inp) inp.value = token;
                form.submit();
              });
            });
          });
        }
      });
      </script>
      <?php endif; ?>

      <input type="hidden" name="action" value="wi_contact_submit">

      <div class="wi-grid">
        <?php foreach ($fields as $f):
          $name  = sanitize_key($f['name'] ?? '');
          if (!$name) continue;
          $type  = in_array(($f['type'] ?? 'text'), ['text','email','tel'], true) ? $f['type'] : 'text';
          $label = (string)($f['label'] ?? '');
          $label_plain = trim(preg_replace('/\s*\*+$/', '', $label)); // očisti eventualnu * iz labela
$req   = !empty($f['required']);
$id    = 'wi_' . $name;

/* placeholder = label + " *" ako je required */
$ph    = rtrim($label_plain, '* ');
if ($req) { $ph .= ' *'; }
        ?>
          <label for="<?php echo esc_attr($id); ?>">
            <span class="wi-label"><?php echo esc_html($label_plain); ?></span>
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

      <input type="hidden" name="pill_services">
      <input type="hidden" name="pill_budget">
      <input type="hidden" name="pill_zeitrahmen">

      <button type="submit" class="wi-submit">Senden</button>
    </form>
    <?php
    return ob_get_clean();
  }

  /* ---------- Slanje mejla ---------- */
  public function handle_submit() {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') wp_die();

    try {
      // reCAPTCHA v3 (opciono)
      $rec_secret = get_option(self::OPT_RECAPTCHA_SECRET, '');
      if ($rec_secret) {
        $token = sanitize_text_field($_POST['wi_recaptcha_token'] ?? '');
        if (empty($token)) { status_header(200); echo 'RECAPTCHA_MISSING'; return; }
        $resp = wp_remote_post('https://www.google.com/recaptcha/api/siteverify', [
          'body'    => ['secret' => $rec_secret, 'response' => $token, 'remoteip' => $_SERVER['REMOTE_ADDR'] ?? ''],
          'timeout' => 12,
        ]);
        if (is_wp_error($resp)) { error_log('WI_CONTACT reCAPTCHA HTTP error: '.$resp->get_error_message()); status_header(200); echo 'RECAPTCHA_ERROR'; return; }
        $body = wp_remote_retrieve_body($resp);
        $json = json_decode($body, true);
        if (empty($json['success']) || (isset($json['score']) && $json['score'] < 0.4)) {
          error_log('WI_CONTACT reCAPTCHA response: ' . substr($body,0,500));
          status_header(200); echo 'RECAPTCHA_FAILED'; return;
        }
      }

      // Polja
      $fields = self::normalize_fields(get_option(self::OPT_FIELDS, self::defaults_fields()));
      $data   = [];
      foreach ($fields as $f) {
        $key = sanitize_key($f['name'] ?? '');
        if (!$key) continue;
        $val = isset($_POST[$key]) ? wp_unslash($_POST[$key]) : '';
        $data[$key] = is_string($val) ? trim($val) : '';
      }

      // Primaoc
      $info     = get_option(self::OPT_INFO, self::defaults_info());
      $email_to = sanitize_email($info['email'] ?? '') ?: get_option('admin_email');

      // Subject & body
      $subject = sprintf('Neue Anfrage über Kontaktformular (%s)', parse_url(home_url(), PHP_URL_HOST));
      $lines = [];
      foreach ($fields as $f) {
        $label = preg_replace('/\s*\*+$/', '', (string)($f['label'] ?? ''));
        $name  = sanitize_key($f['name'] ?? '');
        $val   = $data[$name] ?? '';
        $lines[] = '<p><strong>'.esc_html($label).':</strong> '.nl2br(esc_html($val)).'</p>';
      }
      foreach (['pill_services'=>'Services','pill_budget'=>'Budget','pill_zeitrahmen'=>'Zeitrahmen'] as $k=>$title) {
        if (!empty($_POST[$k])) {
          $v = is_string($_POST[$k]) ? wp_unslash($_POST[$k]) : '';
          $lines[] = '<p><strong>'.esc_html($title).':</strong> '.esc_html($v).'</p>';
        }
      }
      $body = '<html><body>'.implode('', $lines).'</body></html>';

      $headers = ['Content-Type: text/html; charset=UTF-8'];
      if (!empty($data['email']) && is_email($data['email'])) {
        $headers[] = 'Reply-To: '.$data['email'];
      }

      add_action('wp_mail_failed', function($e){ error_log('WI_MAIL_FAIL: ' . print_r($e, true)); });
      $sent = wp_mail($email_to, $subject, $body, $headers);

      status_header(200);
      echo $sent ? 'OK' : 'MAIL_ERROR';
      return;

    } catch (\Throwable $e) {
      if (function_exists('error_log')) {
        error_log('WI_CONTACT FATAL: ' . $e->getMessage() . ' @ ' . $e->getFile() . ':' . $e->getLine());
      }
      status_header(200);
      echo 'SERVER_ERROR: ' . $e->getMessage() . ' @ ' . basename($e->getFile()) . ':' . $e->getLine();
    }

    wp_die();
  }
}

new WI_Contact();
