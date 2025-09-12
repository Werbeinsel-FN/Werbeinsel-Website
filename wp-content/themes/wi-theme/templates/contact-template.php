<?php
/**
 * Template Name: Kontakt (Next static)
 */
get_header();

// Ako server ne servira index.html automatski, koristi
// $next_url = content_url('uploads/next-contact/kontakt/index.html');
$next_url = content_url('uploads/next-contact/index.html?v=5');
?>
<style>
  /* Precizan full-bleed na #contact-main (bez obzira na padding roditelja) */
  #contact-main {
    --bleed: calc(50vw - 50%);                 /* razlika između viewporta i sadržaja */
    width: calc(100% + 2 * var(--bleed));      /* proširi blok za obe strane */
    margin-left: calc(-1 * var(--bleed));      /* izvuci ulevo */
    margin-right: calc(-1 * var(--bleed));     /* izvuci udesno */
    padding: 0 !important;
    overflow: visible !important;
  }
  /* skini padding/margine sa internog kontejnera */
  .contact-main-container { margin:0 !important; padding:0 !important; }
  /* iframe bez radiusa da ništa ne „viri“ */
  #wi-next-contact { border-radius: 0 !important; display:block; width:100%; border:0; }
  /* za svaki slučaj dozvoli „curenje“ iz roditelja ako tema to guši */
  .site, .site-main, .content-area, .entry-content { overflow: visible !important; }
</style>
<main id="contact-main" class="contact-main">
  <div class="contact-main-container full-bleed">
    <iframe
      id="wi-next-contact"
      src="<?php echo esc_url( content_url('uploads/next-contact/index.html?v=9') ); ?>"
      style="width:100%; border:0; display:block; overflow:hidden; height:1px; min-height:400px; border-radius:0;"
      scrolling="no"
      loading="lazy"
      referrerpolicy="no-referrer"
      allow="clipboard-write"
    ></iframe>
  </div>
</main>

<script>
(function () {
  const iframe = document.getElementById('wi-next-contact');
  let last = 0, raf = 0;

  function setH(h) {
    const clamped = Math.max(400, Math.ceil(h));
    if (Math.abs(clamped - last) > 3) {
      last = clamped;
      iframe.style.height = clamped + 'px';
    }
  }

  // 1) postMessage iz Next-a (IframeAutosize)
  window.addEventListener('message', function (e) {
    const d = e.data;
    if (d && d.type === 'wi-iframe-height' && typeof d.height === 'number') {
      cancelAnimationFrame(raf);
      raf = requestAnimationFrame(() => setH(d.height));
    }
  }, false);

  // 2) Fallback: meri direktno DOM iFrame-a (same-origin)
  function fitFromDOM() {
    try {
      const doc = iframe.contentWindow && iframe.contentWindow.document;
      if (!doc) return;
      const bodyH = doc.body ? doc.body.scrollHeight : 0;
      const htmlH = doc.documentElement ? doc.documentElement.scrollHeight : 0;
      const h = Math.max(bodyH, htmlH);
      if (h) setH(h);
    } catch (err) {
      // drugačiji origin -> ignoriši (postMessage će odraditi posao)
    }
  }

  iframe.addEventListener('load', () => {
    fitFromDOM();
    try {
      const doc = iframe.contentWindow && iframe.contentWindow.document;
      if (doc && 'ResizeObserver' in window) {
        const ro = new ResizeObserver(() => fitFromDOM());
        ro.observe(doc.documentElement);
      }
    } catch {}
  });

  window.addEventListener('resize', () => requestAnimationFrame(fitFromDOM));
  // inicijalni fallback (fontovi/slike kasne)
  setTimeout(fitFromDOM, 300);
})();
</script>

<?php get_footer(); ?>
