<?php
/**
 * The main interface for all handlers.
 * Defines the necessary method for handlers to implement
 *
 * @since      1.0.0
 * @package    Project_Submission
 * @author     Vincent Dubroeucq <vincent@vincentdubroeucq.com>
 */
namespace Project_Submission;
interface Handler {
    
    /**
     * Register all the hooks needed for the handler to run
     *
     * @since    1.0.0
	 * @access   public
     **/
    public function register_hooks();
    
}