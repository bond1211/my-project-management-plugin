<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class PMP_Messages {
    public function __construct() {
         add_action( 'init', array( $this, 'register_message_post_type' ) );
         add_action( 'wp_ajax_pmp_send_message', array( $this, 'ajax_send_message' ) );
         add_action( 'wp_ajax_nopriv_pmp_send_message', array( $this, 'ajax_nopriv_callback' ) );
    }
    
    public function register_message_post_type() {
         $args = array(
             'public'    => false,
             'show_ui'   => true,
             'label'     => 'Сообщения',
             'supports'  => array( 'title', 'editor' ),
         );
         register_post_type( 'pmp_message', $args );
    }
    
    public function ajax_send_message() {
         check_ajax_referer( 'pmp_message_nonce', 'msg_nonce' );
         if ( ! current_user_can( 'read' ) ) {
             wp_send_json_error( __( 'Доступ закрыт', 'pmp' ) );
         }
         $message     = sanitize_textarea_field( $_POST['message'] );
         $object_type = sanitize_text_field( $_POST['object_type'] );
         $object_id   = intval( $_POST['object_id'] );
         
         $post_id = wp_insert_post( array(
             'post_type'    => 'pmp_message',
             'post_title'   => 'Сообщение от пользователя ' . get_current_user_id(),
             'post_content' => $message,
             'post_status'  => 'publish',
         ) );
         
         if ( $post_id ) {
             update_post_meta( $post_id, 'object_type', $object_type );
             update_post_meta( $post_id, 'object_id', $object_id );
             wp_send_json_success( array( 'post_id' => $post_id ) );
         } else {
             wp_send_json_error( __( 'Не удалось отправить сообщение', 'pmp' ) );
         }
         wp_die();
    }
    
    public function ajax_nopriv_callback() {
         wp_send_json_error( __( 'Вы не авторизованы', 'pmp' ) );
         wp_die();
    }
}