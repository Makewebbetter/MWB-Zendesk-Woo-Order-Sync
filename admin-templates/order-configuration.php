<?php
/**
 * Exit if accessed directly
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
/**
 * This file includes Zendesk Order configuration settings.
 *
 * @link       https://makewebbbetter.com/
 * @since      2.0.2
 *
 * @package    mwb-zendesk-woo-order-sync
 * @subpackage mwb-zendesk-woo-order-sync/admin-templates
 */

$handled_order_config_options = mwb_zndskwoo_get_order_config_options();

$latest_orders_count = $handled_order_config_options['latest_orders_count'];

// KPI Fields.
$all_kpi_fields = mwb_zndskwoo_order_config_get_all_kpi_fields();
$source_kpi_fields = $handled_order_config_options['source_kpi_fields'];
$selected_kpi_fields = $handled_order_config_options['selected_kpi_fields'];

// Order Fields.
$all_order_fields = mwb_zndskwoo_order_config_get_all_order_fields();
$source_order_fields = $handled_order_config_options['source_order_fields'];
$selected_order_fields = $handled_order_config_options['selected_order_fields'];

?>

<div class="zndsk_setting_ticket_wrapper">
	<div class="zndsk_setting_wrapper mwb-zndsk-order-config-options">
		<h2><?php esc_html_e( __( 'Order Configuration Settings', 'zndskwoo' ) ); ?></h2>
		
		<!-- Settings saved notice. -->
		<div class="mwb-zndsk-order-config-notice settings-saved notice notice-success is-dismissible" style="display: none;"> 
			<p><?php _e( 'Selected Options Saved.', 'zndskwoo' ); ?></p>
		</div>
		<!-- Settings not saved notice. -->
		<div class="mwb-zndsk-order-config-notice settings-not-saved notice notice-warning is-dismissible" style="display: none;"> 
			<p><?php _e( 'No Changes.', 'zndskwoo' ); ?></p>
		</div>

		<form id="mwb-zndsk-order-config-form" method="POST">
		<table class="zndsk_setting_table">
			<tbody>
				<tr>
					<td class="zendesk-column zendesk-col-left  zendesk-url-column"><strong><?php esc_html_e( 'Latest Orders Count', 'zndskwoo' ); ?></strong></td>
					<td class="zendesk-column zendesk-col-right"><input required="required" type="number" min="1" max="100" id="mwb-zndsk-latest-orders-count" value="<?php echo $latest_orders_count; ?>"></td>
				</tr>

				<tr>
					<td class="zendesk-column zendesk-col-left  zendesk-url-column"><strong><?php esc_html_e( 'KPI Fields', 'zndskwoo' ); ?></strong></td>
					<td class="zendesk-column zendesk-col-right">
							
						<div class="mwb-zndsk-order-config-fields__drag-drop mwb-zndsk-clearfix">
	                        <div class="mwb-zndsk-order-config-fields__drag mwb-zndsk-clearfix">

	                            <ul id="mwb-zndsk-kpi-fields-dvsource" class="mwb-zndsk-field-drop kpi-fields">

	                                <?php 

	                                if( ! empty( $source_kpi_fields ) && is_array( $source_kpi_fields ) ) {

	                                    foreach ( $all_kpi_fields as $field_key => $field_name ) {

	                                        if( in_array( $field_key, $source_kpi_fields ) ) : ?>

	                                        	<li class="mwb-zndsk-field-drag kpi-fields" data-name='<?php echo $field_key; ?>'><?php echo $field_name;?></li>

	                                        <?php endif;
	                                    }
	                                }

	                                ?>
	                            </ul>
	                        </div>
	                        <div class="mwb-zndsk-order-config-fields__drop mwb-zndsk-clearfix">

	                            <ul id="mwb-zndsk-kpi-fields-dvdest" class="mwb-zndsk-field-drop kpi-fields">
	                                
	                                <?php 

	                                if( ! empty( $selected_kpi_fields ) && is_array( $selected_kpi_fields ) ) {

	                                    foreach ( $all_kpi_fields as $field_key => $field_name ) {

	                                        if( in_array( $field_key, $selected_kpi_fields ) ) : ?>

	                                         <li class="mwb-zndsk-field-drag kpi-fields" data-name='<?php echo $field_key; ?>'><?php echo $field_name;?></li>

	                                        <?php endif;
	                                    }
	                                }

	                                ?>
	                            </ul>
	                        </div>
	                        <img src="<?php echo MWB_ZENDESK_DIR_URL . 'assets/images/switch.png'?>" alt="" class="mwb-zndsk-switch-icon">
	                    </div> 

					</td>
				</tr>

				<tr>
					<td class="zendesk-column zendesk-col-left  zendesk-url-column"><strong><?php esc_html_e( 'Order Fields', 'zndskwoo' ); ?></strong></td>
					<td class="zendesk-column zendesk-col-right">
							
						<div class="mwb-zndsk-order-config-fields__drag-drop mwb-zndsk-clearfix">
	                        <div class="mwb-zndsk-order-config-fields__drag mwb-zndsk-clearfix">

	                            <ul id="mwb-zndsk-order-fields-dvsource" class="mwb-zndsk-field-drop order-fields">

	                                <?php 

	                                if( ! empty( $source_order_fields ) && is_array( $source_order_fields ) ) {

	                                    foreach ( $all_order_fields as $field_key => $field_name ) {

	                                        if( in_array( $field_key, $source_order_fields ) ) : ?>

	                                        	<li class="mwb-zndsk-field-drag order-fields" data-name='<?php echo $field_key; ?>'><?php echo $field_name;?></li>

	                                        <?php endif;
	                                    }
	                                }

	                                ?>
	                            </ul>
	                        </div>
	                        <div class="mwb-zndsk-order-config-fields__drop mwb-zndsk-clearfix">

	                            <ul id="mwb-zndsk-order-fields-dvdest" class="mwb-zndsk-field-drop order-fields">
	                                
	                                <?php 

	                                if( ! empty( $selected_order_fields ) && is_array( $selected_order_fields ) ) {

	                                    foreach ( $all_order_fields as $field_key => $field_name ) {

	                                        if( in_array( $field_key, $selected_order_fields ) ) : ?>

	                                         <li class="mwb-zndsk-field-drag order-fields" data-name='<?php echo $field_key; ?>'><?php echo $field_name;?></li>

	                                        <?php endif;
	                                    }
	                                }

	                                ?>
	                            </ul>
	                        </div>
	                        <img src="<?php echo MWB_ZENDESK_DIR_URL . 'assets/images/switch.png'?>" alt="" class="mwb-zndsk-switch-icon">
	                    </div> 

					</td>
				</tr>

				<tr>
					<td colspan="2" class="zendesk-submit">
						<button type="submit" class="button button-primary"><?php esc_html_e( 'Save Options', 'zndskwoo' ); ?></button>
					</td>
				</tr>
			</tbody>
		</table></form>
	</div>
</div>
<?php
