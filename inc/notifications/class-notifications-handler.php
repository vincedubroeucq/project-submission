<?php
/**
 * The Notifications handler class.
 * Responsible for managing comments for the projects post type
 *
 * @since      1.0.0
 * @package    Project_Submission
 * @author     Vincent Dubroeucq <vincent@vincentdubroeucq.com>
 */
namespace Project_Submission;
class Notifications_Handler implements Handler {

    /**
     * Register all the hooks needed for the handler to run
     *
     * @since     1.0.0
	 * @access   public
     **/
    public function register_hooks(){
        add_action( 'ps_new_user_processed', array( $this, 'new_user_notification' ), 10, 1 );
        add_action( 'ps_new_project_processed', array( $this, 'new_project_notification' ), 10, 1 );
        add_action( 'comment_post', array( $this, 'new_comment_notification' ), 10, 3 );
    }


    /**
     * Sends a notification to admin when a new user is created.
     *
     * @since    1.0.0
	 * @access   public
	 * @param    int   $new_user_id   The ID of the newly registered user.    
     **/
    public function new_user_notification( $new_user_id ){
        wp_new_user_notification( $new_user_id );
    }


    /**
     * Sends a notification to admin when a new project is submitted.
     *
     * @since    1.0.0
	 * @access   public
	 * @param    int   $new_project_id   The ID of the newly submitted project.    
     **/
    public function new_project_notification( $new_project_id ){
        $message = sprintf(
            __( "A new project has been submitted.\n\rView project : %s", 'project_submission'),
            esc_url( get_permalink( $new_project_id ) )
        );
        wp_mail( get_option( 'admin_email' ), __( 'New project submitted', 'project-submission' ) , $message );
    }

    /**
     * Sends a notification to admin or post author when a comment is submitted on a project.
     *
     * @since    1.0.0
	 * @access   public
	 * @param    int   $new_comment_id   The ID of the newly submitted comment.    
	 * @param    bool  $approved         Whether the comment is approved or not.    
	 * @param    int   $comment_data     Data associated with the comment.    
     **/
    public function new_comment_notification( $new_comment_id, $approved, $comment_data ){
        if( 'project-discussion' !== $comment_data['comment_type'] ){
            return ;
        }

        $notification_recipient = ( get_option( 'admin_email' ) === $comment_data['comment_author_email'] ) ? $comment_data['comment_author_email'] : get_option( 'admin_email' );
        $notification_message = sprintf(
            __( "A new message has been submitted.\n\rAuthor : %s\n\rMessage : %s \n\rView project : %s", 'project_submission'),
            esc_html( $comment_data['comment_author'] ),
            esc_html( $comment_data['comment_content'] ),
            esc_url( get_permalink( $comment_data['comment_post_ID'] ) )
        );
        wp_mail( $notification_recipient, __( 'New comment submitted', 'project-submission' ) , $notification_message );
    }
    
}