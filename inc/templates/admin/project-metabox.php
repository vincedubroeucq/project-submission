<?php
/**
 * The template for the admin project metabox.
 * 
 * @since      1.0.0
 * @package    Project_Submission
 * @author     Vincent Dubroeucq <vincent@vincentdubroeucq.com>
 */
?>
<?php do_action( 'ps_before_project_metabox_fields' ); ?>
<p>
    <label>
        <strong><?php esc_html_e( 'Project status', 'project-submission' ); ?></strong><br />
        <select name="status" class="regular-text">
            <?php foreach( ps_get_project_statuses() as $key => $label ) : ?>
                <option value="<?php echo esc_attr( $key ) ?>" <?php selected( $status, $key ) ?>><?php echo esc_html( $label ); ?></option>
            <?php endforeach; ?>
        </select>
    </label>
</p>
<p>
    </label>
        <strong><?php esc_html_e( 'Project timeframe', 'project-submission' ); ?></strong><br />
        <select name="timeframe" class="regular-text">
            <?php foreach( ps_get_project_timeframe_options() as $key => $label ) : ?>
                <option value="<?php echo esc_attr( $key ) ?>" <?php selected( $timeframe, $key ) ?>><?php echo esc_html( $label ); ?></option>
            <?php endforeach; ?>
        </select>
    </label>
</p>
<p>
    </label>
        <strong><?php esc_html_e( 'Project budget', 'project-submission' ); ?></strong><br />
        <select name="budget" class="regular-text">
            <?php foreach( ps_get_project_budget_options() as $key => $label ) : ?>
                <option value="<?php echo esc_attr( $key ) ?>" <?php selected( $budget, $key ) ?>><?php echo esc_html( $label ); ?></option>
            <?php endforeach; ?>
        </select>
    </label>
</p>
<p>
    </label>
        <strong><?php esc_html_e( 'Start date', 'project-submission' ); ?></strong><br />
        <input type="date" name="start_date" value="<?php echo esc_attr( $formatted_start_date ); ?>" />
    </label>
</p>
<p>
    <strong><?php esc_html_e( 'Project Files', 'project-submission' ); ?></strong><br />
    <?php echo ps_get_project_files( $post->ID ); ?>
</p>
<?php do_action( 'ps_after_project_metabox_fields' ); ?>
