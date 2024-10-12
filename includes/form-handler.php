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
        $name = sanitize_text_field($_POST['name']);
        $email = sanitize_email($_POST['email']);
        $phone = sanitize_text_field($_POST['phone']);
        $occupation = sanitize_text_field($_POST['occupation']);
        $region = sanitize_text_field($_POST['region']);
        $message = sanitize_textarea_field($_POST['message']);
        $agreement = isset($_POST['agreement']) ? 1 : 0;

        global $wpdb;
        $table_name = $wpdb->prefix . 'supporters';
        $wpdb->insert(
            $table_name,
            array(
                'name' => $name,
                'email' => $email,
                'phone' => $phone,
                'occupation' => $occupation,
                'region' => $region,
                'message' => $message,
                'agreement' => $agreement,
                'status' => 0 // Status 0 bedeutet, dass der Beitrag auf Freigabe wartet
            )
        );

        // Bestätigungsmail an den Unterstützer senden
        wp_mail($email, 'Bitte bestätigen Sie Ihre Unterstützung', 'Klicken Sie auf den folgenden Link, um Ihre Unterstützung zu bestätigen.');
    }
}
add_action('init', 'process_supporter_form');
