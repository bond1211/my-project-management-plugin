<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class PMP_Frontend {
    public function __construct() {
         add_action( 'wp_ajax_pmp_create_project', array( $this, 'ajax_create_project' ) );
         add_action( 'wp_ajax_nopriv_pmp_create_project', array( $this, 'ajax_nopriv_callback' ) );
         
         add_action( 'wp_ajax_pmp_create_task', array( $this, 'ajax_create_task' ) );
         add_action( 'wp_ajax_nopriv_pmp_create_task', array( $this, 'ajax_nopriv_callback' ) );
         
         add_action( 'wp_ajax_pmp_assign_project', array( $this, 'ajax_assign_project' ) );
         add_action( 'wp_ajax_nopriv_pmp_assign_project', array( $this, 'ajax_nopriv_callback' ) );
         
         add_action( 'wp_ajax_pmp_update_project', array( $this, 'ajax_update_project' ) );
         add_action( 'wp_ajax_nopriv_pmp_update_project', array( $this, 'ajax_nopriv_callback' ) );
         
         add_action( 'wp_ajax_pmp_accept_project', array( $this, 'ajax_accept_project' ) );
         add_action( 'wp_ajax_nopriv_pmp_accept_project', array( $this, 'ajax_nopriv_callback' ) );
		 
		 add_action( 'wp_ajax_pmp_update_task', array( $this, 'ajax_update_task' ) );
         add_action( 'wp_ajax_pmp_assign_task', array( $this, 'ajax_assign_task' ) );
         add_action( 'wp_ajax_pmp_close_task', array( $this, 'ajax_close_task' ) );

         
         // Дополнительно: функции для задач (ajax_update_task, ajax_assign_task, ajax_close_task) – опущены для краткости
    }
    
    public function ajax_create_project() {
         check_ajax_referer( 'pmp_frontend_nonce', 'nonce' );
         if ( ! is_user_logged_in() ) {
              wp_send_json_error( __( 'Вы не авторизованы', 'pmp' ) );
         }
         if ( ! current_user_can( 'pmp_create_project' ) ) {
              wp_send_json_error( __( 'Доступ закрыт', 'pmp' ) );
         }
         
         $project_name  = sanitize_text_field( $_POST['project_name'] );
         $description   = sanitize_textarea_field( $_POST['description'] );
         $deadline      = sanitize_text_field( $_POST['deadline'] );
         $urgency_level = sanitize_text_field( $_POST['urgency_level'] );
         
         if ( empty( $project_name ) || empty( $description ) || empty( $deadline ) ) {
              wp_send_json_error( __( 'Заполните все обязательные поля', 'pmp' ) );
         }
         
         global $wpdb;
         $table = $wpdb->prefix . 'pmp_projects';
         $current_user = get_current_user_id();
         $result = $wpdb->insert(
              $table,
              array(
                  'project_name'  => $project_name,
                  'description'   => $description,
                  'deadline'      => $deadline,
                  'status'        => 'active',
                  'urgency_level' => $urgency_level,
                  'created_by'    => $current_user,
                  'assigned_to'   => $current_user, // по умолчанию себе
                  'accepted'      => 1,
              ),
              array( '%s', '%s', '%s', '%s', '%s', '%d', '%d', '%d' )
         );
         
         if ( $result ) {
              wp_send_json_success( __( 'Проект создан', 'pmp' ) );
         } else {
              wp_send_json_error( __( 'Ошибка при создании проекта', 'pmp' ) );
         }
         wp_die();
    }
    
    public function ajax_create_task() {
         check_ajax_referer( 'pmp_frontend_nonce', 'nonce' );
         if ( ! is_user_logged_in() ) {
              wp_send_json_error( __( 'Вы не авторизованы', 'pmp' ) );
         }
         if ( ! current_user_can( 'pmp_manage_tasks' ) ) {
              wp_send_json_error( __( 'Доступ закрыт', 'pmp' ) );
         }
         $project_id     = intval( $_POST['project_id'] );
         $task_name      = sanitize_text_field( $_POST['task_name'] );
         $description    = sanitize_textarea_field( $_POST['description'] );
         $urgency_level  = sanitize_text_field( $_POST['urgency_level'] );
         $executors_raw  = sanitize_text_field( $_POST['executors'] );
         $executors_arr  = array_map( 'intval', array_filter( explode( ',', $executors_raw ) ) );
         $executors_json = json_encode( $executors_arr );
         
         if ( empty( $task_name ) || empty( $description ) ) {
              wp_send_json_error( __( 'Заполните обязательные поля задачи', 'pmp' ) );
         }
         
         global $wpdb;
         $table = $wpdb->prefix . 'pmp_tasks';
         $result = $wpdb->insert(
              $table,
              array(
                  'project_id'       => $project_id,
                  'task_name'        => $task_name,
                  'description'      => $description,
                  'urgency_level'    => $urgency_level,
                  'executors'        => $executors_json,
                  'assigned_to'      => get_current_user_id(),
                  'status'           => 'active',
              ),
              array( '%d', '%s', '%s', '%s', '%s', '%d', '%s' )
         );
         if ( $result ) {
              wp_send_json_success( __( 'Задача создана', 'pmp' ) );
         } else {
              wp_send_json_error( __( 'Ошибка создания задачи', 'pmp' ) );
         }
         wp_die();
    }
    
    public function ajax_assign_project() {
         check_ajax_referer( 'pmp_frontend_nonce', 'assign_nonce' );
         if ( ! is_user_logged_in() || ! current_user_can( 'pmp_assign_project' ) ) {
              wp_send_json_error( __( 'Доступ закрыт', 'pmp' ) );
         }
         $project_id = intval( $_POST['project_id'] );
         $user_id    = intval( $_POST['user_id'] );
         
         global $wpdb;
         $table = $wpdb->prefix . 'pmp_projects';
         // При назначении устанавливаем accepted = 0 для получателя
         $result = $wpdb->update(
              $table,
              array( 'assigned_to' => $user_id, 'accepted' => 0 ),
              array( 'id' => $project_id ),
              array('%d', '%d'),
              array('%d')
         );
         if ( false !== $result ) {
              wp_send_json_success( __( 'Проект назначен пользователю', 'pmp' ) );
         } else {
              wp_send_json_error( __( 'Ошибка назначения проекта', 'pmp' ) );
         }
         wp_die();
    }
    
    public function ajax_update_project() {
         check_ajax_referer( 'pmp_frontend_nonce', 'nonce' );
         if ( ! is_user_logged_in() || ! current_user_can( 'pmp_assign_project' ) ) {
              wp_send_json_error( __( 'Доступ закрыт', 'pmp' ) );
         }
         $project_id   = intval( $_POST['project_id'] );
         $status       = sanitize_text_field( $_POST['status'] );
         $description  = sanitize_textarea_field( $_POST['description'] );
         $deadline     = sanitize_text_field( $_POST['deadline'] );
         
         global $wpdb;
         $table = $wpdb->prefix . 'pmp_projects';
         $result = $wpdb->update(
              $table,
              array(
                'status'      => $status,
                'description' => $description,
                'deadline'    => $deadline,
              ),
              array( 'id' => $project_id ),
              array('%s', '%s', '%s'),
              array('%d')
         );
         if ( false !== $result ) {
              wp_send_json_success( __( 'Данные проекта обновлены', 'pmp' ) );
         } else {
              wp_send_json_error( __( 'Ошибка обновления проекта', 'pmp' ) );
         }
         wp_die();
    }
    
    public function ajax_accept_project() {
         check_ajax_referer( 'pmp_frontend_nonce', 'nonce' );
         if ( ! is_user_logged_in() ) {
              wp_send_json_error( __( 'Вы не авторизованы', 'pmp' ) );
         }
         $project_id = intval( $_POST['project_id'] );
         global $wpdb;
         $table = $wpdb->prefix . 'pmp_projects';
         $result = $wpdb->update(
              $table,
              array( 'accepted' => 1 ),
              array( 'id' => $project_id ),
              array('%d'),
              array('%d')
         );
         if ( false !== $result ) {
              wp_send_json_success( __( 'Проект принят', 'pmp' ) );
         } else {
              wp_send_json_error( __( 'Ошибка принятия проекта', 'pmp' ) );
         }
         wp_die();
    }
    
    public function ajax_nopriv_callback() {
         wp_send_json_error( __( 'Вы не авторизованы', 'pmp' ) );
         wp_die();
    }
}