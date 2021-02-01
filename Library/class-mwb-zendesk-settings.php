<?php
/**
 * Exit if accessed directly
 *
 * @package mwb-zendesk-woo-order-sync/Library
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
/**
 * This file manages to send order details to Zendesk.
 *
 * @link       https://makewebbbetter.com/
 * @since      1.0.0
 *
 * @package    mwb-zendesk-woo-order-sync
 * @subpackage mwb-zendesk-woo-order-sync/Library
 */

if ( ! class_exists( 'MWB_ZENDESK_Settings' ) ) {
	/**
	 * This file manages to send order details to Zendesk.
	 *
	 * @link       https://makewebbbetter.com/
	 * @since      1.0.0
	 *
	 * @package    mwb-zendesk-woo-order-sync
	 * @subpackage mwb-zendesk-woo-order-sync/Library
	 */
	class MWB_ZENDESK_Settings {

		/**
		 * Constructor of the class for fetching the endpoint.
		 *
		 * @since    1.0.0
		 */
		public function __construct() {

			$this->mwb_set_actions();
		}
		/**
		 * Adding actions for settings.
		 *
		 * @since    1.0.0
		 * @access   private
		 */
		public function mwb_set_actions() {

			add_action( 'admin_menu', array( $this, 'register_mwb_zndsk_menu_page' ) );
			add_action( 'add_meta_boxes', array( $this, 'mwb_zndsk_add_meta_boxes' ) );

			add_action( 'wp_ajax_mwb_zndsk_save_order_config_options', array( $this, 'save_order_config_options' ) );

		}

		/**
		 * Create/Register menu items for the plugin.
		 *
		 * @since 1.0
		 */
		public function register_mwb_zndsk_menu_page() {

			add_menu_page(
				esc_html__( 'Zendesk Account Settings', 'zndskwoo' ),
				esc_html__( 'Zendesk Account Settings', 'zndskwoo' ),
				'manage_options',
				'mwb-zendesk-settings',
				array( $this, 'mwb_zndsk_settings' ),
				'dashicons-tickets-alt',
				58
			);

			add_submenu_page( 'mwb-zendesk-settings', esc_html__( 'Order Configuration', 'zndskwoo' ), esc_html__( 'Order Configuration', 'zndskwoo' ), 'manage_options', 'mwb-zendesk-order-config', array( $this, 'mwb_zndsk_order_configuration_html' ) );


		}
		/**
		 * Admin settings.
		 *
		 * @since    1.0.0
		 * @access   private
		 */
		public function mwb_zndsk_settings() {

			$details = get_option( 'mwb_zndsk_account_details', array() );

			?>
			<div class="zndsk_setting_ticket_wrapper">
				<div class="zndsk_setting_wrapper">
					<h2>Zendesk Settings</h2>
					<form action="" method="post">
						<table class="zndsk_setting_table">
							<tbody>
								<tr>
									<td class="zendesk-column zendesk-col-left  zendesk-url-column"><strong>Zendesk Url</strong></td>
									<td class="zendesk-column zendesk-col-right"><input type="text" class="setting_text" name="zndsk_setting_zendesk_url" value="<?php if ( isset( $details['acc_url'] ) ) { echo esc_url( $details['acc_url'] ); } ?>"/></td>
									<td class="zendesk-err-message zendesk-column"><span><?php if ( get_option( 'zendesk_url_error' ) ) { echo esc_html( __( 'Invalid URL', 'zndskwoo' ) ); } ?></span></td>
								</tr>
								<tr>
									<td class="zendesk-column zendesk-col-left zendesk-email-column"><strong>Zendesk Admin Email</strong></td>
									<td class="zendesk-column zendesk-col-right"><input type="text" class="setting_text" name="zndsk_setting_zendesk_user_email" value="<?php if ( isset( $details['acc_email'] ) ) { echo esc_html( $details['acc_email'] ); } ?>"/></td>
									<td class="zendesk-err-message zendesk-column"><span><?php if ( get_option( 'zendesk_email_error' ) ) { echo esc_html( __( 'Invalid Email', 'zndskwoo' ) ); } ?></span></td>
								</tr>
									<td class="zendesk-column zendesk-col-left zendesk-pass-column"><strong>Zendesk API Token</strong></td>
									<td class="zendesk-column zendesk-col-right"><input type="password" class="setting_text" name="zndsk_setting_zendesk_api_token" value="" placeholder="<?php if ( ! empty( $details['acc_api_token'] ) ) { echo esc_html( __( 'Hidden', 'zndskwoo' ) ); } ?>"/>
										<p><a target="_blank" href="https://support.zendesk.com/hc/en-us/articles/226022787-Generating-a-new-API-token-">Generating a new API token &rarr;</a></p>
									</td>
								</tr>	
								<tr>
									<td colspan="2" class="zendesk-submit">
										<input type="submit" class="button button-primary" name="zndsk_setting_save_btn" value="Submit">
									</td>
								</tr>
							</tbody>
						</table>
						<?php wp_nonce_field( 'zndsk_submit', 'zndsk_secure_check' ); ?>
					</form>
				</div>
			</div>
			<?php
		}

		/**
		 * Order Configuration content.
		 *
		 * @since    2.0.2
		 */
		public function mwb_zndsk_order_configuration_html() {

			include_once( MWB_ZENDESK_DIR_PATH . 'admin-templates/order-configuration.php' );
		}

		/**
		 * Adding meta box for showing zendesk tickets.
		 *
		 * @since    1.0.0
		 * @access   private
		 */
		public function mwb_zndsk_add_meta_boxes() {

			add_meta_box( 'mwb_zendesk_tickets', __( 'Zendesk Tickets', 'woocommerce' ), array( $this, 'mwb_zndsk_tickets_config' ), 'shop_order', 'side', 'core' );
		}
		/**
		 * Zendesk tickets layout.
		 *
		 * @since    1.0.0
		 * @access   private
		 */
		public function mwb_zndsk_tickets_config() {

			$tickets = MWB_ZENDESK_Manager::mwb_fetch_useremail();

			if ( ! empty( $tickets ) && 'empty_zndsk_account_details' == $tickets ) {

				?>
					<div style="display:block;">
						<span><?php echo esc_html_e( 'Please Setup Zendesk Account details.', 'zndskwoo' ); ?></span>
					</div>
				<?php

				return;
			}

			if ( ! empty( $tickets ) && 'zndsk_api_error' == $tickets ) {

				?>
					<div style="display:block;">
						<span><?php echo esc_html_e( 'Zendesk API Error. Please enter correct details or contact MakeWebBetter support.', 'zndskwoo' ); ?></span>
					</div>
				<?php

				return;
			}

			if ( ! empty( $tickets ) && is_array( $tickets ) ) {

				foreach ( $tickets as $single_data ) {
					?>
					<div class="zndsk-ticket-content" style="display:block;">
						<button class="data zndsk_accordion">Ticket#<?php echo esc_attr( $single_data['id'] ); ?></button>
						<div class="zndsk_panel">
							<label class="head zendesk-status zendesk-status-left">Status:</label>
							<div class="zendesk-status-right zendesk-status">
								<span class="" data-status="<?php echo esc_attr( $single_data['status'] ); ?>"><?php echo esc_attr( $single_data['status'] ); ?>
								</span>
							</div>	
							<label class="head zendesk-sub zendesk-status-left">Subject:</label>
							<div class="zendesk-status-right zendesk-sub">
								<span class=""><?php echo esc_attr( $single_data['subject'] ); ?>
								</span>
							</div>

							<label class="head zendesk-description zendesk-status-left">Description:</label>
							<div class="zendesk-status-right zendesk-description">
								<span class=""><?php echo esc_attr( $single_data['description'] ); ?>
								</span>
							</div>
						</div>
					</div>
					<?php
				}
			} else {
				?>
					<div class="zndsk-no-ticket" style="display:block;">
						<span><?php echo esc_html_e( 'No tickets found', 'zndskwoo' ); ?></span>
					</div>
				<?php
			}
		}

		/**
		 * Save Zendesk Order Configuration options.
		 *
		 * @since    2.0.2
		 */
		public function save_order_config_options() {

			check_ajax_referer( 'zndsk_security', 'zndskSecurity' );

			$order_config_options = array();

			$order_config_options['latest_orders_count'] =  ! empty( $_POST['latest_orders_count'] ) ? sanitize_text_field( wp_unslash( $_POST['latest_orders_count'] ) ) : '';

			$order_config_options['source_kpi_fields'] = ! empty( $_POST['source_kpi_fields'] ) ? array_map( 'sanitize_text_field', wp_unslash( $_POST['source_kpi_fields'] ) ) : array() ;
			$order_config_options['selected_kpi_fields'] = ! empty( $_POST['selected_kpi_fields'] ) ? array_map( 'sanitize_text_field', wp_unslash( $_POST['selected_kpi_fields'] ) ) : array() ;

			$order_config_options['source_order_fields'] = ! empty( $_POST['source_order_fields'] ) ? array_map( 'sanitize_text_field', wp_unslash( $_POST['source_order_fields'] ) ) : array() ;
			$order_config_options['selected_order_fields'] = ! empty( $_POST['selected_order_fields'] ) ? array_map( 'sanitize_text_field', wp_unslash( $_POST['selected_order_fields'] ) ) : array() ;

			$selected_options_saved = update_option( 'mwb_zndsk_order_config_options', $order_config_options );

			echo json_encode( $selected_options_saved );

			wp_die();
		}
	}
}
$init = new MWB_ZENDESK_Settings();
