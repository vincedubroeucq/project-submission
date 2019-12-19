<?php
/**
 * The Login form shortcode
 * Defines the shortcode callback and any helpers.
 *
 * @since      1.0.0
 * @package    Project_Submission
 * @author     Vincent Dubroeucq <vincent@vincentdubroeucq.com>
 */
namespace Project_Submission;
class Login_Form extends Shortcode {    

    /**
	 * Define the initial properties of the class instance.
	 *
	 * @since     1.0.0
	 */
    public function __construct(){
        $this->tag = 'login_form';
    }
    
    
    /**
     * Outputs the shortcode's HTML
     * Displays the login form, lost password form, or project form if the user is already logged in.
     * 
     * @since     1.0.0
	 * @access   public
	 * 
	 * @param    array    $atts      An array of attributes passed in to the shortcode
	 * @param    string   $content   The content enclosed within the shortcodes opening and closing tags.
	 * @param    string   $tag       The tag of the shortode itself.
	 * @return   string   $html      The output of the shortcode
     **/
    public function callback( $atts = array(), $content = null, $tag = '' ){
		
		// Load our form styles
        wp_enqueue_style( 'ps-form-styles', PROJECT_SUBMISSION_URL . 'assets/css/front.css' );

        ob_start();
        include( ps_locate_template( 'frontend/login-form.php' ) );
		return ob_get_clean();

	}
	

	/**
	 * Outputs a notice on the login form
	 * 
	 * @since     1.0.0
	 * @access   public
	 **/
	public function login_form_notice(){
        if ( is_user_logged_in() && ! isset( $_GET['message'] ) ) : ?>
            <p class="project-submission-notice">
                <?php printf(
                    __( 'It looks like you are <strong>already logged in</strong>. You can <a href="%1$s">submit a project</a>, or <a href="%2$s">continue browsing the site</a>.', 'project-submission' ),
                    esc_url( ps_get_page_url( 'project' ) ),
                    esc_url( home_url() ) );
                ?>
            </p> 
        <?php endif;
    }


    /**
	 * Defines the base login URL.
     * 
     * @since    1.0.0
	 * @access   public
	 *
	 * @param   string   $login_url      The URL for login.
     * @param   string   $redirect       The URL to redirect back to upon successful login.
     * @param   bool     $force_reauth   Whether to force reauthorization, even if a cookie is present.
	 */
    public function login_url( $login_url, $redirect, $force_reauth ){
        $pages_settings = get_option( 'ps-pages' );
        if ( ! empty( $pages_settings['login-page'] ) ){
            $pagelink = get_permalink( $pages_settings['login-page'] );
            if ( $pagelink ){
                $login_url = esc_url( $pagelink );
            }
        }
        return $login_url;
    }


    /**
	 * Defines the successful login redirect URL.
     * Basically redirects to the same page with '?message=login-success' query arg
     * 
     * @since   1.0.0
	 * @access  public
	 *
	 * @param   string   $redirect_to   URL to redirect to.
     * @param   string   $referrer      URL the user is coming from.
     * @param   object   $user          Logged user's data.
	 */
    public function login_redirect( $redirect_to, $referrer, $user ){
        $pages_settings = get_option( 'ps-pages' );
        if ( ! empty( $pages_settings['login-page'] ) ){
            $redirect_to = esc_url( add_query_arg( 'message', 'login-success', ps_get_page_url( 'dashboard' ) ) );
        }
        return $redirect_to;
    }


    /**
	 * Defines the login failed redirect URL.
	 * 
     * @since    1.0.0
	 * @access   public
     * 
     * @param   string   $username   The username/email that failed.
	 */
    public function failed_redirect( $username ){
        $pages_settings = get_option( 'ps-pages' );
        if ( ! empty( $pages_settings['login-page'] ) ){
            wp_safe_redirect( esc_url( add_query_arg( 'message', 'login-failed', wp_login_url() ) ) );
            exit;
        }
    }
    

    /**
	 * Defines the lost password URL.
     * The lost password form is handled by the login_form shortcode
     * 
     * @since   1.0.0
	 * @access  public
	 *
	 * @param   string   $lostpassword_url   URL to redirect to.
     * @param   string   $redirect           The path to redirect to
	 */
    function lost_password_url( $lostpassword_url, $redirect ) {
        $pages_settings = get_option( 'ps-pages' );
        if ( ! empty( $pages_settings['login-page'] ) ){
            $lostpassword_url = esc_url( add_query_arg( 'action', 'lostpassword', wp_login_url() ) );
        }
        return $lostpassword_url;
    }


    /**
	 * Defines the lost password URL redirect.
     * 
     * @since   1.0.0
	 * @access  public
	 *
	 * @param   string   $lostpassword_redirect   URL to redirect after submitting a new password request.
	 */
    function lost_password_redirect( $lostpassword_redirect ) {
        $pages_settings = get_option( 'ps-pages' );
        if ( ! empty( $pages_settings['login-page'] ) ){
            $lostpassword_redirect = esc_url( wp_login_url() );
        }
        return $lostpassword_redirect;
    }


    /**
	 * Defines the log out redirect URL.
     * 
     * @since   1.0.0
	 * @access  public
	 *
	 * @param   string    $redirect_to             The redirect destination URL.
	 * @param   string    $requested_redirect_to   The requested redirect destination URL passed as a parameter.
	 * @param   WP_User   $user                    The WP_User object for the user that's logging out.
	 */
    public function logout_redirect( $redirect_to, $requested_redirect_to, $user ){
        return esc_url( add_query_arg( 'message', 'logout-success', home_url() ) );
    }

	
	/**
     * Hooks any functions needed
     *
     * @since     1.0.0
	 * @access   public
     **/
    public function register_hooks(){
        add_action( 'init', array( $this, 'register_shortcode' ) );
		add_action( 'ps_before_login_form', array( $this, 'login_form_notice' ) );
        add_filter( 'login_url', array( $this, 'login_url'), 10, 3 );
        add_filter( 'login_redirect', array( $this, 'login_redirect'), 10, 3 );
        add_action( 'wp_login_failed', array( $this, 'failed_redirect' ), 10, 1 );
        add_filter( 'lostpassword_url', array( $this, 'lost_password_url' ), 10, 2 );
        add_filter( 'lostpassword_redirect', array( $this, 'lost_password_redirect' ), 10, 1 );
        add_filter( 'logout_redirect', array( $this, 'logout_redirect' ), 10, 3 );
    }

}