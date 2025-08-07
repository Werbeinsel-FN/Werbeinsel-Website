(function ($) {
  $(document).ready(function () {
    const menuButton = $("#toggle-main-menu");
    const menu = $(".resp-menu");
    const closeBtn = document.getElementById("close-menu");
    let lastScrollPosition = 0;
    let scrollTimeout;
    const menuItems = document.querySelectorAll(".mod-menu li");

    let activeLink = null;

    // Menü schließen, wenn der Nutzer gescrollt hat (aber mit Verzögerung)
    $(window).on("scroll", function () {
      if (menu.hasClass("open") && window.innerWidth <= 768) {
        clearTimeout(scrollTimeout);

        scrollTimeout = setTimeout(function () {
          if (Math.abs($(window).scrollTop() - lastScrollPosition) > 50) {
            closeMenu();
          }
        }, 300);
      }
      lastScrollPosition = $(window).scrollTop();
    });

    function handleMenuClick(menuItem, targetURL, scrollTarget) {
      menuItem.on("click", function (event) {
        event.preventDefault();

        const isHomePage =
          window.location.pathname === "/" ||
          window.location.pathname.includes("index");

        if (isHomePage && scrollTarget.length) {
          $("html, body").animate(
            { scrollTop: scrollTarget.offset().top },
            800
          );
        } else {
          window.location.href = targetURL;
        }

        closeMenu();
      });
    }
    // Closing menu
    menuButton.on("click", function () {
      menu.addClass("open");
      menuButton.hide();
    });

    $("#close-menu").on("click", function () {
      menu.removeClass("open");
      menuButton.show();
    });

    handleMenuClick(
      $(".lineup-main-menu-item"),
      "/#lineup",
      $(".lineup-container")
    );

    handleMenuClick($(".area-main-menu-item"), "/#area", $(".area-container"));

    handleMenuClick($(".home-main-menu-item"), "/", $("html, body"));
  });
})(jQuery);
