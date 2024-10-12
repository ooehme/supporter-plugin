<?php
function supporter_form_shortcode() {
    ob_start(); ?>
    <form action="" method="post">
        <!-- Formularfelder hier -->
        <!-- Code wie im vorherigen Beispiel -->
    </form>
    <?php
    return ob_get_clean();
}
add_shortcode('supporter_form', 'supporter_form_shortcode');

function process_supporter_form() {
    if (isset($_POST['submit_supporter'])) {
        // Formularverarbeitung und Speichern in der Datenbank
        // Code wie im vorherigen Beispiel
    }
}
add_action('init', 'process_supporter_form');
