<?php
/**
 * The template for the user account creation form.
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

<?php do_action( 'ps_before_signup_form' ); ?>

<?php if ( ! is_user_logged_in() ) : ?>

    <form method="POST" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" class="ps-form" enctype="multipart/form-data">
        
        <?php wp_nonce_field( 'project-submission-signup', 'project-submission-signup-nonce' ); ?>
        <input type="text" name="honeyfield" />
        <input type="hidden" name="action" value="signup" />

        <?php do_action( 'ps_before_signup_form_fields' ); ?>

        <p class="ps-form-field">
            <label>
                <span><?php esc_html_e( 'Your username', 'project-submission' ); ?></span><span class="ps-form-help"><?php esc_html_e( 'Required. Use only lowercase characters.', 'project-submission' ); ?></span><br />
                <input type="text" name="username" placeholder="johndoe" pattern="^[a-z0-9_-]+$" required />
            </label>
        </p>
        
        <p class="ps-form-field">
            <label>
                <span><?php esc_html_e( 'Your email', 'project-submission' ); ?></span><span class="ps-form-help"><?php esc_html_e( 'Required. Make sure there is a single @ and no dot before it.', 'project-submission' ); ?></span><br />
                <input type="email" name="email" placeholder="jdoe@example.com" required />
            </label>
        </p>
        
        <p class="ps-form-field">
            <label>
                <span><?php esc_html_e( 'Your password', 'project-submission' ); ?></span><span class="ps-form-help"><?php esc_html_e( 'Required. Use at least 8 characters with at least one lowercase, one uppercase, and a number.', 'project-submission' ); ?></span><br />
                <input type="password" name="password" value="<?php echo wp_generate_password( 16 ); ?>" minlength="8" pattern="^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?!.*\s).*$" required />
            </label>
            <label id="show-password"><input type="checkbox" /><?php esc_html_e( 'Show password', 'project-submission' ); ?></label>
        </p>
        
        <p class="ps-form-field">
            <label>
                <span><?php esc_html_e( 'Profile picture', 'project-submission' ); ?></span><span class="ps-form-help"><?php esc_html_e( 'If you don\'t use a gravatar, you can upload a 150px by 150px profile image.', 'project-submission' ); ?></span><br />
                <input type="file" name="avatar" accept="image/png, image/jpeg" />
            </label>
        </p>

        <?php if ( (int) get_option( 'wp_page_for_privacy_policy' ) ) : ?>
            <p class="ps-form-field">
                <label>
                    <input type="checkbox" name="privacy" required />
                    <span><?php printf( __( 'I have read and agree with the <a href="%s" target="__blank">privacy policy</a>.', 'project-submission' ), get_permalink( get_option( 'wp_page_for_privacy_policy' ) ) ) ?></span>
                </label>
            </p>
        <?php endif; ?>

        <?php do_action( 'ps_after_signup_form_fields' ); ?>

        <p class="ps-form-field">
            <input type="submit" name="submit" value="<?php esc_attr_e( 'Signup', 'project-submission' ); ?>" />
        </p>
        
    </form>

<?php endif; ?>

<?php do_action( 'ps_after_signup_form' ); ?>

