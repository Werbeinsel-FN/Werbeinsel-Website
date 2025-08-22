jQuery(document).ready(function ($) {
    function handleMediaUpload(buttonId, inputId, previewId) {
        $(buttonId).on('click', function (e) {
            e.preventDefault();

            var mediaUploader;
            if (mediaUploader) {
                mediaUploader.open();
                return;
            }

            mediaUploader = wp.media({
                title: 'Wähle ein Logo',
                button: { text: 'Logo auswählen' },
                multiple: false
            });

            mediaUploader.on('select', function () {
                var attachment = mediaUploader.state().get('selection').first().toJSON();
                $(inputId).val(attachment.url); // Setze die URL im versteckten Input-Feld
                $(previewId).html('<img src="' + attachment.url + '" alt="Logo Vorschau" style="max-width: 200px;">'); // Aktualisiere die Vorschau
            });

            mediaUploader.open();
        });
    }

    // Für Light-Logo
    handleMediaUpload('#wi_theme_logo_button', '#wi_theme_logo', '#wi_theme_logo_preview');
    
    $('.my-color-field').wpColorPicker();
});