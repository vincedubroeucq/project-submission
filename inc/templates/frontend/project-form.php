<?php
/**
 * The template for the project submission form.
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

<?php do_action( 'ps_before_project_form' ); ?>

<?php if( is_user_logged_in() ) : ?>

    <form method="POST" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" class="ps-form" enctype="multipart/form-data">
        
        <?php wp_nonce_field( 'project-submission-new-project', 'project-submission-new-project-nonce' ); ?>
        <input type="hidden" name="action" value="new_project">
        <input type="text" name="honeyfield" />
       
        <?php do_action( 'ps_before_project_form_fields' ); ?>

        <p class="ps-form-field">
            <label>
                <span><?php esc_html_e( 'Title for your project', 'project-submission' ); ?></span><br />
                <input type="text" name="title" placeholder="<?php esc_attr_e( 'Custom theme design', 'project-submission' ); ?>" required />
            </label>
        </p>

        <?php if ( ! empty( $types ) ) : ?>
            <p class="ps-form-field">
                <label>
                    <span><?php esc_html_e( 'Type of project', 'project-submission' ); ?></span><br />
                    <select name="type" required>
                        <?php foreach ( $types as $key => $type) : ?>
                            <option value="<?php echo esc_attr( $type->slug ); ?>" title="<?php echo esc_attr( $type->description); ?>"><?php echo esc_html( $type->name ); ?></option>
                        <?php endforeach; ?>
                    </select>
                </label>
            </p>
        <?php endif; ?>

        <?php if ( ! empty( $settings['budget-options'] ) ) : ?>
            <p class="ps-form-field">
                <label>
                    <span><?php esc_html_e( 'Your budget for this project', 'project-submission'  ); ?></span><br />
                    <select name="budget" required>
                        <?php foreach ( $settings['budget-options'] as $key => $value ) : ?>
                            <option value="<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $value ); ?></option>
                        <?php endforeach; ?>
                    </select>
                </label>
            </p>
        <?php endif; ?>

        <?php if ( ! empty( $settings['timeframe-options'] ) ) : ?>
            <p class="ps-form-field">
                <label>
                    <span><?php esc_html_e( 'Your timeframe for this project', 'project-submission' ); ?></span><br />
                    <select name="timeframe" required>
                        <?php foreach ( $settings['timeframe-options'] as $key => $value ) : ?>
                            <option value="<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $value ); ?></option>
                        <?php endforeach; ?>
                    </select>
                </label>
            </p>
        <?php endif; ?>

        <p class="ps-form-field">
            <label>
                <span><?php esc_html_e( 'Project details', 'project-submission' ); ?></span><span class="ps-form-help"><?php esc_html_e( 'Be specific. Explain who you are, what you\'re trying to achieve and what you\'re looking for. The clearer the description, the more chance of starting out the project on the right foot.', 'project-submission' ); ?></span><br />
                <textarea name="description" rows=10 required></textarea>
            </label>
        </p>

        <p class="ps-form-field">
            <label>
                <span><?php esc_html_e( 'Project files', 'project-submission' ); ?></span><span class="ps-form-help"><?php esc_html_e( 'Upload any files you have that could help study the project. Accepts pdf, jpeg, and png files.', 'project-submission' ); ?></span><br />
                <input type="file" name="project-files[]" accept="application/pdf, image/png, image/jpeg" multiple />
            </label>
        </p>
        
        <?php do_action( 'ps_after_project_form_fields' ); ?>
        
        <p class="ps-form-field">
            <input type="submit" name="submit" value="<?php esc_attr_e( 'Submit a project', 'project-submission' ); ?>" />
        </p>
        
    </form>

<?php endif; ?>

<?php do_action( 'ps_after_project_form' );