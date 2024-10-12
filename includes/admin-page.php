<?php
// Backend-Verwaltungsseite
function supporter_admin_menu() {
    add_menu_page(
        'Unterstützer Verwaltung',
        'Unterstützer',
        'manage_options',
        'supporter-admin',
        'supporter_admin_page'
    );
}
add_action('admin_menu', 'supporter_admin_menu');

function supporter_admin_page() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'supporters';

    // Verarbeite die Freigabe
    if (isset($_GET['action']) && $_GET['action'] == 'approve' && isset($_GET['id'])) {
        $id = intval($_GET['id']);
        $nonce = isset($_GET['_wpnonce']) ? $_GET['_wpnonce'] : '';
        
        if (wp_verify_nonce($nonce, 'approve_supporter_' . $id)) {
            $wpdb->update(
                $table_name,
                array('status' => 1),
                array('id' => $id)
            );
            $_SESSION['supporter_admin_notice'] = 'Unterstützer erfolgreich freigegeben.';
        } else {
            $_SESSION['supporter_admin_notice'] = 'Sicherheitsüberprüfung fehlgeschlagen. Bitte versuchen Sie es erneut.';
        }
        wp_redirect(admin_url('admin.php?page=supporter-admin'));
        exit;
    }

    $supporters = $wpdb->get_results("SELECT * FROM $table_name WHERE status = 0");

    echo '<div class="wrap">';
    echo '<h1>Unterstützer Verwaltung</h1>';
    echo '<table class="wp-list-table widefat fixed striped">';
    echo '<thead><tr><th>Name</th><th>E-Mail</th><th>Tätigkeit</th><th>Region</th><th>Aktionen</th></tr></thead>';
    echo '<tbody>';
    foreach ($supporters as $supporter) {
        echo '<tr>';
        echo '<td>' . esc_html($supporter->name) . '</td>';
        echo '<td>' . esc_html($supporter->email) . '</td>';
        echo '<td>' . esc_html($supporter->occupation) . '</td>';
        echo '<td>' . esc_html($supporter->region) . '</td>';
        echo '<td><a href="' . wp_nonce_url(admin_url('admin.php?page=supporter-admin&action=approve&id=' . $supporter->id), 'approve_supporter_' . $supporter->id) . '" class="button">Freigeben</a></td>';
        echo '</tr>';
    }
    echo '</tbody></table>';
    echo '</div>';
}
