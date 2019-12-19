<?php
/**
 * The main abstract class for all settings sections.
 *
 * @since      1.0.0
 * @package    Project_Submission
 * @author     Vincent Dubroeucq <vincent@vincentdubroeucq.com>
 */
namespace Project_Submission;
Abstract Class Settings_Section {

    /**
     * The id of the section.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $id    The name for that section. 
	 */
    protected $id;


    /**
     * The title of that section.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $title    The title for that section. 
	 */
    protected $title;


    /**
     * The decription of the section.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $id    The description for that section. 
	 */
    protected $description;
    
    
    /**
     * The page that section should appear under.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $page    The parent page 
	 */
    protected $page;
    
    
    /**
     * The settings for that section.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      array    $setting    The settings for that section 
	 */
    protected $settings;
    
    
    /**
     * The default settings for that section.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      array    $defaults    The default settings for that section 
	 */
    protected $defaults;


    /**
	 * Gets the id for that section
	 *
	 * @since    1.0.0
     * @return   string   The id for that section. 
	 */
    public function get_id(){
        return $this->id;
    }


    /**
	 * Gets the title of that section
	 *
	 * @since    1.0.0
     * @return   string   The title for that section. 
	 */
    public function get_title(){
        return $this->title;
    }
    
    
    /**
	 * Gets the description of that section
	 *
	 * @since    1.0.0
     * @return   string   The description for that section. 
	 */
    public function get_description(){
        return $this->description;
    }


    /**
	 * Gets the parent page for that section
	 *
	 * @since    1.0.0
     * @return   string   The page that section should belong to. 
	 */
    public function get_page(){
        return $this->page;
    }


    /**
	 * Gets the settings for that section
	 *
	 * @since    1.0.0
     * @return   array   The settings for that section. 
	 */
    public function get_settings(){
        return $this->settings;
    }


    /**
	 * Gets the default settings for that section
	 *
	 * @since    1.0.0
     * @return   array   The settings for that section. 
	 */
    public function get_defaults(){
        return $this->defaults;
    }
    
    
    /**
     * Outputs the section's HTML
     *
     * @since    1.0.0
	 * @access   public
     **/
    public function callback(){}
    
    
     /**
     * Sanitizes our settings
     *
     * @since    1.0.0
	 * @access   public
     * 
     * @param     array   $settings   The section's settings to sanitize.
     * @return    array   $settings   The section's sanitized settings.
     **/
    public abstract function sanitize( $settings );
    

    /**
	 * Registers our section
	 *
	 * @since    1.0.0
     * @access   public
	 */
	public function register_sections() {
        add_settings_section( $this->get_id(), $this->get_title(), array( $this, 'callback' ), $this->get_page() );
        $this->register_settings();
    }
    
    
    /**
     * Registers this section's settings within WordPress
     *
     * @since    1.0.0
	 * @access   public
     **/
    public function register_settings(){
        // Register only one settings array for each section
        register_setting( $this->get_page(), $this->get_id(), array( 'sanitize_callback' => array( $this, 'sanitize' ), 'description' => $this->get_description(), 'default' => $this->get_defaults() ) );
        
        // Add all of our settings fields
        foreach ( $this->get_settings() as $name => $args ) {
            add_settings_field( $args['name'], $args['title'], $args['callback'], $this->get_page(), $this->get_id(), $args['args'] );
        }
    }


    /**
     * Register all the hooks needed for the manager to run
     *
     * @since    1.0.0
	 * @access   public
     **/
    public function register_hooks(){
        add_action( 'admin_init', array( $this, 'register_sections' ) );
    }


    /**
     * Helper function to generate page select fields
     *
     * @since    1.0.0
	 * @access   public
     * @param    array   $args   The arguments passed in to the setting field callback. See add_settings_field() documentation. 
     **/
    public function page_select_field( $args ){
       
        $pages = get_posts( array(
            'post_type'   => 'page',
            'numberposts' => -1,
            'fields'      => 'ids',
        ) );

        // Build the page options array
        $options = array();
        if( ! empty( $pages ) ){
            foreach ( $pages as $id ){
                $options[$id] = get_the_title( $id );
            }
        } else {
            $options[] = __( 'No page to display.', 'project_submission' ); 
        }

        $args['options'] = $options;
        $this->select_field( $args );
   
    }


    /**
     * Helper function to generate standard select fields
     *
     * @since    1.0.0
     * @access   public
     * @param    array   $args   The arguments passed in to the setting field callback. See add_settings_field() documentation. 
     **/
    public function select_field( $args ){

        if( empty( $args['options'] ) ){
            $args['options'][] = __( 'No option to display.', 'project_submission' );
        }
        
        $html = '<select class="regular-text" name="' . esc_attr( $args['label_for'] ) . '">';
        $html .= '<option value="">' . esc_html__( 'Select an option', 'project-submission' ) . '</option>';
            foreach( $args['options'] as $key => $label ){
                $html .= '<option value="' . $key . '" ' . selected( $args['value'], $key, false ) . '>' . esc_html( $label ) . '</option>';
            }
        $html .= '</select>';
        $html .= ! empty( $args['description'] ) ? '<p class="description">' . esc_html( $args['description'] ) . '</p>' : '';

        echo $html;
    }

    
    /**
     * Helper function to generate standard checkbox fields
     *
     * @since    1.0.0
     * @access   public
     * @param    array   $args   The arguments passed in to the setting field callback. See add_settings_field() documentation. 
     **/
    function checkbox_field( $args ){
        $html = '<input type="checkbox" name="' . esc_attr( $args['label_for'] ) . '"' . checked( $args['value'], true, false ) . ' />';
        if( ! empty( $args['description'] ) ){
            $html .= '<span>' . esc_html( $args['description'] ) . '</span>';
        }
        echo $html;
    }
    
}