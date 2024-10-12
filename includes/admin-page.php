<?php
// Datenbank erstellen beim Aktivieren des Plugins
function supporter_install() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'supporters';
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        name tinytext NOT NULL,
        email text NOT NULL,
        phone text,
        occupation tinytext NOT NULL,
        region tinytext NOT NULL,
        message text NOT NULL,
        agreement boolean DEFAULT 0,
        status int DEFAULT 0,
        PRIMARY KEY  (id)
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}

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
    $supporters = $wpdb->get_results("SELECT * FROM $table_name WHERE status = 0");

    echo '<h1>Unterstützer Verwaltung</h1>';
    echo '<table>';
    echo '<tr><th>Name</th><th>Tätigkeit</th><th>Region</th><th>Aktionen</th></tr>';
    foreach ($supporters as $supporter) {
        echo '<tr>';
        echo '<td>' . esc_html($supporter->name) . '</td>';
        echo '<td>' . esc_html($supporter->occupation) . '</td>';
        echo '<td>' . esc_html($supporter->region) . '</td>';
        echo '<td><a href="?approve=' . $supporter->id . '">Freigeben</a></td>';
        echo '</tr>';
    }
    echo '</table>';
}

// Unterstützer freigeben
if (isset($_GET['approve'])) {
    $id = intval($_GET['approve']);
    $wpdb->update(
        $table_name,
        array('status' => 1),
        array('id' => $id)
    );
}
