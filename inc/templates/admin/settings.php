<?php
/**
 * The template for the general settings section, in the admin settings
 * Displays all the registered settings for the section
 * 
 * @since      1.0.0
 * @package    Project_Submission
 * @author     Vincent Dubroeucq <vincent@vincentdubroeucq.com>
 */
?>
<form method="POST" action="options.php">
    <?php
        settings_fields( 'project-submission-settings' );
        do_settings_sections( 'project-submission-settings' );
        submit_button(); 
    ?>
</form>

