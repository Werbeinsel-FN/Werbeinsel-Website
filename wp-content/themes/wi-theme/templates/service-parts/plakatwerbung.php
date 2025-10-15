<?php
/**
 * Partial: Plakatwerbung – hero carousel sa više slika i zasebnim CSS-om
 * Var: $slug, $svc, $page_title, $page_id
 */

// 1) Učitaj SAMO ovde CSS
$css_path = get_stylesheet_directory() . '/css/service-plakatwerbung.css';
$css_url  = get_stylesheet_directory_uri() . '/css/service-plakatwerbung.css';
if (file_exists($css_path)) { $css_url .= '?v=' . filemtime($css_path); }
echo '<link rel="stylesheet" href="'.esc_url($css_url).'">';

// 2) Slike za slider: prvo iz plugina/featured (ako postoji), pa Unsplash
$slides = [];
if (!empty($svc['image_url'])) $slides[] = $svc['image_url']; // ostavi grid/featured ako postoji

// stabilne landscape slike sa Picsum (uvek rade, fiksni ID)
$slides = [
  'https://picsum.photos/id/1011/1600/900',
  'https://picsum.photos/id/1015/1600/900',
  'https://picsum.photos/id/1020/1600/900',
  'https://picsum.photos/id/1035/1600/900',
  'https://picsum.photos/id/1043/1600/900',
  'https://picsum.photos/id/1059/1600/900',
  'https://picsum.photos/id/1067/1600/900',
];

// ako ima grid/featured sliku – dodaj je NA KRAJ
if (!empty($svc['image_url'])) {
  $slides[] = $svc['image_url'];
}

$slides = array_values(array_unique(array_filter($slides)));

$carousel_id = 'plakatwerbung-hero'; // jedinstveni ID
?>

<section id="<?php echo esc_attr($carousel_id); ?>" class="svc-hero-carousel">
  <div class="svc-hero-carousel__wrap">
    <div class="svc-hero-carousel__viewport">

      <?php foreach ($slides as $i => $url): ?>
        <div
          class="svc-hero-carousel__bg<?php echo $i === 0 ? ' is-active' : ''; ?>"
          style="background-image:url('<?php echo esc_url($url); ?>');"
          role="img"
          aria-label="Plakatwerbung Slide <?php echo (int)$i+1; ?>">
        </div>
      <?php endforeach; ?>

      <button class="svc-hero-carousel__nav svc-hero-carousel__nav--prev" aria-label="Vorheriges Bild">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="svc-hero-carousel__icon" aria-hidden="true">
          <path d="m15 18-6-6 6-6" fill="none" stroke="currentColor" stroke-width="4" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
      </button>

      <button class="svc-hero-carousel__nav svc-hero-carousel__nav--next" aria-label="Nächstes Bild">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="svc-hero-carousel__icon" aria-hidden="true">
          <path d="m9 18 6-6-6-6" fill="none" stroke="currentColor" stroke-width="4" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
      </button>

      <div class="svc-hero-carousel__dots" role="tablist" aria-label="Slider Pagination">
        <?php foreach ($slides as $i => $_): ?>
          <button class="svc-hero-carousel__dot<?php echo $i === 0 ? ' is-active' : ''; ?>" data-index="<?php echo (int)$i; ?>" aria-label="Gehe zu Slide <?php echo (int)$i+1; ?>" role="tab"></button>
        <?php endforeach; ?>
      </div>
    </div>
  </div>
</section>
<section class="svc-intro">
  <div class="content-container svc-intro__wrap">
    <h2 class="svc-intro__title">
      <span>PLAKAT</span><br><span>WERBUNG</span>
    </h2>
    <p class="svc-intro__lead">
      Plakatwerbung ist eine der effektivsten Formen der Außenwerbung. Mit strategisch platzierten Plakaten
      erreichen Sie täglich tausende von potenziellen Kunden genau dort, wo sie leben, arbeiten und einkaufen.
      WERBEINSEL sorgt dafür, dass Ihre Botschaft im Straßenbild unübersehbar wird.
    </p>
  </div>
</section>
<section class="svc-steps">
  <div class="content-container">
    <h2 class="svc-steps__title">ÜBERLEGE<br>NOCH</h2>
  </div>
