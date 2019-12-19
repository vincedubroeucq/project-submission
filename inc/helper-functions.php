<?php
/**
 * This file contains templating and other helper functions, available globally.
 *
 * @since      1.0.0
 * @package    Project_Submission
 * @author     Vincent Dubroeucq <vincent@vincentdubroeucq.com>
 */


/**
 * Retrieves the path to a template, looking first in the child theme, then parent theme
 *
 * @since    1.0.0
 * @param    string/array   $template_names   Names of the template you're looking for
 * @return   string         $path             Relative path to the template, either in the child theme, parent theme, or this plugin's template folder 
 **/
function ps_locate_template( $template_names ){
    $path = '';
    
    foreach ( (array) $template_names as $template_name ) {
        if ( ! $template_name )
            continue;
        if ( file_exists( STYLESHEETPATH . '/project-submission/templates/' . $template_name )) {
            $path = STYLESHEETPATH . '/project-submission/templates/' . $template_name;
            break;
        } elseif ( file_exists( TEMPLATEPATH . '/project-submission/templates/' . $template_name ) ) {
            $path = TEMPLATEPATH . '/project-submission/templates/' . $template_name;
            break;
        } elseif ( file_exists( PROJECT_SUBMISSION_TEMPLATE_PATH . $template_name ) ) {
            $path = PROJECT_SUBMISSION_TEMPLATE_PATH . $template_name;
            break;
        }
    }
    
    return $path;
}


/**
 * Returns an array with all front-end messages
 *
 * @since    1.0.0
 * @return   array   $messages   An array of all front-end messages.
 **/
function ps_get_notices(){
    $messages = array(
        'success' => array(
            'success' => true,
            'message' => __( 'Done successfully !', 'project-submission' ),
        ),
        'error' => array(
            'success' => false,
            'message' => __( 'There was an error. Please try again.', 'project-submission' ),
        ),
        'missing-field' => array(
            'success' => false,
            'message' => __( 'Please fill in all the required fields', 'project-submission' ),
        ),
        'invalid-email' => array(
            'success' => false,
            'message' => __( 'The email provided is not valid.', 'project-submission' ),
        ),
        'already-registered' => array(
            'success' => false,
            'message' => __( 'This user is already registered.', 'project-submission' ),
        ),
        'invalid-file-type' => array(
            'success' => false,
            'message' => __( 'The file you\'re trying to upload is invalid.', 'project-submission' ),
        ),
        'file-too-large' => array(
            'success' => false,
            'message' => __( 'The file you\'re trying to upload is too large.', 'project-submission' ),
        ),
        'no-agreement' => array(
            'success' => false,
            'message' => __( 'It looks like you don\'t agree with our privacy policy.', 'project-submission' ),
        ),
        'signup-failed' => array(
            'success' => false,
            'message' => __( 'There was a problem with your account submission. Please try again.', 'project-submission' ),
        ),
        'signup-success' => array(
            'success' => true,
            'message' => __( 'You\'ve successfully signed up. You\'re ready to submit your first project !', 'project-submission' ),
        ),
        'check-email' => array(
            'success' => false,
            'message' => __( 'You should have received your new password. Please check your email and try to log in again.', 'project-submission' ),
        ),
        'login-failed' => array(
            'success' => false,
            'message' => sprintf(
                __( 'Sorry, there was a problem. We couldn\'t log you in. Please try again or <a href="%s">ask for a new password.</a>', 'project-submission' ),
                esc_url( wp_lostpassword_url() ) 
            ),
        ),
        'login-success' => array(
            'success' => true,
            'message' => sprintf(
                __( 'You\'ve successfully logged in. Now you can <a href="%s">submit your project</a>', 'project-submission' ),
                esc_url( ps_get_page_url( 'project' ) ) 
            ),
        ),
        'logout-success' => array(
            'success' => true,
            'message' => __( 'You\'re successfully logged out.', 'project-submission' ),
        ),
        'submission-success' => array(
            'success' => true,
            'message' => __( 'Your project was submitted successfully !', 'project-submission' ),
        ),
        'submission-failed' => array(
            'success' => false,
            'message' => __( 'There was a problem with your submission. Please try again.', 'project-submission' ),
        ),
        
    );
    return apply_filters( 'ps_notices', $messages );
}


