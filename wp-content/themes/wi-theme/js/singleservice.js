(function(){
  const hero  = document.getElementById('wi-hero');
  if(!hero) return;

  const slides = Array.from(hero.querySelectorAll('.wi-slide'));
  const dots   = Array.from(hero.querySelectorAll('.wi-dot'));
  const prevBt = hero.querySelector('.wi-arrow--left');
  const nextBt = hero.querySelector('.wi-arrow--right');

  // bočni thumbnailovi
  const prevThumb = document.getElementById('wi-prev-thumb');
  const nextThumb = document.getElementById('wi-next-thumb');
  const leftSide  = document.querySelector('.wi-side--prev');
  const rightSide = document.querySelector('.wi-side--next');

  let idx = 0, timer = null;

  function getBG(i){
    const s = slides[i];
    if (!s) return '';
    // prioritet: data-bgurl (postavljeno iz PHP-a); fallback: pronađi <img> src
    const data = s.getAttribute('data-bgurl') || '';
    if (data) return data;
    const img = s.querySelector('img');
    return img ? img.getAttribute('src') : '';
  }

  function updateThumbs(){
    if (!slides.length) return;
    const pi = (idx - 1 + slides.length) % slides.length;
    const ni = (idx + 1) % slides.length;
    const psrc = getBG(pi);
    const nsrc = getBG(ni);
    if (prevThumb && psrc) prevThumb.src = psrc;
    if (nextThumb && nsrc) nextThumb.src = nsrc;
  }

  function show(i){
    slides[idx].classList.remove('is-active');
    dots[idx].classList.remove('is-active');
    idx = (i + slides.length) % slides.length;
    slides[idx].classList.add('is-active');
    dots[idx].classList.add('is-active');
    updateThumbs();
  }

  function auto(){
    clearInterval(timer);
    timer = setInterval(()=> show(idx+1), 6000);
  }

  // Kontrole
  prevBt.addEventListener('click', ()=>{ show(idx-1); auto(); });
  nextBt.addEventListener('click', ()=>{ show(idx+1); auto(); });
  dots.forEach(d => d.addEventListener('click', ()=>{ show(parseInt(d.dataset.index,10)); auto(); }));

  // Klik na bočne thumbove (ponašaju se kao prev/next)
  if (leftSide)  leftSide.addEventListener('click', ()=>{ show(idx-1); auto(); });
  if (rightSide) rightSide.addEventListener('click', ()=>{ show(idx+1); auto(); });

  // Init
  updateThumbs();
  auto();
})();