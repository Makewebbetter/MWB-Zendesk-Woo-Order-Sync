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
if ( empty( $tickets ) ) {

	?>
		<div class="mwb-error-messege" style="display:block;" >
			<span><?php echo esc_html_e( 'Presently no ticket available', 'zndskwoo' ); ?></span>
		</div>
	<?php

}
if ( ! empty( $tickets ) && is_array( $tickets ) ) {
	$user_data = wp_get_current_user();
	$user_mail = $user_data->data->user_email;

	$zndsk_acc_details = get_option( 'mwb_zndsk_account_details' );
?>
<div class="mwb-zndsk-ticket-table">
	<table class="woocommerce-orders-table woocommerce-MyAccount-orders shop_table shop_table_responsive my_account_orders account-orders-table">
		<thead>
			<tr>
									<th class="woocommerce-orders-table__header woocommerce-orders-table__header-order-subject><span class="nobr"><?php esc_html_e( 'Ticket-id', 'zndskwoo' ); ?></span></th>
									<th class="woocommerce-orders-table__header woocommerce-orders-table__header-order-number><span class="nobr"><?php esc_html_e( 'Subject', 'zndskwoo' ); ?></span></th>
									<th class="woocommerce-orders-table__header woocommerce-orders-table__header-order-description><span class="nobr"><?php esc_html_e( 'Description', 'zndskwoo' ); ?></span></th>
									<th class="woocommerce-orders-table__header woocommerce-orders-table__header-order-status><span class="nobr"><?php esc_html_e( 'Status', 'zndskwoo' ); ?></span></th>
									<th class="woocommerce-orders-table__header woocommerce-orders-table__header-order-actions><span class="nobr"><?php esc_html_e( 'Update', 'zndskwoo' ); ?></span></th>
			</tr>
		</thead>
		<tbody>
			<?php foreach ( $tickets as $single_data ) { ?>
				<tr class="woocommerce-orders-table__row woocommerce-orders-table__row--status-processing order">
					<td class="woocommerce-orders-table__cell woocommerce-orders-table__cell-order-number" data-title="Order">
						<?php echo esc_attr( $single_data['id'] ); ?>
					</td>
					<td class="woocommerce-orders-table__cell woovaluecommerce-orders-table__cell-order-subject" data-title="subject">
						<?php echo esc_attr( $single_data['subject'] ); ?>
					</td>
					<td class="woocommerce-orders-table__cell woocommerce-orders-table__cell-order-description" data-title="description">
						<?php echo esc_attr( $single_data['description'] ); ?>
					</td>
					<td class="woocommerce-orders-table__cell woocommerce-orders-table__cell-order-status" data-title="Status">
						<?php echo esc_attr( $single_data['status'] ); ?>
					</td>
					<td class="woocommerce-orders-table__cell woocommerce-orders-table__cell-order-actions" data-title="Actions">
					<a href="#TB_inline?&width=600&height=500&inlineId=<?php echo esc_attr( $single_data['id'] );?>" class="thickbox mwb-zndsk-update-ticket"><?php esc_html_e( 'Update', 'zndskwoo' ); ?></a>
						<div id="<?php echo esc_attr( $single_data['id'] ); ?>" style="display:none;">
							<form action="" method="post">
								<input type="hidden" name="nonce-for-update" value= "<?php echo wp_create_nonce ( 'zndsk_ticket_updates' ); ?>">
								<div class="mwb-ticket-id">
									<p><b><?php esc_html_e( 'Ticket-id-', 'zndskwoo' ); ?></b><label for="ticket-id"><?php echo esc_attr( $single_data['id'] ); ?></label></p>
									<input type="hidden" name="mwb-ticket-no" value="<?php echo esc_attr( $single_data['id'] ); ?>">
								</div>
								<div id="mwb-updated-subject" >
								<p><b><?php esc_html_e( 'Subject-', 'zndskwoo' ); ?></b><label for="ticket-subject"><?php echo esc_attr( $single_data['subject'] ); ?></label></p>
								<p><input type="hidden"  name="mwb-update-subject" value ="<?php echo esc_attr( $single_data['subject'] ); ?>"></p>
								</div>
								<div id="mwb-updated-comment" >
									<label for="ticket-comment"><?php esc_html_e( 'Comment', 'zndskwoo' ); ?></label>
									<p><input type="text" name="mwb-update-comment" value ="<?php echo esc_attr( $single_data['description'] ); ?>"></p>
								</div>
								<?php
									$select_array_email = array();
									$customer_orders    = get_posts(
										array(
											'numberposts' => -1,
											'meta_key'    => '_customer_user',
											'meta_value'  => get_current_user_id(),
											'post_type'   => wc_get_order_types(),
											'post_status' => array_keys( wc_get_order_statuses() ),
										)
									);
									if ( ! empty( $customer_orders ) && is_array( $customer_orders ) ) {
										foreach ( $customer_orders as $key => $value ) {
											$order = wc_get_order( $value->ID );
											$order_data = $order->get_data();
											$user_email = $order_data['billing']['email'];
											if( ! in_array( $user_email, $select_array_email ,true ) ) {
												array_push( $select_array_email, $order_data['billing']['email'] );
											}
										}
									?>
									<div id="select_box_email_ticket_create">
										<p><label for="email"><?php esc_html_e( 'Choose your Email', 'zndskwoo' ); ?></label></p>
										<p>
											<select name="mwb-update-email">
											<?php foreach ( $select_array_email as $key => $value ) { ?>
											<option value="<?php esc_attr_e( $value ); ?>" ><?php esc_attr_e( $value ); ?></option> 
											<?php  }?>
											</select>
										</p>
									</div>
									<?php } else {
									?>
										<div>
										<input type="email" name="mwb-create-email" value="<?php echo $user_mail; ?>">
										</div>
										<?php
									} ?>
									<div>
								<div>
								<p>
									<input type="submit"  name="update_ticket_all" class="update_ticket_all" value="Update-Ticket">
								</p>
								</div>
							</form>
						</div>
				</tr>
			<?php } } ?>
		</tbody>
	</table>
	<?php
		$user_data = wp_get_current_user();
		$user_mail = $user_data->data->user_email;
	?>
	<div class="form-button">
		<button class="mwb-zendesk-hitbutton"><b><?php esc_html_e( 'Add a new ticket from here', 'zndskwoo' ); ?></b></button>
	</div>
