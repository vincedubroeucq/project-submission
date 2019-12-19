<?php
/**
 * The File Handler class.
 * Responsible for handling front-end file uploading and downloading
 *
 * @since      1.0.0
 * @package    Project_Submission
 * @author     Vincent Dubroeucq <vincent@vincentdubroeucq.com>
 */
namespace Project_Submission;
class File_Handler implements Handler {

    /**
     * Action performed for this file
     *
     * @since    1.0.0
	 * @access   private
     * @var      string    $action    Determines what the user is trying to do with the file.
     **/
    private $action;

    /**
     * Type of the file being uploaded or downloaded.
     *
     * @since    1.0.0
	 * @access   private
     * @var      string    $context    Determines what type of file is being uploaded or downloaded
     **/
    private $type;


    /**
     * Mime types accepted for the upload action.
     *
     * @since    1.0.0
	 * @access   private
     * @var      array    $mimes    Determines the accepted mime types.
     **/
    private $mimes;


    /**
	 * Define the initial properties of the class instance.
	 *
	 * @since     1.0.0
     * @param  string  $context  The context the file is uploaded in. Determines the type of files to validate and where to upload it.
     */
    public function __construct( $action = 'download', $type = 'project-files' ){
        $this->action  = $action;
        $this->type    = $type;
        $this->mimes   = $this->set_mimes();
    }


    /**
     * Register all the hooks needed for the manager to run
     *
     * @since    1.0.0
	 * @access   public
     **/
    public function register_hooks(){
        add_filter( 'upload_dir', array( $this, 'user_upload_dir' ),10, 1 );
        add_filter( 'get_avatar_url', array( $this, 'custom_avatar' ), 10, 3 );
        add_action( 'admin_post_download', array( $this, 'deliver_file' ) );
    }


    /**
	 * Sets the mime type for the type of files being uploaded.
	 *
	 * @since  1.0.0
     * @param  string  $context  The context the file is uploaded in. Determines the type of files to validate and where to upload it.
     */
    public function set_mimes(){
        switch ( $this->type ) {
            case 'avatar':
                return array( 'image/jpg', 'image/jpeg', 'image/png' );
                break;
            default:
                return array( 'image/jpg', 'image/jpeg', 'image/png', 'application/pdf' );
                break;
        }
    }


    /**
	 * Retrieves the mime type for the type of files being uploaded.
	 *
	 * @since  1.0.0
     * @param  string  $context  The context the file is uploaded in. Determines the type of files to validate and where to upload it.
     */
    public function get_mimes(){
        return apply_filters( 'ps_allowed_mimes', $this->mimes );
    }


    /**
     * Filters the upload dir when processing project files.
     *
     * @since    1.0.0
	 * @access   public
     * @param    array   $upload_dir   Array of upload directory data
     * @return   array   $upload_dir   Array of modified upload directory data
     **/
    public function user_upload_dir( $upload_dir ){
        
        // Only modify when processing user form.
        if( defined( 'IS_PROCESSING_PROJECT_FILES' ) && is_int( IS_PROCESSING_PROJECT_FILES ) ){
            $project_id = IS_PROCESSING_PROJECT_FILES;
            $user_id    = get_post_field( 'post_author', $project_id, 'raw' );
            if ( $user_id && ps_can_user_view( $project_id ) ){
                $project_folder = $this->get_project_folder( $project_id );
                if( $project_folder ){
                    $upload_dir['subdir'] = $project_folder;
                    $upload_dir['path']   = $upload_dir['basedir'] . $upload_dir['subdir'];
                    $upload_dir['url']    = $upload_dir['baseurl'] . $upload_dir['subdir'];
                }
            }
        }
        
        return $upload_dir;

    }


    /**
     * Gets the path to a user's project folder from a passed in project_id.
     *
     * @since    1.0.0
	 * @access   public
     * @param    int      $project_id      ID of the project you need the folder from.
     * @return   string   $project_folder  Path to the user's file folder, or false.
     **/
    public function get_project_folder( $project_id ){
        $project_folder = false;
        
        if ( is_int( $project_id ) ) {
            $user_id     = get_post_field ( 'post_author', $project_id, 'raw' );
            $user_folder = $this->get_user_folder( $user_id );
            if( $user_folder ){
                $project_slug   = get_post_field( 'post_name', $project_id );
                $project_folder = $user_folder . $project_slug;
            }
        }
        return $project_folder;
    }


