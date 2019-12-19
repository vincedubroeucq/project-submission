<?php
/**
 * The Settings page class.
 * Contains page properties, callback function, and other related functions.
 *
 * @since      1.0.0
 * @package    Project_Submission
 * @author     Vincent Dubroeucq <vincent@vincentdubroeucq.com>
 */
namespace Project_Submission;
class Settings_Page {

    /**
     * The slug of the parent page.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $slug    The slug of the parent page, if any. 
	 */
    protected $parent_slug = '';

    /**
     * The title of the page.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $page_title    The title of the page. 
	 */
    protected $page_title = '';

    
    /**
     * The slug of the page.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $menu_slug    The slug of the menu page. 
	 */
    protected $menu_slug = '';


    /**
     * The menu title of the page.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $menu_title    The menu title of the page. 
	 */
    protected $menu_title = '';


    /**
     * The capability required to view the page.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $capability    The needed capability. 
	 */
    protected $capability = 'manage_options';

    /**
     * The sections for the page.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      array    $sections    The array of sections if there are any. 
	 */
    protected $sections = array();
    
    /**
     * The tabs for the page.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      array    $tabs    The array of tabs if there are any. 
	 */
    protected $tabs = array();
    

    /**
	 * Define the initial properties of the class instance.
	 *
	 * @since    1.0.0
	 */
	public function __construct( $sections ) {		
		$this->parent_slug  = 'edit.php?post_type=ps_projects';
		$this->page_title   = __( 'Projects Submission Settings', 'project-submission' );
		$this->menu_slug    = 'project-submission-settings';
        $this->menu_title   = __( 'Settings', 'project-submission' );
        $this->sections     = $sections;
        $this->tabs         = array(
            'settings'   => __( 'General' , 'project-submission'),
        );
    }


    /**
     * Hooks any functions needed
     *
     * @since    1.0.0
	 * @access   public
     **/
    public function register_hooks(){
        add_action( 'admin_menu', array( $this, 'register_page' ) );
        foreach ( $this->get_sections() as $section ) {
            $section->register_hooks();
        }
    }


    /**
	 * Registers our page
	 *
	 * @since    1.0.0
     * @access   public
	 */
	public function register_page() {
        add_submenu_page( 
            $this->parent_slug, 
            $this->page_title,
            $this->menu_title,
            $this->capability,
            $this->menu_slug,
            array( $this, 'callback' )
        );	
    }

    /**
	 * Gets the registered tabs
	 *
	 * @since    1.0.0
     * @access   public
     * @return   array  $tabs  Array of registered tabs
	 */
	public function get_tabs() {
        return apply_filters( 'ps_settings_tabs', $this->tabs );
    }


    /**
	 * Gets the registered sections
	 *
	 * @since    1.0.0
     * @access   public
     * @return   array  $tabs  Array of registered sections
	 */
	public function get_sections() {
        return apply_filters( 'ps_settings_sections', $this->sections );
    }


    /**
     * The menu page callback function, responsible for displaying its content.
     *
     * @since    1.0.0
	 * @access   public
     * 
     **/
    public function callback(){
        ?>
            <div class="wrap">
                <?php settings_errors(); ?>
                <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
                <?php 
                    $this->the_tab_navigation();
                    foreach ( $this->get_tabs() as $tab_slug => $tab_label ) {
                        if ( $this->is_current_tab( $tab_slug ) ){
                            include( PROJECT_SUBMISSION_PATH . 'inc/templates/admin/' . $tab_slug . '.php' );                   
                        }
                    }
                ?>
            </div>
        <?php
    }


    /**
     * Renders the tabs navigation
     *
     * @since    1.0.0
	 * @access   public
     **/
	public function the_tab_navigation(){
        ?>
            <?php if( count( $this->get_tabs() ) > 1 ) : ?>
                <h2 class="nav-tab-wrapper">
                    <?php foreach ( $this->get_tabs() as $tab_slug => $tab_label ) : ?>
                        <?php $active = $this->is_current_tab( $tab_slug ) ? 'nav-tab-active' : ''; ?>
                        <a href="<?php echo esc_url( add_query_arg( 'tab', urlencode( $tab_slug ) ) ); ?>" class="nav-tab <?php echo $active ?>"><?php echo esc_html( $tab_label ); ?></a>
                    <?php endforeach; ?>
                </h2>
            <?php endif; ?>
        <?php
    }


    /**
     * Is the tab passed in currently being displayed ?
     *
     * @since    1.0.0
     * @access   public
     * @param    string   $tab_slug   The slug of the tab to check.
     * @return   bool                 True if the slug passd in is active.
     **/
	public function is_current_tab( $tab_slug ){
        
        // If no tab is selected, return true for the 'settings' tab. That is, make it a default.
        if ( ! isset( $_GET['tab'] ) && $tab_slug === 'settings' ) {
            return true;
        }
        
        // Else return true if this tab can be found in the $_GET variable.
        return isset( $_GET['tab'] ) && ( $_GET['tab'] === $tab_slug ) ? true : false; 
    }
}