</section>
<?php
  // promeni na pravu mapu kad budeš imao asset u Media Library
  $region_img_url = 'https://picsum.photos/id/1016/1600/1200';
?>
<section class="svc-region">
  <div class="content-container svc-region__wrap">
    <h2 class="svc-region__title">REGION</h2>

    <div class="svc-region__grid">
      <div class="svc-region__col">
        <div class="svc-region__text">
          <p class="svc-region__p">
            Wir plakatieren in der gesamten Region Friedrichshafen und Umgebung. Unsere strategisch ausgewählten
            Standorte garantieren maximale Sichtbarkeit für Ihre Kampagne.
          </p>
          <p class="svc-region__p">
            Mit über 50 Premium-Standorten erreichen Sie täglich tausende von potenziellen Kunden an
            hochfrequentierten Verkehrsknotenpunkten, Einkaufszentren und zentralen Stadtbereichen.
          </p>
          <p class="svc-region__p">
            Mit über 50 Premium-Standorten erreichen Sie täglich tausende von potenziellen Kunden an
            hochfrequentierten Verkehrsknotenpunkten, Einkaufszentren und zentralen Stadtbereichen.
          </p>
        </div>
      </div>

      <div class="svc-region__imgwrap">
        <img
          class="svc-region__img"
          src="<?php echo esc_url($region_img_url); ?>"
          alt="Verteilergebiet Bodensee Region"
          loading="lazy"
        >
      </div>
    </div>
  </div>
</section>
<?php
// pokušaj da pronađeš /contact, fallback na /kontakt/
$contact_url = get_permalink( get_page_by_path('contact') );
if (!$contact_url) $contact_url = home_url('/contact/');
?>
<section class="svc-cta">
  <div class="content-container svc-cta__wrap">
    <h2 class="svc-cta__title">BEREIT FÜR<br>MAXIMUM IMPACT?</h2>
    <a class="svc-cta__btn" href="<?php echo esc_url($contact_url); ?>">
      JA
    </a>
  </div>
</section>
<section class="svc-faq">
  <div class="content-container svc-faq__wrap">
    <div class="svc-faq__head">
      <h2 class="svc-faq__title">HÄUFIGE<br>FRAGEN</h2>
      <p class="svc-faq__lead">Antworten auf die wichtigsten Fragen zur Plakatwerbung</p>
    </div>

    <div class="svc-faq__list">
      <?php
      // helper za jedan FAQ red (pitanje + (opciono) odgovor)
      function svc_faq_item($q, $a = ''){ ?>
        <div class="svc-faq__item">
          <button class="svc-faq__btn" type="button" aria-expanded="false">
            <h3 class="svc-faq__q"><?php echo esc_html($q); ?></h3>
    <span class="svc-faq__plus" aria-hidden="true">
  <svg viewBox="0 0 24 24" class="svc-faq__icon" focusable="false">
    <path class="svc-faq__icon-h" d="M5 12h14"></path>
    <path class="svc-faq__icon-v" d="M12 5v14"></path>
  </svg>
</span>
          </button>
          <div class="svc-faq__panel" >
            <div class="svc-faq__a">
              <?php echo $a !== '' ? wp_kses_post($a) : '—'; ?>
            </div>
          </div>
        </div>
      <?php }

      // Stavke (možeš dopuniti odgovor kasnije)
      svc_faq_item('Wie lange im Voraus sollte ich meine Plakatwerbung buchen?', 'Idealerweise 2–4&nbsp;Wochen im Voraus. Bei größeren Kampagnen empfehlen wir mehr Vorlauf.');
      svc_faq_item('Welche Plakatgrößen bieten Sie an?', 'Gängige Formate sind DIN&nbsp;A1 und DIN&nbsp;A0. Sonderformate sind nach Absprache möglich.');
      svc_faq_item('Erstellen Sie auch das Design für die Plakate?', 'Ja, unser Grafikteam übernimmt Konzeption, Layout und Druckdaten.');
      svc_faq_item('Wie wählen Sie die Standorte für meine Plakate aus?', 'Nach Zielgruppe, Frequenz und Sichtachsen – mit Fokus auf Premium-Standorte.');
      svc_faq_item('Was passiert bei schlechtem Wetter oder Vandalismus?', 'Regelmäßige Kontrollen; beschädigte Plakate ersetzen wir nach Absprache zeitnah.');
      ?>
    </div>
  </div>
