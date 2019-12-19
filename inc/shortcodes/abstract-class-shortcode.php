<?php
/**
 * The main abstract class for all shortcodes.
 * Defines the necessary method and properties for shortcodes to implement
 *
 * @since      1.0.0
 * @package    Project_Submission
 * @author     Vincent Dubroeucq <vincent@vincentdubroeucq.com>
 */
namespace Project_Submission;
Abstract Class Shortcode {

    /**
     * The tag name of the shortcode.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $tag    The tag name of the shortcode. 
	 */
    protected $tag;


    /**
	 * Gets the tag name of the shortcode
	 *
	 * @since    1.0.0
     * @return   string   The tag name of the shortcode.
	 */
    public function get_tag(){
        return $this->tag;
    }
    
    
    /**
     * Outputs the shortcode's HTML
     *
     * @since    1.0.0
	 * @access   public
     * 
     * @param     array    $atts      An array of attributes passed in to the shortcode
	 * @param     string   $content   The content enclosed within the shortcodes opening and closing tags.
	 * @param     string   $tag       The tag of the shortode itself.
     **/
    public abstract function callback( $atts = array(), $content = null, $tag = '' );


    /**
	 * Registers our shortcode
	 *
	 * @since    1.0.0
     * @access   public
	 */
	public function register_shortcode() {
        add_shortcode( $this->get_tag(), array( $this, 'callback' ) );
    }

    /**
     * Hooks any functions needed
     *
     * @since    1.0.0
	 * @access   public
     **/
    abstract public function register_hooks();
        
}