<?php
/**
 * The project metabox class.
 * Contains the render callback function and all related handlers.
 *
 * @since      1.0.0
 * @package    Project_Submission
 * @author     Vincent Dubroeucq <vincent@vincentdubroeucq.com>
 */
namespace Project_Submission;
class Project_Metabox {

    /**
     * The id of the metabox.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $id    The name of the metabox 
	 */
    protected $id;

    /**
     * The title of the metabox.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $title    The name of the metabox 
	 */
    protected $title;

    /**
     * The screen the metabox should appear on.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $screen    The admin screen the metabox should live in.
	 */
    protected $screen = null;
    
    /**
     * The context of the metabox on the edit page.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $context    The context of the metabox on the edit page: 'normal', 'side', or 'advanced'.
	 */
    protected $context = 'advanced';

    /**
     * The priority of the metabox on the edit page.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $priority    The priority of the metabox.
	 */
    protected $priority = 'default';

    /**
     * The callback args passed in to the callback function.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      array    $callback_args    An array of arguments passed in to the callback.
	 */
    protected $callback_args = null;

    
    /**
	 * Define the initial properties of the class instance.
	 *
	 * @since    1.0.0
	 */
    public function __construct(){
        $this->id      = 'project_details';
        $this->title   = __( 'Project details', 'project-submission' );
        $this->screen  = 'ps_projects';
        $this->context = 'side';
    }


    /**
     * Hooks any functions needed
     *
     * @since    1.0.0
	 * @access   public
     **/
    public function register_hooks(){
        add_action( 'add_meta_boxes', array( $this, 'register_metabox' ) );
        add_action( "save_post_{$this->screen}", array( $this, 'save' ), 10, 3 );
    }


    /**
	 * Registers our metabox
	 *
	 * @since    1.0.0
     * @access   public
	 */
	public function register_metabox() {
        add_meta_box( $this->id, $this->title, array( $this, 'callback' ), $this->screen, $this->context, $this->priority, $this->callback_args );
    }


    /**
     * Outputs the metabox's HTML
     *
     * @since    1.0.0
	 * @access   public
     * @param    $post   The post object currently being edited
     * @param    $args   Callback arguments
     **/
    public function callback( $post, $args = null ){
        wp_nonce_field( 'projects_metabox_save', 'projects_metabox_nonce' );
        $status     = ps_get_project_status( $post->ID, false );
        $timeframe  = ps_get_project_timeframe( $post->ID, false );
        $budget     = ps_get_project_budget( $post->ID, false );
        $start_date = ps_get_project_start_date( $post->ID, false );
        $formatted_start_date = ! empty( $start_date ) ? date( 'Y-m-d' , $start_date ) : '';
        include PROJECT_SUBMISSION_PATH . 'inc/templates/admin/project-metabox.php';
    }


    /**
     * Saves metabox content
     *
     * @since    1.0.0
	 * @access   public
     * 
     * @param    $post_id   The ID of thepost being saved
     * @param    $post      The post object
     * @param    $update    Whether the post is being updated.
     **/
    public function save( $post_id, $post, $update ){
        
        // Check user's permissions.
        if ( ! current_user_can( 'edit_post', $post_id ) ) {
            return;
        }

        // Check nonce and update
        if ( isset( $_POST['projects_metabox_nonce'] ) && wp_verify_nonce( $_POST['projects_metabox_nonce'], 'projects_metabox_save' ) ) {
            $new_status     = ( isset( $_POST['status'] ) ) ? sanitize_key( $_POST['status'] ) : '';
            $new_timeframe  = ( isset( $_POST['timeframe'] ) ) ? sanitize_key( $_POST['timeframe'] ) : '';
            $new_budget     = ( isset( $_POST['budget'] ) ) ? sanitize_key( $_POST['budget'] ) : '';
            $new_start_date = ( isset( $_POST['start_date'] ) ) ? strtotime( $_POST['start_date'] ) : '';
            update_post_meta( $post_id, 'status', $new_status );
            update_post_meta( $post_id, 'timeframe', $new_timeframe );
            update_post_meta( $post_id, 'budget', $new_budget );
            update_post_meta( $post_id, 'start_date', $new_start_date );
        }
    }

}