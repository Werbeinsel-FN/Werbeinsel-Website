/**
 * Jobs: Akkordeon, Bewerbungs-Formular, reCAPTCHA v3 (action: contact), AJAX an admin-post.php
 * reCAPTCHA: gleicher Site-Key wie Kontakt (wp_enqueue wi-recaptcha-v3), Warte-Logik + CMP-Events.
 */
(function () {
  var cfg = window.WI_JOBS || {};
  var adminPostUrl = cfg.adminPostUrl || '';
  var siteKey = cfg.recaptchaSite || '';

  function qsa(sel, root) {
    return Array.prototype.slice.call((root || document).querySelectorAll(sel));
  }

  function recaptchaScriptPresent() {
    return !!document.querySelector(
      'script[src*="google.com/recaptcha/api.js"], script[src*="recaptcha.net/recaptcha/api.js"]'
    );
  }

  function injectRecaptchaScript() {
    if (!siteKey || recaptchaScriptPresent()) return;
    var s = document.createElement('script');
    s.src = 'https://www.google.com/recaptcha/api.js?render=' + encodeURIComponent(siteKey);
    s.async = true;
    s.defer = true;
    document.head.appendChild(s);
  }

  ['UC_UI_ACCEPT_ALL', 'UC_UI_CHANGE', 'UC_UI_INITIALIZED'].forEach(function (ev) {
    window.addEventListener(ev, injectRecaptchaScript);
  });

  function waitForGrecaptcha(maxWait) {
    var deadline = Date.now() + (maxWait || 10000);
    return new Promise(function (resolve, reject) {
      function tick() {
        if (window.grecaptcha && typeof window.grecaptcha.execute === 'function') {
          resolve(window.grecaptcha);
          return;
        }
        if (Date.now() >= deadline) {
          reject(new Error('recaptcha_timeout'));
          return;
        }
        setTimeout(tick, 80);
      }
      tick();
    });
  }

  function executeRecaptchaToken(gc) {
    return new Promise(function (resolve, reject) {
      gc.ready(function () {
        gc.execute(siteKey, { action: 'contact' }).then(resolve).catch(reject);
      });
    });
  }

  function getRecaptchaTokenForSubmit() {
    if (!siteKey) {
      return Promise.resolve('');
    }
    injectRecaptchaScript();
    return waitForGrecaptcha(10000)
      .catch(function () {
        injectRecaptchaScript();
        return waitForGrecaptcha(4000);
      })
      .then(function (gc) {
        return executeRecaptchaToken(gc);
      });
  }

  /* ----- Akkordeon ----- */
  qsa('[data-wi-job-acc]').forEach(function (acc) {
    var btn = acc.querySelector('.wi-jobs-acc__head');
    var panel = acc.querySelector('.wi-jobs-acc__panel');
    if (!btn || !panel) return;
    btn.addEventListener('click', function () {
      var open = acc.classList.toggle('wi-jobs-acc--open');
      btn.setAttribute('aria-expanded', open ? 'true' : 'false');
      if (open) panel.removeAttribute('hidden');
      else panel.setAttribute('hidden', '');
    });
  });

  qsa('[data-wi-apply]').forEach(function (b) {
    b.addEventListener('click', function (e) {
      e.stopPropagation();
      var title = b.getAttribute('data-wi-apply') || '';
      var form = document.getElementById('wi-jobs-form-el');
      if (!form) return;
      var hid = document.getElementById('wi_jobs_position');
      if (hid) hid.value = title;
      qsa('[data-wi-pills="position"] .wi-jobs-pill').forEach(function (p) {
        p.classList.toggle('wi-jobs-pill--active', p.getAttribute('data-value') === title);
      });
      var err = document.querySelector('[data-err-position]');
      if (err) {
        err.textContent = '';
        err.classList.add('wi-jobs-field-error--hidden');
      }
      var target = document.getElementById('wi-jobs-form');
      if (target) target.scrollIntoView({ behavior: 'smooth', block: 'start' });
    });
  });

  var form = document.getElementById('wi-jobs-form-el');
  if (!form) return;

  var posHid = document.getElementById('wi_jobs_position');
  var startHid = document.getElementById('wi_jobs_start');
  var tokenEl = document.getElementById('wi_jobs_recaptcha_token');
  var fileInput = document.getElementById('wi_jobs_files');
  var fileListEl = document.querySelector('[data-wi-file-list]');
  var dropLabel = document.querySelector('[data-wi-drop]');
  var toast = document.getElementById('wi-jobs-toast');
  var storedFiles = [];

  function wirePills(group) {
    var root = document.querySelector('[data-wi-pills="' + group + '"]');
    if (!root) return;
    root.addEventListener('click', function (ev) {
      var pill = ev.target.closest('.wi-jobs-pill');
      if (!pill || !root.contains(pill)) return;
      var val = pill.getAttribute('data-value') || '';
      qsa('.wi-jobs-pill', root).forEach(function (p) {
        p.classList.remove('wi-jobs-pill--active');
      });
      pill.classList.add('wi-jobs-pill--active');
      if (group === 'position' && posHid) {
        posHid.value = val;
        var er = document.querySelector('[data-err-position]');
        if (er) {
          er.classList.add('wi-jobs-field-error--hidden');
        }
      }
      if (group === 'start' && startHid) {
        startHid.value = val;
        var er2 = document.querySelector('[data-err-start]');
        if (er2) {
          er2.classList.add('wi-jobs-field-error--hidden');
        }
      }
    });
  }
  wirePills('position');
  wirePills('start');

  function humanSize(n) {
    if (!n) return '0 Bytes';
    var k = 1024;
    var sizes = ['Bytes', 'KB', 'MB', 'GB'];
    var i = Math.floor(Math.log(n) / Math.log(k));
    return Math.round((n / Math.pow(k, i)) * 100) / 100 + ' ' + sizes[i];
  }

  function renderFileList() {
    if (!fileListEl) return;
    fileListEl.innerHTML = '';
    if (storedFiles.length === 0) {
      fileListEl.hidden = true;
      return;
    }
    fileListEl.hidden = false;
    storedFiles.forEach(function (file) {
      var row = document.createElement('div');
      row.className = 'wi-jobs-file-row';
      row.innerHTML =
        '<div><strong></strong><br><small></small></div>' +
        '<button type="button" class="wi-jobs-file-row__rm" aria-label="Entfernen">' +
        '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/></svg></button>';
      row.querySelector('strong').textContent = file.name;
      row.querySelector('small').textContent = humanSize(file.size);
      row.querySelector('.wi-jobs-file-row__rm').addEventListener('click', function () {
        storedFiles = storedFiles.filter(function (f) {
          return f !== file;
        });
        renderFileList();
      });
      fileListEl.appendChild(row);
    });
  }

  function addFiles(fileArr) {
    var allowed = { pdf: 1, jpg: 1, jpeg: 1, png: 1, zip: 1 };
    var maxTotal = 10 * 1024 * 1024;
    var total = storedFiles.reduce(function (a, f) {
      return a + f.size;
    }, 0);
    for (var i = 0; i < fileArr.length; i++) {
      var f = fileArr[i];
      var ext = (f.name.split('.').pop() || '').toLowerCase();
      if (!allowed[ext]) continue;
      if (total + f.size > maxTotal) continue;
      storedFiles.push(f);
      total += f.size;
    }
    renderFileList();
  }

  if (fileInput) {
    fileInput.addEventListener('change', function () {
      if (fileInput.files && fileInput.files.length) {
        addFiles(Array.prototype.slice.call(fileInput.files));
      }
      fileInput.value = '';
    });
  }

  if (dropLabel) {
    ['dragenter', 'dragover'].forEach(function (ev) {
      dropLabel.addEventListener(ev, function (e) {
        e.preventDefault();
        e.stopPropagation();
        dropLabel.classList.add('wi-jobs-upload--active');
      });
    });
    ['dragleave', 'drop'].forEach(function (ev) {
      dropLabel.addEventListener(ev, function (e) {
        e.preventDefault();
        e.stopPropagation();
        if (ev !== 'drop') dropLabel.classList.remove('wi-jobs-upload--active');
      });
    });
    dropLabel.addEventListener('drop', function (e) {
      dropLabel.classList.remove('wi-jobs-upload--active');
      var dt = e.dataTransfer;
      if (dt && dt.files && dt.files.length) {
        addFiles(Array.prototype.slice.call(dt.files));
      }
    });
  }

  function showToast(ok, title, sub) {
    if (!toast) {
      if (!ok) window.alert(title + '\n' + sub);
      return;
    }
    toast.classList.remove('wi-jobs-toast--hidden', 'wi-jobs-toast--err');
    if (!ok) toast.classList.add('wi-jobs-toast--err');
    toast.innerHTML =
      '<div class="wi-jobs-toast__title"></div><div class="wi-jobs-toast__sub"></div>';
    toast.querySelector('.wi-jobs-toast__title').textContent = title;
    toast.querySelector('.wi-jobs-toast__sub').textContent = sub;
    setTimeout(function () {
      toast.classList.add('wi-jobs-toast--hidden');
    }, 5000);
  }

  function fieldError(inp, msg) {
    var name = inp.getAttribute('name');
    var el = form.querySelector('.wi-jobs-field-error[data-for="' + name + '"]');
    if (!el) return;
    if (msg) {
      inp.classList.add('wi-invalid');
      el.textContent = msg;
      el.classList.remove('wi-jobs-field-error--hidden');
    } else {
      inp.classList.remove('wi-invalid');
      el.textContent = '';
      el.classList.add('wi-jobs-field-error--hidden');
    }
  }

  function errVisible(sel) {
    var node = document.querySelector(sel);
    return node && !node.classList.contains('wi-jobs-field-error--hidden');
  }

  function scrollToFirstJobsError() {
    var firstInp = form.querySelector('input.wi-invalid, textarea.wi-invalid');
    if (firstInp) {
      firstInp.scrollIntoView({ behavior: 'smooth', block: 'center' });
      setTimeout(function () {
        try {
          firstInp.focus({ preventScroll: true });
        } catch (_) {}
      }, 350);
      return;
    }
    if (errVisible('[data-err-position]')) {
      var pos = document.querySelector('[data-wi-pills="position"]');
      (pos || document.querySelector('[data-err-position]')).scrollIntoView({
        behavior: 'smooth',
        block: 'center',
      });
      return;
    }
    if (errVisible('[data-err-start]')) {
      var st = document.querySelector('[data-wi-pills="start"]');
      (st || document.querySelector('[data-err-start]')).scrollIntoView({
        behavior: 'smooth',
        block: 'center',
      });
      return;
    }
    if (errVisible('[data-err-privacy]')) {
      var pr =
        document.querySelector('.wi-jobs-privacy') || document.getElementById('wi_jobs_privacy');
      (pr || document.querySelector('[data-err-privacy]')).scrollIntoView({
        behavior: 'smooth',
        block: 'center',
      });
      return;
    }
    var sec = document.getElementById('wi-jobs-form');
    if (sec) sec.scrollIntoView({ behavior: 'smooth', block: 'start' });
  }

  function serverMessage(text) {
    var t = (text || '').trim();
    if (t === 'RECAPTCHA_MISSING' || t === 'RECAPTCHA_ERROR') {
      return {
        title: 'Sicherheitsprüfung',
        sub: 'reCAPTCHA war nicht bereit. Bitte Cookies / Skripte für Google erlauben und erneut senden.',
      };
    }
    if (t === 'RECAPTCHA_FAILED') {
      return {
        title: 'Sicherheitsprüfung',
        sub: 'reCAPTCHA konnte nicht bestätigt werden. Bitte Seite neu laden und erneut versuchen.',
      };
    }
    if (t === 'VALIDATION_ERROR') {
      return {
        title: 'Angaben prüfen',
        sub: 'Bitte alle Pflichtfelder ausfüllen und das Formular erneut senden.',
      };
    }
    if (t === 'PRIVACY') {
      return {
        title: 'Datenschutz',
        sub: 'Bitte die Datenschutzerklärung akzeptieren.',
      };
    }
    if (t === 'UPLOAD_ERROR' || t === 'BAD_TYPE' || t === 'TOO_LARGE') {
      return {
        title: 'Anhänge',
        sub: 'Datei-Upload fehlgeschlagen (Typ oder Größe). Max. 10 MB, PDF/JPG/PNG/ZIP.',
      };
    }
    if (t.indexOf('MAIL_ERROR') === 0) {
      return { title: 'UPS!', sub: 'E-Mail konnte nicht gesendet werden. Bitte später erneut versuchen.' };
    }
    return { title: 'UPS!', sub: 'Fehler beim Senden. Bitte versuchen Sie es erneut.' };
  }

  ['input', 'change'].forEach(function (ev) {
    form.addEventListener(
      ev,
      function (e) {
        var t = e.target;
        if (!t || !t.name) return;
        if (t.name === 'jobs_name' || t.name === 'jobs_email' || t.name === 'jobs_phone') {
          fieldError(t, '');
        }
        if (t.id === 'wi_jobs_privacy' && t.checked) {
          var erPr = document.querySelector('[data-err-privacy]');
          if (erPr) erPr.classList.add('wi-jobs-field-error--hidden');
        }
      },
      true
    );
  });

  form.addEventListener('submit', function (e) {
    e.preventDefault();

    var name = form.querySelector('[name="jobs_name"]');
    var email = form.querySelector('[name="jobs_email"]');
    var phone = form.querySelector('[name="jobs_phone"]');
    var privacy = document.getElementById('wi_jobs_privacy');
    var ok = true;

    [name, email, phone].forEach(function (inp) {
      if (!inp) return;
      var v = (inp.value || '').trim();
      var msg = '';
      if (!v) msg = inp.getAttribute('data-required-msg') || 'Pflichtfeld';
      else if (inp.type === 'email' && !inp.checkValidity()) msg = 'Ungültige E-Mail-Adresse';
      fieldError(inp, msg);
      if (msg) ok = false;
    });

    if (posHid && !(posHid.value || '').trim()) {
      var erp = document.querySelector('[data-err-position]');
      if (erp) {
        erp.textContent = 'Bitte eine Position wählen';
        erp.classList.remove('wi-jobs-field-error--hidden');
      }
      ok = false;
    } else {
      var erp2 = document.querySelector('[data-err-position]');
      if (erp2) erp2.classList.add('wi-jobs-field-error--hidden');
    }

    if (startHid && !(startHid.value || '').trim()) {
      var ers = document.querySelector('[data-err-start]');
      if (ers) {
        ers.textContent = 'Bitte Verfügbarkeit wählen';
        ers.classList.remove('wi-jobs-field-error--hidden');
      }
      ok = false;
    } else {
      var ers2 = document.querySelector('[data-err-start]');
      if (ers2) ers2.classList.add('wi-jobs-field-error--hidden');
    }

    var erPr = document.querySelector('[data-err-privacy]');
    if (!privacy || !privacy.checked) {
      if (erPr) erPr.classList.remove('wi-jobs-field-error--hidden');
      ok = false;
    } else if (erPr) {
      erPr.classList.add('wi-jobs-field-error--hidden');
    }

    if (!ok) {
      scrollToFirstJobsError();
      return;
    }

    var btn = form.querySelector('.wi-jobs-submit');
    function setBusy(b) {
      if (btn) btn.disabled = b;
    }
    setBusy(true);

    getRecaptchaTokenForSubmit()
      .then(function (token) {
        if (tokenEl) tokenEl.value = token || '';
        var fd = new FormData();
        fd.set('action', 'wi_contact_submit');
        fd.set('wi_submission_type', 'jobs');
        fd.set('wi_recaptcha_token', token || '');
        fd.set('jobs_name', name.value.trim());
        fd.set('jobs_email', email.value.trim());
        fd.set('jobs_phone', phone.value.trim());
        fd.set('jobs_position', (posHid && posHid.value) || '');
        fd.set('jobs_start', (startHid && startHid.value) || '');
        var port = form.querySelector('[name="jobs_portfolio"]');
        var msg = form.querySelector('[name="jobs_message"]');
        if (port) fd.set('jobs_portfolio', port.value.trim());
        if (msg) fd.set('jobs_message', msg.value.trim());
        if (privacy && privacy.checked) fd.set('wi_privacy', '1');
        storedFiles.forEach(function (f) {
          fd.append('jobs_files[]', f, f.name);
        });

        return fetch(adminPostUrl, { method: 'POST', body: fd, credentials: 'same-origin' }).then(
          function (r) {
            return r.text().then(function (t) {
              return { ok: r.ok, text: t };
            });
          }
        );
      })
      .then(function (res) {
        setBusy(false);
        var t = (res.text || '').trim();
        if (res.ok && (t === 'OK' || t === '')) {
          showToast(true, 'DANKE!', 'Ihre Bewerbung wurde gesendet. Wir melden uns in Kürze.');
          form.reset();
          storedFiles = [];
          renderFileList();
          qsa('.wi-jobs-pill--active').forEach(function (p) {
            p.classList.remove('wi-jobs-pill--active');
          });
          if (posHid) posHid.value = '';
          if (startHid) startHid.value = '';
          qsa('input.wi-invalid, textarea.wi-invalid', form).forEach(function (el) {
            el.classList.remove('wi-invalid');
          });
          return;
        }
        var m = serverMessage(t);
        showToast(false, m.title, m.sub);
        if (t === 'VALIDATION_ERROR' || t === 'PRIVACY') {
          scrollToFirstJobsError();
        }
        if (
          t === 'RECAPTCHA_MISSING' ||
          t === 'RECAPTCHA_ERROR' ||
          t === 'RECAPTCHA_FAILED'
        ) {
          var sec = document.getElementById('wi-jobs-form');
          if (sec) sec.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
      })
      .catch(function () {
        setBusy(false);
        showToast(
          false,
          'Sicherheitsprüfung',
          'reCAPTCHA konnte nicht geladen werden. Bitte Google-Skripte erlauben und erneut versuchen.'
        );
        injectRecaptchaScript();
        var sec = document.getElementById('wi-jobs-form');
        if (sec) sec.scrollIntoView({ behavior: 'smooth', block: 'start' });
      });
  });

  if (siteKey) {
    injectRecaptchaScript();
  }
})();
