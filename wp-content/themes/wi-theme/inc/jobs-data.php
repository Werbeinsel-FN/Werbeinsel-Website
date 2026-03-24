<?php
/**
 * Jobs-Seite: Standardinhalte (Desktop/sajt JobsPage.tsx) + Zusammenführung aus Post-Meta.
 */
if (!defined('ABSPATH')) {
  exit;
}

/**
 * @return array<string, mixed>
 */
function wi_jobs_default_data() {
  return [
    'hero_title'   => "WERDE TEIL\nDES TEAMS",
    'hero_intro'   => 'Kreative Köpfe gesucht! Wir sind eine Werbeagentur mit Leidenschaft für mutige Kampagnen und außergewöhnliches Design.',
    'open_title'   => 'HIER IST PLATZ FÜR DICH',
    'process_title'=> 'IN 4 SCHRITTEN ZUM NEUEN JOB',
    'form_title'   => 'BEWIRB DICH JETZT',
    'form_subtitle'=> 'Fülle das Formular aus – dauert keine 3 Minuten.',
    'close_title'  => 'WORAUF WARTEST DU?',
    'close_text'   => 'Wir freuen uns darauf, dich kennenzulernen! Egal ob du Erfahrung mitbringst oder gerade erst durchstartest – bei uns zählt deine Leidenschaft und dein Wille, etwas zu bewegen.',
    'positions_lines' => "Plakatierer/in\nMarketing Manager/in\nPraktikant/in\nInitiativbewerbung",
    'dates_lines'     => "sofort\nnächsten Monat\nin 3 Monaten\nflexibel",
    'jobs' => [
      [
        'title' => 'Plakatierer/in',
        'tags' => [
          ['text' => 'Vollzeit', 'highlighted' => true],
          ['text' => 'Vor Ort', 'highlighted' => false],
          ['text' => 'Ab sofort', 'highlighted' => false],
        ],
        'description' => 'Du bist gerne draußen unterwegs, packst mit an und sorgst dafür, dass unsere Werbebotschaften sichtbar werden? Perfekt!',
        'tasks' => [
          'Professionelle Plakatierung an Litfaßsäulen, Werbetafeln und City-Light-Postern',
          'Auf- und Abbau von Werbemitteln im Außenbereich',
          'Pflege und Kontrolle der Plakatflächen',
          'Dokumentation der durchgeführten Arbeiten',
        ],
        'requirements' => [
          'Zuverlässigkeit, Pünktlichkeit und Teamfähigkeit',
          'Führerschein Klasse B erforderlich',
          'Körperliche Fitness und handwerkliches Geschick',
          'Bereitschaft zu flexiblen Arbeitszeiten (auch früh morgens oder am Wochenende)',
        ],
      ],
      [
        'title' => 'Marketing Manager/in',
        'tags' => [
          ['text' => 'Vollzeit', 'highlighted' => true],
          ['text' => 'Vor Ort', 'highlighted' => false],
          ['text' => 'Ab sofort', 'highlighted' => false],
        ],
        'description' => 'Du entwickelst gerne Strategien und bringst Kampagnen zum Erfolg? Verstärke unser Team!',
        'tasks' => [
          'Planung und Umsetzung von Marketingkampagnen',
          'Social Media Management und Content-Strategie',
          'Kundenkommunikation und Projektkoordination',
          'Analyse und Optimierung von Kampagnen-Performance',
        ],
        'requirements' => [
          'Studium oder Ausbildung im Bereich Marketing/Kommunikation',
          'Erfahrung im Bereich Online- und Offline-Marketing',
          'Ausgezeichnete Kommunikations- und Organisationsfähigkeiten',
          'Analytisches Denken und Hands-on-Mentalität',
        ],
      ],
      [
        'title' => 'Praktikant/in',
        'tags' => [
          ['text' => 'Praktikum', 'highlighted' => true],
          ['text' => '3–6 Monate', 'highlighted' => false],
          ['text' => 'Vor Ort', 'highlighted' => false],
        ],
        'description' => 'Du möchtest in die Welt der Werbung eintauchen und erste Praxiserfahrung sammeln?',
        'tasks' => [
          'Unterstützung bei der Gestaltung von Werbemitteln',
          'Mitarbeit an kreativen Projekten und Kampagnen',
          'Social Media Content-Erstellung',
          'Einblick in alle Bereiche einer Werbeagentur',
        ],
        'requirements' => [
          'Student/in im Bereich Design, Marketing oder Kommunikation',
          'Erste Erfahrung mit Adobe Creative Suite von Vorteil',
          'Kreativität, Lernbereitschaft und Engagement',
          'Praktikumsdauer: mindestens 3 Monate',
        ],
      ],
    ],
    'steps' => [
      ['num' => '01', 'title' => 'Bewerbung', 'text' => 'Formular ausfüllen & Unterlagen hochladen'],
      ['num' => '02', 'title' => 'Rückmeldung', 'text' => 'Wir melden uns innerhalb von 48 Stunden'],
      ['num' => '03', 'title' => 'Kennenlernen', 'text' => 'Lockeres Gespräch – wir wollen dich kennenlernen'],
      ['num' => '04', 'title' => 'Willkommen!', 'text' => 'Probetag & Start in dein neues Abenteuer'],
    ],
  ];
}

/**
 * @param int $post_id
 * @return array<string, mixed>
 */
