<?php
/**
 * Plugin Name: Zendesk Woocommerce Order Sync
 * Plugin URI: http://makewebbetter.com
 * Description: Get your WooCommerce order details to your Zendesk account.
 * Author: Makewebbetter
 * Author URI: http://makewebbetter.com
 * Version: 1.0.0
 * Requires at least: 3.8
 * Tested up to: 4.9.4
 * WC Tested up to: 3.4.3
 * Text Domain: zndskwoo
 * Domain Path: /i18n/languages
 */

/**
 * Exit if accessed directly
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; 
}

$activated = true;
if ( function_exists('is_multisite') && is_multisite() ) {
	
	include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
	
	if ( !is_plugin_active( 'woocommerce/woocommerce.php' ) ) {
		
		$activated = false;
	}
}
else {
	
	if ( ! in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
		
		$activated = false;
	}
}

/**
 * Check if WooCommerce is active
 **/
if ( $activated ) {
	
	if( !defined( 'MWB_ZENDESK_PREFIX' ) )
		define( 'MWB_ZENDESK_PREFIX', 'mwb_zendesk' );
	
	if( !defined('MWB_ZENDESK_DIR' ) )
		define( 'MWB_ZENDESK_DIR', dirname( __FILE__ ) );
	
	if( !defined( 'MWB_ZENDESK_DIR_URL' ) )
		define( 'MWB_ZENDESK_DIR_URL', plugin_dir_url( __FILE__ ) );
	
	if( !defined( 'MWB_ZENDESK_DIR_PATH' ) )
		define( 'MWB_ZENDESK_DIR_PATH', plugin_dir_path( __FILE__ ) );
	
	register_activation_hook(	__FILE__, 'mwb_zndsk_activation' );
	add_action(	"wp_loaded", 'mwb_zndsk_activation' );
	/**
	 * Activation hook
	 *
	 * @since    1.0.0
	 */
	function mwb_zndsk_activation() {
		
		do_action( 'mwb_zndsk_init' );
	}

	/**
	 * Permission check
	 *
	 * @since    1.0.0
	 */
	function mwb_zndsk_get_items_permissions_check( $request ) {
		
		return true;
	}


	function mwb_zndsk_add_api_file_for_plugin() {

		//including supporting file of plugin.
		include_once MWB_ZENDESK_DIR."/mwb_zendeskwoocommerce_api.php";
		$mwb_zndsk_instance = MWB_ZENDESK_connect_api::getInstance();
	
		add_action( 'rest_api_init', array( $mwb_zndsk_instance, 'mwb_zndsk_register_routes' ) );
		
		add_action( 'init', array( $mwb_zndsk_instance, 'mwb_zndsk_init_steps' ) );
	}

	add_action( 'plugins_loaded', 'mwb_zndsk_add_api_file_for_plugin' );

}

else {
	/**
	 * Error notice
	 *
	 * @since    1.0.0
	 */
	function mwb_zndsk_plugin_error_notice() {
		?>
			<div class="error notice is-dismissible">
			<p><?php _e( 'Woocommerce is not activated, please activate woocommerce first to install and use zendesk woocommerce plugin.', 'zndskwoo' ); ?></p>
			</div>
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
		
		global $wp_rewrite;
		$wp_rewrite->flush_rules();
		
		add_action( 'admin_notices', 'mwb_zndsk_plugin_error_notice' );
	}
}