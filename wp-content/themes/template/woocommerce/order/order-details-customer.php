<?php
/**
 * Order Customer Details
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/order/order-details-customer.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see 	https://docs.woocommerce.com/document/template-structure/
 * @author  WooThemes
 * @package WooCommerce/Templates
 * @version 3.3.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
$show_shipping = ! wc_ship_to_billing_address_only() && $order->needs_shipping_address();
?>
<section class="woocommerce-customer-details">

	<?php if ( $show_shipping ) : ?>

	<section class="woocommerce-columns woocommerce-columns--2 woocommerce-columns--addresses addresses">
		<div class="woocommerce-column woocommerce-column--1 woocommerce-column--billing-address woocommerce-billing-fields">

	<?php endif; ?>

<hr />
<div class="row mt-4">
	<div class="col-lg-6">
	<h4 class="woocommerce-column__title mb-3"><?php _e( 'Billing address', 'woocommerce' ); ?></h4>
	<div class="card">
		<div class="card-body">
			<h4 class="card-title"><?php echo $order->get_billing_first_name()." ".$order->get_billing_last_name(); ?></h4>
			<h6 class="card-subtitle"><i class="fa fa-map-marker"></i> <?php echo $order->get_billing_address_1()." ".$order->get_billing_address_2()." ".$order->get_billing_city()." ".$order->get_billing_state()." ".$order->get_billing_postcode(); ?></h6>
			<p class="card-text"><b>Phone:</b> <?php echo esc_html( $order->get_billing_phone() ); ?></p>
			<p class="card-text"><b>Email:</b> <?php echo esc_html( $order->get_billing_email() ); ?></p>
			<p class="card-text"><b>Company:</b> <?php $comp_name = esc_html( $order->get_billing_company() ); if($comp_name){echo $comp_name;}else{echo "N/A";} ?></p>
			<p class="card-text"><b>VAT:</b>N/A</p>
		</div>
	</div>
</div>
<div class="col-lg-6">



			<div class="woocommerce-column woocommerce-column--2 woocommerce-column--shipping-address woocommerce-shipping-fields">
				<h4 class="woocommerce-column__title mb-3"><?php _e( 'Shipping address', 'woocommerce' ); ?></h4>
				<div class="card">
					<div class="card-body">
						<h4 class="card-title"><?php echo $order->get_shipping_first_name()." ".$order->get_shipping_last_name(); ?></h4>
						<h6 class="card-subtitle"><i class="fa fa-map-marker"></i> <?php echo $order->get_shipping_address_1()." ".$order->get_shipping_address_2()." ".$order->get_shipping_city()." ".$order->get_shipping_state()." ".$order->get_shipping_postcode(); ?></h6>
						<p class="card-text"><b class="w-100">Additional Requests:</b></p>
						<p class="card-text">N/A</p>
					</div>
			</div><!-- /.col-2 -->


</div>
</div>



</section>
