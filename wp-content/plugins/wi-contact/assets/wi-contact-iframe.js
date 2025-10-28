/*!
 * WI Contact – Iframe loader + reCAPTCHA v3
 * V1.0
 * - Ubacuje formu u #wi-next-contact iframe
 * - Dinamički učitava reCAPTCHA v3 u IFRAME dokument
 * - Radi i sa Usercentrics (UC_UI_* eventi)
 */

(function () {
  // ======= Podesivo =======
  var IFRAME_ID = "wi-next-contact";

  // 1) SITE KEY – možeš hardkodirati ovde:
  var DEFAULT_SITE_KEY = "6Lf1580rAAAAAB_MSp7fq6UVnrUzHyiMW1BL9pSq";

  // 2) Opcioni CSS fajl za formu (pusti prazno ako ne treba):
  var CSS_URL = "/wp-content/plugins/wi-contact/assets/front.css?v=8";

  // ======= Helperi =======
  function $(root, sel) {
    return (root || document).querySelector(sel);
  }

  function addCssToDoc(doc, href) {
    if (!href) return;
    var l = doc.createElement("link");
    l.rel = "stylesheet";
    l.href = href;
    doc.head.appendChild(l);
  }

  function addScriptToDoc(doc, src, onload) {
    var s = doc.createElement("script");
    s.src = src;
    s.async = true;
    s.defer = true;
    if (onload) s.onload = onload;
    doc.head.appendChild(s);
  }

  function getSiteKeyFromIframe(el) {
    // Možeš proslediti site-key i preko data atributa (preporučeno):
    // <iframe id="wi-next-contact" data-sitekey="..."></iframe>
    var dk = el && el.getAttribute("data-sitekey");
    return dk && dk.trim() ? dk.trim() : DEFAULT_SITE_KEY;
  }

  // ======= Glavno =======
  function init() {
    var iframe = document.getElementById(IFRAME_ID);
    if (!iframe) return;

    // SITE KEY iz data atributa ili default
    var SITE_KEY = getSiteKeyFromIframe(iframe);

    // Kad se IFRAME učita, ubaci formu i učitaj reCAPTCHA
    iframe.addEventListener("load", function () {
      var doc = iframe.contentDocument || iframe.contentWindow.document;

      // 1) Ubaci CSS (opcionalno)
      addCssToDoc(doc, CSS_URL);

      // 2) Ubaci HTML forme (BEZ <script> u stringu!)
      //    – slobodno prilagodi polja ako želiš.
      var formHtml = [
        '<form class="wi-contact-form" action="' +
          window.location.origin +
          '/wp-admin/admin-post.php" method="post">',
        '  <input type="hidden" name="action" value="wi_contact_submit">',
        '  <input type="hidden" name="wi_recaptcha_token" id="wi_recaptcha_token" value="">',

        '  <div class="wi-grid">',
        '    <label for="wi_name"><span class="wi-label">Name</span>',
        '      <input id="wi_name" name="name" type="text" placeholder="Name" required aria-required="true"></label>',

        '    <label for="wi_e-mail"><span class="wi-label">E-Mail</span>',
        '      <input id="wi_e-mail" name="email" type="email" placeholder="E-Mail" required aria-required="true"></label>',

        '    <label for="wi_unternehmen"><span class="wi-label">Unternehmen</span>',
        '      <input id="wi_unternehmen" name="unternehmen" type="text" placeholder="Unternehmen"></label>',

        '    <label for="wi_telefon"><span class="wi-label">Telefon</span>',
        '      <input id="wi_telefon" name="telefon" type="tel" placeholder="Telefon"></label>',
        "  </div>",

        "  <!-- Hidden za pills -->",
        '  <input type="hidden" name="pill_services">',
        '  <input type="hidden" name="pill_budget">',
        '  <input type="hidden" name="pill_zeitrahmen">',

        '  <button type="submit" class="wi-submit">Senden</button>',
        "</form>",
      ].join("");

      doc.open();
      doc.write(
        "<!doctype html><html><head><meta charset='utf-8'></head><body>" +
          formHtml +
          "</body></html>"
      );
      doc.close();

      // 3) Funkcija koja kači submit i poziva grecaptcha.execute iz IFRAME-a
      function hookSubmit() {
        var form = doc.querySelector(".wi-contact-form");
        var tokenInput = doc.getElementById("wi_recaptcha_token");
        if (!form) return;

        form.addEventListener("submit", function (e) {
          e.preventDefault();

          // Ako se token već dobio (retry), samo pošalji
          if (tokenInput && tokenInput.value) {
            form.submit();
            return;
          }

          var w = iframe.contentWindow;
          function fail(msg) {
            if (typeof console !== 'undefined' && console.warn) {
              console.warn(msg || "reCAPTCHA nije dostupna (iframe)");
            }
            // Prikaži grešku korisniku bez alert() - forma ostaje dostupna za retry
            if (form) {
              var errorDiv = doc.createElement('div');
              errorDiv.style.cssText = 'padding:12px;margin:12px 0;background:#fee;color:#c00;border:1px solid #fcc;border-radius:4px;';
              errorDiv.textContent = 'reCAPTCHA je blokirana ili nije učitana. Dozvoli Google skripte i pokušaj ponovo.';
              form.insertBefore(errorDiv, form.firstChild);
              setTimeout(function() {
                if (errorDiv.parentNode) errorDiv.parentNode.removeChild(errorDiv);
              }, 10000);
            }
          }

          if (w && w.grecaptcha && w.grecaptcha.execute) {
            w.grecaptcha.ready(function () {
              w.grecaptcha
                .execute(SITE_KEY, { action: "contact" })
                .then(function (token) {
                  if (tokenInput) tokenInput.value = token;
                  form.submit();
                })
                .catch(function () {
                  fail("reCAPTCHA execute error");
                });
            });
          } else {
            // spor load – pokušaj još jednom kratko kasnije
            setTimeout(function () {
              if (w && w.grecaptcha && w.grecaptcha.execute) {
                hookSubmit(); // ponovo se uveži (sada će proći)
              } else {
                fail("reCAPTCHA nije učitana (iframe)");
              }
            }, 600);
          }
        });
      }

      // 4) Dinamičko učitavanje reCAPTCHA skripte U IFRAME-U
      var recaptchaLoaded = false;
      function loadRecaptchaIntoIframe() {
        if (
          recaptchaLoaded ||
          (iframe.contentWindow && iframe.contentWindow.grecaptcha)
        )
          return;
        recaptchaLoaded = true;
        addScriptToDoc(
          doc,
          "https://www.google.com/recaptcha/api.js?render=" +
            encodeURIComponent(SITE_KEY),
          hookSubmit
        );
      }

      // 5) Usercentrics podrška — učitaj reCAPTCHA tek nakon pristanka
      //    Ako nema UC, ili je već dozvoljeno, poziv ispod će odmah učitati.
      window.addEventListener("UC_UI_INITIALIZED", function () {
        try {
          var UC = window.UC_UI;
          if (UC && UC.getServicesBaseInfo) {
            var services = UC.getServicesBaseInfo();
            var active = services.some(function (s) {
              return /recaptcha/i.test(s.name) && s.isActive;
            });
            if (active) loadRecaptchaIntoIframe();
          } else {
            // Ako UC objekt ne postoji kako treba, pokušaj svakako
            loadRecaptchaIntoIframe();
          }
        } catch (e) {
          loadRecaptchaIntoIframe();
        }
      });
      window.addEventListener("UC_UI_ACCEPT_ALL", loadRecaptchaIntoIframe);
      window.addEventListener("UC_UI_CHANGE", loadRecaptchaIntoIframe);

      // Ako nema CMP-a ili je već dopušteno – probaj odmah
      loadRecaptchaIntoIframe();
    });
  }

  // Start
  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", init);
  } else {
    init();
  }
})();