    /**
     * Gets the unique path to the user folder.
     *
     * @since    1.0.0
	 * @access   public
     * @param    int      $user_id         ID of the user you need the folder from.
     * @return   string   $user_folder     Path to the user's file folder, or false.
     **/
    public function get_user_folder( $user_id ){
        $user_folder = false;
        if ( (int) $user_id ){
            $key      = $this->get_key( (int) $user_id );
            $username = get_userdata( $user_id )->user_login;
            if( $key ){
                $hash = md5( $key . $username );
                $user_folder = trailingslashit( '/project-submission/' . $hash );
            }
        }
        return $user_folder;
    }


    /**
     * Gets the unique key associated with that user.
     *
     * @since    1.0.0
	 * @access   public
     * @param    int      $user_id      ID of the user you need the key from.
     * @return   string   $user_key     Unique key for the user, or false.
     **/
    public function get_key( $user_id ){
        if ( (int) $user_id ){
            $key = get_user_meta( (int) $user_id, '_ps_custom_key', true );
            return $key;
        } 
        return false;
    }


    /**
     * Filters the avatar if a custom avatar has been uploaded
     *
     * @since    1.0.0
	 * @access   public
     * @param    string  $url           URL of the avatar to retrieve. Default to gravatar or default avatar.
     * @param    mixed   $id_or_email   The Gravatar to retrieve. Accepts a user_id, gravatar md5 hash,
     *                                  user email, WP_User object, WP_Post object, or WP_Comment object.
     * @param    array   $args          Arguments passed to get_avatar_data(), after processing.
     * @return   string  $url           The url to the custom avatar, if any.
     * 
     **/
    public function custom_avatar( $url, $id_or_email, $args ){
        $user_id = $this->get_user_id_from_id_or_email( $id_or_email );
        if ( $user_id && ! empty( $avatar_id = get_user_meta( $user_id, 'ps_custom_avatar_id', true ) ) ){
            $avatar = wp_get_attachment_image_src( $avatar_id );
            $url = $avatar[0];
        }
        return $url;
    }


    /**
     * Tries to get the user id from id, email, or content object passed in.
     *
     * @since    1.0.0
	 * @access   public
     * @param    mixed   $id_or_email   The Gravatar to retrieve. Accepts a user_id, gravatar md5 hash,
     *                                  user email, WP_User object, WP_Post object, or WP_Comment object.
     * @return   int     $user_id       The id of the user if registered.
     **/
    function get_user_id_from_id_or_email( $id_or_email ){
    
        // Process the user identifier.
        if ( is_numeric( $id_or_email ) ) {
            return (int) $id_or_email;
        } elseif ( is_string( $id_or_email ) && ! strpos( $id_or_email, '@md5.gravatar.com' ) ) {
            // email address
            $user = get_user_by( 'email', $id_or_email );
            if ( $user && isset( $user->ID ) ){
                return (int) $user->ID;
            }
        } elseif ( $id_or_email instanceof WP_User ) {
            // User Object
            return (int) $id_or_email->ID;
        } elseif ( $id_or_email instanceof WP_Post ) {
            // Post Object
            return (int) $id_or_email->post_author;
        } elseif ( is_object( $id_or_email ) && isset( $id_or_email->comment_ID ) ) {
            if ( ! empty( $id_or_email->user_id ) ) {
                return (int) $id_or_email->user_id;
            }
            if ( ! empty( $id_or_email->comment_author_email ) ) {
                $user = get_user_by( 'email', $id_or_email->comment_author_email );
                if ( $user && isset( $user->ID ) ){
                    return (int) $user->ID;
                }
            }
        }
    
        return false;
    
    }



     /**
     * Admin-post script that handles single file delivery.
     * 
     * @since    1.0.0
	 * @access   public
     **/
    public function deliver_file(){
        
        // Basic security checks
        if( ! isset( $_GET['filekey'] ) || ! wp_verify_nonce( $_GET['nonce'], 'download' ) ){
            wp_die( __( 'You are not allowed to do that', 'project-submission' ) );
            exit;
        }

        $filekey   = sanitize_key( $_GET['filekey'] );
        $file_data = false;

        if( ! empty( $_GET['project_id'] ) ){
            $project_id = (int) $_GET['project_id'];
            $file_data  = ps_get_project_file_data( $project_id, $filekey );
        }

        if( ! empty( $_GET['comment_id'] ) ){
            $comment_id = (int) $_GET['comment_id'];
            $file_data  = ps_get_attachment_data( $comment_id, $filekey );
            $comment    = get_comment( $comment_id );
            $project_id = (int) $comment->comment_post_ID;
        }

        if( ! $project_id ||  ! ps_can_user_view( $project_id )  ){
            wp_die( __( 'You are not allowed to do that', 'project-submission' ) );
            exit;
        }

        if( ! empty( $file_data ) && file_exists( $file = $file_data['file'] ) ){
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Transfer-Encoding: Binary');
            header('Content-Disposition: attachment; filename="'.basename( $file ).'"');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize( $file ) );
            while ( ob_get_level() > 0 ) {
                ob_end_clean();
            }
            readfile( $file );
            exit;
        }
        
