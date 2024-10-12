<?php
/*
Plugin Name: Unterstützer-Formular Plugin
Description: Ein einfaches Plugin, um Unterstützerdaten zu erfassen und zu verwalten.
Version: 1.0
Author: Oliver OEhme
*/

// Laden von zusätzlichen Dateien
require_once plugin_dir_path(__FILE__) . 'includes/form-handler.php';
require_once plugin_dir_path(__FILE__) . 'includes/admin-page.php';

// Aktivierungs-Hook für die Datenbankerstellung
register_activation_hook(__FILE__, 'supporter_install');

function supporter_enqueue_styles() {
    wp_enqueue_style('supporter-styles', plugin_dir_url(__FILE__) . 'assets/css/styles.css');
}
add_action('wp_enqueue_scripts', 'supporter_enqueue_styles');