/**
 * Returns a single notice
 *
 * @since    1.0.0
 * @return   array   $message   The message for the given message key, or a default error message.
 **/
function ps_get_notice( $key = '' ){
    $messages = ps_get_notices();
    if ( ! empty( $key ) && isset( $messages[ $key ] ) ){
        return $messages[ $key ];
    }
    return $messages['error'];
}


/**
 * Displays form error/success messages, if any
 *
 * @since    1.0.0
 **/
function ps_notice(){
    if( ! empty( $_GET['message'] ) ){
        $message = ps_get_notice( sanitize_key( $_GET['message'] ) );
        $class   = isset( $message['success'] ) && ( true == $message['success'] ) ? 'ps-success' : 'ps-error'; 
        $message = $message['message'];
        echo "<p class='{$class}'>{$message}</p>";
    }
}


/**
 * Gets the page URL to the passed in page.
 *
 * @since    1.0.0
 * @param    string    $page   Page you're looking for. 
 * @return   string    $url    URL to the page with the corresponding shortcode set in the settings, or home_url. 
 **/
function ps_get_page_url( $page = '' ){
    $url = home_url();

    if ( ! empty( $page ) && in_array( $page, array( 'project', 'signup', 'login', 'dashboard' ) ) ){
        if ( $page_id = get_option( 'ps-pages' )["{$page}-page"] ){
            $page_url = get_permalink( $page_id );
            if ( $page_url ){
                $url = $page_url;
            }
        }
    }

    return esc_url( $url );
}


/**
 * Gets a project specific data.
 *
 * @since    1.0.0
 * @param    int      $project_id   Id of the project you need data from.
 * @param    string   $field        Field you need the data for.
 * @param    string   $display      If true, then label is returned. Else the key.
 * @return   string                 Key/label for the field, or empty string. 
 **/
function ps_get_project_field( $project_id, $field, $display = true ){
    
    $field_value = get_post_meta( $project_id, $field, true );
    $key = '';
    $options = array();
    
    switch ( $field ) {
        case 'status':
            $options = ps_get_project_statuses();
            break;
        case 'timeframe':
            $options = ! empty( get_option( 'ps-forms' )['timeframe-options'] ) ? get_option( 'ps-forms' )['timeframe-options'] : '';
            break;
        case 'budget':
            $options = ! empty( get_option( 'ps-forms' )['budget-options'] ) ? get_option( 'ps-forms' )['budget-options'] : '';
            break;
        case 'start_date':
            return $display && ! empty ( $field_value ) ? date_i18n( get_option( 'date_format' ), $field_value ) : $field_value ;
            break;
        default:
            $options = array();
            break;
    }

    if( ! empty( $field_value ) && array_key_exists( $field_value, $options ) ){
        $key = $display ? $options[$field_value] : $field_value;
    }

    return $key;

}


/**
 * Gets all the available project statuses and their label.
 *
 * @since    1.0.0
 * @return   array    Array of available statuses. 
 **/
function ps_get_project_statuses(){
    return apply_filters( 'ps_available_project_statuses', array(
        'new'         => __( 'New', 'project-submission' ),
        'in-progress' => __( 'In progress', 'project-submission' ),
        'on-hold'     => __( 'On hold', 'project-submission' ),
        'complete'    => __( 'Complete', 'project-submission' ),
        'cancelled'   => __( 'Cancel', 'project-submission' ),
    ) );
}


/**
 * Gets all the project timeframe options and their label.
 *
 * @since    1.0.0
 * @return   array    Array of available timeframe options. 
 **/
