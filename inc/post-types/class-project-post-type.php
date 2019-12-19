<?php
/**
 * The project post types handler class.
 * Responsible for registering the project post type, and managing other features of this post type.
 *
 * @since      1.0.0
 * @package    Project_Submission
 * @author     Vincent Dubroeucq <vincent@vincentdubroeucq.com>
 */
namespace Project_Submission;
class Project_Post_Type implements Handler {

    /**
     * The post type's arguments.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      array    $args    An array of arguments for our project post type. 
	 */
	protected $args;
    
    
    /**
	 * Define the initial properties of the class instance.
	 *
	 * @since     1.0.0
	 */
	public function __construct() {
		$this->args = array(
            'labels' => array(
                'name'                  => _x( 'Projects', 'Post type general name', 'project-submission' ),
                'singular_name'         => _x( 'Project', 'Post type singular name', 'project-submission' ),
                'menu_name'             => _x( 'Projects', 'Admin Menu text', 'project-submission' ),
                'name_admin_bar'        => _x( 'Project', 'Add New on Toolbar', 'project-submission' ),
                'add_new'               => __( 'Add New', 'project-submission' ),
                'add_new_item'          => __( 'Add New Project', 'project-submission' ),
                'new_item'              => __( 'New Project', 'project-submission' ),
                'edit_item'             => __( 'Edit Project', 'project-submission' ),
                'view_item'             => __( 'View Project', 'project-submission' ),
                'all_items'             => __( 'All Projects', 'project-submission' ),
                'search_items'          => __( 'Search Projects', 'project-submission' ),
                'parent_item_colon'     => __( 'Parent Projects:', 'project-submission' ),
                'not_found'             => __( 'No projects found.', 'project-submission' ),
                'not_found_in_trash'    => __( 'No projects found in Trash.', 'project-submission' ),
                'archives'              => _x( 'Projects archives', 'The post type archive label used in nav menus. Default “Post Archives”. Added in 4.4', 'project-submission' ),
                'insert_into_item'      => _x( 'Insert into project', 'Overrides the “Insert into post”/”Insert into page” phrase (used when inserting media into a post). Added in 4.4', 'project-submission' ),
                'uploaded_to_this_item' => _x( 'Uploaded to this project', 'Overrides the “Uploaded to this post”/”Uploaded to this page” phrase (used when viewing media attached to a post). Added in 4.4', 'project-submission' ),
                'filter_items_list'     => _x( 'Filter projects list', 'Screen reader text for the filter links heading on the post type listing screen. Default “Filter posts list”/”Filter pages list”. Added in 4.4', 'project-submission' ),
                'items_list_navigation' => _x( 'projects list navigation', 'Screen reader text for the pagination heading on the post type listing screen. Default “Posts list navigation”/”Pages list navigation”. Added in 4.4', 'project-submission' ),
                'items_list'            => _x( 'projects list', 'Screen reader text for the items list heading on the post type listing screen. Default “Posts list”/”Pages list”. Added in 4.4', 'project-submission' ),
            ),
            'description'         => __( 'All projects you submitted.', 'project-submission' ),
            'public'              => true,
            'exclude_from_search' => true,
            'menu_icon'           => 'dashicons-portfolio',
            'supports'            => array( 'title', 'editor', 'comments', 'author', 'excerpt' ),
            'taxonomies'          => array( 'ps_project_type' ),
        );
    }


    /**
     * Register all the hooks needed for the handler to run
     *
     * @since     1.0.0
	 * @access   public
     **/
    public function register_hooks(){
        add_action( 'init', array( $this, 'register_post_type' ) );
        add_action( 'manage_ps_projects_posts_columns', array( $this, 'ps_projects_admin_columns' ) );
        add_action( 'manage_ps_projects_posts_custom_column', array( $this, 'ps_projects_admin_columns_content' ), 10, 2 );
        add_filter( 'next_post_link', array( $this, 'posts_link' ), 10, 5 );
        add_filter( 'previous_post_link', array( $this, 'posts_link' ), 10, 5 );
        add_filter( 'the_content', array( $this, 'content' ), 10, 1 );
    }
   

