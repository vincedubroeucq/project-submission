<?php
/**
 * The Roles handler class.
 * Registers new roles and capabilities.
 *
 * @since      1.0.0
 * @package    Project_Submission
 * @author     Vincent Dubroeucq <vincent@vincentdubroeucq.com>
 */
namespace Project_Submission;
class Roles_Handler {

     /**
     * The roles to register.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      array    $roles    An array of custom roles to register. 
	 */
    protected $roles;
    

    /**
	 * Define the initial properties of the class instance.
	 *
	 * @since    1.0.0
	 */
	public function __construct( $roles = array() ) {
        $this->roles = array(
            'project-owner' => array(
                'role'         => 'project-owner',
                'display_name' => __( 'Project Owner', 'project-submission' ),
                'capabilities' => array(
                    'read' => true,
                    'level_0' => true,
                ),
            ),
        );
    }


    /**
	 * Gets the roles to register
	 *
	 * @since     1.0.0
	 * @return    array   An array of custom roles to register
	 */
	public function get_roles() {
		return $this->roles;
    }
    

    /**
     * Register all the roles in the db
     *
     * @since    1.0.0
	 * @access   public
     **/
    public function register_roles(){
        foreach ( $this->get_roles() as $key => $role_data ) {
            add_role( $role_data['role'], $role_data['display_name'], $role_data['capabilities'] );
            translate_user_role( $role_data['display_name'] );
        }
    }


    /**
     * Deregister all the roles in the db
     *
     * @since    1.0.0
	 * @access   public
     **/
    public function deregister_roles(){
        foreach ( $this->get_roles() as $key => $role_data ) {
            remove_role( $role_data['role'] );
        }
    }


    /**
     * Remove roles from users
     *
     * @since    1.0.0
	 * @access   public
     **/
    public function remove_custom_roles_from_users(){
        $roles = wp_list_pluck( $this->get_roles(), 'role' );
        
        // For each custom role, check if there are users with that role, and set them back to subscribers.
        foreach ( $roles as $key => $role ) {
            $users = get_users( array( 'role' => (array) $role ) );
            if ( ! empty( $users ) ){
                foreach ( $users as $user) {
                    $user->remove_role( $role );
                    $user->remove_cap( $role );
                    $user->set_role( get_option( 'default_role' ) );
                }
            }
        }
        
    }
           
}