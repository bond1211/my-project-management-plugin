<?php
/*
Plugin Name: Project Management Module
Description: Модуль управления проектами с ролевой системой доступа, системой сообщений и административными настройками.
Version: 1.0
Author: Your Name
Requires at least: 6.7.2
*/

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Не допускаем прямой вызов
}

define( 'PMP_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'PMP_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

// Подключаем файлы
require_once PMP_PLUGIN_DIR . 'includes/class-pmp-init.php';
require_once PMP_PLUGIN_DIR . 'includes/class-pmp-access.php';
require_once PMP_PLUGIN_DIR . 'includes/class-pmp-shortcodes.php';
require_once PMP_PLUGIN_DIR . 'includes/class-pmp-frontend.php';
require_once PMP_PLUGIN_DIR . 'includes/class-pmp-messages.php';
require_once PMP_PLUGIN_DIR . 'includes/class-pmp-settings.php';

function pmp_init_plugin() {
    new PMP_Init();
    new PMP_Access();
    new PMP_Shortcodes();
    new PMP_Frontend();
    new PMP_Messages();
    new PMP_Settings();
}
add_action( 'plugins_loaded', 'pmp_init_plugin' );

function pmp_activate_plugin() {
    require_once PMP_PLUGIN_DIR . 'includes/class-pmp-activation.php';
    PMP_Activation::activate();
}
register_activation_hook( __FILE__, 'pmp_activate_plugin' );