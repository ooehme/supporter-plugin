<?php
function supporter_form_shortcode() {
    ob_start(); ?>
    <form action="" method="post">
        <label for="name">Name:</label>
        <input type="text" name="name" required><br>
        
        <label for="email">E-Mail:</label>
        <input type="email" name="email" required><br>
        
        <label for="phone">Telefonnummer (optional):</label>
        <input type="text" name="phone"><br>
        
        <label for="occupation">Tätigkeit:</label>
        <input type="text" name="occupation" required><br>
        
        <label for="region">Region:</label>
        <input type="text" name="region" required><br>
        
        <label for="message">Nachricht:</label>
        <textarea name="message" required></textarea><br>
        
        <label for="agreement">Ich bin mit der Veröffentlichung meines Namens, meiner Tätigkeit und Region einverstanden.</label>
        <input type="checkbox" name="agreement"><br>
        
        <input type="submit" name="submit_supporter" value="Absenden">
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
