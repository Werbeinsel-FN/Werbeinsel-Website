<?php
/**
 * Template Name: Impressum
 * Description: Impressum page – design as sajt/ImpressumPage.tsx
 */
if (!function_exists('wi_nav_url')) {
    require_once get_template_directory() . '/inc/wi-nav-links.php';
}
get_header();

$page_id = get_the_ID();
$data = get_post_meta($page_id, '_wi_impressum_data', true);
if (!is_array($data)) $data = [];
if (function_exists('wi_impressum_default_data')) {
    $defaults = wi_impressum_default_data();
    $data = wp_parse_args($data, $defaults);
    foreach (array_keys($defaults) as $k) {
        if (is_array($defaults[$k]) && isset($data[$k]) && is_array($data[$k])) {
            $data[$k] = wp_parse_args($data[$k], $defaults[$k]);
        }
    }
} else {
    $data = wp_parse_args($data, [
        'ddg' => ['title' => '', 'paras' => []],
        'kontakt' => ['title' => '', 'paras' => []],
        'ustid' => ['title' => '', 'text' => ''],
        'verantwortlich' => ['title' => '', 'paras' => []],
        'disclaimer_title' => '',
        'haftung_inhalte' => ['title' => '', 'text' => ''],
        'haftung_links' => ['title' => '', 'text' => ''],
        'urheberrecht' => ['title' => '', 'text' => ''],
    ]);
}

$home_url = home_url('/');
?>
<link href="https://fonts.googleapis.com/css2?family=Unbounded:wght@400;700;800;900&display=swap" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">

