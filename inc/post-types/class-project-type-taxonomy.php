<?php
/**
 * The taxonomies handler class.
 * Responsible for registering all of our custom taxonomies.
 *
 * @since      1.0.0
 * @package    Project_Submission
 * @author     Vincent Dubroeucq <vincent@vincentdubroeucq.com>
 */
namespace Project_Submission;
class Project_Type_Taxonomy implements Handler {

    /**
     * The taxonomies to register.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      array    $taxonomies    An array of custom taxonomies to register. 
	 */
	protected $taxonomies;
    
    
    /**
	 * Define the initial properties of the class instance.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		$this->taxonomies = array(
            'ps_project_type' => array(
                'post_type' => array( 'ps_projects' ),
                'args'      => array(
                    'labels'       => array(
                        'name'              => _x( 'Project types', 'taxonomy general name', 'project-submission' ),
                        'singular_name'     => _x( 'Project type', 'taxonomy singular name', 'project-submission' ),
                        'search_items'      => __( 'Search Project types', 'project-submission' ),
                        'all_items'         => __( 'All Project types', 'project-submission' ),
                        'parent_item'       => __( 'Parent Project type', 'project-submission' ),
                        'parent_item_colon' => __( 'Parent Project type:', 'project-submission' ),
                        'edit_item'         => __( 'Edit Project type', 'project-submission' ),
                        'update_item'       => __( 'Update Project type', 'project-submission' ),
                        'add_new_item'      => __( 'Add New Project type', 'project-submission' ),
                        'new_item_name'     => __( 'New Project type Name', 'project-submission' ),
                        'menu_name'         => __( 'Project types', 'project-submission' ),
                    ),
                    'description'       => __( 'Types of projects the visitors can submit.', 'project-submission'),
                    'public'            => true,
                    'hierarchical'      => true,
                    'show_tagcloud'     => false,
                    'show_admin_column' => true,
                ),
            ),
        );
    }
    

    /**
	 * Gets the taxonomies to register
	 *
	 * @since     1.0.0
	 * @return    array   An array of taxonomies to register
	 */
	public function get_taxonomies() {
		return apply_filters( 'ps_registered_taxonomies', $this->taxonomies );
    }

    
    /**
     * Register all the hooks needed for the handler to run
     *
     * @since    1.0.0
	 * @access   public
     **/
    public function register_hooks(){
        add_action( 'init', array( $this, 'register_taxonomies' ) );
    }


    /**
     * Registers our custom taxonomies
     *
     * @since    1.0.0
	 * @access   public
     **/
    public function register_taxonomies(){
        foreach ( $this->get_taxonomies() as $slug => $data) {
            register_taxonomy( $slug, $data['post_type'], $data['args'] );
        }
    }

}