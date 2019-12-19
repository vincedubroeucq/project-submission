<?php
/**
 * The Login settings section class
 * Register all the settings that should appear under the Forms tab and Login section, in the settings page.
 * Also defines handlers for these settings.
 *
 * @since      1.0.0
 * @package    Project_Submission
 * @author     Vincent Dubroeucq <vincent@vincentdubroeucq.com>
 */
namespace Project_Submission;
class Pages_Section extends Settings_Section {

    /**
	 * Define the initial properties of the class instance.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {		
		$this->id          = 'ps-pages';
        $this->title       = __( 'Pages settings', 'project-submission' );
        $this->description = __( 'This section defines settings related to where the forms are located.', 'project-submission' );
        $this->page        = 'project-submission-settings';
        $this->defaults    = array();
        $this->settings    = array(
            'login-page'    => array(
                'name'        => 'login-page',
                'title'       => __( 'Login page', 'project-submission' ),
                'callback'    => array( $this, 'page_select_field' ),
                'args'        => array(
                    'label_for'   => 'ps-pages[login-page]',
                    'value'       => ! empty( get_option( 'ps-pages' )['login-page'] ) ? get_option( 'ps-pages' )['login-page'] : 0,
                    'description' => __( 'Choose the page used to login, or let visitors login via the default WordPress forms. The login page should have the [login_form] shortcode in its content to properly show the login form.', 'project-submission' ),
                ),
            ),
            'signup-page'    => array(
                'name'        => 'signup-page',
                'title'       => __( 'Signup page', 'project-submission' ),
                'callback'    => array( $this, 'page_select_field' ),
                'args'        => array(
                    'label_for'   => 'ps-pages[signup-page]',
                    'value'       => ! empty( get_option( 'ps-pages' )['signup-page'] ) ? get_option( 'ps-pages' )['signup-page'] : 0,
                    'description' => __( 'Choose the page used to signup, or let visitors register an account using the default WordPress forms. The signup page should have the [signup_form] shortcode in its content to properly show the signup form.', 'project-submission' ),
                ),
            ),
            'project-page'    => array(
                'name'        => 'project-page',
                'title'       => __( 'Project page', 'project-submission' ),
                'callback'    => array( $this, 'page_select_field' ),
                'args'        => array(
                    'label_for'   => 'ps-pages[project-page]',
                    'value'       => ! empty( get_option( 'ps-pages' )['project-page'] ) ? get_option( 'ps-pages' )['project-page'] : 0,
                    'description' => __( 'Choose the page used to submit a project. The project submission page should have the [project_form] shortcode in its content to properly show the form.', 'project-submission' ),
                ),
            ),
            'dashboard-page'    => array(
                'name'        => 'dashboard-page',
                'title'       => __( 'Dashboard page', 'project-submission' ),
                'callback'    => array( $this, 'page_select_field' ),
                'args'        => array(
                    'label_for'   => 'ps-pages[dashboard-page]',
                    'value'       => ! empty( get_option( 'ps-pages' )['dashboard-page'] ) ? get_option( 'ps-pages' )['dashboard-page'] : 0,
                    'description' => __( 'Choose the page users will use to view all submitted projects. The dashboard page should have the [dashboard] shortcode in its content to properly show all the submitted projects.', 'project-submission' ),
                ),
            ),
        );
    }


    /**
     * Sanitizes our settings
     *
     * @since    1.0.0
	 * @access   public
     * 
     * @param     array   $settings   The section's settings to sanitize.
     * @return    array   $settings   The section's sanitized settings.
     **/
    public function sanitize( $settings ){
        $pages = get_posts( array(
            'post_type'   => 'page',
            'numberposts' => -1,
            'fields'      => 'ids',
        ) );
        $settings['login-page']   = in_array( $settings['login-page'], $pages ) ? (int) $settings['login-page'] : 0 ;
        $settings['project-page'] = in_array( $settings['project-page'], $pages ) ? (int) $settings['project-page'] : 0 ;
        $settings['signup-page']  = in_array( $settings['signup-page'], $pages ) ? (int) $settings['signup-page'] : 0 ;
        $settings['dashboard-page']  = in_array( $settings['dashboard-page'], $pages ) ? (int) $settings['dashboard-page'] : 0 ;
        return $settings;
    }

}