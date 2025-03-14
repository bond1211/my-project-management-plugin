<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class PMP_Settings {
    public function __construct() {
         add_action( 'admin_menu', array( $this, 'add_settings_page' ) );
         add_action( 'admin_init', array( $this, 'register_settings' ) );
    }
    
    public function add_settings_page() {
         add_submenu_page(
            'pmp_projects',
            'Настройки модуля',
            'Настройки модуля',
            'manage_options',
            'pmp_settings',
            array( $this, 'settings_page_callback' )
         );
    }
    
    public function register_settings() {
         register_setting( 'pmp_settings_group', 'pmp_settings' );
         add_settings_section(
             'pmp_settings_section',
             'Общие настройки',
             null,
             'pmp_settings'
         );
         add_settings_field(
             'urgency_colors',
             'Цветовая схема срочности',
             array( $this, 'urgency_colors_callback' ),
             'pmp_settings',
             'pmp_settings_section'
         );
    }
    
    public function urgency_colors_callback() {
         $options = get_option( 'pmp_settings' );
         $colors = isset( $options['urgency_colors'] ) ? $options['urgency_colors'] : '';
         echo '<input type="text" name="pmp_settings[urgency_colors]" value="' . esc_attr( $colors ) . '" />';
         echo '<p class="description">Задайте цветовую схему для уровней срочности (например: критический: red, высокий: orange, нормальный: green).</p>';
    }
    
    public function settings_page_callback() {
         ?>
         <div class="wrap">
             <h2>Настройки модуля управления проектами</h2>
             <form method="post" action="options.php">
                 <?php
                 settings_fields( 'pmp_settings_group' );
                 do_settings_sections( 'pmp_settings' );
                 submit_button();
                 ?>
             </form>
         </div>
         <?php
    }
}