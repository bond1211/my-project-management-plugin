<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class PMP_Activation {
    public static function activate() {
        global $wpdb;
        $charset_collate = $wpdb->get_charset_collate();

        // Таблица проектов с новыми полями: created_by и accepted.
        $table_projects = $wpdb->prefix . 'pmp_projects';
        $sql_projects = "CREATE TABLE $table_projects (
              id mediumint(9) NOT NULL AUTO_INCREMENT,
              project_name varchar(255) NOT NULL,
              description TEXT NOT NULL,
              deadline DATETIME NOT NULL,
              status varchar(50) NOT NULL,                   -- active | future | closed
              urgency_level varchar(50) NOT NULL,
              created_by bigint(20) NOT NULL,                -- ID пользователя, создавшего проект
              assigned_to bigint(20) NOT NULL,               -- ID пользователя, которому назначен проект
              accepted TINYINT(1) DEFAULT 1,                 -- 1: принят, 0: не принят (назначенному)
              created_at datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
              PRIMARY KEY  (id)
         ) $charset_collate;";

        // Таблица задач с полями для описания, списка исполнителей (JSON) и дат.
        $table_tasks = $wpdb->prefix . 'pmp_tasks';
        $sql_tasks = "CREATE TABLE $table_tasks (
              id mediumint(9) NOT NULL AUTO_INCREMENT,
              project_id mediumint(9) NOT NULL,
              task_name varchar(255) NOT NULL,
              description TEXT NOT NULL,
              urgency_level varchar(50) NOT NULL,
              executors TEXT NOT NULL,                       -- JSON-сериализованный массив ID пользователей
              status varchar(50) NOT NULL,                     -- active | closed
              received_date datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
              start_date datetime DEFAULT '0000-00-00 00:00:00',
              actual_finish_date datetime DEFAULT '0000-00-00 00:00:00',
              review_date datetime DEFAULT '0000-00-00 00:00:00',
              gip_date datetime DEFAULT '0000-00-00 00:00:00',
              PRIMARY KEY  (id)
         ) $charset_collate;";

        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        dbDelta( $sql_projects );
        dbDelta( $sql_tasks );
        
        // Регистрируем роли (если они еще не существуют)
        add_role( 'head_of_department', 'Начальник отдела', array(
            'read'               => true,
            'manage_module'      => true,
            'pmp_create_project' => true,
            'pmp_assign_project' => true,
            'pmp_manage_tasks'   => true,
        ) );
        add_role( 'director', 'Руководитель направления', array(
            'read'               => true,
            'manage_projects'    => true,
            'pmp_create_project' => true,
            'pmp_assign_project' => true,
        ) );
        add_role( 'group_manager', 'Руководитель группы', array(
            'read'               => true,
            'assign_architect'   => true,
            'pmp_assign_project' => true,
            'pmp_manage_tasks'   => true,
        ) );
        add_role( 'architect', 'Архитектор', array(
            'read' => true,
        ) );
    }
}