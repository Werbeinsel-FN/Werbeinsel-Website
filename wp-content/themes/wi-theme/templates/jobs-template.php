<?php
/**
 * Template Name: Jobs
 */
if (!defined('ABSPATH')) {
  exit;
}
require_once get_template_directory() . '/inc/jobs-data.php';

get_header();

while (have_posts()) {
  the_post();
  $data = wi_jobs_get_page_data(get_the_ID());
  $rec_site = get_option('wi_contact_recaptcha_site', '');
  ?>

<main class="wi-jobs">
  <section class="wi-jobs__hero">
    <div class="wi-jobs__inner">
      <div class="wi-jobs__hero-text">
        <h1 class="wi-jobs__h1"><?php echo nl2br(esc_html($data['hero_title'])); ?></h1>
        <p class="wi-jobs__lead"><?php echo esc_html($data['hero_intro']); ?></p>
      </div>
    </div>
  </section>

  <section class="wi-jobs__open">
    <div class="wi-jobs__inner">
      <h2 class="wi-jobs__h2 wi-jobs__h2--dark"><?php echo esc_html($data['open_title']); ?></h2>
      <div class="wi-jobs__accordion">
        <?php foreach ($data['jobs'] as $j_idx => $job) : ?>
        <div class="wi-jobs-acc" data-wi-job-acc>
          <button type="button" class="wi-jobs-acc__head" aria-expanded="false" aria-controls="wi-job-panel-<?php echo (int) $j_idx; ?>" id="wi-job-head-<?php echo (int) $j_idx; ?>">
            <span class="wi-jobs-acc__head-main">
              <span class="wi-jobs-acc__title"><?php echo esc_html($job['title']); ?></span>
              <span class="wi-jobs-acc__tags">
                <?php foreach ($job['tags'] as $tag) : ?>
                  <span class="wi-jobs-acc__tag <?php echo !empty($tag['highlighted']) ? 'wi-jobs-acc__tag--hi' : ''; ?>"><?php echo esc_html($tag['text']); ?></span>
                <?php endforeach; ?>
              </span>
            </span>
            <span class="wi-jobs-acc__chev" aria-hidden="true">
              <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="m6 9 6 6 6-6"/></svg>
            </span>
          </button>
          <div class="wi-jobs-acc__panel" id="wi-job-panel-<?php echo (int) $j_idx; ?>" role="region" hidden>
            <div class="wi-jobs-acc__body">
              <p class="wi-jobs-acc__desc"><?php echo esc_html($job['description']); ?></p>
              <div class="wi-jobs-acc__grid">
                <div>
                  <h4 class="wi-jobs-acc__h4">Deine Aufgaben</h4>
                  <ul class="wi-jobs-acc__list">
                    <?php foreach ($job['tasks'] as $task) : ?>
                      <li><span class="wi-jobs-acc__bullet" aria-hidden="true">•</span><span><?php echo esc_html($task); ?></span></li>
                    <?php endforeach; ?>
                  </ul>
                </div>
                <div>
                  <h4 class="wi-jobs-acc__h4">Das bringst du mit</h4>
                  <ul class="wi-jobs-acc__list">
                    <?php foreach ($job['requirements'] as $req) : ?>
                      <li><span class="wi-jobs-acc__bullet" aria-hidden="true">•</span><span><?php echo esc_html($req); ?></span></li>
                    <?php endforeach; ?>
                  </ul>
                </div>
              </div>
              <div class="wi-jobs-acc__cta">
                <button type="button" class="wi-jobs-acc__apply" data-wi-apply="<?php echo esc_attr($job['title']); ?>">Jetzt bewerben</button>
              </div>
            </div>
          </div>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
  </section>

  <section class="wi-jobs__process">
    <div class="wi-jobs__inner">
      <h2 class="wi-jobs__h2 wi-jobs__h2--process"><?php echo esc_html($data['process_title']); ?></h2>
      <div class="wi-jobs__steps">
        <?php foreach ($data['steps'] as $step) : ?>
          <div class="wi-jobs-step">
            <div class="wi-jobs-step__num"><?php echo esc_html($step['num']); ?></div>
            <h3 class="wi-jobs-step__title"><?php echo esc_html($step['title']); ?></h3>
            <p class="wi-jobs-step__text"><?php echo esc_html($step['text']); ?></p>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  </section>

  <section class="wi-jobs__form-section" id="wi-jobs-form">
    <div class="wi-jobs__inner">
      <form class="wi-jobs-form" id="wi-jobs-form-el" method="post" enctype="multipart/form-data" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" novalidate>
        <input type="hidden" name="action" value="wi_contact_submit">
        <input type="hidden" name="wi_submission_type" value="jobs">
        <?php if (class_exists('WI_Contact')) { WI_Contact::spam_fields(); } ?>
        <input type="hidden" name="wi_recaptcha_token" id="wi_jobs_recaptcha_token" value="">
        <input type="hidden" name="jobs_position" id="wi_jobs_position" value="">
        <input type="hidden" name="jobs_start" id="wi_jobs_start" value="">

        <div class="wi-jobs-form__intro">
          <h2 class="wi-jobs__h2 wi-jobs__h2--dark"><?php echo esc_html($data['form_title']); ?></h2>
          <p class="wi-jobs-form__sub"><?php echo esc_html($data['form_subtitle']); ?></p>
        </div>

        <div class="wi-jobs-form__row2">
          <div class="wi-jobs-form__cell">
            <label class="wi-sr-only" for="wi_jobs_name">Name</label>
            <input id="wi_jobs_name" class="wi-jobs-input" type="text" name="jobs_name" placeholder="Name *" required autocomplete="name" data-required-msg="Name ist erforderlich">
            <span class="wi-jobs-field-error wi-jobs-field-error--hidden" data-for="jobs_name" role="alert"></span>
          </div>
          <div class="wi-jobs-form__cell">
            <label class="wi-sr-only" for="wi_jobs_email">E-Mail</label>
            <input id="wi_jobs_email" class="wi-jobs-input" type="email" name="jobs_email" placeholder="E-Mail *" required autocomplete="email" data-required-msg="E-Mail ist erforderlich">
            <span class="wi-jobs-field-error wi-jobs-field-error--hidden" data-for="jobs_email" role="alert"></span>
          </div>
        </div>
        <div class="wi-jobs-form__full">
          <label class="wi-sr-only" for="wi_jobs_phone">Telefon</label>
          <input id="wi_jobs_phone" class="wi-jobs-input" type="tel" name="jobs_phone" placeholder="Telefon *" required autocomplete="tel" data-required-msg="Telefonnummer ist erforderlich">
          <span class="wi-jobs-field-error wi-jobs-field-error--hidden" data-for="jobs_phone" role="alert"></span>
        </div>

        <h3 class="wi-jobs-form__h3">Gewünschte Position</h3>
        <div class="wi-jobs-pills" data-wi-pills="position">
          <?php
          $pos_count = count($data['positions']);
          foreach ($data['positions'] as $pi => $pos) :
            $dash = ($pi === $pos_count - 1) ? ' wi-jobs-pill--dash' : '';
            ?>
            <button type="button" class="wi-jobs-pill<?php echo esc_attr($dash); ?>" data-value="<?php echo esc_attr($pos); ?>"><?php echo esc_html($pos); ?></button>
          <?php endforeach; ?>
        </div>
        <p class="wi-jobs-field-error wi-jobs-field-error--center wi-jobs-field-error--hidden" data-err-position role="alert"></p>

        <h3 class="wi-jobs-form__h3">Verfügbar ab</h3>
        <div class="wi-jobs-pills" data-wi-pills="start">
          <?php foreach ($data['start_dates'] as $start_line) : ?>
            <button type="button" class="wi-jobs-pill" data-value="<?php echo esc_attr($start_line); ?>"><?php echo esc_html($start_line); ?></button>
          <?php endforeach; ?>
        </div>
        <p class="wi-jobs-field-error wi-jobs-field-error--center wi-jobs-field-error--hidden" data-err-start role="alert"></p>

        <div class="wi-jobs-upload-wrap">
          <input type="file" name="jobs_files[]" id="wi_jobs_files" class="wi-jobs-upload-input" multiple accept=".pdf,.jpg,.jpeg,.png,.zip,application/pdf,image/jpeg,image/png,application/zip">
          <label for="wi_jobs_files" class="wi-jobs-upload" data-wi-drop>
            <span class="wi-jobs-upload__icon" aria-hidden="true">
              <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 15V3"/><path d="m17 8-5-5-5 5"/><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/></svg>
            </span>
            <span class="wi-jobs-upload__t1">Lebenslauf & Arbeitsproben hier ablegen</span>
            <span class="wi-jobs-upload__t2">oder klicken zum Auswählen – PDF, JPG, PNG, ZIP (max. 10 MB)</span>
          </label>
        </div>
        <div class="wi-jobs-files" data-wi-file-list hidden></div>

        <label class="wi-sr-only" for="wi_jobs_portfolio">Portfolio</label>
        <input id="wi_jobs_portfolio" class="wi-jobs-input" type="url" name="jobs_portfolio" placeholder="Portfolio / Behance / Dribbble / LinkedIn (optional)" autocomplete="url">

        <label class="wi-sr-only" for="wi_jobs_message">Nachricht</label>
        <textarea id="wi_jobs_message" class="wi-jobs-textarea" name="jobs_message" rows="8" placeholder="Möchtest du uns noch etwas mitteilen? (optional)"></textarea>

        <div class="wi-jobs-privacy">
          <input type="checkbox" name="wi_privacy" id="wi_jobs_privacy" value="1" required>
          <label for="wi_jobs_privacy">Ich habe die Datenschutzerklärung gelesen und akzeptiere sie. Ich bin damit einverstanden, dass meine Daten zur Bearbeitung meiner Anfrage verwendet werden.</label>
        </div>
        <p class="wi-jobs-field-error wi-jobs-field-error--center wi-jobs-field-error--hidden" data-err-privacy role="alert">Bitte akzeptieren Sie die Datenschutzerklärung</p>

        <div class="wi-jobs-form__submit">
          <button type="submit" class="wi-jobs-submit">BEWERBUNG ABSENDEN</button>
        </div>
      </form>
    </div>
  </section>

  <section class="wi-jobs__close">
    <div class="wi-jobs__inner wi-jobs__close-inner">
      <h2 class="wi-jobs__h2 wi-jobs__h2--close"><?php echo esc_html($data['close_title']); ?></h2>
      <p class="wi-jobs__close-text"><?php echo esc_html($data['close_text']); ?></p>
    </div>
  </section>
</main>

<div id="wi-jobs-toast" class="wi-jobs-toast wi-jobs-toast--hidden" role="status" aria-live="polite"></div>

<script>
  window.WI_JOBS = {
    adminPostUrl: <?php echo wp_json_encode(admin_url('admin-post.php')); ?>,
    recaptchaSite: <?php echo wp_json_encode($rec_site); ?>
  };
</script>
  <?php
}

get_footer();
