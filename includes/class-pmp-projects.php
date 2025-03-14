<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class PMP_Projects {
    public function __construct() {
         // Добавление пункта меню в админке
         add_action( 'admin_menu', array( $this, 'add_admin_pages' ) );
         // AJAX: асинхронная загрузка данных для панели заданий
         add_action( 'wp_ajax_pmp_get_projects', array( $this, 'ajax_get_projects' ) );
    }
    
    public function add_admin_pages() {
        add_menu_page(
            'Управление проектами', // Титул страницы
            'Проекты',              // Название меню
            'pmp_manage',           // Необходимая возможность (проверка в PMP_Access)
            'pmp_projects',         // Слаг страницы
            array( $this, 'dashboard_page' ),
            'dashicons-portfolio',
            6
        );
    }
    
    public function dashboard_page() {
       // Проверка прав доступа
       if ( ! PMP_Access::user_can_access( 'pmp_manage' ) ) {
            wp_die( __( 'Доступ закрыт', 'pmp' ) );
       }
       // Подключаем шаблон с отображением трёх колонок и плавающей верхней панелью 
       include PMP_PLUGIN_DIR . 'templates/dashboard.php';
    }
    
    public function ajax_get_projects() {
         // Проверка nonce и прав доступа
         check_ajax_referer( 'pmp_projects_nonce', 'nonce' );
         
         global $wpdb;
         $table = $wpdb->prefix . 'pmp_projects';
         $results = $wpdb->get_results( "SELECT * FROM {$table}" );
         
         // Здесь можно добавить кэширование и оптимизацию запросов
         wp_send_json_success( $results );
    }
}