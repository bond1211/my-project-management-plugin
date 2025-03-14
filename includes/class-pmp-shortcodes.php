<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class PMP_Shortcodes {
    public function __construct() {
         add_shortcode( 'pmp_dashboard', array( $this, 'render_dashboard' ) );
    }
    
    public function render_dashboard( $atts ) {
         if ( ! is_user_logged_in() ) {
              return __( 'Пожалуйста, войдите для доступа к панели проектов.', 'pmp' );
         }
         ob_start();
         if ( isset( $_GET['pmp_task_id'] ) && intval( $_GET['pmp_task_id'] ) ) {
             include PMP_PLUGIN_DIR . 'templates/frontend-task-page.php';
         } elseif ( isset( $_GET['pmp_project_id'] ) && intval( $_GET['pmp_project_id'] ) ) {
             include PMP_PLUGIN_DIR . 'templates/frontend-project-page.php';
         } else {
             include PMP_PLUGIN_DIR . 'templates/frontend-dashboard.php';
         }
         return ob_get_clean();
    }
}