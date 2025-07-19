<?php
/**
 * Template Name: Kontakt
 * Description: Template for the contact page
 */

get_header(); ?>


<link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/css/contact.css?v=<?php echo filemtime(get_template_directory() . '/css/contact.css'); ?>">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/foundation-sites@6.7.5/dist/css/foundation.min.css">
<!-- <link href="https://fonts.googleapis.com/css2?family=Unbounded:wght@400;700;800&display=swap" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;700;800&display=swap" rel="stylesheet"> -->

<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<!-- FontAwesome 5 -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
<!-- FontAwesome 6 -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">


<main id="wi-contact-main" class="wi-contact-main">
    <div id="contact-form" class="content-form contact-form">
	
        <div class="content row loaded">
        
            <h3 class="contact-form headline text-center">
                Kontakt</h3>
            <div class="contact-form subline text-center">
                Einfach das Formular ausfüllen - wir freuen uns auf Sie!
            </div>

            <?php echo do_shortcode('[wi_form id="30"]'); ?> 
        </div>
    </div>
    <div class="contact-bottom-wrapper">
        <div class="contact-address-wrapper">
            <div class="address">
                <div class="icon-outer-wrapper">
                    <div class="icon-wrapper">
                        <i class="fa fa-map-marker"></i>
                    </div>
                </div>
                <div class="data-wrapper">
                    <div class="data-head-wrapper">
                        <h4>Adresse</h4>
                    </div>
                    <div class="data-content-wrapper">
                        Am Flugplatz 76/3<br />
                        88045 Friedrichshafen
                    </div>
                </div>
            </div>
            <div class="email">
                <div class="icon-outer-wrapper">
                    <div class="icon-wrapper">
                    <i class="fas fa-map"></i>
                    </div>
                </div>
                <div class="data-wrapper">
                    <div class="data-head-wrapper">
                        <h4>E-Mail</h4>
                    </div>
                    <div class="data-content-wrapper">
                        <a href="mailto:hallo@werbeinsel.de">hallo@werbeinsel.de</a>
                    </div>
                </div>
            </div>
            <div class="phone">
                <div class="icon-outer-wrapper">
                    <div class="icon-wrapper">
                        <i class="fa-solid fa-phone"></i>
                    </div>
                </div>
                <div class="data-wrapper">
                    <div class="data-head-wrapper">
                        <h4>Telefon</h4>
                    </div>
                    <div class="data-content-wrapper">
                        <a href="tel:+4975417005744">+49 7541 700 57 44</a>
                    </div>
                </div>                
            </div>
        </div>
        <div class="contact-map-outer-wrapper">
            <div id="mapid" class="contact-map-wrapper">

            </div>
        </div>
    </div>
</main>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const map = L.map('mapid').setView([47.6543, 9.4797], 10); // Koordinaten: Bodensee-Region z. B.

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: 'Leaflet | © <a href="https://www.openstreetmap.org/">OpenStreetMap</a>',
        maxZoom: 18,
    }).addTo(map);

});
</script>


<?php get_footer();