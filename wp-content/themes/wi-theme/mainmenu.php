<nav id="mainmenu">
    <div class="resp-menu">
        <ul class="mod-menu">
            <?php
            wp_nav_menu(array(
                'theme_location' => 'primary',
                'container'      => false,
                'items_wrap'     => '%3$s'
            ));
            ?>
        </ul>
        <button id="toggle-main-menu" type="button">
            <span class="menu-ball">
                <span class="line"></span>
                <span class="line"></span>
                <span class="line"></span>
            </span>
            <span class="label">Menü</span>
        </button>
    </div>
</nav>