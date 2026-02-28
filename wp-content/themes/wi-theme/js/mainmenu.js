(function ($) {
  $(function () {
    var $btn = $("#toggle-main-menu");
    var $overlay = $("#menu-overlay");
    var $close = $(".wi-menu-overlay__close");
    var $links = $(".wi-menu-overlay__nav a");

    function openMenu() {
      $("body").addClass("menu-open");
      $overlay.addClass("open").attr("aria-hidden", "false");
      $btn.addClass("open").attr("aria-expanded", "true");
    }

    function closeMenu() {
      $("body").removeClass("menu-open");
      $overlay.removeClass("open").attr("aria-hidden", "true");
      $btn.removeClass("open").attr("aria-expanded", "false");
    }

    // Otvori
    $btn.on("click", function (e) {
      e.preventDefault();
      e.stopImmediatePropagation();
      openMenu();
    });

    // Zatvori na X
    $close.on("click", function (e) {
      e.preventDefault();
      closeMenu();
    });

    // Zatvori klikom na pozadinu
    $overlay.on("click", function (e) {
      if (e.target === this) {
        closeMenu();
      }
    });

    // ESC
    $(document).on("keydown", function (e) {
      if (e.key === "Escape") closeMenu();
    });

    // Klik na link zatvara overlay
    $links.on("click", function () {
      closeMenu();
    });
  });
})(jQuery);
