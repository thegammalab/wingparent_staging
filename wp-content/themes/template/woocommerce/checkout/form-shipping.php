<?php
/**
* Checkout shipping information form
*
* This template can be overridden by copying it to yourtheme/woocommerce/checkout/form-shipping.php.
*
* HOWEVER, on occasion WooCommerce will need to update template files and you
* (the theme developer) will need to copy the new files to your theme to
* maintain compatibility. We try to do this as little as possible, but it does
* happen. When this occurs the version of the template file will be bumped and
* the readme will list any important changes.
*
* @see     https://docs.woocommerce.com/document/template-structure/
* @author  WooThemes
* @package WooCommerce/Templates
* @version 3.0.9
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

?>
<script>
jQuery(document).ready(function(){
	jQuery("#address_sel_wrapper").prepend(jQuery("#shipping_alt_field"));
	jQuery("#shipping_alt_field").find("label").html("<?php _e("Ship to"); ?>");
	jQuery("#shipping_alt_field").find("select").find("option:first").remove();
})
</script>
<div class="woocommerce-shipping-fields mb-5">
	<?php if ( true === WC()->cart->needs_shipping_address() ) : ?>
		<h3 id="ship-to-different-address" class="mb-4 mt-2"><?php _e( 'Shipping address', 'woocommerce' ); ?></h3>
		<div class="card">
			<div class="card-body">
				<div id="address_sel_wrapper"><a href="<?php echo get_permalink( wc_get_page_id( 'myaccount' ) ); ?>/ma-manage-address/"><?php _e("Add"); ?></a></div>
				<h4 class="card-title"><span id="val_shipping_first_name"></span> <span id="val_shipping_last_name"></span></h4>
				<h6 class="card-subtitle"><i class="fa fa-map-marker"></i> <span id="val_shipping_address_1"></span> <span id="val_shipping_address_1"></span> <span id="val_shipping_city"></span> <span id="val_shipping_state"></span> <span id="val_shipping_postcode"></span></h6>

					<div class="woocommerce-additional-fields">
						<?php do_action( 'woocommerce_before_order_notes', $checkout ); ?>

						<?php if ( apply_filters( 'woocommerce_enable_order_notes_field', 'yes' === get_option( 'woocommerce_enable_order_comments', 'yes' ) ) ) : ?>

							<?php if ( ! WC()->cart->needs_shipping() || wc_ship_to_billing_address_only() ) : ?>

								<h3><?php _e( 'Additional information', 'woocommerce' ); ?></h3>

							<?php endif; ?>

							<div class="woocommerce-additional-fields__field-wrapper">
								<?php foreach ( $checkout->get_checkout_fields( 'order' ) as $key => $field ) : ?>
									<?php woocommerce_form_field( $key, $field, $checkout->get_value( $key ) ); ?>
								<?php endforeach; ?>
							</div>

						<?php endif; ?>

						<?php do_action( 'woocommerce_after_order_notes', $checkout ); ?>
					</div>
								</div>
			</div>

			<div class="" style="display: none;">
				<input id="ship-to-different-address-checkbox" class="woocommerce-form__input woocommerce-form__input-checkbox input-checkbox" <?php checked(1); ?> type="checkbox" name="ship_to_different_address" value="1" />

				<?php do_action( 'woocommerce_before_checkout_shipping_form', $checkout ); ?>

				<div class="woocommerce-shipping-fields__field-wrapper">
					<?php
					$fields = $checkout->get_checkout_fields( 'shipping' );

					foreach ( $fields as $key => $field ) {
						if ( isset( $field['country_field'], $fields[ $field['country_field'] ] ) ) {
							$field['country'] = $checkout->get_value( $field['country_field'] );
						}
						woocommerce_form_field( $key, $field, $checkout->get_value( $key ) );
					}
					?>
				</div>

				<?php do_action( 'woocommerce_after_checkout_shipping_form', $checkout ); ?>

			</div>

		<?php endif; ?>
	</div>
