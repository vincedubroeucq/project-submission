<?php
/**
 * The template for the lost password form.
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

do_action( 'ps_before_lostpassword_form' ); ?>

    <form name="lostpasswordform" id="lostpasswordform" action="<?php echo esc_url( network_site_url( 'wp-login.php?action=lostpassword', 'login_post' ) ); ?>" method="post">
        <p class="ps-form-field">
            <label>
                <span><?php esc_html_e( 'Username or Email Address', 'project-submission' ); ?></span><br />
                <input type="text" name="user_login" required />
            </label>
        </p>
        <p class="ps-form-field">
            <input type="hidden" name="redirect_to" value="<?php echo esc_url( add_query_arg( 'message', 'check-email', wp_login_url() ) ); ?>" />
            <input type="submit" name="wp-submit" id="wp-submit" class="button button-primary button-large" value="<?php esc_attr_e( 'Get New Password' ); ?>" />
        </p>
    </form>

<?php do_action( 'ps_after_lostpassword_form' );