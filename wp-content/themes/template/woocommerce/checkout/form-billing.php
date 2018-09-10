<?php
/**
* Checkout billing information form
*
* This template can be overridden by copying it to yourtheme/woocommerce/checkout/form-billing.php.
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

/** @global WC_Checkout $checkout */


?>
<div class="woocommerce-billing-fields mb-5">
	<?php if ( wc_ship_to_billing_address_only() && WC()->cart->needs_shipping() ) : ?>

		<h3 class="mb-4 mt-2"><?php _e( 'Billing &amp; Shipping', 'woocommerce' ); ?></h3>

	<?php else : ?>

		<h3 class="mb-4 mt-2"><?php _e( 'Billing details', 'woocommerce' ); ?></h3>

	<?php endif; ?>
	<div class="card">
		<div class="card-body">
			<h4 class="card-title"><span id="val_billing_first_name">Mihai</span> <span id="val_billing_last_name">Curteanu</span></h4>
			<h6 class="card-subtitle"><i class="fa fa-map-marker"></i> <span id="val_billing_address_1"></span> <span id="val_billing_address_1"></span> <span id="val_billing_city"></span> <span id="val_billing_state"></span> <span id="val_billing_postcode"></span></h6>
			<p class="card-text"><b>Phone:</b><span id="val_billing_phone"></span></p>
			<p class="card-text"><b>Email:</b><span id="val_billing_email"></span></p>
			<p class="card-text"><b>Company:</b><span id="val_billing_company"></span></p>
			<p class="card-text"><b>VAT:</b><span id="val_billing_vat"></span></p>

			<script>
jQuery(document).ready(function(){
	jQuery("#shipping_alt option").each(function(){
		if(jQuery(this).attr("value")==1){
			jQuery(this).append(" (FREE)");
		}else{
			jQuery(this).append(" (+&euro;5.50)");
		}
	})

	jQuery("#shipping_alt").change(function(){
		jQuery.ajax({
		  url: "<?php echo get_bloginfo("url")."/wp-admin/admin-ajax.php?action=update_ship_address&val="; ?>"+jQuery(this).val(),
		  context: document.body
		}).done(function(data){
			if(data==1){
				jQuery("#shipping_alt").change();
				location.reload();
			}
		})
	});
	jQuery("#shipping_alt").val(<?php echo get_option("shipping_address_".get_current_user_id()); ?>);
	jQuery("#shipping_alt").change();
})

setInterval(function(){
	jQuery("#val_shipping_first_name").html(jQuery("#shipping_first_name_field input").val());
	jQuery("#val_shipping_last_name").html(jQuery("#shipping_last_name_field input").val());
	jQuery("#val_shipping_address_1").html(jQuery("#shipping_address_1_field input").val());
	jQuery("#val_shipping_address_2").html(jQuery("#shipping_address_2_field input").val());
	jQuery("#val_shipping_state").html(jQuery("#shipping_state_field input").val());
	jQuery("#val_shipping_city").html(jQuery("#shipping_city_field input").val());
	jQuery("#val_shipping_postcode").html(jQuery("#shipping_postcode_field input").val());

	jQuery("#val_billing_first_name").html(jQuery("#billing_first_name_field input").val());
	jQuery("#val_billing_last_name").html(jQuery("#billing_last_name_field input").val());
	jQuery("#val_billing_address_1").html(jQuery("#billing_address_1_field input").val());
	jQuery("#val_billing_address_2").html(jQuery("#billing_address_2_field input").val());
	jQuery("#val_billing_state").html(jQuery("#billing_state_field input").val());
	jQuery("#val_billing_city").html(jQuery("#billing_city_field input").val());
	jQuery("#val_billing_postcode").html(jQuery("#billing_postcode_field input").val());

	jQuery("#val_billing_phone").html(jQuery("#billing_phone_field input").val());
	jQuery("#val_billing_email").html(jQuery("#billing_email_field input").val());

	if(jQuery("#billing_company_field input").val()){
		jQuery("#val_billing_company").html(jQuery("#billing_company_field input").val());
	}else{
		jQuery("#val_billing_company").html("N/A");
	}

	if(jQuery("#vat_number_field input").val()){
		jQuery("#val_billing_vat").html(jQuery("#vat_number_field input").val());
	}else{
		jQuery("#val_billing_vat").html("N/A");
	}

	//jQuery(".shipping td").html(jQuery(".shipping td .woocommerce-Price-amount")[0].outerHTML + jQuery(".shipping td .tax_label")[0].outerHTML + jQuery(".shipping td input")[0].outerHTML);
},500);
			</script>

			<button type="button" class="card-link text-uppercase w-100" data-toggle="modal" data-target="#update_billing">Edit billing information</button>
		</div>
	</div>

	<div class="modal fade" id="update_billing" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="exampleModalLabel">Update Billing Info</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<div id="edit_billing_form" style="display:none1;">
						<?php do_action( 'woocommerce_before_checkout_billing_form', $checkout ); ?>

						<div class="woocommerce-billing-fields__field-wrapper">
							<?php
							$fields = $checkout->get_checkout_fields( 'billing' );

							foreach ( $fields as $key => $field ) {
								if ( isset( $field['country_field'], $fields[ $field['country_field'] ] ) ) {
									$field['country'] = $checkout->get_value( $field['country_field'] );
								}
								woocommerce_form_field( $key, $field, $checkout->get_value( $key ) );
							}
							?>
						</div>

						<?php do_action( 'woocommerce_after_checkout_billing_form', $checkout ); ?>
					</div>
					<hr />
					<button class="btn btn-secondary w-100" data-dismiss="modal">Update</button>

				</div>
			</div>
		</div>
	</div>

</div>

<?php if ( ! is_user_logged_in() && $checkout->is_registration_enabled() ) : ?>
	<div class="woocommerce-account-fields">
		<?php if ( ! $checkout->is_registration_required() ) : ?>

			<p class="form-row form-row-wide create-account">
				<label class="woocommerce-form__label woocommerce-form__label-for-checkbox checkbox">
					<input class="woocommerce-form__input woocommerce-form__input-checkbox input-checkbox" id="createaccount" <?php checked( ( true === $checkout->get_value( 'createaccount' ) || ( true === apply_filters( 'woocommerce_create_account_default_checked', false ) ) ), true ) ?> type="checkbox" name="createaccount" value="1" /> <span><?php _e( 'Create an account?', 'woocommerce' ); ?></span>
				</label>
			</p>

		<?php endif; ?>

		<?php do_action( 'woocommerce_before_checkout_registration_form', $checkout ); ?>

		<?php if ( $checkout->get_checkout_fields( 'account' ) ) : ?>

			<div class="create-account">
				<?php foreach ( $checkout->get_checkout_fields( 'account' ) as $key => $field ) : ?>
					<?php woocommerce_form_field( $key, $field, $checkout->get_value( $key ) ); ?>
				<?php endforeach; ?>
				<div class="clear"></div>
			</div>

		<?php endif; ?>

		<?php do_action( 'woocommerce_after_checkout_registration_form', $checkout ); ?>
	</div>
<?php endif; ?>