        wp_safe_redirect( esc_url( wp_get_referer() . '?message=error-downloading' ) );
        exit;
    }


    /**
     * Handles the avatar file validation
     *
     * @since    1.0.0
	 * @access   public
     * @param    array   $errors   The errors array, build during form submission.
     * @return   bool              True if file is valid.
     **/
    public function validate_avatar( &$errors = array() ){      
        if( ! empty( $_FILES['avatar'] ) && empty( $_FILES['avatar']['error'] ) ){
            $file_data = wp_check_filetype( $_FILES['avatar']['name'] ); 
            if ( in_array( $file_data['type'], array( 'image/jpg', 'image/jpeg', 'image/png' ) ) ){
                $size = getimagesize( $_FILES['avatar']['tmp_name'] );
                if( $size && 200 >= $size[0] && 200 >= $size[1] ){
                    return true; 
                } else {
                    $errors[] = 'file-too-large';
                }
            } else {
                $errors[] = 'invalid-file-type';
            }
        }
        return false;
    }


    /**
     * Handles the project files validation
     *
     * @since    1.0.0
	 * @access   public
     * @param    array   $files    The files to upload, build during project form submission.
     * @param    array   $errors   The errors array, build during project form submission.
     * @return   bool              True if file(s) is(are) valid.
     **/
    public function validate_project_files( $files = array(), &$errors = array() ){
        
        $uploaded_files = array();
        if( ! empty( $files ) ){
            
            // Rearrange the files array
            foreach( $files as $key => $all ){
                foreach( $all as $i => $val ){
                    $uploaded_files[$i][$key] = $val;    
                }    
            }
            
            // Validate our files before uploading them
            foreach ( $uploaded_files as $filedata ) {
                switch ( $filedata['error'] ) {
                    case 0:
                        $wp_filetype = wp_check_filetype_and_ext( $filedata['tmp_name'], $filedata['name'] );
                        if ( ! in_array( $wp_filetype['type'], $this->get_mimes() ) ){
                            $errors[] = 'invalid-file-type';
                            return false;
                        }
                        $filedata['name'] = sanitize_file_name( $filedata['name'] );
                        break;
                    case 4:
                        return false;
                        break;
                    default:
                        $errors[] = 'upload-error';
                        return false;
                    break;
                }              
            }
        }

        return $uploaded_files;

    }


    /**
     * Handles the avatar file upload
     *
     * @since    1.0.0
	 * @access   public
     * @param    int   $user_id    The ID of the user to upload avatar for.
     * @return   int               ID of the uploaded file, or false.
     **/
    public function upload_avatar( $user_id = null ){
        $custom_avatar_id = media_handle_upload( 'avatar', 0 );
        if( empty( $user_id ) ){
            $user_id = get_current_user_id();
        }
        if ( is_int( $custom_avatar_id ) ) {
            update_user_meta( $user_id, 'ps_custom_avatar_id', $custom_avatar_id );
        }
        return $custom_avatar_id;
    }


     /**
     * Handles the project files uploading
     *
     * @since    1.0.0
	 * @access   public
     * @param    array   $files       The files to upload build during project form submission.
     * @param    int     $project_id  ID of the project to upload for.
     * @param    array   $errors      The errors array, build during project form submission.
     * @return   bool                 True if file(s) is(are) valid.
     **/
    public function upload_project_files( $files, $project_id, &$errors = array() ){
        define( 'IS_PROCESSING_PROJECT_FILES', $project_id );
        $uploaded_files = array();
        foreach ( $files as $filedata ) {
            $uploaded_file = wp_handle_upload( $filedata, array( 'test_form' => false ) );
            if ( isset( $uploaded_file['error'] ) ){
                $errors[] = 'upload-error';
            } else {
                $file_key = md5( $uploaded_file['file'] );
                $uploaded_file['file_key'] = $file_key;
                $uploaded_files[$file_key] = $uploaded_file;
            }
        }
        return $uploaded_files;
    }

}