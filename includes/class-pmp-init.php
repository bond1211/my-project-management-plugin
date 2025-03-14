<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class PMP_Init {
    public function __construct() {
         add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ) );
         add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_frontend_scripts') );
    }

    public function enqueue_admin_scripts() {
         wp_enqueue_style( 'pmp_admin_css', PMP_PLUGIN_URL . 'assets/css/style.css' );
         wp_enqueue_script( 'pmp_admin_js', PMP_PLUGIN_URL . 'assets/js/main.js', array('jquery'), false, true );
         wp_localize_script( 'pmp_admin_js', 'pmp_frontend_obj', array(
             'ajaxurl' => admin_url( 'admin-ajax.php' ),
             'nonce'   => wp_create_nonce( 'pmp_frontend_nonce' )
         ) );
    }

    public function enqueue_frontend_scripts() {
         wp_enqueue_style( 'pmp_frontend_css', PMP_PLUGIN_URL . 'assets/css/style.css' );
         wp_enqueue_script( 'pmp_frontend_js', PMP_PLUGIN_URL . 'assets/js/main.js', array('jquery'), false, true );
         wp_localize_script( 'pmp_frontend_js', 'pmp_frontend_obj', array(
              'ajaxurl' => admin_url( 'admin-ajax.php' ),
              'nonce'   => wp_create_nonce( 'pmp_frontend_nonce' )
         ) );
    }
}