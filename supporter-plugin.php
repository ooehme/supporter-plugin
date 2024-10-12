<?php
/*
Plugin Name: Unterstützer-Formular Plugin
Description: Ein einfaches Plugin, um Unterstützerdaten zu erfassen und zu verwalten.
Version: 1.1
Author: Oliver Oehme
License: GPL2
GitHub Plugin URI: https://github.com/ooehme/supporter-plugin
*/

// Laden von zusätzlichen Dateien
require_once plugin_dir_path(__FILE__) . 'includes/form-handler.php';
require_once plugin_dir_path(__FILE__) . 'includes/admin-page.php';

// Aktivierungs-Hook für die Datenbankerstellung
register_activation_hook(__FILE__, 'supporter_install');

// Funktion zum Erstellen der Datenbanktabelle
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

// Enqueue Styles
function supporter_enqueue_styles() {
    wp_enqueue_style('supporter-styles', plugin_dir_url(__FILE__) . 'assets/css/styles.css');
}
add_action('wp_enqueue_scripts', 'supporter_enqueue_styles');

// Aktiviere Sitzungen für Nachrichten
function supporter_start_session() {
    if (!session_id()) {
        session_start();
    }
}
add_action('init', 'supporter_start_session');

// Funktion zum Anzeigen von Admin-Benachrichtigungen
function supporter_admin_notices() {
    if (isset($_SESSION['supporter_admin_notice'])) {
        echo '<div class="notice notice-success is-dismissible"><p>' . $_SESSION['supporter_admin_notice'] . '</p></div>';
        unset($_SESSION['supporter_admin_notice']);
    }
}
add_action('admin_notices', 'supporter_admin_notices');