</section>
<section class="svc-quote">
  <div class="content-container svc-quote__wrap">
    <div class="svc-quote__stage">

      <button class="svc-quote__nav svc-quote__nav--prev" aria-label="Vorherige Kundenstimme">
        <svg viewBox="0 0 24 24" class="svc-quote__icon" aria-hidden="true">
          <path d="m15 18-6-6 6-6" fill="none" stroke="currentColor" stroke-width="4" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
      </button>

      <div class="svc-quote__viewport">
        <?php
        // možeš slobodno menjati/širiti listu
        $quotes = [
          [
            'text'   => '„Exzellente Standortwahl und perfekte Ausführung. Unsere Markenbekanntheit ist durch die strategisch platzierten Plakate enorm gestiegen. Absolut empfehlenswert!“',
            'org'    => 'Bodensee Events AG',
            'person' => 'Sandra Müller, Geschäftsführerin',
          ],
          [
            'text'   => '„Sichtbar bessere Reichweite im Stadtgebiet. Planung, Plakatierung und Reporting waren top.“',
            'org'    => 'City Kultur GmbH',
            'person' => 'Lukas Hartmann, Marketing',
          ],
          [
            'text'   => '„Schnelle Umsetzung und sehr gute Standorte – wir buchen wieder.“',
            'org'    => 'Seepark Center',
            'person' => 'Mira Hoffmann, Leitung Kommunikation',
          ],
        ];
        foreach ($quotes as $i => $q): ?>
          <figure class="svc-quote__slide<?php echo $i===0 ? ' is-active' : ''; ?>">
            <blockquote class="svc-quote__txt"><?php echo esc_html($q['text']); ?></blockquote>
            <figcaption class="svc-quote__meta">
              <div class="svc-quote__org"><?php echo esc_html($q['org']); ?></div>
              <div class="svc-quote__person"><?php echo esc_html($q['person']); ?></div>
            </figcaption>
          </figure>
        <?php endforeach; ?>
      </div>

      <button class="svc-quote__nav svc-quote__nav--next" aria-label="Nächste Kundenstimme">
        <svg viewBox="0 0 24 24" class="svc-quote__icon" aria-hidden="true">
          <path d="m9 18 6-6-6-6" fill="none" stroke="currentColor" stroke-width="4" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
      </button>

    </div>
  </div>
</section>

<section class="svc-detail__content content-container">
  <?php while (have_posts()) : the_post(); the_content(); endwhile; ?>
</section>
<script>
document.addEventListener('DOMContentLoaded', function(){
  const root = document.querySelector('.svc-quote');
  if (!root) return;

  const slides = Array.from(root.querySelectorAll('.svc-quote__slide'));
  const prev   = root.querySelector('.svc-quote__nav--prev');
  const next   = root.querySelector('.svc-quote__nav--next');
  let i = 0, n = slides.length;

  function show(idx){
    i = (idx + n) % n;
    slides.forEach((el,k)=> el.classList.toggle('is-active', k === i));
  }

  prev.addEventListener('click', ()=> show(i - 1));
  next.addEventListener('click', ()=> show(i + 1));

  // tastatura
  root.addEventListener('keydown', (e)=>{
    if (e.key === 'ArrowLeft')  { e.preventDefault(); show(i-1); }
    if (e.key === 'ArrowRight') { e.preventDefault(); show(i+1); }
  });
  root.tabIndex = 0;

  // auto-rotate (pauza na hover)
  let t = setInterval(()=> show(i+1), 7000);
  root.addEventListener('mouseenter', ()=> clearInterval(t));
  root.addEventListener('mouseleave', ()=> t = setInterval(()=> show(i+1), 7000));

  show(0);
});
</script>

