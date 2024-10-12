<?php
function supporter_form_shortcode() {
    ob_start();
    
    if (isset($_GET['supporter_submitted']) && $_GET['supporter_submitted'] == '1') {
        echo '<div class="supporter-message">Vielen Dank für Ihre Unterstützung! Ihr Eintrag wird überprüft.</div>';
    }
    
    ?>
    <form action="" method="post">
        <?php wp_nonce_field('submit_supporter_form', 'supporter_form_nonce'); ?>
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
        
        <label for="agreement">
            <input type="checkbox" name="agreement" required>
            Ich bin mit der Veröffentlichung meines Namens, meiner Tätigkeit und Region einverstanden.
        </label><br>
        
        <input type="submit" name="submit_supporter" value="Absenden">
    </form>
    <?php
    return ob_get_clean();
}
add_shortcode('supporter_form', 'supporter_form_shortcode');

function display_supporters() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'supporters';
    $results = $wpdb->get_results("SELECT * FROM $table_name WHERE status = 1");

    ob_start();
    if ($results) {
        echo '<div class="supporters-list">';
        foreach ($results as $supporter) {
            echo '<div class="supporter-item">';
            echo '<h3>' . esc_html($supporter->name) . ' aus ' . esc_html($supporter->region) . '</h3>';
            echo '<p>' . esc_html($supporter->occupation) . ': ' . esc_html($supporter->message) . '</p>';
            echo '</div>';
        }
        echo '</div>';
    } else {
        echo '<p>Noch keine Unterstützer.</p>';
    }
    return ob_get_clean();
}
add_shortcode('supporter_list', 'display_supporters');

function process_supporter_form() {
    if (isset($_POST['submit_supporter'])) {
        if (!isset($_POST['supporter_form_nonce']) || !wp_verify_nonce($_POST['supporter_form_nonce'], 'submit_supporter_form')) {
            wp_die('Sicherheitsüberprüfung fehlgeschlagen. Bitte versuchen Sie es erneut.');
        }

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

        // Bestätigungsmail mit einem echten Link senden
        $confirmation_link = add_query_arg(array(
            'action' => 'confirm_supporter',
            'email' => urlencode($email),
            'token' => wp_create_nonce('confirm_supporter_' . $email)
        ), home_url());

        $message = "Vielen Dank für Ihre Unterstützung!\n\n";
        $message .= "Bitte bestätigen Sie Ihre E-Mail-Adresse, indem Sie auf den folgenden Link klicken:\n";
        $message .= $confirmation_link;

        wp_mail($email, 'Bitte bestätigen Sie Ihre Unterstützung', $message);

        // Umleitung zur gleichen Seite, um das Formular zurückzusetzen
        wp_safe_redirect(add_query_arg('supporter_submitted', '1', wp_get_referer()));
        exit;
    }
}
add_action('init', 'process_supporter_form');

// Funktion zur Bestätigung der E-Mail-Adresse
function confirm_supporter_email() {
    if (isset($_GET['action']) && $_GET['action'] == 'confirm_supporter' && isset($_GET['email']) && isset($_GET['token'])) {
        $email = urldecode($_GET['email']);
        $token = $_GET['token'];

        if (wp_verify_nonce($token, 'confirm_supporter_' . $email)) {
            global $wpdb;
            $table_name = $wpdb->prefix . 'supporters';
            $wpdb->update(
                $table_name,
                array('status' => 1), // Status auf "bestätigt" setzen
                array('email' => $email)
            );
            wp_die('Vielen Dank! Ihre E-Mail-Adresse wurde erfolgreich bestätigt.');
        } else {
            wp_die('Ungültiger Bestätigungslink. Bitte versuchen Sie es erneut oder kontaktieren Sie uns.');
        }
    }
}
add_action('init', 'confirm_supporter_email');
