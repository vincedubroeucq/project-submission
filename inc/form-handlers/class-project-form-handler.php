<?php
/**
 * The project form handler class.
 * Responsible for handling front-end project form submissions.
 *
 * @since      1.0.0
 * @package    Project_Submission
 * @author     Vincent Dubroeucq <vincent@vincentdubroeucq.com>
 */
namespace Project_Submission;
class Project_Form_Handler extends Form_Handler {

    /**
     * Register all the hooks needed for the manager to run
     *
     * @since    1.0.0
	 * @access   public
     **/
    public function register_hooks(){
        add_action( 'admin_post_new_project', array( $this, 'process_form' ) );
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
        if ( ! isset ( $_POST['project-submission-new-project-nonce'] ) || ! wp_verify_nonce( $_POST['project-submission-new-project-nonce'], 'project-submission-new-project' ) ){
            $errors[] = 'invalid-nonce';
        }

        // Check the user is logged in and is permitted to submit project
        if ( ! is_user_logged_in() || ! current_user_can( 'project-owner' ) || ! empty( $_POST['honeyfield'] ) ){
            $errors[] = 'unauthorized';
        }

        // Check our required inputs are not empty
        if ( empty( $_POST['title'] ) || empty( $_POST['description'] ) ) {
            $errors[] = 'missing-field';
        }

        // Validate our project files type
        $file_handler  = new File_Handler( 'upload', 'project-files' );
        $project_files = false;
        if ( ! empty( $_FILES['project-files'] ) ) {
            $project_files = $file_handler->validate_project_files( $_FILES['project-files'], $errors );
        }

        // If we have any errors, return to the form.
        if( ! empty( $errors ) ){
            $this->redirect_to_form( $errors[0] );
        }

        // Sanitize our form data
        $data = array(
            'title'       => wp_strip_all_tags( $_POST['title'] ),
            'type'        => ! empty( $_POST['type'] ) ? sanitize_key( $_POST['type'] ) : '',
            'description' => wp_kses_post( $_POST['description'] ) ,
            'timeframe'   => ! empty( $_POST['timeframe'] ) ? sanitize_key( $_POST['timeframe'] ) : '',
            'budget'      => ! empty( $_POST['budget'] ) ? sanitize_key( $_POST['budget'] ) : '',
        );

        // Allow other plugins to manipulate data before creating the post
        $data = apply_filters( 'ps_project_form_data', $data );

        // Now we can create our post
        $new_post_id = wp_insert_post( apply_filters( 'ps_new_project_args' , array(
            'post_title'   => $data['title'],
            'post_content' => $data['description'],
            'post_name'    => md5( $data['title'] . get_current_user_id() ),
            'post_author'  => get_current_user_id(),
            'post_type'    => 'ps_projects',
            'post_status'  => 'publish',
            'meta_input'   => array(
                'timeframe' => $data['timeframe'],
                'budget'    => $data['budget'],
                'status'    => 'new',
            ),
        ) ) );
        
        if ( ! $new_post_id ){
            $this->redirect_to_form( 'submission-failed' );
        }
        
        // Set the correct taxonomy term.
        $new_terms = wp_set_object_terms( $new_post_id, $data['type'], 'ps_project_type' );

        // Process uploaded files last.
        if ( ! empty( $project_files ) ){
            $uploaded_files = $file_handler->upload_project_files( $project_files, $new_post_id, $errors );
            update_post_meta( $new_post_id, '_ps_project_files', $uploaded_files );
        }

        // If you need to manipulate the post
        do_action( 'ps_new_project_processed', $new_post_id );

        // Redirect to 'Dashboard' page.
        $message = ! empty( $errors ) ? 'upload-error' : 'submission-success';
        $url     = add_query_arg( 'message', $message, ps_get_page_url( 'dashboard' ) );
        wp_safe_redirect( esc_url( $url ) );
        exit;

    }

}