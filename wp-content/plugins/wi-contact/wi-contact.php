<?php
/**
 * Plugin Name: WI Contact
 * Description: Kontakt forma sa podesivim poljima (min. 4) + podesive grupe dugmadi (Services/Budget/Zeitrahmen). Shortcode: [wi_contact_form]
 * Version: 1.2.0
 * Author: Werbeinsel
 */

if (!defined('ABSPATH')) exit;

class WI_Contact {
    /** Opcije */
    const OPT        = 'wi_contact_fields';   // polja forme
    const OPT_PILLS  = 'wi_contact_pills';    // grupe dugmadi
    /** Submit */
    const NONCE  = 'wi_contact_nonce';
    const ACTION = 'wi_contact_submit';

    public function __construct() {
        // Admin
        add_action('admin_menu',            [$this,'menu']);
        add_action('admin_init',            [$this,'register_settings']);
        add_action('admin_enqueue_scripts', [$this,'admin_assets']);

        // Front
        add_action('wp_enqueue_scripts',    [$this,'front_assets']);
        add_shortcode('wi_contact_form',    [$this,'shortcode']);

        // Submit handler
        add_action('admin_post_nopriv_' . self::ACTION, [$this,'handle_submit']);
        add_action('admin_post_'      . self::ACTION,    [$this,'handle_submit']);

        // Defaults
        register_activation_hook(__FILE__,  [$this,'activate_defaults']);
    }

    /* ===================== DEFAULTS ===================== */

    /** 4 osnovna polja (kao na slici) */
    public static function defaults() {
        return [
            ['key'=>'name',     'label'=>'Name',        'type'=>'text',  'required'=>true,  'placeholder'=>'', 'enabled'=>true],
            ['key'=>'email',    'label'=>'E-Mail',      'type'=>'email', 'required'=>true,  'placeholder'=>'', 'enabled'=>true],
            ['key'=>'company',  'label'=>'Unternehmen', 'type'=>'text',  'required'=>false, 'placeholder'=>'', 'enabled'=>true],
            ['key'=>'phone',    'label'=>'Telefon',     'type'=>'tel',   'required'=>false, 'placeholder'=>'', 'enabled'=>true],
        ];
    }

    /** Grupe dugmadi (kolone) */
    public static function defaults_pills() {
        return [
            'services' => [
                'title'    => 'SERVICES',
                'multiple' => true,
                'items'    => ['Außenwerbung','Beschriftung','Grafikdesign','Webdesign'],
            ],
            'budget' => [
                'title'    => 'BUDGET',
                'multiple' => false,
                'items'    => ['< 5.000€','5.000€ - 15.000€','15.000€ - 50.000€','> 50.000€'],
            ],
            'zeitrahmen' => [
                'title'    => 'ZEITRAHMEN',
                'multiple' => false,
                'items'    => ['Sofort','Innerhalb 1 Monat','1–3 Monate','> 3 Monate'],
            ],
        ];
    }

    public function activate_defaults() {
        if (!get_option(self::OPT))       update_option(self::OPT,       self::defaults());
        if (!get_option(self::OPT_PILLS)) update_option(self::OPT_PILLS, self::defaults_pills());
    }

    /* ===================== ADMIN ===================== */

    public function menu() {
        add_menu_page(
            'WI Contact', 'WI Contact', 'manage_options',
            'wi-contact', [$this,'settings_page'], 'dashicons-feedback', 58
        );
        add_submenu_page('wi-contact','Kontakt polja','Kontakt polja','manage_options','wi-contact',       [$this,'settings_page']);
        add_submenu_page('wi-contact','Opcije dugmadi','Opcije dugmadi','manage_options','wi-contact-pills',[$this,'pills_page']);
    }

