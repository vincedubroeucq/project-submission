<?php
/**
 * The core plugin class.
 * Loads our plugin dependencies and orchestrates hooks to get the ball rolling.
 *
 * @since      1.0.0
 * @package    Project_Submission
 * @author     Vincent Dubroeucq <vincent@vincentdubroeucq.com>
 */
namespace Project_Submission;
class Project_Submission {

	/**
     * The registered handlers to run.
     * Each handler is an instance of a class implementing the Handler interface.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      array    $handlers    An array of handler instances. 
	 */
	protected $handlers;


	/**
	 * Define the core properties of the plugin.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		$this->handlers = array();
	}

	
	/**
	 * Gets the registered handlers of the plugin
	 *
	 * @since    1.0.0
	 * @access   public
	 * @return   array    An array of registered handlers.
	 */
	public function get_handlers() {
		return apply_filters( 'ps_registered_handlers', $this->handlers );
	}


	/**
	 * Load the required files.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {
		
		// Load what's always needed first
		require plugin_dir_path( __FILE__ ) . 'helper-functions.php';
		require_once plugin_dir_path( __FILE__ ) . 'interface-handler.php';
		require_once plugin_dir_path( __FILE__ ) . 'post-types/class-project-type-taxonomy.php';
		require_once plugin_dir_path( __FILE__ ) . 'post-types/class-project-post-type.php';
		require_once plugin_dir_path( __FILE__ ) . 'post-types/class-project-comments-handler.php';
		require_once plugin_dir_path( __FILE__ ) . 'form-handlers/class-file-handler.php';
		require_once plugin_dir_path( __FILE__ ) . 'notifications/class-notifications-handler.php';
	
		// Load admin only / front-end only dependencies.
		if( is_admin() ){
			require plugin_dir_path( __FILE__ ) . 'metaboxes/class-project-metabox.php';
			require plugin_dir_path( __FILE__ ) . 'form-handlers/abstract-class-form-handler.php';
			require plugin_dir_path( __FILE__ ) . 'form-handlers/class-project-form-handler.php';
			require plugin_dir_path( __FILE__ ) . 'form-handlers/class-signup-form-handler.php';
			require plugin_dir_path( __FILE__ ) . 'settings/class-settings-page.php';
			require plugin_dir_path( __FILE__ ) . 'settings/abstract-class-settings-section.php';
			require plugin_dir_path( __FILE__ ) . 'settings/class-pages-section.php';
			require plugin_dir_path( __FILE__ ) . 'settings/class-form-section.php';
		} else {
			require plugin_dir_path( __FILE__ ) . 'shortcodes/abstract-class-shortcode.php';
			require plugin_dir_path( __FILE__ ) . 'shortcodes/class-project-form.php';
			require plugin_dir_path( __FILE__ ) . 'shortcodes/class-login-form.php';
			require plugin_dir_path( __FILE__ ) . 'shortcodes/class-signup-form.php';
			require plugin_dir_path( __FILE__ ) . 'shortcodes/class-dashboard.php';
		}

	}


	/**
     * Instantiate all the plugin's handlers.
	 *
     * @since    1.0.0
     * @access   public
     **/
	public function load_handlers(){
		
		$this->handlers = array(
			'project-type-taxonomy' => new Project_Type_Taxonomy(),
			'project-post-type'     => new Project_Post_Type(),
			'project-comments'      => new Project_Comments_Handler(),
			'file-handler' 			=> new File_Handler(),
			'notifications-handler' => new Notifications_Handler(),
		);

		if ( is_admin() ){
			$this->handlers['project-metabox'] = new Project_Metabox();
			$this->handlers['settings-pages']  = new Settings_Page( array(
				new Pages_Section(),
				new Form_Section()
			) );
			$this->handlers['project-form-handler'] = new Project_Form_Handler();
			$this->handlers['signup-form-handler']  = new Signup_Form_Handler();
		} else {
			$this->handlers['project-form'] = new Project_Form();
			$this->handlers['login-form']   = new Login_Form();
			$this->handlers['signup-form']  = new Signup_Form();
			$this->handlers['dashboard']    = new Dashboard();
		}
	}


	/**
     * Executes the hooks for all the registered handlers.
	 *
     * @since    1.0.0
     * @access   public
     **/
	public function register_hooks(){
		// Hook in the handlers
		foreach ( $this->get_handlers() as $handler ) {
			$handler->register_hooks();
		}
	}


	/**
     * Loads styles and scripts.
	 *
     * @since    1.0.0
     * @access   public
     **/
	public function enqueue_scripts(){
		if( ! is_admin() ){
			wp_enqueue_style( 'ps-helpers', PROJECT_SUBMISSION_URL . 'assets/css/helpers.css' );
		}
	}


	/**
	 * Register all of the hooks with WordPress.
	 *
	 * @since	1.0.0
	 */
	public function run() {
		$this->load_dependencies();
		add_action( 'plugins_loaded', array( $this, 'load_handlers' ) );
		add_action( 'plugins_loaded', array( $this, 'register_hooks' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );		
	}

}
