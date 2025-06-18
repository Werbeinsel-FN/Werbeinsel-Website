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

    (function () {
      const menu = document.querySelector(".resp-menu");

      function checkScroll() {
        if (window.scrollY > 10) {
          // kad je skrol veći od 10px
          menu.classList.add("visible");
        } else {
          menu.classList.remove("visible");
        }
      }

      window.addEventListener("scroll", checkScroll);
      document.addEventListener("DOMContentLoaded", checkScroll); // da proveri odmah na load
    })();
    // Menü öffnen
    menu.on("click", function () {
      menu.addClass("open"); // Menü fährt langsam hoch
      menuButton.hide(); // Blendet den Button aus
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
            // Mindestens 50px Scroll-Distanz
            closeMenu();
          }
        }, 300); // Verzögertes Schließen nach 300ms
      }
      lastScrollPosition = $(window).scrollTop();
    });

    // Funktion zum Schließen des Menüs
    function closeMenu() {
      if (!menu.hasClass("open")) return;

      const buttonHeight = menuButton.outerHeight();
      const menuHeight = menu.outerHeight();

      // Fiksiraj trenutnu visinu da nema skakanja
      menu.height(menuHeight);
      menu.css("opacity", 1);

      // Prvo odmah sakrij tekst (opacity)
      menu.find(".mod-menu").css("opacity", 0);

      // Sada animiraj visinu na visinu dugmeta
      menu.animate({ height: buttonHeight }, 500, () => {
        menu.removeClass("open");
        menu.css({ height: "", opacity: "", pointerEvents: "" });
        menu.find(".mod-menu").css("opacity", ""); // vrati opacity da ne bi ostao 0
        menuButton.show();
      });
    }
    // Funktion zum Scrollen oder Weiterleiten
    function handleMenuClick(menuItem, targetURL, scrollTarget) {
      menuItem.on("click", function (event) {
        event.preventDefault(); // Standard Link-Verhalten verhindern

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
        closeMenu(); // Menü nach Klick schließen
      });
    }

    // Scroll-Handling für den "Line-Up"-Menüpunkt
    handleMenuClick(
      $(".lineup-main-menu-item"),
      "/#lineup",
      $(".lineup-container")
    );

    // Scroll-Handling für den "Area"-Menüpunkt
    handleMenuClick($(".area-main-menu-item"), "/#area", $(".area-container"));

    // Scroll-Handling für den "Home"-Menüpunkt (Back-to-Top-Funktion)
    handleMenuClick($(".home-main-menu-item"), "/", $("html, body"));

    // Falls die Seite mit #lineup oder #area geöffnet wird -> Smooth Scroll nach Laden
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