    public function register_settings() {
        register_setting('wi_contact_group', self::OPT, [
            'type' => 'array',
            'sanitize_callback' => [$this,'sanitize_fields'],
            'default' => self::defaults(),
        ]);

        register_setting('wi_contact_group_pills', self::OPT_PILLS, [
            'type' => 'array',
            'sanitize_callback' => function($in) {
                $def = self::defaults_pills(); $out = [];
                foreach ($def as $key=>$g) {
                    $row       = isset($in[$key]) ? $in[$key] : [];
                    $title     = sanitize_text_field($row['title'] ?? $g['title']);
                    $multiple  = !empty($row['multiple']);
                    $itemsText = trim($row['items_text'] ?? implode("\n",$g['items']));
                    $items     = array_values(array_filter(array_map(function($s){
                        return sanitize_text_field(trim($s));
                    }, preg_split("/\r\n|\r|\n/",$itemsText))));
                    if (!$items) $items = $g['items'];
                    $out[$key] = ['title'=>$title,'multiple'=>$multiple,'items'=>$items];
                }
                return $out;
            },
            'default' => self::defaults_pills(),
        ]);
    }

    public function sanitize_fields($input) {
        $clean = [];
        if (!is_array($input)) $input = [];
        foreach ($input as $row) {
            if (empty($row['key']) && empty($row['label'])) continue;
            $key = sanitize_key($row['key'] ?: $row['label']);
            if (!$key) continue;
            $clean[] = [
                'key'        => $key,
                'label'      => sanitize_text_field($row['label'] ?? $key),
                'type'       => in_array(($row['type'] ?? 'text'), ['text','email','tel','textarea']) ? $row['type'] : 'text',
                'required'   => !empty($row['required']),
                'placeholder'=> sanitize_text_field($row['placeholder'] ?? ''),
                'enabled'    => !empty($row['enabled']),
            ];
        }
        // uvek zadrži minimum 4
        while (count($clean) < 4) {
            $clean[] = ['key'=>'field'.(count($clean)+1),'label'=>'Feld','type'=>'text','required'=>false,'placeholder'=>'','enabled'=>true];
        }
        return array_values($clean);
    }

    public function admin_assets($hook) {
        $hooks_ok = ['toplevel_page_wi-contact', 'wi-contact_page_wi-contact', 'wi-contact_page_wi-contact-pills'];
        if (!in_array($hook, $hooks_ok, true)) return;
        wp_enqueue_style('wi-contact-admin', plugin_dir_url(__FILE__).'assets/admin.css', [], '1.0.0');
        wp_enqueue_script('wi-contact-admin', plugin_dir_url(__FILE__).'assets/admin.js', ['jquery'], '1.0.0', true);
    }

    public function settings_page() {
        $fields = get_option(self::OPT, self::defaults()); ?>
        <div class="wrap">
            <h1>Kontakt polja</h1>
            <p>Menjaj nazive, tip, obaveznost, redosled. Minimalno 4 polja uvek ostaju.</p>

            <form method="post" action="options.php">
                <?php settings_fields('wi_contact_group'); ?>
                <table class="widefat fixed striped" id="wi-forms-table">
                    <thead>
                        <tr>
                            <th style="width:18%">Name (slug)</th>
                            <th style="width:22%">Label</th>
                            <th style="width:14%">Tip</th>
                            <th style="width:12%">Obavezno</th>
                            <th style="width:22%">Placeholder</th>
                            <th style="width:12%">Akcija</th>
                        </tr>
                    </thead>
                    <tbody id="wi-forms-rows">
                        <?php foreach ($fields as $i=>$f): ?>
                        <tr>
                            <td><input type="text" name="<?php echo self::OPT; ?>[<?php echo $i; ?>][key]" value="<?php echo esc_attr($f['key']); ?>" required></td>
                            <td><input type="text" name="<?php echo self::OPT; ?>[<?php echo $i; ?>][label]" value="<?php echo esc_attr($f['label']); ?>" required></td>
                            <td>
                                <select name="<?php echo self::OPT; ?>[<?php echo $i; ?>][type]">
                                    <?php foreach (['text'=>'Text','email'=>'E-Mail','tel'=>'Telefon','textarea'=>'Textarea'] as $v=>$t): ?>
                                        <option value="<?php echo esc_attr($v); ?>" <?php selected($f['type'],$v); ?>><?php echo esc_html($t); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </td>
                            <td><label><input type="checkbox" name="<?php echo self::OPT; ?>[<?php echo $i; ?>][required]" <?php checked(!empty($f['required'])); ?>> Obavezno</label></td>
                            <td><input type="text" name="<?php echo self::OPT; ?>[<?php echo $i; ?>][placeholder]" value="<?php echo esc_attr($f['placeholder']); ?>"></td>
                            <td>
                                <button class="button wi-row-up">Gore</button>
                                <button class="button wi-row-down">Dole</button>
                                <button class="button button-danger wi-row-del">Obriši</button>
                            </td>
                            <input type="hidden" name="<?php echo self::OPT; ?>[<?php echo $i; ?>][enabled]" value="1">
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <p><button id="wi-add-row" class="button button-primary">+ Dodaj polje</button></p>
                <?php submit_button('Sačuvaj'); ?>
            </form>

            <!-- Template reda -->
            <table style="display:none"><tbody>
            <tr id="wi-row-template">
                <td><input type="text" name="<?php echo self::OPT; ?>[__i__][key]" required></td>
                <td><input type="text" name="<?php echo self::OPT; ?>[__i__][label]" required></td>
                <td>
                    <select name="<?php echo self::OPT; ?>[__i__][type]">
                        <option value="text">Text</option>
                        <option value="email">E-Mail</option>
                        <option value="tel">Telefon</option>
                        <option value="textarea">Textarea</option>
                    </select>
                </td>
                <td><label><input type="checkbox" name="<?php echo self::OPT; ?>[__i__][required]"> Obavezno</label></td>
                <td><input type="text" name="<?php echo self::OPT; ?>[__i__][placeholder]"></td>
                <td>
                    <button class="button wi-row-up">Gore</button>
                    <button class="button wi-row-down">Dole</button>
                    <button class="button button-danger wi-row-del">Obriši</button>
                </td>
                <input type="hidden" name="<?php echo self::OPT; ?>[__i__][enabled]" value="1">
            </tr>
            </tbody></table>
        </div>
    <?php }

