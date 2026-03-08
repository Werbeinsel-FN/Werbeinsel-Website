<?php
/**
 * Helper za navigacijske linkove – pronalazi WP stranice po slug-u
 */

if (!function_exists('wi_nav_url')) {
  function wi_nav_url($key) {
    $home = home_url('/');
    $front_id = (int) get_option('page_on_front');
    $is_home_front = $front_id && is_front_page();

    $map = [
      'home' => $home,
      'services' => null,
      'plakatierung' => null,
      'folierung' => null,
      'digitale-werbemittel' => null,
      'drucksachen' => null,
      'arbeiten' => null,
      'prozess' => null,
      'kontakt' => null,
      'impressum' => null,
      'datenschutz' => null,
      'agb' => null,
    ];

    if (isset($map[$key]) && $map[$key] !== null) {
      return $map[$key];
    }

    switch ($key) {
      case 'services':
        $p = get_page_by_path('services') ?: get_page_by_path('leistungen');
        if ($p) return get_permalink($p);
        return rtrim($home, '/') . '/#services';
      case 'plakatierung':
        return rtrim($home, '/') . '/services/#plakatwerbung';
      case 'folierung':
        return rtrim($home, '/') . '/services/#lass-kleben';
      case 'digitale-werbemittel':
        return rtrim($home, '/') . '/services/#pixel-code';
      case 'drucksachen':
        return rtrim($home, '/') . '/services/#print-design';
      case 'arbeiten':
        return rtrim($home, '/') . '/#arbeiten';
      case 'prozess':
        return rtrim($home, '/') . '/#prozess';
      case 'kontakt':
        $p = get_page_by_path('kontakt') ?: get_page_by_path('contact');
        return $p ? get_permalink($p) : rtrim($home, '/') . '/kontakt/';
      case 'impressum':
        $p = get_page_by_path('impressum');
        return $p ? get_permalink($p) : rtrim($home, '/') . '/impressum/';
      case 'datenschutz':
        $p = get_page_by_path('datenschutz') ?: get_page_by_path('privacy');
        return $p ? get_permalink($p) : rtrim($home, '/') . '/datenschutz/';
      case 'agb':
        $p = get_page_by_path('agbs') ?: get_page_by_path('agb') ?: get_page_by_path('agb-s');
        return $p ? get_permalink($p) : rtrim($home, '/') . '/agbs/';
      default:
        return $home;
    }
  }
}
