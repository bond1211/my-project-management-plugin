<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class PMP_Access {
    public function __construct() {
         add_action( 'init', array( $this, 'register_custom_capabilities' ) );
    }
    
    public function register_custom_capabilities() {
         $roles_caps = array(
             'head_of_department' => array(
                 'pmp_create_project' => true,
                 'pmp_assign_project' => true,
                 'pmp_manage_tasks'   => true,
             ),
             'director' => array(
                 'pmp_create_project' => true,
                 'pmp_assign_project' => true,
             ),
             'group_manager' => array(
                 'pmp_assign_project' => true,
                 'pmp_manage_tasks'   => true,
             ),
         );
         foreach( $roles_caps as $role_name => $caps ) {
             $role = get_role( $role_name );
             if( $role ) {
                 foreach( $caps as $cap => $grant ) {
                     $role->add_cap( $cap, $grant );
                 }
             }
         }
    }
}