    public function pills_page() {
        $p = get_option(self::OPT_PILLS, self::defaults_pills()); ?>
        <div class="wrap">
            <h1>Opcije dugmadi (Services / Budget / Zeitrahmen)</h1>
            <form method="post" action="options.php">
                <?php settings_fields('wi_contact_group_pills'); ?>
                <?php foreach (['services','budget','zeitrahmen'] as $key): $g = $p[$key]; ?>
                    <h2 style="margin-top:24px;"><?php echo esc_html(strtoupper($key)); ?></h2>
                    <table class="form-table" role="presentation">
                        <tr>
                            <th scope="row"><label>Naslov kolone</label></th>
                            <td><input type="text" name="<?php echo self::OPT_PILLS; ?>[<?php echo esc_attr($key); ?>][title]" value="<?php echo esc_attr($g['title']); ?>" class="regular-text"></td>
                        </tr>
                        <tr>
                            <th scope="row"><label>Dozvoli više izbora?</label></th>
                            <td><label><input type="checkbox" name="<?php echo self::OPT_PILLS; ?>[<?php echo esc_attr($key); ?>][multiple]" <?php checked(!empty($g['multiple'])); ?>> Više izbora</label></td>
                        </tr>
                        <tr>
                            <th scope="row"><label>Stavke (po jedna u redu)</label></th>
                            <td>
                                <textarea rows="6" cols="60" name="<?php echo self::OPT_PILLS; ?>[<?php echo esc_attr($key); ?>][items_text]"><?php echo esc_textarea(implode("\n",$g['items'])); ?></textarea>
                                <p class="description">Redosled = redosled prikaza.</p>
                            </td>
                        </tr>
                    </table>
                    <hr>
                <?php endforeach; ?>
                <?php submit_button('Sačuvaj'); ?>
            </form>
        </div>
    <?php }

    /* ===================== FRONT ===================== */

    public function front_assets() {
        // blagi default stil – izgled (rounded, border) je u assets/front.css
        wp_enqueue_style('wi-contact-front', plugin_dir_url(__FILE__).'assets/front.css', [], '1.0.1');
    }

