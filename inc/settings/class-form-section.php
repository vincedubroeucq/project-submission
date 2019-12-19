<?php
/**
 * The Projects settings section class
 * Register all the settings that should appear under the Projects tab, in the settings page.
 * Also defines handlers for these settings.
 *
 * @since      1.0.0
 * @package    Project_Submission
 * @author     Vincent Dubroeucq <vincent@vincentdubroeucq.com>
 */
namespace Project_Submission;
class Form_Section extends Settings_Section {

    /**
	 * Define the initial properties of the class instance.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {		
		$this->id          = 'ps-forms';
        $this->title       = __( 'Form settings', 'project-submission' );
        $this->description = __( 'This section groups all the settings related to the project submission form.', 'project-submission' );
        $this->page        = 'project-submission-settings';
        $this->defaults    = array();
        $this->settings    = array(
            'budget-options'  => array(
                'name'        => 'budget-options',
                'title'       => __( 'Budget field options', 'project-submission' ),
                'callback'    => array( $this, 'budget_options' ),
                'args'        => array(
                    'label_for'   => 'ps-forms[budget-options]',
                ),
            ),
            'timeframe-options' => array(
                'name'        => 'timeframe-options',
                'title'       => __( 'Timeframe field options', 'project-submission' ),
                'callback'    => array( $this, 'timeframe_options' ),
                'args'        => array(
                    'label_for'   => 'ps-forms[timeframe-options]',
                ),
            ),
        );
    }


    /**
     * Outputs the Budget options field HTML
     *
     * @since    1.0.0
	 * @access   public
     **/
    public function budget_options(){
        $settings = get_option( 'ps-forms' );
        $values   = ! empty( $settings['budget-options'] ) ? $settings['budget-options'] : array();         
        ?>
            <textarea name="ps-forms[budget-options]" class="regular-text" rows="5"><?php foreach ( $values as $key => $value ){echo esc_textarea( trim( $key ) . ' : ' . trim( $value ) . "\n" );}?></textarea>
            <p class="description"><?php esc_html_e( 'Add the options you want to offer in the budget field on the front end project submission form. Format your options "key : label", each on its own line. Or leave blank to disable the field.', 'project-submission' ); ?></p>
        <?php
    }


    /**
     * Outputs the timeframe options field HTML
     *
     * @since    1.0.0
	 * @access   public
     **/
    public function timeframe_options(){
        $settings = get_option( 'ps-forms' );
        $values   = ! empty( $settings['timeframe-options'] ) ? $settings['timeframe-options'] : array();           
        ?>
            <textarea name="ps-forms[timeframe-options]" class="regular-text" rows="5"><?php foreach ( $values as $key => $value ){echo esc_textarea( trim( $key ) . ' : ' . trim( $value ) . "\n" );}?></textarea>
            <p class="description"><?php esc_html_e( 'Add the options you want to offer in the timeframe field on the front end project submission form. Format your options "key : label", each on its own line. Or leave blank to disable the field.', 'project-submission' ); ?></p>
        <?php
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
        $settings['budget-options']    = ! is_array( $settings['budget-options'] ) ? $this->parse_textarea( $settings['budget-options'] ) : $settings['budget-options'];
        $settings['timeframe-options'] = ! is_array( $settings['timeframe-options'] ) ? $this->parse_textarea( $settings['timeframe-options'] ) : $settings['timeframe-options'];
        return $settings;
    }


    /**
     * Parses textareas used to define <select> fields choices in the settings.
     * 
     * @since    1.0.0
	 * @access   public
     * 
     * @param     string   $content   The textarea content.
     * @return    array               The options in array format, with key/value pairs ready to use in a select field.
     **/
    public function parse_textarea( $content, $defaults = array() ){
        
        // Clean up our content first and restore defaults
        $content = esc_textarea( trim( $content ) );
        $values  = $defaults;
        
        if( ! empty( $content ) ){
            // Get each line and break them on the ":"
            $values = array();
            $lines  = explode( "\n", $content );
            foreach ( $lines as $line ) {
                $elements = explode( ':', $line );
                if ( 2 === count( $elements ) ){
                    $key   = sanitize_key( trim( $elements[0] ) );
                    $value = sanitize_text_field( trim( $elements[1] ) );
                    if ( $key && $value ){
                        $values[$key] = $value;
                    }
                }
            }
        }

        return $values;

    }

}