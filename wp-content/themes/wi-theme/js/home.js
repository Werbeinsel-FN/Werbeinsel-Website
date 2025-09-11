document.addEventListener("DOMContentLoaded", function () {
  const carousels = document.querySelectorAll(".carousel");
  const flktyInstances = [];

  const isMobile = window.innerWidth < 1024;

  // Initialize Flickity carousels
  carousels.forEach((carousel) => {
    const carouselId = parseInt(carousel.dataset.carouselId, 10);

    const flkty = new Flickity(carousel, {
      cellAlign: "left",
      contain: true,
      wrapAround: true,
      prevNextButtons: false,
      pageDots: false,
      draggable: isMobile,
      pauseAutoPlayOnHover: false,
      imagesLoaded: true,
    });

    flktyInstances.push({
      instance: flkty,
      direction: carouselId === 2 ? 1 : -1,
    });
  });

  // Continuous scroll animation
  function smoothAnimate() {
    flktyInstances.forEach(({ instance, direction }) => {
      instance.x += 0.5 * direction;
      instance.settle(instance.x);
    });
    requestAnimationFrame(smoothAnimate);
  }

  smoothAnimate();
});

window.addEventListener("resize", adjustCellWidth);
document.addEventListener("DOMContentLoaded", adjustCellWidth);