    public function shortcode() {
        $fields = get_option(self::OPT, self::defaults());
        $groups = get_option(self::OPT_PILLS, self::defaults_pills());

        ob_start(); ?>
        <form class="wi-contact-form" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" method="post" novalidate>
            <input type="hidden" name="action" value="<?php echo esc_attr(self::ACTION); ?>">
            <?php wp_nonce_field(self::NONCE, self::NONCE); ?>

            <div class="wi-grid">
            <?php foreach ($fields as $f): if (empty($f['enabled'])) continue;
                $ph = trim($f['placeholder']) !== '' ? $f['placeholder'] : ($f['label'] . (!empty($f['required']) ? ' *' : '')); ?>
                <div class="wi-field wi-type-<?php echo esc_attr($f['type']); ?>">
                    <label>
                        <span><?php echo esc_html($f['label']); ?><?php echo !empty($f['required']) ? ' *' : ''; ?></span>
                        <?php if ($f['type']==='textarea'): ?>
                            <textarea name="<?php echo esc_attr($f['key']); ?>" placeholder="<?php echo esc_attr($ph); ?>" <?php echo !empty($f['required'])?'required':''; ?>></textarea>
                        <?php else: ?>
                            <input type="<?php echo esc_attr($f['type']); ?>" name="<?php echo esc_attr($f['key']); ?>" placeholder="<?php echo esc_attr($ph); ?>" <?php echo !empty($f['required'])?'required':''; ?>>
                        <?php endif; ?>
                    </label>
                </div>
            <?php endforeach; ?>
            </div>

            <?php // Hidden polja za grupe dugmadi – puni ih JS u iframe-u ?>
            <?php foreach ($groups as $k=>$g): ?>
                <input type="hidden" name="pill_<?php echo esc_attr($k); ?>" value="">
            <?php endforeach; ?>

            <input type="text" name="website" style="position:absolute;left:-9999px;" tabindex="-1" autocomplete="off">
            <button type="submit" class="wi-submit">Senden</button>

            <?php if (!empty($_GET['wi_ok'])): ?>
                <div class="wi-alert ok">Danke! Ihre Nachricht wurde gesendet.</div>
            <?php elseif (!empty($_GET['wi_error'])): ?>
                <div class="wi-alert err"><?php echo esc_html($_GET['wi_error']); ?></div>
            <?php endif; ?>
        </form>
        <?php
        return ob_get_clean();
    }

    /* ===================== SUBMIT ===================== */

    public function handle_submit() {
        if (!isset($_POST[self::NONCE]) || !wp_verify_nonce($_POST[self::NONCE], self::NONCE)) wp_die('Invalid request', 400);
        if (!empty($_POST['website'])) { wp_redirect(wp_get_referer() ?: home_url('/')); exit; }

        $fields = get_option(self::OPT, self::defaults());
        $data   = []; $errors = [];

        foreach ($fields as $f) {
            if (empty($f['enabled'])) continue;
            $key = $f['key'];
            $val = isset($_POST[$key]) ? trim(wp_unslash($_POST[$key])) : '';

            if (!empty($f['required']) && $val==='') $errors[] = sprintf('"%s" ist erforderlich.', $f['label']);
            if ($f['type']==='email' && $val && !is_email($val)) $errors[] = sprintf('E-Mail in "%s" ist ungültig.', $f['label']);

            $data[$f['label']] = $val;
        }

        // Uključi i vrednosti iz grupa dugmadi (pills)
        $gp = get_option(self::OPT_PILLS, self::defaults_pills());
        foreach ($gp as $k=>$g) {
            $val = isset($_POST['pill_'.$k]) ? trim(wp_unslash($_POST['pill_'.$k])) : '';
            if ($val !== '') $data[$g['title']] = $val;
        }

        if ($errors) {
            wp_redirect(add_query_arg(['wi_error'=>urlencode(implode(' ', $errors))], wp_get_referer() ?: home_url('/')));
            exit;
        }

        // Telo poruke
        $lines = [];
        foreach ($data as $label=>$val) $lines[] = $label . ': ' . $val;
        $body = implode("\n", $lines);

        $to       = get_option('admin_email');
        $subject  = 'Neue Kontaktanfrage';
        $headers  = [];

        // Reply-To iz prvog email polja (ako postoji)
        foreach ($fields as $f) {
            if ($f['type']==='email') {
                $reply = isset($_POST[$f['key']]) ? sanitize_email($_POST[$f['key']]) : '';
                if ($reply) $headers[] = 'Reply-To: '.$reply;
                break;
            }
        }

        wp_mail($to, $subject, $body, $headers);

        wp_redirect(add_query_arg(['wi_ok'=>1], wp_get_referer() ?: home_url('/')));
        exit;
    }
}

new WI_Contact();
