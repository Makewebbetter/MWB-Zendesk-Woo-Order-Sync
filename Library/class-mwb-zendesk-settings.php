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
		}

		/**
		 * Create/Register menu items for the plugin.
		 *
		 * @since 1.0
		 */
		public function register_mwb_zndsk_menu_page() {

			add_menu_page(
				__( 'Zendesk Account Settings', 'zndskwoo' ),
				'Zendesk Account Settings',
				'manage_options',
				'zendesk_settings',
				array( $this, 'mwb_zndsk_settings' ),
				'dashicons-admin-generic',
				6
			);
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
					<h2>Zendesk Settings:</h2>
					<form action="" method="post">
						<table class="zndsk_setting_table">
							<tbody>
								<tr>
									<td class="zendesk-column zendesk-col-left  zendesk-url-column">Zendesk Url:-</td>
									<td class="zendesk-column zendesk-col-right"><input type="text" class="setting_text" name="zndsk_setting_zendesk_url" value="<?php if ( $details['acc_url'] ) { echo esc_url( $details['acc_url'] ); } ?>"/></td>
									<td class="zendesk-err-message zendesk-column"><span><?php if ( get_option( 'zendesk_url_error' ) ) { echo esc_html( __( 'Invalid URL', 'zndskwoo' ) ); } ?></span></td>
								</tr>
								<tr>
									<td class="zendesk-column zendesk-col-left zendesk-email-column">Zendesk Admin Email:-</td>
									<td class="zendesk-column zendesk-col-right"><input type="text" class="setting_text" name="zndsk_setting_zendesk_user_email" value="<?php if ( $details['acc_email'] ) { echo esc_html( $details['acc_email'] ); } ?>"/></td>
									<td class="zendesk-err-message zendesk-column"><span><?php if ( get_option( 'zendesk_email_error' ) ) { echo esc_html( __( 'Invalid Email', 'zndskwoo' ) ); } ?></span></td>
								</tr>
								<tr>
									<td class="zendesk-column zendesk-col-left zendesk-pass-column">Zendesk Password:-</td>
									<td class="zendesk-column zendesk-col-right"><input type="password" class="setting_text" name="zndsk_setting_zendesk_pass" value="" placeholder="<?php if ( '' != $details['acc_pass'] ) { echo esc_html( __( 'Hidden', 'zndskwoo' ) ); } ?>"/></td>
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
	}
}
$init = new MWB_ZENDESK_Settings();