function wi_jobs_get_page_data($post_id) {
  $d = wi_jobs_default_data();

  $meta_str = static function ($key, $default) use ($post_id) {
    $v = get_post_meta($post_id, $key, true);
    if ($v === '' || $v === false) {
      return $default;
    }
    return is_string($v) ? $v : $default;
  };

  $hero_title = $meta_str('_wi_jobs_hero_title', $d['hero_title']);
  $hero_intro = $meta_str('_wi_jobs_hero_intro', $d['hero_intro']);
  $open_title = $meta_str('_wi_jobs_open_title', $d['open_title']);
  $process_title = $meta_str('_wi_jobs_process_title', $d['process_title']);
  $form_title    = $meta_str('_wi_jobs_form_title', $d['form_title']);
  $form_subtitle = $meta_str('_wi_jobs_form_subtitle', $d['form_subtitle']);
  $close_title   = $meta_str('_wi_jobs_close_title', $d['close_title']);
  $close_text    = $meta_str('_wi_jobs_close_text', $d['close_text']);
  $positions_lines = $meta_str('_wi_jobs_positions_lines', $d['positions_lines']);
  $dates_lines     = $meta_str('_wi_jobs_dates_lines', $d['dates_lines']);

  $split_lines = static function ($text) {
    $lines = preg_split('/\r\n|\r|\n/', (string) $text);
    $lines = array_map('trim', $lines);
    return array_values(array_filter($lines, static function ($x) {
      return $x !== '';
    }));
  };

  $jobs = [];

  if (metadata_exists('post', $post_id, '_wi_jobs_list')) {
    $raw = get_post_meta($post_id, '_wi_jobs_list', true);
    if (is_string($raw) && $raw !== '') {
      $decoded = json_decode($raw, true);
      if (is_array($decoded)) {
        foreach ($decoded as $item) {
          if (!is_array($item) || empty($item['title'])) {
            continue;
          }
          $tags_out = [];
          if (!empty($item['tags']) && is_array($item['tags'])) {
            foreach ($item['tags'] as $tg) {
              if (!is_array($tg) || empty($tg['text'])) {
                continue;
              }
              $tags_out[] = [
                'text'        => (string) $tg['text'],
                'highlighted' => !empty($tg['highlighted']),
              ];
            }
          }
          $tasks_in     = $item['tasks'] ?? [];
          $req_in       = $item['requirements'] ?? [];
          $tasks_lines  = is_array($tasks_in) ? $tasks_in : $split_lines((string) $tasks_in);
          $req_lines    = is_array($req_in) ? $req_in : $split_lines((string) $req_in);
          $jobs[]       = [
            'title'        => (string) $item['title'],
            'tags'         => $tags_out,
            'description'  => isset($item['description']) ? (string) $item['description'] : '',
            'tasks'        => array_values(array_filter(array_map('trim', $tasks_lines))),
            'requirements' => array_values(array_filter(array_map('trim', $req_lines))),
          ];
        }
      }
    }
    if ($jobs === []) {
      $jobs = $d['jobs'];
    }
  } else {
    foreach ([1, 2, 3] as $ji) {
      $def_job = $d['jobs'][$ji - 1] ?? $d['jobs'][0];
      $title   = $meta_str("_wi_jobs_j{$ji}_title", $def_job['title']);
      $desc    = $meta_str("_wi_jobs_j{$ji}_desc", $def_job['description']);
      $tasks_t = $meta_str("_wi_jobs_j{$ji}_tasks", implode("\n", $def_job['tasks']));
      $req_t   = $meta_str("_wi_jobs_j{$ji}_req", implode("\n", $def_job['requirements']));

      $tags = [];
      for ($ti = 1; $ti <= 3; $ti++) {
        $def_tag = $def_job['tags'][$ti - 1] ?? ['text' => '', 'highlighted' => false];
        $tt      = $meta_str("_wi_jobs_j{$ji}_tag{$ti}", $def_tag['text']);
        $hi      = get_post_meta($post_id, "_wi_jobs_j{$ji}_tag{$ti}_hi", true);
        if ($hi === '' || $hi === false) {
          $hi = !empty($def_tag['highlighted']);
        } else {
          $hi = (string) $hi === '1';
        }
        if ($tt !== '') {
          $tags[] = ['text' => $tt, 'highlighted' => (bool) $hi];
        }
      }
      if ($title !== '') {
        $jobs[] = [
          'title'        => $title,
          'tags'         => $tags,
          'description'  => $desc,
          'tasks'        => $split_lines($tasks_t),
          'requirements' => $split_lines($req_t),
        ];
      }
    }
    if ($jobs === []) {
      $jobs = $d['jobs'];
    }
  }

  $steps = [];
  foreach ([1, 2, 3, 4] as $si) {
    $def_s = $d['steps'][$si - 1];
    $num   = $meta_str("_wi_jobs_s{$si}_num", $def_s['num']);
    $st    = $meta_str("_wi_jobs_s{$si}_title", $def_s['title']);
    $tx    = $meta_str("_wi_jobs_s{$si}_text", $def_s['text']);
    $steps[] = ['num' => $num, 'title' => $st, 'text' => $tx];
  }

  return [
    'hero_title'    => $hero_title,
    'hero_intro'    => $hero_intro,
    'open_title'    => $open_title,
    'process_title' => $process_title,
    'form_title'    => $form_title,
    'form_subtitle' => $form_subtitle,
    'close_title'   => $close_title,
    'close_text'    => $close_text,
    'positions'     => $split_lines($positions_lines),
    'start_dates'   => $split_lines($dates_lines),
    'jobs'          => $jobs,
    'steps'         => $steps,
  ];
}

/**
 * @param int $post_id
 */
function wi_is_jobs_template($post_id) {
  $tpl = (string) get_page_template_slug($post_id);
  return $tpl !== '' && strpos($tpl, 'jobs-template') !== false;
}