<main id="impressum-main" class="wi-impressum">
  <!-- Breadcrumb -->
  <nav class="wi-impressum__breadcrumb" aria-label="Breadcrumb">
    <div class="wi-impressum__container">
      <ol class="wi-impressum__breadcrumb-list">
        <li><a href="<?php echo esc_url($home_url); ?>">Startseite</a></li>
        <li><span aria-current="page">Impressum</span></li>
      </ol>
    </div>
  </nav>

  <!-- Hero Section (yellow) -->
  <section class="wi-impressum__hero">
    <div class="wi-impressum__container">
      <div class="wi-impressum__hero-inner">
        <h1 class="wi-impressum__title"><?php echo esc_html(get_the_title() ?: 'IMPRESSUM'); ?></h1>
      </div>
    </div>
  </section>

  <!-- Content Section (white) -->
  <section class="wi-impressum__content">
    <div class="wi-impressum__container">
      <div class="wi-impressum__content-inner">

        <?php if (!empty($data['ddg']['title']) || !empty($data['ddg']['paras'])) : ?>
        <?php
        $ddg_title = (string) $data['ddg']['title'];
        $ddg_title = preg_replace('/\s*\(/', "\n(", $ddg_title, 1);
        $ddg_parts = explode("\n", $ddg_title, 2);
        $ddg_first = trim($ddg_parts[0]);
        $ddg_second = isset($ddg_parts[1]) ? trim($ddg_parts[1]) : '';
        ?>
        <div class="wi-impressum__block">
          <h2 class="wi-impressum__h2">
            <span class="wi-impressum__h2-nowrap"><?php echo esc_html($ddg_first); ?></span>
            <?php if ($ddg_second !== '') : ?><span class="wi-impressum__h2-line2"><?php echo esc_html($ddg_second); ?></span><?php endif; ?>
          </h2>
          <div class="wi-impressum__text">
            <?php
            $paras = (array) $data['ddg']['paras'];
            foreach ($paras as $i => $line) :
                $line = trim((string) $line);
                if ($line === '') continue;
                ?><p class="<?php echo $i === 0 ? 'wi-impressum__p-first' : ''; ?>"><?php echo esc_html($line); ?></p><?php
            endforeach;
            ?>
          </div>
        </div>
        <?php endif; ?>

        <?php if (!empty($data['kontakt']['title']) || !empty($data['kontakt']['paras'])) : ?>
        <?php
        $kontakt_title = str_replace(' (', "\n(", (string) $data['kontakt']['title']);
        $kontakt_lines = explode("\n", $kontakt_title, 2);
        $kontakt_lines[0] = ucfirst(strtolower(trim($kontakt_lines[0])));
        $kontakt_title = implode("\n", $kontakt_lines);
        ?>
        <div class="wi-impressum__block">
          <h2 class="wi-impressum__h2"><?php echo nl2br(esc_html($kontakt_title)); ?></h2>
          <div class="wi-impressum__text">
            <?php foreach ((array) $data['kontakt']['paras'] as $line) : $line = trim((string) $line); if ($line === '') continue; ?>
              <p><?php echo esc_html($line); ?></p>
            <?php endforeach; ?>
          </div>
        </div>
        <?php endif; ?>

        <?php if (!empty($data['ustid']['title']) || !empty($data['ustid']['text'])) : ?>
        <?php
        $ustid_title = str_replace(' (', "\n(", (string) $data['ustid']['title']);
        $ustid_lines = explode("\n", $ustid_title, 2);
        $ustid_lines[0] = ucfirst(strtolower(trim($ustid_lines[0])));
        $ustid_lines[0] = str_replace('-id', '-ID', $ustid_lines[0]);
        $ustid_title = implode("\n", $ustid_lines);
        ?>
        <div class="wi-impressum__block">
          <h2 class="wi-impressum__h2"><?php echo nl2br(esc_html($ustid_title)); ?></h2>
          <div class="wi-impressum__text">
            <p><?php echo nl2br(esc_html(trim((string) (isset($data['ustid']['text']) ? $data['ustid']['text'] : '')))); ?></p>
          </div>
        </div>
        <?php endif; ?>

        <?php if (!empty($data['verantwortlich']['title']) || !empty($data['verantwortlich']['paras'])) : ?>
        <div class="wi-impressum__block">
          <h2 class="wi-impressum__h2"><?php echo nl2br(esc_html(str_replace(' (', "\n(", (string) $data['verantwortlich']['title']))); ?></h2>
          <div class="wi-impressum__text">
            <?php foreach ((array) $data['verantwortlich']['paras'] as $line) : $line = trim((string) $line); if ($line === '') continue; ?>
              <p><?php echo esc_html($line); ?></p>
            <?php endforeach; ?>
          </div>
        </div>
        <?php endif; ?>

        <?php if (!empty($data['disclaimer_title']) || !empty($data['haftung_inhalte']['text']) || !empty($data['haftung_links']['text']) || !empty($data['urheberrecht']['text'])) : ?>
        <?php
        $disclaimer_title = preg_replace('/\s*\(/', "\n(", (string) $data['disclaimer_title'], 1);
        $disclaimer_parts = explode("\n", $disclaimer_title, 2);
        $disclaimer_first = trim($disclaimer_parts[0]);
        $disclaimer_second = isset($disclaimer_parts[1]) ? trim($disclaimer_parts[1]) : '';
        ?>
        <div class="wi-impressum__block wi-impressum__block--disclaimer">
          <h2 class="wi-impressum__h2 wi-impressum__h2--large">
            <span class="wi-impressum__h2-nowrap"><?php echo esc_html($disclaimer_first); ?></span>
            <?php if ($disclaimer_second !== '') : ?><span class="wi-impressum__h2-line2"><?php echo esc_html($disclaimer_second); ?></span><?php endif; ?>
          </h2>

          <?php if (!empty($data['haftung_inhalte']['title']) || !empty($data['haftung_inhalte']['text'])) : ?>
          <div class="wi-impressum__subblock">
            <h3 class="wi-impressum__h3"><?php echo esc_html(function_exists('mb_convert_case') ? mb_convert_case((string) $data['haftung_inhalte']['title'], MB_CASE_TITLE, 'UTF-8') : (string) $data['haftung_inhalte']['title']); ?></h3>
            <div class="wi-impressum__text wi-impressum__text--paragraphs">
              <?php echo wp_kses_post(wpautop((string) $data['haftung_inhalte']['text'])); ?>
            </div>
          </div>
          <?php endif; ?>

          <?php if (!empty($data['haftung_links']['title']) || !empty($data['haftung_links']['text'])) : ?>
          <div class="wi-impressum__subblock">
            <h3 class="wi-impressum__h3"><?php echo esc_html(function_exists('mb_convert_case') ? mb_convert_case((string) $data['haftung_links']['title'], MB_CASE_TITLE, 'UTF-8') : (string) $data['haftung_links']['title']); ?></h3>
            <div class="wi-impressum__text wi-impressum__text--paragraphs">
              <?php echo wp_kses_post(wpautop((string) $data['haftung_links']['text'])); ?>
            </div>
          </div>
          <?php endif; ?>

          <?php if (!empty($data['urheberrecht']['title']) || !empty($data['urheberrecht']['text'])) : ?>
          <div class="wi-impressum__subblock">
            <h3 class="wi-impressum__h3"><?php echo esc_html(function_exists('mb_convert_case') ? mb_convert_case((string) $data['urheberrecht']['title'], MB_CASE_TITLE, 'UTF-8') : (string) $data['urheberrecht']['title']); ?></h3>
            <div class="wi-impressum__text wi-impressum__text--paragraphs">
              <?php echo wp_kses_post(wpautop((string) $data['urheberrecht']['text'])); ?>
            </div>
          </div>
          <?php endif; ?>
        </div>
        <?php endif; ?>

      </div>
    </div>
  </section>
</main>

<?php get_footer(); ?>
