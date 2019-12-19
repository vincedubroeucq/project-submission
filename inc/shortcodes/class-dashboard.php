<?php
/**
 * The Dashboard form shortcode
 * Defines the shortcode callback and any helpers.
 *
 * @since      1.0.0
 * @package    Project_Submission
 * @author     Vincent Dubroeucq <vincent@vincentdubroeucq.com>
 */
namespace Project_Submission;
class Dashboard extends Shortcode {

    /**
	 * Define the initial properties of the class instance.
	 *
	 * @since    1.0.0
	 */
    public function __construct(){
        $this->tag = 'dashboard';
    }

    /**
     * Outputs the shortcode's HTML
     *
     * @since    1.0.0
	 * @access   public
	 * 
	 * @param     array    $atts      An array of attributes passed in to the shortcode
	 * @param     string   $content   The content enclosed within the shortcodes opening and closing tags.
	 * @param     string   $tag       The tag of the shortode itself.
	 * @return    string   $html      The output of the shortcode
     **/
    public function callback( $atts = array(), $content = null, $tag = '' ){		
        
        // Load our form and notice styles
        wp_enqueue_style( 'ps-form-styles', PROJECT_SUBMISSION_URL . 'assets/css/front.css' );

        // Prepare list of projects
        $user_projects = get_posts( array(
            'post_type'   => 'ps_projects',
            'numberposts' => -1,
            'author'      => get_current_user_id(),
            'fields'      => 'ids',
        ) );

        ob_start();
        include( ps_locate_template( 'frontend/dashboard.php' ) );
        return ob_get_clean();
    }

    /**
     * Hooks any functions needed
     *
     * @since    1.0.0
	 * @access   public
     **/
    public function register_hooks(){
        add_action( 'init', array( $this, 'register_shortcode' ) );
    }

}