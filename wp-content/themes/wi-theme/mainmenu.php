<nav id="mainmenu">
    <div class="resp-menu visible">
        <ul class="mod-menu">
            <?php
            wp_nav_menu(array(
                'theme_location' => 'primary',
                'container'      => false,
                'menu_class'     => 'mod-menu',
            ));
            ?>
        </ul>
        <button id="toggle-main-menu" type="button">
            <span class="menu-ball">
                <span class="line"></span>
                <span class="line"></span>
                <span class="line"></span>
            </span>
            <span class="label">Menu</span>
        </button>
        <button id="close-menu" aria-label="Close menu">&times;</button>
    </div>
</nav>