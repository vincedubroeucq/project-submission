<?php
/**
 * The signup form handler class.
 * Responsible for handling front-end user signup form submissions.
 *
 * @since      1.0.0
 * @package    Project_Submission
 * @author     Vincent Dubroeucq <vincent@vincentdubroeucq.com>
 */
namespace Project_Submission;
class Signup_Form_Handler extends Form_Handler {

    /**
     * Register all the hooks needed for the manager to run
     *
     * @since    1.0.0
	 * @access   public
     **/
    public function register_hooks(){
        add_action( 'admin_post_nopriv_signup', array( $this, 'process_form' ) );
    }


    /**
     * Handles the form submission
     *
     * @since    1.0.0
	 * @access   public
     **/
    public function process_form(){

        $errors = array();

        // Check our nonce
        if ( ! isset ( $_POST['project-submission-signup-nonce'] ) || ! wp_verify_nonce( $_POST['project-submission-signup-nonce'], 'project-submission-signup' ) ){
            $errors[] = 'invalid-nonce';
        }

        // Check the user is not already logged in or it's not a bot submission
        if ( is_user_logged_in() || ! empty( $_POST['honeyfield'] ) ){
            $errors[] = 'unauthorized';
        }

        // Check our required inputs are not empty
        if ( empty( $_POST['username'] ) || empty( $_POST['email'] ) || empty( $_POST['password'] ) ) {
            $errors[] = 'missing-field';
        }

        // Check the user agreed with RGPD if page was setup
        if ( (int) get_option( 'wp_page_for_privacy_policy' ) && empty( $_POST['privacy'] ) ){
            $errors[] = 'no-agreement';
        }

        // Check the email is valid
        if ( ! is_email( $_POST['email'] ) ){
            $errors[] = 'invalid-email';
        }

        // Check the user doesn't already exist
        if ( username_exists( $_POST['username'] ) || email_exists( $_POST['email'] ) ){
            $errors[] = 'already-registered';
        }

        // Check the filetype and size of the submitted file.
        $file_handler = new File_Handler( 'upload', 'avatar' );
        $valid_avatar = $file_handler->validate_avatar( $errors );

        // If we have any errors, return to the form.
        if( ! empty( $errors ) ){
            $this->redirect_to_form( $errors[0] );
        }

        // Now we can process our inputs
        $data = array(
            'user_login'  => sanitize_text_field( trim( $_POST['username'] ) ),
            'user_email'  => sanitize_email( trim( $_POST['email'] ) ),
            'user_pass'   => sanitize_text_field( trim( $_POST['password'] ) ),
        );
        
        // Allow other plugins to manipulate data before creating the user
        $data = apply_filters( 'ps_signup_form_data', $data );
        
        // Create our user
        $new_user_id = wp_insert_user( $data );
        if ( is_wp_error( $new_user_id ) ){
            $this->redirect_to_form( 'signup-failed' );
        }
            
        // Set him his role.
        $user = get_user_by( 'ID', $new_user_id );
        $user->set_role( 'project-owner' );

        // Upload the avatar.
        if ( $valid_avatar ){
            $avatar_id = $file_handler->upload_avatar( $new_user_id );
        }
        
        // Create the user's unique key.
        update_user_meta( $new_user_id, '_ps_custom_key', wp_generate_password() );

        // Do you need to do something with the user too ?
        do_action( 'ps_new_user_processed', $new_user_id );
        
        // Log him in
		$logged_user = wp_signon( array(
            'user_login'    => $data['user_login'],
            'user_password' => $data['user_pass'],
            'remember'      => true,
        ) );

        // If we couldn't log him in, redirect to the login page
        if ( is_wp_error( $logged_user ) ){
            wp_safe_redirect( esc_url( add_query_arg( 'message', 'login-failed', wp_login_url() ) ) );
            exit;
        }

        // Redirect to the project form page.
        wp_safe_redirect( esc_url( add_query_arg( 'message', 'signup-success', ps_get_page_url( 'project' ) ) ) );
        exit;
    }

}