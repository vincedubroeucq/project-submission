<?php
/**
 * The Register form shortcode
 * Defines the shortcode callback and any helpers.
 *
 * @since      1.0.0
 * @package    Project_Submission
 * @author     Vincent Dubroeucq <vincent@vincentdubroeucq.com>
 */
namespace Project_Submission;
class Signup_Form extends Shortcode {    

    /**
	 * Define the initial properties of the class instance.
	 *
	 * @since    1.0.0
	 */
    public function __construct(){
        $this->tag = 'signup_form';
    }
    
    
    /**
     * Outputs the shortcode's HTML
     * Displays the register form if the user is not logged in.
     * 
     * @since    1.0.0
	 * @access   public
	 * 
	 * @param    array    $atts      An array of attributes passed in to the shortcode
	 * @param    string   $content   The content enclosed within the shortcodes opening and closing tags.
	 * @param    string   $tag       The tag of the shortode itself.
	 * @return   string   $html      The output of the shortcode
     **/
    public function callback( $atts = array(), $content = null, $tag = '' ){
		
		// Load our form styles and script.
        wp_enqueue_style( 'ps-form-styles', PROJECT_SUBMISSION_URL . 'assets/css/front.css' );
		wp_enqueue_script( 'ps-form-script', PROJECT_SUBMISSION_URL . 'assets/js/form.js', array(), null, true );

        ob_start();
        include( ps_locate_template( 'frontend/signup-form.php' ) );
		return ob_get_clean();

	}
	

	/**
	 * Outputs a notice on the signup form
	 * 
	 * @since    1.0.0
	 * @access   public
	 **/
	public function signup_form_notice(){
		if ( is_user_logged_in() ) : ?>
            <p class="ps-notice">
                <?php printf(
                    __( 'It looks like you are <strong>already registered and logged in.</strong> You can <a href="%1$s">submit a project</a>, or <a href="%2$s">continue browsing the site</a>.', 'project-submission' ),
                    esc_url( ps_get_page_url( 'project' ) ),
                    esc_url( home_url() ) );
                ?>
            </p> 
        <?php endif;
	}


	/**
	 * Defines the URL of the signup form
	 * 
	 * @since    1.0.0
	 * @access   public
	 * 
	 * @param   string   $register_url   The URL to the default registration form.
	 **/
	function signup_url( $register_url ){
		if ( get_option( 'ps-pages' )['signup-page'] ){
			$pagelink = get_permalink( get_option( 'ps-pages' )['signup-page'] );
            if ( $pagelink ){
				$register_url = esc_url( $pagelink );
            } 
		}
		return $register_url;
	}


	/**
     * Hooks any functions needed
     *
     * @since    1.0.0
	 * @access   public
     **/
    public function register_hooks(){
		add_action( 'init', array( $this, 'register_shortcode' ) );
		add_filter( 'register_url', array( $this, 'signup_url' ), 10, 1  );
		add_action( 'ps_before_signup_form', array( $this, 'signup_form_notice' ) );
    }

}