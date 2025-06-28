document.addEventListener('DOMContentLoaded', function () {
  const carousels = document.querySelectorAll('.carousel');
  const flktyInstances = [];

  carousels.forEach((carousel) => {
    const carouselId = parseInt(carousel.dataset.carouselId, 10);

    const flkty = new Flickity(carousel, {
      cellAlign: 'left',
      contain: true,
      wrapAround: true,
      prevNextButtons: false,
      pageDots: false,
      draggable: false,
      pauseAutoPlayOnHover: false,
      imagesLoaded: true,
    });

    flktyInstances.push({
      instance: flkty,
      direction: carouselId === 2 ? 1 : -1, // NUR das mittlere Carousel scrollt nach rechts
    });
  });

  function smoothAnimate() {
    flktyInstances.forEach(({ instance, direction }) => {
      instance.x += 0.5 * direction;
      instance.settle(instance.x);
    });
    requestAnimationFrame(smoothAnimate);
  }

  smoothAnimate();
  });


  function adjustCellWidth() {
  document.querySelectorAll('.carousel').forEach(carousel => {
    const carouselWidth = carousel.offsetWidth;
    const cells = carousel.querySelectorAll('.carousel-cell');
    const itemsToShow = Math.floor(carouselWidth / 450); // z.B. 250px pro Item
    const cellWidthPercent = 100 / itemsToShow;

    cells.forEach(cell => {
      cell.style.width = `${cellWidthPercent}%`;
    });
  });
}

window.addEventListener('resize', adjustCellWidth);
document.addEventListener('DOMContentLoaded', adjustCellWidth);  