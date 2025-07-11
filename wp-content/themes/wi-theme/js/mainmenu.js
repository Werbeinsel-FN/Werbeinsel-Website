(function ($) {
  $(document).ready(function () {
    const menuButton = $("#toggle-main-menu");
    const menu = $(".resp-menu");
    let lastScrollPosition = 0;
    let scrollTimeout;
    const menuItems = document.querySelectorAll(".mod-menu li");

    let activeLink = null;

    $(".resp-menu a").on("mouseenter", function () {
      if (activeLink) {
        activeLink.removeClass("active");
      }
      $(this).addClass("active");
      activeLink = $(this);
    });

    $(".resp-menu").on("mouseleave", function () {});

    // Menü öffnen
    menuButton.on("click", function () {
      menu.addClass("open");
      menuButton.hide();
    });

    // Menü schließen, wenn außerhalb geklickt wird
    $(document).on("click", function (event) {
      const isClickInsideMenu =
        $(event.target).closest(".resp-menu").length > 0;
      const isClickOnButton =
        $(event.target).closest("#toggle-main-menu").length > 0;

      if (!isClickInsideMenu && !isClickOnButton) {
        closeMenu();
      }
    });

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

    function closeMenu() {
      if (!menu.hasClass("open")) return;

      const buttonHeight = menuButton.outerHeight();
      const menuHeight = menu.outerHeight();

      menu.height(menuHeight);
      menu.css("opacity", 1);

      menu.find(".mod-menu").css("opacity", 0);

      menu.animate({ height: buttonHeight }, 500, () => {
        menu.removeClass("open");
        menu.css({ height: "", opacity: "", pointerEvents: "" });
        menu.find(".mod-menu").css("opacity", "");
        menuButton.show();
      });
    }

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

    handleMenuClick(
      $(".lineup-main-menu-item"),
      "/#lineup",
      $(".lineup-container")
    );

    handleMenuClick($(".area-main-menu-item"), "/#area", $(".area-container"));

    handleMenuClick($(".home-main-menu-item"), "/", $("html, body"));

    function checkHashAndScroll(hash, target) {
      if (window.location.hash === hash && target.length) {
        setTimeout(function () {
          $("html, body").animate({ scrollTop: target.offset().top }, 800);
        }, 300);
      }
    }

    checkHashAndScroll("#lineup", $(".lineup-container"));
    checkHashAndScroll("#area", $(".area-container"));
  });
})(jQuery);