function ps_get_project_timeframe_options(){
    return ! empty( get_option( 'ps-forms' )['timeframe-options'] ) ? get_option( 'ps-forms' )['timeframe-options'] : '';
}


/**
 * Gets all the project budget options and their label.
 *
 * @since    1.0.0
 * @return   array    Array of available budget options. 
 **/
function ps_get_project_budget_options(){
    return ! empty( get_option( 'ps-forms' )['budget-options'] ) ? get_option( 'ps-forms' )['budget-options'] : '';
}


/**
 * Gets the status of the passed in project.
 *
 * @since    1.0.0
 * @param    int      $project_id   Id of the project you need status for.
 * @param    string   $display      Context for the status. If true, then label is returned.
 * @return   string                 Key/label for the status, or empty string. 
 **/
function ps_get_project_status( $project_id, $display = true ){
    return ps_get_project_field( $project_id, 'status', $display);
}


/**
 * Gets the start date of the passed in project.
 *
 * @since    1.0.0
 * @param    int      $project_id   Id of the project you need status for.
 * @param    string   $display      Context for the status. If true, then label is returned.
 * @return   string                 Key/label for the status, or empty string. 
 **/
function ps_get_project_start_date( $project_id, $display = true ){
    return ps_get_project_field( $project_id, 'start_date', $display);
}


/**
 * Gets the budget of the passed in project.
 *
 * @since    1.0.0
 * @param    int      $project_id   Id of the project you need budget for.
 * @param    string   $display      Context for the budget. If true, then label is returned.
 * @return   string                 Key/label for the budget, or empty string. 
 **/
function ps_get_project_budget( $project_id, $display = true ){
    return ps_get_project_field( $project_id, 'budget', $display);
}


/**
 * Gets the timeframe of the passed in project.
 *
 * @since    1.0.0
 * @param    int      $project_id   Id of the project you need timeframe for.
 * @param    string   $display      Context for the timeframe. If true, then label is returned.
 * @return   string                 Key/label for the timeframe, or empty string. 
 **/
function ps_get_project_timeframe( $project_id, $display = true ){
    return ps_get_project_field( $project_id, 'timeframe', $display);
}


/**
 * Gets the project files of the passed in project.
 *
 * @since    1.0.0
 * @param    int      $project_id   Id of the project you need files for.
 * @param    string   $display      Context for the timeframe. If true, then label is returned.
 * @return   string                 Html for the list of files, or empty string. 
 **/
function ps_get_project_files( $project_id, $display = true ){
    
    $project_files = get_post_meta( $project_id, '_ps_project_files' , true );

    if ( $display ) {
        return ps_file_list_html( $project_files, $project_id );
    } else {
        return $project_files;
    }

}


/**
 * Gets the attachments for a given project comment.
 *
 * @since    1.0.0
 * @param    int      $comment_id   Id of the comment you need attachment for.
 * @param    string   $display      Context for the timeframe. If true, then label is returned.
 * @return   string                 Key/label for the timeframe, or empty string. 
 **/
function ps_get_attachments( $comment_id, $display = true ){
    $attachments = get_comment_meta( $comment_id, '_ps_comment_files', true );
    
    if ( $display ){
        return ps_file_list_html( $attachments, $comment_id, 'ps_attachment' );
    } else {
        return $attachments;
    }

}



/**
 * Returns an unordered list of files, in a download link
 *
 * @since    1.0.0
 * @param    array    $files    Files to list
 * @return   string             Html unordered list 
 **/
function ps_file_list_html( $files, $project_or_comment_id, $type = 'ps_project_file' ){
    $html = '';

    if( ! empty( $files ) ){
        $html = '<ul>';
            foreach ( $files as $filekey => $filedata) {
                switch ( $type ) {
                    case 'ps_attachment':
                        $url = ps_get_attachment_link_url( $filekey, $project_or_comment_id );
                        break;
                    case 'ps_project_file':
                        $url = ps_get_project_file_link_url( $filekey, $project_or_comment_id );
                        break;
                }
                $html .= '<li><a href="' . esc_url( $url ) . '" target="_blank">' . esc_html( basename( $filedata['file'] ) ) . '</a></li>';
            }
        $html .= '</ul>';
    }

    return $html;  
}