</div>
<div class="mwb-zendesk-ticket-form">
	<form action="" method="POST">
		<input type="hidden" name="nonce" value= "<?php echo wp_create_nonce ( 'zndsk_ticket_check' ); ?>">
		<div>
			<label for="Subject"><?php esc_html_e( 'Subject', 'zndskwoo' ); ?></label>
			<p><input type="text" id="mwb-create-subject" name="mwb-create-subject"></p>
			<p class="mwb-subject-error"></p>
		</div>
		<div>
			<label for="Comment"><?php esc_html_e( 'Comment', 'zndskwoo' ); ?></label>
			<p><input type="text" id="mwb-create-comment" name="mwb-create-comment"></p>
			<p class="mwb-error-comment"></p>
		</div>
		<?php
			$select_array_email = array();
			$customer_orders    = get_posts(
				array(
					'numberposts' => -1,
					'meta_key'    => '_customer_user',
					'meta_value'  => get_current_user_id(),
					'post_type'   => wc_get_order_types(),
					'post_status' => array_keys( wc_get_order_statuses() ),
				)
			);
			if ( ! empty( $customer_orders ) && is_array( $customer_orders ) ) {
				foreach ( $customer_orders as $key => $value ) {
					$order = wc_get_order( $value->ID );
					$order_data = $order->get_data();
					$user_email = $order_data['billing']['email'];
					if( ! in_array( $user_email, $select_array_email ,true ) ) {
						array_push( $select_array_email, $order_data['billing']['email'] );
					}
				}
			?>
			<div id="select_box_email_ticket_create">
				<p><label for="email"><?php esc_html_e( 'Choose Your Email', 'zndskwoo' ); ?></label></p>
				<p>
					<select name="mwb-create-email">
					<?php foreach ( $select_array_email as $key => $value ) { ?>
					<option value="<?php esc_attr_e( $value ); ?>"> <?php esc_attr_e( $value ); ?></option> 
					<?php  }?>
					</select>
				</p>
			</div>
			<?php } else {
				?>
				<div>
				<input type="email" name="mwb-create-email" value="<?php echo $user_mail; ?>">
				</div>
				<?php
			} ?>
			<div>
				<p><input type="submit" id="mwb-create-submit-ticket" name="submit_ticket_all" value="Create Ticket"></p>
			</div>
	</form>
	<div>
			<button class="return-back"><?php esc_html_e( 'Return', 'zndskwoo' ); ?></button>
	</div>
</div>
