<?php
/**
 * The Project Comments handler class.
 * Responsible for managing comments for the projects post type
 *
 * @since      1.0.0
 * @package    Project_Submission
 * @author     Vincent Dubroeucq <vincent@vincentdubroeucq.com>
 */
namespace Project_Submission;
class Project_Comments_Handler implements Handler {

    /**
     * Register all the hooks needed for the handler to run
     *
     * @since     1.0.0
	 * @access   public
     **/
    public function register_hooks(){
        // Comment markup
        add_action( 'comment_form_before', array( $this, 'comment_form_before' ) );
        add_action( 'comment_form_after', array( $this, 'comment_form_after' ) );
        add_filter( 'comment_form_field_comment', array( $this, 'add_file_field' ), 999, 1 );
        add_filter( 'comment_text', array( $this, 'display_attachements' ), 10, 3 );

        // Comment processing
        add_filter( 'preprocess_comment', array( $this, 'set_comment_type' ), 10, 1 );
        add_action( 'comment_post', array( $this, 'process_attachment' ), 10, 3 );

        // Comment display
        add_filter( 'get_avatar_comment_types', array( $this, 'allow_avatar' ), 10, 1 );
        add_filter( 'wp_list_comments_args', array( $this, 'comment_list_args' ), 10, 1 );
        add_filter( 'comment_reply_link', array( $this, 'remove_reply_link' ), 10, 4 );

        // Admin tweaks
        add_filter( 'admin_comment_types_dropdown', array( $this, 'comment_types_dropdown' ) );
        add_filter( 'comments_open', array( $this, 'open_comment_form' ), 10, 2 );
        add_filter( 'option_thread_comments', array( $this, 'thread_comments' ), 10, 2 );

        // Notifications
        add_filter( 'notify_post_author', array( $this, 'notify_post_author' ), 10, 2 );
        add_filter( 'notify_moderator', array( $this, 'notify_moderator' ), 10, 2 );        
    }


    /**
     * Buffering the output of the comment form, to be able to manipulate it afterwards.
     *
     * @since     1.0.0
	 * @access   public
     **/
    public function comment_form_before(){
        if ( is_singular( 'ps_projects' ) ){
            ob_start();
        }
    }


    /**
     * Filtering the output of the comment form, to add proper enctype attribute.
     *
     * @since     1.0.0
	 * @access   public
     **/
    function comment_form_after(){
        if ( is_singular( 'ps_projects' ) ){
            $comment_form = ob_get_clean();
            echo str_replace('<form ','<form enctype="multipart/form-data" ', $comment_form );
        }
    }


    /**
     * Adds a input field to upload a project file in the standard comment form
     *
     * @since   1.0.0
     * @access  public
     * @param   array  $fields  Default comment fields.
     * @return  array  $fields  New comment fields.
     **/
    public function add_file_field( $field ){
        if ( is_singular( 'ps_projects' ) ){
            $project_field = '<p class="comment-form-project-files">';
            $project_field .= '<label for="project-files">' . esc_html__( 'Attachments', 'project-submission' ) . '</label>';
            $project_field .= '<input type="file" name="project-files[]" accept="application/pdf, image/png, image/jpeg" multiple />';
            $project_field .= '</p>';
            $field .= $project_field; 
        }
        return $field;
    }


    /**
     * Displays attachements to the comment if any
     *
     * @since   1.0.0
     * @access  public
     * @param   string      $comment_text  Comment content.
     * @param   WP_Comment  $comment       Comment object.
     * @param   array       $args          Comment arguments.
     * @return  string      $comment_text  Comment content, with list of files appended.
     **/
    public function display_attachements( $comment_text, $comment, $args ){
        
        // Do not filter non-existing yet comments, or when we're not on the front end.
        if ( null === $comment || ! is_singular( 'ps_projects' ) ){
            return $comment_text;
        }

        $attachments = ps_get_attachments( $comment->comment_ID );

        if ( ! empty ( $attachments ) ){
            $comment_text .= sprintf(
                '<div class="project-submission-attachments"><strong>%s</strong>%s</div>',
                esc_html__( 'Attachments', 'project-submission' ),
                $attachments
            );
        }

        return $comment_text;
    }


    /**
     * Sets any project comment type to 'project-discussion'.
     *
     * @since   1.0.0
	 * @access  public
     * @param   array  $comment_data  Data about to be inserted.
     * @return  array  $comment_data  Data to be inserted.
     **/
    public function set_comment_type( $comment_data ){
        if ( 'ps_projects' === get_post_type( (int) $comment_data['comment_post_ID'] ) ){
            $comment_data['comment_type'] = 'project-discussion';
        }
        return $comment_data;
    }


