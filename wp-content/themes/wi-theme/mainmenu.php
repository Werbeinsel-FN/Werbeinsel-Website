<?php
/**
 * Main menu template
 * Path: wp-content/themes/wi-theme/mainmenu.php
 */
?>
<nav id="mainmenu" role="navigation" aria-label="Primary">
    <div class="resp-menu">

        <!-- (Opciono) Klasičan WP meni markup koji tema već koristi.
             Ostavljamo ga zbog kompatibilnosti / SEO-a, ali overlay koristi svoj spisak ispod. -->

        <!-- Figma MENU dugme (floating, žuto) -->
        <button id="toggle-main-menu" type="button" class="fixed bottom-5p left-1_2 translate-x--1_2 bg-ffed00 border-2-black rounded-full px-9 py-5 flex items-center gap-4 hover-grow z-40" aria-haspopup="true" aria-controls="menu-overlay" aria-expanded="false">
            <span class="bars" aria-hidden="true">
                <span class="bar"></span>
                <span class="bar"></span>
                <span class="bar"></span>
            </span>
            <span class="label">MENU</span>
        </button>

        <!-- FULLSCREEN OVERLAY (otvara se klikom na dugme iznad) -->
        <div id="menu-overlay" class="menu-overlay" aria-hidden="true">
            <button class="menu-overlay-close" aria-label="Close">
                <!-- X ikonica -->
                <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-8 h-8" aria-hidden="true">
                    <path d="M18 6 6 18"></path>
                    <path d="m6 6 12 12"></path>
                </svg>
            </button>

            <nav class="menu-overlay-nav" role="menu">
                <ul class="overlay-menu">
                    <?php
                    // Izvuci PRAVI primarni meni kao <li><a> za fullscreen overlay
                    wp_nav_menu(array(
                        'theme_location' => 'primary',
                        'container'      => false,
                        'items_wrap'     => '%3$s',
                        'link_before'    => '',
                        'link_after'     => ''
                    ));
                    ?>
                </ul>
            </nav>
        </div>
        <!-- /FULLSCREEN OVERLAY -->

    </div>
</nav>

