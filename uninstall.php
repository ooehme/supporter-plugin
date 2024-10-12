<?php
// Wenn WordPress die Datei nicht direkt aufruft, abbrechen
if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

// Datenbanktabelle löschen
global $wpdb;
$table_name = $wpdb->prefix . 'supporters';
$wpdb->query("DROP TABLE IF EXISTS $table_name");

// Optionen löschen (falls vorhanden)
delete_option('supporter_plugin_version');

// Transients löschen (falls vorhanden)
delete_transient('supporter_cache');