    /**
     * Processes any files attached to a comment after it is inserted in db.
     *
     * @since   1.0.0
	 * @access  public
     * @param   int    $comment_id    ID of the comment.
     * @param   bool   $approved      Whether the comment has been approved.
     * @param   array  $comment_data  Comment data.
     **/
    public function process_attachment( $comment_id, $comment_approved, $comment_data ){
        if( 'project-discussion' !== $comment_data['comment_type'] ){
            return false;
        }
                
        // Process submitted files
        if ( ! empty( $_FILES['project-files'] ) ) {
            $file_handler   = new File_Handler( 'upload', 'project-files' );
            $errors         = array();
            $project_id     = (int) $comment_data['comment_post_ID'];
            $uploaded_files = $file_handler->validate_project_files( $_FILES['project-files'], $errors );
            
            if ( ! empty( $uploaded_files ) ){
                $admin_path = str_replace( get_bloginfo( 'url' ) . '/', ABSPATH, get_admin_url() );
                require_once $admin_path . '/includes/file.php';
                $comment_files = $file_handler->upload_project_files( $uploaded_files, $project_id, $errors );
                update_comment_meta( $comment_id, '_ps_comment_files', $comment_files );
            }
        }

    }


    /**
     * Set the comment type of a project comment about to be inserted in db.
     * Process any file attached.
     *
     * @since   1.0.0
     * @access  public
     * @param   array  $types  Registered comment types.
     * @return  array  $types  Registered comment types, with project-discussion added.
     **/
    public function allow_avatar( $types ){
        $types[] = 'project-discussion';
        return $types;
    }


    /**
     * Add 'project-discussion' to the comment type dropdown in the admin
     *
     * @since   1.0.0
     * @access  public
     * @param   array  $types  Registered comment types.
     * @return  array  $types  New comment types.
     **/
    public function comment_types_dropdown( $types ){
        $types['project-discussion'] = __( 'Project Discussion', 'project-submission' );
        return $types;
    }


    /**
    * Adjust display of comments on projects pages.
    *
    * @since     1.0.0
    * @param   array   $args     Arguments passed to the wp_comment_list function.
    * @return  array   $args     Modified arguments for the comment list
    */
	public function comment_list_args( $args ) {
        if ( 'ps_projects' === get_post_type() ){
            $args['type'] = 'project-discussion';
            $args['reverse_top_level'] = true;
            $args['reverse_children']  = true;
            if( ! is_user_logged_in() ){
                $args['echo'] = false;
            }
        }
        return $args;
    }


    /**
    * Get rid of the comment reply link, to avoid nested comments.
    *
    * @since   1.0.0
    * @param   string  $link     The link to display.
    * @param   array   $args     Arguments passed to the comment_reply_link function.
    * @param   string  $comment  The current comment object 
    * @param   string  $post     The current post object
    * @return  string  $link     The link to display.
    */
	public function remove_reply_link( $link, $args, $comment, $post ) {
        if ( is_singular( 'ps_projects' ) ){
            return '';
        }
        return $link;
    }


    /**
    * Open comments on projects page if user is logged in.
    *
    * @since   1.0.0
    * @param   bool   $open     Current comment status for the project.
    * @param   int    $post_id  ID of the current post.
    * @return  bool   $open     True if we're on a project page and the user is logged in.
    */
	public function open_comment_form( $open, $post_id ) {
        if( 'ps_projects' === get_post_type( $post_id ) ){
            return ps_can_user_view( $post_id );
        }
        return $open;
    }


    /**
     * Disable threaded comments on projects.
     *
     * @param   string  $value   The value of the 'threaded_comments' option.
     * @param   string  $option  The name of the option
     * @return  string  $value   False on single project pages.
     * @since   1.0.0
	 * @access  public
     **/
    public function thread_comments( $value, $option ){
        if ( is_singular( 'ps_projects' ) ) {
            return false;
        }
        return $value;
    }


    /**
     * Notify post author when a new comment is posted.
     *
     * @since   1.0.0
	 * @access  public
     * @param   bool  $maybe_notify  Whether to notify post author (WordPress setting)
     * @param   int   $comment_id    ID of the comment just being posted.
     * @return  bool  $maybe_notify  True if its a project discussion.
     **/
    public function notify_post_author( $maybe_notify, $comment_id ){
        if( 'project-discussion' === get_comment_type( $comment_id ) ){
            return true;
        }
        return $maybe_notify;
    }


    /**
     * Notify moderator or admin when a new comment is posted.
     *
     * @since   1.0.0
	 * @access  public
     * @param   bool  $maybe_notify  Whether to notify moderator (WordPress setting)
     * @param   int   $comment_id    ID of the comment just being posted.
     * @return  bool  $maybe_notify  True if its a project discussion.
     **/
    public function notify_moderator( $maybe_notify, $comment_id ){
        if( 'project-discussion' === get_comment_type( $comment_id ) ){
            if ( get_comment_author_email( $comment_id ) !== get_option( 'admin_email' ) ){
                return true;
            }
        }
        return $maybe_notify;
    }

}