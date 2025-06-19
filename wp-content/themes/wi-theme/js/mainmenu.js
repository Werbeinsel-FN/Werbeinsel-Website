(function($) {
    $(document).ready(function() {
        const menuButton = $('#toggle-main-menu');
        const menu = $('.resp-menu');
        let lastScrollPosition = 0;
        let scrollTimeout;

        // Menü öffnen
        menuButton.on('click', function() {
            menu.addClass('open'); // Menü fährt langsam hoch
            menuButton.hide(); // Blendet den Button aus
        });

        // Menü schließen, wenn außerhalb geklickt wird
        $(document).on('click', function(event) {
            if (!$(event.target).closest('.resp-menu, #toggle-main-menu').length) {
                closeMenu();
            }
        });

        // Menü schließen, wenn der Nutzer gescrollt hat (aber mit Verzögerung)
        $(window).on('scroll', function() {
            if (menu.hasClass('open') && window.innerWidth <= 768) {
                clearTimeout(scrollTimeout);

                scrollTimeout = setTimeout(function() {
                    if (Math.abs($(window).scrollTop() - lastScrollPosition) > 50) { // Mindestens 50px Scroll-Distanz
                        closeMenu();
                    }
                }, 300); // Verzögertes Schließen nach 300ms
            }
            lastScrollPosition = $(window).scrollTop();
        });

        // Funktion zum Schließen des Menüs
        function closeMenu() {
            menu.removeClass('open');
            menuButton.show();
        }

        // Funktion zum Scrollen oder Weiterleiten
        function handleMenuClick(menuItem, targetURL, scrollTarget) {
            menuItem.on('click', function(event) {
                event.preventDefault(); // Standard Link-Verhalten verhindern

                const isHomePage = window.location.pathname === "/" || window.location.pathname.includes("index");

                if (isHomePage && scrollTarget.length) {
                    $('html, body').animate({ scrollTop: scrollTarget.offset().top }, 800);
                } else {
                    window.location.href = targetURL;
                }
                closeMenu(); // Menü nach Klick schließen
            });
        }

        // Scroll-Handling für den "Line-Up"-Menüpunkt
        handleMenuClick($('.lineup-main-menu-item'), "/#lineup", $('.lineup-container'));

        // Scroll-Handling für den "Area"-Menüpunkt
        handleMenuClick($('.area-main-menu-item'), "/#area", $('.area-container'));

        // Scroll-Handling für den "Home"-Menüpunkt (Back-to-Top-Funktion)
        handleMenuClick($('.home-main-menu-item'), "/", $('html, body'));

        // Falls die Seite mit #lineup oder #area geöffnet wird -> Smooth Scroll nach Laden
        function checkHashAndScroll(hash, target) {
            if (window.location.hash === hash && target.length) {
                setTimeout(function() {
                    $('html, body').animate({ scrollTop: target.offset().top }, 800);
                }, 300);
            }
        }

        checkHashAndScroll("#lineup", $('.lineup-container'));
        checkHashAndScroll("#area", $('.area-container'));
    });
})(jQuery);