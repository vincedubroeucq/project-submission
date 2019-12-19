<?php
/**
 * Plugin Name:       Project Submission
 * Plugin URI:        https://vincentdubroeucq.com/project-submission/
 * Description:       Allows your clients to submit projects, and you to keep all communication with the client on your site. No more searching your inbox.
 * Version:           1.0.0
 * Author:            Vincent Dubroeucq
 * Author URI:        https://vincentdubroeucq.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       project-submission
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
defined( 'ABSPATH' ) or die( 'Cheatin\' uh?' );

/**
 * Define a few helper constants used throughout the plugin
 */
define( 'PROJECT_SUBMISSION_PATH', plugin_dir_path( __FILE__ ) );
define( 'PROJECT_SUBMISSION_URL', plugin_dir_url( __FILE__ ) );
define( 'PROJECT_SUBMISSION_TEMPLATE_PATH', plugin_dir_path( __FILE__ ) . '/inc/templates/' );


register_activation_hook( __FILE__, 'ps_activation' );
/**
 * The code that runs during plugin activation.
 */
function ps_activation() {

    // Create our new roles and capabilities
    require_once plugin_dir_path( __FILE__ ) . 'inc/users/class-roles-handler.php';
    $roles_handler = new Project_Submission\Roles_Handler();
    $roles_handler->register_roles();
    
    // Register our custom post types and taxonomies on activation, the flush rewrite rules.
    require_once plugin_dir_path( __FILE__ ) . 'inc/interface-handler.php';
    require_once plugin_dir_path( __FILE__ ) . 'inc/post-types/class-project-type-taxonomy.php';
    require_once plugin_dir_path( __FILE__ ) . 'inc/post-types/class-project-post-type.php';
    $taxonomies_handler = new Project_Submission\Project_Type_Taxonomy();
    $post_types_handler = new Project_Submission\Project_Post_Type();
    $taxonomies_handler->register_taxonomies();
    $post_types_handler->register_post_type();
    flush_rewrite_rules();
}


register_deactivation_hook( __FILE__, 'ps_deactivation' );
/**
 * The code that runs during plugin deactivation.
 */
function ps_deactivation() {
    // De-registering roles should be done on UNINSTALL !
    require_once plugin_dir_path( __FILE__ ) . 'inc/users/class-roles-handler.php';
    $roles_handler = new Project_Submission\Roles_Handler();
    $roles_handler->deregister_roles();
    $roles_handler->remove_custom_roles_from_users();
}


/**
 * Load our core class, and run our plugin
 */
require_once plugin_dir_path( __FILE__ ) . 'inc/class-project-submission.php';
$project_submission = new Project_Submission\Project_Submission();
$project_submission->run();