<?php
/**
 * The template for the project details box, on single project views.
 * 
 * You can easily override this template in your child theme.
 * First, create a 'project-submission' folder in your child theme's root directory
 * Then create a 'templates' folder in it and copy this file in this new folder.
 * The plugin will look for custom templates in the child theme first, then in the parent theme.
 * If no custom templates are found, the default template provided by the plugin will be used.
 * 
 * @since      1.0.0
 * @package    Project_Submission
 * @author     Vincent Dubroeucq <vincent@vincentdubroeucq.com>
 */
?>

<?php ps_notice(); ?>

<?php if( is_user_logged_in() ) : ?>

    <div class="ps-project-details">
        <?php do_action( 'ps_before_project_details' ); ?>

        <h2><?php esc_html_e( 'Project Details', 'project_submission' ) ?></h2>
        <ul class="project-details">
            <?php foreach ( $project_data as $field => $value ) : ?>
                <?php if ( ! empty( $value ) ) : ?>
                    <li><strong><?php echo esc_html( $field ); ?></strong> : <?php echo esc_html( $value ); ?></li>
                <?php endif; ?>
            <?php endforeach; ?>
            <?php if ( ! empty( $project_files ) ) : ?>
                <li><strong><?php esc_html_e( 'Project files', 'project_submission' ); ?></strong> : <?php echo $project_files; ?></li>
            <?php endif; ?>           
        </ul>
        
        <?php do_action( 'ps_after_project_details' ); ?>
    </div>

<?php endif; ?>