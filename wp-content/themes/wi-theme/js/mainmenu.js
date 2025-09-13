(function ($) {
  $(function () {
    var $btn = $("#toggle-main-menu");
    var $overlay = $("#menu-overlay");
    var $close = $(".menu-overlay-close");
    var $links = $(".overlay-menu a");

    function openMenu() {
      $("body").addClass("menu-open");
      $overlay.addClass("open").attr("aria-hidden", "false");
      $btn.addClass("open");
    }

    function closeMenu() {
      $("body").removeClass("menu-open");
      $overlay.removeClass("open").attr("aria-hidden", "true");
      $btn.removeClass("open");
    }

    // Otvori
    $btn.on("click", function (e) {
      e.preventDefault();
      openMenu();
    });

    // Zatvori na X
    $close.on("click", function (e) {
      e.preventDefault();
      closeMenu();
    });

    // Zatvori klikom na praznu pozadinu
    $overlay.on("click", function (e) {
      if (e.target === this) {
        closeMenu();
      }
    });

    // Zatvori na Escape
    $(document).on("keydown", function (e) {
      if (e.key === "Escape") closeMenu();
    });

    // Zatvori posle klika na link (pa idi na destinaciju)
    $links.on("click", function () {
      closeMenu();
      // Ako su to anchor linkovi na istoj strani i želiš smooth scroll:
      // setTimeout da ne preseče transition
      var href = $(this).attr("href") || "";
      if (href.startsWith("#") && href.length > 1) {
        var $t = $(href);
        if ($t.length) {
          setTimeout(function () {
            $("html, body").animate({ scrollTop: $t.offset().top }, 800);
          }, 50);
        }
      }
    });
  });
})(jQuery);
