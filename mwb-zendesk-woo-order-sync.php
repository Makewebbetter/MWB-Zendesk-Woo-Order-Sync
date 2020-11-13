<?php
/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link https://makewebbetter.com/
 * @since 1.0.0
 * @package mwb-zendesk-woo-order-sync
 *
 * @wordpress-plugin
 * Plugin Name: Order Sync with Zendesk for WooCommerce
 * Plugin URI: https://wordpress.org/plugins/mwb-zendesk-woo-order-sync/
 * Description: Sends your WooCommerce order details to your Zendesk account.
 * Version: 2.0.0
 * Author: makewebbetter
 * Author URI: https://makewebbetter.com/
 * License: GPL-3.0+
 * License URI: https://www.gnu.org/licenses/gpl-3.0.txt
 * Text Domain: zndskwoo
 * Tested up to: 5.5.1
 * WC tested up to: 4.5.1
 * Domain Path: /languages
 */

/**
 * Exit if accessed directly
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$activated = true;

if ( ! in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {

	$activated = false;
}
/**
 * Check if WooCommerce is active
 *
 * @since 1.0.0
 */

if ( $activated ) {

	if ( ! defined( 'MWB_ZENDESK_PREFIX' ) ) {
		define( 'MWB_ZENDESK_PREFIX', 'mwb_zendesk' );
	}

	if ( ! defined( 'MWB_ZENDESK_DIR' ) ) {
		define( 'MWB_ZENDESK_DIR', dirname( __FILE__ ) );
	}

	if ( ! defined( 'MWB_ZENDESK_DIR_URL' ) ) {
		define( 'MWB_ZENDESK_DIR_URL', plugin_dir_url( __FILE__ ) );
	}

	if ( ! defined( 'MWB_ZENDESK_DIR_PATH' ) ) {
		define( 'MWB_ZENDESK_DIR_PATH', plugin_dir_path( __FILE__ ) );
	}

	register_activation_hook( __FILE__, 'mwb_zndsk_activation' );
	register_deactivation_hook( __FILE__, 'mwb_zndsk_deactivation' );
	add_action( 'wp_loaded', 'mwb_zndsk_activation' );
	/**
	 * Activation hook
	 *
	 * @since    1.0.0
	 */
	function mwb_zndsk_activation() {

		do_action( 'mwb_zndsk_init' );
	}
	/**
	 * Deactivation hook
	 *
	 * @since    1.0.0
	 */
	function mwb_zndsk_deactivation() {

		delete_option( 'mwb_zndsk_account_details' );
		delete_option( 'zendesk_email_error' );
		delete_option( 'zendesk_url_error' );
	}
	/**
	 * Permission check
	 *
	 * @since    1.0.0
	 * @param string $request sends request as true.
	 * @return boolean true
	 */
	function mwb_zndsk_get_items_permissions_check( $request ) {

		return true;
	}
	/**
	 * Add connect api file
	 *
	 * @since    1.0.0
	 */
	function mwb_zndsk_add_api_file_for_plugin() {

		// including supporting file of plugin.
		include_once MWB_ZENDESK_DIR . '/class-mwb-zendesk-connect-api.php';
		$mwb_zndsk_instance = MWB_ZENDESK_Connect_Api::get_instance();

		add_action( 'rest_api_init', array( $mwb_zndsk_instance, 'mwb_zndsk_register_routes' ) );
	}

	add_action( 'plugins_loaded', 'mwb_zndsk_add_api_file_for_plugin' );
	/**
	 * Enqueue scripts and styles
	 *
	 * @since    1.0.0
	 */
	function mwb_zndsk_enqueue_script() {

		wp_register_style( 'zndsk_scripts', MWB_ZENDESK_DIR_URL . 'assets/zndsk-admin.css', false, '2.0.0', 'all' );
		wp_enqueue_style( 'zndsk_scripts' );
		wp_register_script( 'zndsk_scripts', MWB_ZENDESK_DIR_URL . 'assets/zndsk-admin.js', array( 'jquery' ), '2.0.0', true );
		wp_enqueue_script( 'zndsk_scripts' );
		wp_localize_script(
			'zndsk_scripts', 'zndsk_ajax_object',
			array(
				'ajax_url'             => admin_url( 'admin-ajax.php' ),
				'zndskSecurity'        => wp_create_nonce( 'zndsk_security' ),
				'zndskMailSuccess'     => __( 'Mail Sent Successfully.', 'zndskwoo' ),
				'zndskMailFailure'     => __( 'Mail not sent', 'zndskwoo' ),
				'zndskMailAlreadySent' => __( 'Mail already sent', 'zndskwoo' ),
			)
		);
	}
	add_action( 'admin_init', 'mwb_zndsk_enqueue_script' );
	/**
	 * Show plugin development notice
	 *
	 * @since    1.0.0
	 */
	function mwb_zndsk_admin_notice__success() {

		$suggest_sent    = get_option( 'zendesk_suggestions_sent', '' );
		$suggest_ignored = get_option( 'zendesk_suggestions_later', '' );
		?>
		<div class="notice notice-success mwb-zndsk-form-div" style="<?php echo ( '1' === $suggest_sent || '1' === $suggest_ignored ) ? 'display: none;' : 'display: block;'; ?>">
			<p><?php esc_html_e( 'Support the MWB Zendesk Woo Order Sync plugin development by sending us tracking data( we just want your Email Address and Name that too only once ).', 'zndskwoo' ); ?></p>
			<input type="button" class="button button-primary mwb-accept-button" name="mwb_accept_button" value="Accept">
			<input type="button" class="button mwb-reject-button" name="mwb_reject_button" value="Ignore">
		</div>
		<div style="display: none;" class="loading-style-bg" id="zndsk_loader">
			<img src="<?php echo esc_url( MWB_ZENDESK_DIR_URL . 'assets/images/loader.gif' ); ?>">
		</div>
		<?php
	}
	add_action( 'admin_notices', 'mwb_zndsk_admin_notice__success' );

} else {
	/**
	 * Error notice
	 *
	 * @since    1.0.0
	 */
	function mwb_zndsk_plugin_error_notice() {
		?>
			<div class="error notice is-dismissible">
			<p><?php esc_html_e( 'WooCommerce is not activated, please activate WooCommerce first to install and use Order Sync with Zendesk for WooCommerce plugin.', 'zndskwoo' ); ?></p>
			</div>
			<style>
			#message{display:none;}
			</style>
			<?php
	}

	add_action( 'admin_init', 'mwb_zndsk_plugin_deactivate' );
	/**
	 * Deactivation hook
	 *
	 * @since    1.0.0
	 */
	function mwb_zndsk_plugin_deactivate() {

		deactivate_plugins( plugin_basename( __FILE__ ) );
		add_action( 'admin_notices', 'mwb_zndsk_plugin_error_notice' );
	}
}
