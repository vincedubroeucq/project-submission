<?php
/**
 * The abstract class for the form handlers.
 *
 * @since      1.0.0
 * @package    Project_Submission
 * @author     Vincent Dubroeucq <vincent@vincentdubroeucq.com>
 */
namespace Project_Submission;
abstract class Form_Handler implements Handler {

    /**
     * Register all the hooks needed for the manager to run
     *
     * @since    1.0.0
	 * @access   public
     **/
    abstract public function register_hooks();
    
    
    /**
     * Processes the form
     *
     * @since    1.0.0
	 * @access   public
     **/
    abstract public function process_form();


    /**
     * Handles redirection to form if there's an error
     *
     * @since    1.0.0
     * @access   public
     **/
    public function redirect_to_form( $error_code = '' ){
        $redirect = ! empty( $_POST['_wp_http_referer'] ) ? $_POST['_wp_http_referer'] : home_url();
        if ( ! empty ( $error_code ) ){
            $redirect = add_query_arg( 'message', urlencode( $error_code ) , $redirect );
        }
        wp_safe_redirect( esc_url( $redirect ) );
        exit;
    }

}