    /**
	 * Gets the arguments of the project post type
	 *
	 * @since     1.0.0
	 * @return    array   An array of arguments
	 */
	public function get_args() {
		return apply_filters( 'ps_projects_post_type_args', $this->args );
    }


    /**
     * Registers our post types
     *
     * @since     1.0.0
	 * @access   public
     **/
    public function register_post_type(){
        register_post_type( 'ps_projects', $this->get_args() );
    }
    

    /**
	 * Adds new columns in the admin for our projects custom post type
	 *
	 * @since     1.0.0
     * @param     array   $columns   The registered columns
	 * @return    array   $columns   An array of columns to display
	 */
	public function ps_projects_admin_columns( $columns ) {
        $columns['date']       = __( 'Date submitted', 'project-submission' );
        $columns['timeframe']  = __( 'Timeframe', 'project-submission' );
        $columns['budget']     = __( 'Budget', 'project-submission' );
        $columns['status']     = __( 'Status', 'project-submission' );
        $columns['start_date'] = __( 'Start Date', 'project-submission' );
        return $columns;
    }
    

    /**
	 * Adds columns content in the admin table for our projects custom post type
	 *
	 * @since     1.0.0
     * @param     string   $column_name   The name of the column
     * @param     int      $post_id       The id of the post
	 */
	public function ps_projects_admin_columns_content( $column_name, $post_id ) {
        switch ( $column_name ) {
            case 'timeframe':
                echo esc_html( ps_get_project_timeframe( $post_id ) );
                break;
            case 'budget':
                echo esc_html( ps_get_project_budget( $post_id ) );
                break;
            case 'status':
                echo esc_html( ps_get_project_status( $post_id ) );
                break;
            case 'start_date':
                echo esc_html( ps_get_project_start_date( $post_id ) );
                break;
            default:
                break;
        }
    }


    /**
    * Get rid of the posts navigation on single post views.
    *
    * @since     1.0.0
    * @param     $output   The actual html displayed on the page.
    * @return    $output   Empty string if on a project page.
    */
	public function posts_link( $output, $format, $link, $post, $adjacent ) {
        if ( 'ps_projects' === get_post_type( $post ) ){
            return '';
        }
        return $output;
    }


    /**
	 * Appends data about the project before the main description.
	 *
	 * @since     1.0.0
     * @param     $content       The description of the project, as typed in by user.
     * @return    $content       The description of the project, with a details box appended.
	 */
	public function content( $content ) {
        if ( 'ps_projects' !== get_post_type() ){
            return $content;
        }

        if( ! ps_can_user_view() ){
           return __( 'You\'re not authorized to see this content', 'project-submission' );
        }

        // Load our custom styles
        wp_enqueue_style( 'project-submission-form-styles', PROJECT_SUBMISSION_URL . 'assets/css/front.css' );
        
        // Prepare our project data
        $project_id   = get_the_ID();
        $project_data = array(
            __( 'Project type', 'project-submission')  => esc_html( join( ', ', wp_list_pluck( get_the_terms( $project_id, 'ps_project_type' ) , 'name' ) ) ),
            __( 'Budget', 'project-submission')        => ps_get_project_budget( $project_id ),
            __( 'Timeframe ', 'project-submission')    => ps_get_project_timeframe( $project_id ),
            __( 'Start date', 'project-submission')    => ps_get_project_start_date( $project_id ),
            __( 'Status', 'project-submission')        => ps_get_project_status( $project_id ),
        );
        $project_files = ps_get_project_files( $project_id );

        ob_start();
        include( ps_locate_template( 'frontend/project-details.php' ) );
        $project_details = ob_get_clean();

        return $project_details . $content;
    }

}