/**
 * Gets a single project file data.
 *
 * @since    1.0.0
 * @param    int      $project_id   Id of the project you need the file from.
 * @param    string   $filekey      Key of the file.
 * @return   array                  File data. 
 **/
function ps_get_project_file_data( $project_id, $filekey ){

    $project_files = ps_get_project_files( $project_id, false );

    if ( array_key_exists( $filekey, $project_files ) ){
        return $project_files[$filekey];
    }

    return false;
}


/**
 * Gets a single comment attachment file data.
 *
 * @since    1.0.0
 * @param    int      $comment_id   Id of the comment you need the attachment from.
 * @param    string   $filekey      Key of the file.
 * @return   array                  File data. 
 **/
function ps_get_attachment_data( $comment_id, $filekey ){

    $attachments = ps_get_attachments( $comment_id, false );

    if ( array_key_exists( $filekey, $attachments ) ){
        return $attachments[$filekey];
    }

    return false;
}


/**
 * Gets a single project file link tag for a passed in project_id and filekey.
 *
 * @since    1.0.0
 * @param    string  $filekey      Key of the file to get.
 * @param    int     $project_id   Id of the project to get file from
 * @return   string  $url          URL to the file, with parameters added.
 **/
function ps_get_project_file_link_url( $filekey, $project_id ){

    if( ! ps_can_user_view( $project_id ) ){
        return false;
    }

    $url = add_query_arg( array(
        'action'      => 'download',
        'project_id'  => $project_id,
        'filekey'     => $filekey,
        'nonce'       => wp_create_nonce( 'download' ),
    ), admin_url( 'admin-post.php' ) );

    return $url;

}


/**
 * Gets a single comment attachment link tag for a passed in comment_id and filekey.
 *
 * @since    1.0.0
 * @param    string  $filekey      Key of the file to get.
 * @param    int     $comment_id   Id of the comment to get file from
 * @return   string  $url          URL to the file, with parameters added.
 **/
function ps_get_attachment_link_url( $filekey, $comment_id ){

    $url = add_query_arg( array(
        'action'      => 'download',
        'comment_id'  => $comment_id,
        'filekey'     => $filekey,
        'nonce'       => wp_create_nonce( 'download' ),
    ), admin_url( 'admin-post.php' ) );

    return $url;

}


/**
 * Returns whether the current user can view a project.
 *
 * @since    1.0.0
 * @param    int  $project_id  ID of the project you want to check permissions for.
 * @return   bool              True if the current user can view the project or false. 
 **/
function ps_can_user_view( $project_id = null ){

    if( ! $project_id ){
        $project_id = get_the_ID();
    }

    if( current_user_can( 'project-owner' ) && get_post_field( 'post_author', $project_id ) == get_current_user_id() ){
        return true;
    }

    if( current_user_can( 'administrator' ) ){
        return true;
    }

    return false;

}


/**
 * Gets the latest project message.
 *
 * @since    1.0.0
 * @param    int   $project_id  ID of the project you want to check permissions for.
 * @return   string             Latest message of the project discussion. 
 **/
function ps_get_latest_message( $project_id = null ){

    if( ! ps_can_user_view( $project_id ) ){
        return false;
    }

    if( ! $project_id ){
        $project_id = get_the_ID();
    }

    $message = __( 'No discussion yet.', 'project-submission' );

    $comments = get_comments( apply_filters( 'ps_latest_message_args' , array(
        'number' => 1,
        'post_id' => $project_id,
        'type' => 'project-discussion',
        'fields' => 'ids',
    ) ) );

    if( ! empty( $comments ) ){
        $message = get_comment_text( $comments[0] );
    }

    return $message;
}   