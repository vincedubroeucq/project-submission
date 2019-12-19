<?php
/**
 * The Project form shortcode
 * Defines the shortcode callback and any helpers.
 *
 * @since      1.0.0
 * @package    Project_Submission
 * @author     Vincent Dubroeucq <vincent@vincentdubroeucq.com>
 */
namespace Project_Submission;
class Project_Form extends Shortcode {    

    /**
	 * Define the initial properties of the class instance.
	 *
	 * @since    1.0.0
	 */
    public function __construct(){
        $this->tag = 'project_form';
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

		// Load our form styles and scripts
		wp_enqueue_style( 'ps-form-styles', PROJECT_SUBMISSION_URL . 'assets/css/front.css' );
		
		// Prepare any variable we need in our template and load the form template
		$settings = get_option( 'ps-forms' );
		$types    = get_terms( array(
			'taxonomy' => 'ps_project_type',
			'hide_empty' => false,
		) );
		
		ob_start();
		include( ps_locate_template( 'frontend/project-form.php' ) );
		return ob_get_clean();
	}
	

	/**
	 * Outputs a notice on the project form
	 * 
	 * @since    1.0.0
	 * @access   public
	 **/
	function project_form_notice(){
		if ( ! is_user_logged_in() && ! isset( $_GET['message'] ) ) : ?> 
			<p class="project-submission-notice">
                <?php printf(
                    __( 'It looks like you are <strong>not logged in</strong>. Please <a href="%1$s">login</a> first if you already have an account on this site, or <a href="%2$s">create an account.</a>', 'project-submission' ),
					esc_url( wp_login_url() ),
					esc_url( wp_registration_url() )
				);
                ?>
            </p>
		<?php endif;
	}

	
	/**
     * Hooks any functions needed
     *
     * @since    1.0.0
	 * @access   public
     **/
    public function register_hooks(){
		add_action( 'init', array( $this, 'register_shortcode' ) );
		add_action( 'ps_before_project_form', array( $this, 'project_form_notice' ) );
		
		// The same notice is used on the dashboard, if accessed while not logged in.
		add_action( 'ps_before_dashboard', array( $this, 'project_form_notice' ) );
	}

}