<script>
document.addEventListener('DOMContentLoaded', function(){
  const root = document.querySelector('.svc-faq');
  if (!root) return;

  const items = Array.from(root.querySelectorAll('.svc-faq__item'));

  // inicijalno: sve zatvoreno
  items.forEach(item => {
    const btn   = item.querySelector('.svc-faq__btn');
    const panel = item.querySelector('.svc-faq__panel');
    btn.setAttribute('aria-expanded','false');
    item.classList.remove('is-open');
    panel.style.maxHeight = '0px';
  });

  function closeItem(item){
    const btn   = item.querySelector('.svc-faq__btn');
    const panel = item.querySelector('.svc-faq__panel');
    item.classList.remove('is-open');
    btn.setAttribute('aria-expanded','false');
    panel.style.maxHeight = '0px';
  }

  function openItem(item){
    const btn   = item.querySelector('.svc-faq__btn');
    const panel = item.querySelector('.svc-faq__panel');
    item.classList.add('is-open');
    btn.setAttribute('aria-expanded','true');
    // mora posle reflow-a da se pročita scrollHeight
    requestAnimationFrame(() => {
      panel.style.maxHeight = panel.scrollHeight + 'px';
    });
  }

  // klik handler
  root.querySelectorAll('.svc-faq__btn').forEach(btn => {
    btn.addEventListener('click', () => {
      const item  = btn.closest('.svc-faq__item');
      const open  = item.classList.contains('is-open');

      // accordion: zatvori ostale
      items.forEach(it => { if (it !== item) closeItem(it); });

      // toggle trenutnog
      if (open) { closeItem(item); }
      else { openItem(item); }
    });
  });

  // na resize osveži maxHeight otvorenog panela (da ne iseče sadržaj)
  window.addEventListener('resize', () => {
    const open = root.querySelector('.svc-faq__item.is-open .svc-faq__panel');
    if (open) open.style.maxHeight = open.scrollHeight + 'px';
  }, {passive:true});
});
</script>


<script>
document.addEventListener('DOMContentLoaded', function(){
  const root = document.getElementById('plakatwerbung-hero');
  if (!root) return;

  const viewport = root.querySelector('.svc-hero-carousel__viewport');
  let slides = Array.from(root.querySelectorAll('.svc-hero-carousel__bg'));

  // 1) preflight – zadrži samo slike koje se mogu učitati
  Promise.all(slides.map(el => new Promise(resolve => {
    const url = el.style.backgroundImage.replace(/^url\(["']?/, '').replace(/["']?\)$/, '');
    const img = new Image();
    img.onload  = () => resolve({el, ok:true});
    img.onerror = () => resolve({el, ok:false});
    img.src = url;
  }))).then(results => {
    results.filter(r => !r.ok).forEach(r => r.el.remove());
    slides = Array.from(root.querySelectorAll('.svc-hero-carousel__bg'));

    // 2) dots = tačan broj
    const dotsWrap = root.querySelector('.svc-hero-carousel__dots');
    if (dotsWrap) {
      dotsWrap.innerHTML = slides.map((_, i) =>
        `<button class="svc-hero-carousel__dot${i===0?' is-active':''}" data-index="${i}" role="tab" aria-label="Gehe zu Slide ${i+1}"></button>`
      ).join('');
    }
    const dots = root.querySelectorAll('.svc-hero-carousel__dot');
    const prev = root.querySelector('.svc-hero-carousel__nav--prev');
    const next = root.querySelector('.svc-hero-carousel__nav--next');

    // 3) osiguraj da prvi VALIDNI ima .is-active
    slides.forEach((el, k)=> el.classList.toggle('is-active', k===0));

    // 4) navigacija
    let i = 0, n = slides.length;
    function show(idx){
      if (!n) return;
      i = (idx + n) % n;
      slides.forEach((el, k)=> el.classList.toggle('is-active', k===i));
      dots.forEach((el, k)=> el.classList.toggle('is-active',  k===i));
    }
    prev && prev.addEventListener('click', ()=> show(i-1));
    next && next.addEventListener('click', ()=> show(i+1));
    dots.forEach(d => d.addEventListener('click', ()=> show(parseInt(d.dataset.index||'0',10))));

    // auto-rotate
    let t = setInterval(()=> show(i+1), 6000);
    root.addEventListener('mouseenter', ()=> clearInterval(t));
    root.addEventListener('mouseleave', ()=> t = setInterval(()=> show(i+1), 6000));
  });
});
</script>

