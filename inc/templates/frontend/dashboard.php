<?php
/**
 * The template for the dashboard project list.
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

<?php do_action( 'ps_before_dashboard' ); ?>

<?php if( is_user_logged_in() && current_user_can( 'project-owner' ) ) : ?>

    <?php if( empty( $user_projects ) ) : ?>

        <?php printf ( 
            __( '<p>This is your dashboard. It looks like you haven\'t submitted a project yet. You can do so on the <a href="%s">project page</a>.' , 'project-submission' ),
            ps_get_page_url( 'project' )
        ); ?>

    <?php else : ?>
        
        <?php do_action( 'ps_before_dashboard_table' ); ?>

        <table class="ps-table">
            <thead>
                <tr>
                    <th><?php esc_html_e( 'Project title', 'project-submission' ); ?></th>
                    <th><?php esc_html_e( 'Latest message', 'project-submission' ); ?></th>
                    <th><?php esc_html_e( 'Status', 'project-submission' ); ?></th>
                    <?php do_action( 'ps_dashboard_table_headings' ); ?>
                </tr>
            </thead>
            <tbody>
                <?php foreach ( $user_projects as $project_id ) : ?>
                    <tr>
                        <td><?php printf( __( '<a href="%1$s">%2$s</a>', 'project-submission' ), esc_url( get_permalink( $project_id ) ), esc_html( get_the_title( $project_id ) ) );?></td>
                        <td><?php echo esc_html( ps_get_latest_message( $project_id ) ); ?></td>
                        <td><?php echo wp_kses_post( ps_get_project_status( $project_id ) ); ?></td>
                        <?php do_action( 'ps_dashboard_table_cell', $project_id ); ?>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <?php do_action( 'ps_before_dashboard_table' ); ?>
    <?php endif; ?>

<?php endif; ?>

<?php do_action( 'ps_after_dashboard' );