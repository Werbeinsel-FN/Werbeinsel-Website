<?php
/**
 * Footer template
 */
?>
<script>
document.addEventListener("DOMContentLoaded", function () {
  const rocketBtn = document.querySelector(".rocket-btn");
  const rocketIcon = rocketBtn ? rocketBtn.querySelector(".rocket") : null;

  if (rocketBtn && rocketIcon) {
    rocketBtn.addEventListener("click", function (e) {
      e.preventDefault();

      // animacija rotacije
      rocketIcon.classList.add("launched");
      setTimeout(() => rocketIcon.classList.remove("launched"), 400); // vrati nazad posle 0.4s

      // glatko skrolovanje na vrh
      window.scrollTo({
        top: 0,
        behavior: "smooth"
      });
    });
  }
});
</script>

<?php
// PREUZMI reCAPTCHA SITE KEY iz plugin opcije (ako je postavljen)
$wi_recaptcha_site = get_option('wi_contact_recaptcha_site', '');
?>

<?php if ( !empty($wi_recaptcha_site) ) : ?>
  <!-- reCAPTCHA v3 loader (dinamički, ako plugin već nije ubacio) -->
  <script>
  (function(){
    // čuvamo site key u globalu da JS ispod može da ga koristi
    window.RECAPTCHA_SITE_KEY = <?php echo json_encode($wi_recaptcha_site); ?>;

    function ensureHiddenInput(form){
      var inp = form.querySelector('#wi_recaptcha_token');
      if (!inp) {
        inp = document.createElement('input');
        inp.type = 'hidden';
        inp.name = 'wi_recaptcha_token'; // OVO ime backend očekuje
        inp.id   = 'wi_recaptcha_token';
        form.appendChild(inp);
      }
      return inp;
    }

    function getToken(action){
      return new Promise(function(resolve, reject){
        if (typeof grecaptcha === 'undefined') return reject(new Error('grecaptcha_not_loaded'));
        grecaptcha.ready(function(){
          grecaptcha.execute(window.RECAPTCHA_SITE_KEY, {action: action || 'contact'})
            .then(resolve).catch(reject);
        });
      });
    }

    function attach(form){
      if (form.__wiCaptchaBound) return; // izbegni dupli bind
      form.__wiCaptchaBound = true;

      form.addEventListener('submit', function(ev){
        // uvek stopiraj default; tek posle tokena šalji
        ev.preventDefault();

        var submitBtn = form.querySelector('[type="submit"]');
        if (submitBtn) {
          submitBtn.disabled = true;
          submitBtn.dataset.prevText = submitBtn.textContent;
          submitBtn.textContent = 'Verifikuje…';
        }

        ensureHiddenInput(form);

        getToken('contact').then(function(token){
          form.querySelector('#wi_recaptcha_token').value = token;
          form.submit(); // sad zaista šaljemo (handler se ne aktivira ponovo)
        }).catch(function(err){
          console.error('reCAPTCHA error:', err);
          alert('Greška pri verifikaciji. Pokušaj ponovo.');
          if (submitBtn) {
            submitBtn.disabled = false;
            submitBtn.textContent = submitBtn.dataset.prevText || 'Senden';
          }
        });
      }, {capture:true}); // capture da ništa ne pretekne
    }

    function init(){
      // ciljamo WI Contact formu koja šalje na admin-post.php
      var forms = document.querySelectorAll('form.wi-contact-form[action*="admin-post.php"]');
      forms.forEach(attach);
    }

    // 1) odmah veži handler da submit ne pobegne
    document.addEventListener('DOMContentLoaded', init);

    // 2) ako reCAPTCHA skripta nije već tu, ubaci je
    if (typeof grecaptcha === 'undefined') {
      var s = document.createElement('script');
      s.src = 'https://www.google.com/recaptcha/api.js?render=' + encodeURIComponent(window.RECAPTCHA_SITE_KEY);
      s.async = true; s.defer = true;
      s.onload = init;
      document.head.appendChild(s);
    }
  })();
  </script>
<?php endif; ?>

<footer class="site-footer">
  <div class="content-container">
    <div class="footer-bar">
      <!-- Levo: meni -->
      <nav class="footer-left" aria-label="<?php esc_attr_e('Footer', 'wi-theme'); ?>">
        <?php
        if (has_nav_menu('footer')) {
          wp_nav_menu([
            'theme_location' => 'footer',
            'container'      => false,
            'menu_class'     => 'footer-menu',
            'depth'          => 1,
          ]);
        } else {
          echo '<ul class="footer-menu">'
             . '<li><a href="#">Impressum</a></li>'
             . '<li><a href="#">AGBs</a></li>'
             . '<li><a href="#">Datenschutz</a></li>'
             . '<li><a href="#">Jobs</a></li>'
             . '</ul>';
        }
        ?>
      </nav>

      <!-- Desno: hashtag + ikonice -->
      <div class="footer-right">
        <a href="#" class="footer-hashtag">#agency</a>

        <div class="footer-social">
          <a class="social-btn" href="#" aria-label="Instagram">
            <!-- instagram -->
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon">
              <rect width="20" height="20" x="2" y="2" rx="5" ry="5"></rect>
              <path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"></path>
              <line x1="17.5" x2="17.51" y1="6.5" y2="6.5"></line>
            </svg>
          </a>
          <a class="social-btn" href="#" aria-label="Facebook">
            <!-- facebook -->
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon">
              <path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"></path>
            </svg>
          </a>
          <a class="social-btn" href="#" aria-label="Chat">
            <svg class="icon" viewBox="0 0 24 24" fill="currentColor" width="20" height="20"><path d="M19.59 6.69a4.83 4.83 0 01-3.77-4.25 4.83 4.83 0 01-8.65 0 4.83 4.83 0 01-3.77 4.25 4.83 4.83 0 000 8.65 4.83 4.83 0 013.77 4.25 4.83 4.83 0 018.65 0 4.83 4.83 0 013.77-4.25 4.83 4.83 0 000-8.65z"/></svg>
          </a>
          <a class="social-btn rocket-btn" href="#" aria-label="Kontakt">
            <!-- raketa -->
            <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="rocket icon">
              <path d="M4.5 16.5c-1.5 1.26-2 5-2 5s3.74-.5 5-2c.71-.84.7-2.13-.09-2.91a2.18 2.18 0 0 0-2.91-.09z"></path>
              <path d="m12 15-3-3a22 22 0 0 1 2-3.95A12.88 12.88 0 0 1 22 2c0 2.72-.78 7.5-6 11a22.35 22.35 0 0 1-4 2z"></path>
              <path d="M9 12H4s.55-3.03 2-4c1.62-1.08 5 0 5 0"></path>
              <path d="M12 15v5s3.03-.55 4-2c1.08-1.62 0-5 0-5"></path>
            </svg>
          </a>
        </div>
      </div>
    </div>
  </div>

  <?php wp_footer(); ?>
</footer>
