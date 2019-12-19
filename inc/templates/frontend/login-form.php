<?php
/**
 * The template for the login form.
 * 
 * You can easily override this template in your child theme.
 * First, create a 'project-submission' folder in your child theme's root directory
 * Then create a 'templates' folder in it and copy this file in this new folder.
 * The plugin will look for custom templates in the child theme first, then in the parent theme.
 * If no custom templates are found, the default template provided by the plugin will be used.
 * 
 * @since      1.0.0
 * @package    Project_Submission
 * @author     Vincent Dubroeucq <vincent@vincentdubroeucq.com>
 */
ps_notice();

do_action( 'ps_before_login_form' );

if( ! is_user_logged_in() ) {

    if ( isset( $_GET['action'] ) && 'lostpassword' === $_GET['action'] ){
        
        include( ps_locate_template( "frontend-forms/lostpassword-form.php" ) );
    
    } else {
   
        wp_login_form( array(
            'echo'           => true,
            'remember'       => true,
            'value_remember' => true,
        ) );
   
    }

}

do_action( 'ps_after_login_form' );