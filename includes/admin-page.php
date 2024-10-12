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

    // Verarbeite die Aktionen
    if (isset($_GET['action']) && isset($_GET['id'])) {
        $id = intval($_GET['id']);
        $nonce = isset($_GET['_wpnonce']) ? $_GET['_wpnonce'] : '';
        
        if ($_GET['action'] == 'approve' && wp_verify_nonce($nonce, 'approve_supporter_' . $id)) {
            $wpdb->update(
                $table_name,
                array('status' => 1),
                array('id' => $id)
            );
            $_SESSION['supporter_admin_notice'] = 'Unterstützer erfolgreich freigegeben.';
        } elseif ($_GET['action'] == 'delete' && wp_verify_nonce($nonce, 'delete_supporter_' . $id)) {
            $wpdb->delete(
                $table_name,
                array('id' => $id)
            );
            $_SESSION['supporter_admin_notice'] = 'Unterstützer erfolgreich gelöscht.';
        } else {
            $_SESSION['supporter_admin_notice'] = 'Sicherheitsüberprüfung fehlgeschlagen. Bitte versuchen Sie es erneut.';
        }
    }

    // Hole alle Unterstützer
    $supporters = $wpdb->get_results("SELECT * FROM $table_name ORDER BY status ASC, id DESC");

    echo '<div class="wrap">';
    echo '<h1>Unterstützer Verwaltung</h1>';
    
    // Zeige Admin-Benachrichtigungen
    if (isset($_SESSION['supporter_admin_notice'])) {
        echo '<div class="notice notice-success is-dismissible"><p>' . $_SESSION['supporter_admin_notice'] . '</p></div>';
        unset($_SESSION['supporter_admin_notice']);
    }
    
    echo '<table class="wp-list-table widefat fixed striped">';
    echo '<thead><tr><th>Name</th><th>E-Mail</th><th>Tätigkeit</th><th>Region</th><th>Status</th><th>Aktionen</th></tr></thead>';
    echo '<tbody>';
    foreach ($supporters as $supporter) {
        echo '<tr>';
        echo '<td>' . esc_html($supporter->name) . '</td>';
        echo '<td>' . esc_html($supporter->email) . '</td>';
        echo '<td>' . esc_html($supporter->occupation) . '</td>';
        echo '<td>' . esc_html($supporter->region) . '</td>';
        echo '<td>' . ($supporter->status == 1 ? 'Freigegeben' : 'Ausstehend') . '</td>';
        echo '<td>';
        if ($supporter->status == 0) {
            echo '<a href="' . wp_nonce_url(admin_url('admin.php?page=supporter-admin&action=approve&id=' . $supporter->id), 'approve_supporter_' . $supporter->id) . '" class="button button-primary">Freigeben</a> ';
        }
        echo '<a href="' . wp_nonce_url(admin_url('admin.php?page=supporter-admin&action=delete&id=' . $supporter->id), 'delete_supporter_' . $supporter->id) . '" class="button button-secondary" onclick="return confirm(\'Sind Sie sicher, dass Sie diesen Unterstützer löschen möchten?\')">Löschen</a>';
        echo '</td>';
        echo '</tr>';
    }
    echo '</tbody></table>';
    echo '</div>';
}

// Füge JavaScript hinzu, um die Seite nach einer Aktion neu zu laden
function supporter_admin_scripts() {
    ?>
    <script type="text/javascript">
    jQuery(document).ready(function($) {
        // Überprüfe, ob die URL einen action-Parameter enthält
        if (window.location.href.indexOf('action=') > -1) {
            // Entferne den action-Parameter und lade die Seite neu
            var cleanUrl = window.location.href.split('?')[0] + '?page=supporter-admin';
            window.history.replaceState({}, document.title, cleanUrl);
            location.reload();
        }
    });
    </script>
    <?php
}
add_action('admin_footer', 'supporter_admin_scripts');
