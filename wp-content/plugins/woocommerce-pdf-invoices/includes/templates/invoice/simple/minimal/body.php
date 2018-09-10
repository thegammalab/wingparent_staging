<?php
/**
* PDF invoice template body.
*
* This template can be overridden by copying it to youruploadsfolder/woocommerce-pdf-invoices/templates/invoice/simple/yourtemplatename/body.php.
*
* HOWEVER, on occasion WooCommerce PDF Invoices will need to update template files and you
* (the theme developer) will need to copy the new files to your theme to
* maintain compatibility. We try to do this as little as possible, but it does
* happen. When this occurs the version of the template file will be bumped and
* the readme will list any important changes.
*
* @author  Bas Elbers
* @package WooCommerce_PDF_Invoices/Templates
* @version 0.0.1
*/

$templater                      = WPI()->templater();
$order                          = $templater->order;
$invoice                        = $templater->invoice;
$line_items                     = $order->get_items( 'line_item' );
$formatted_shipping_address     = $order->get_formatted_shipping_address();
$formatted_billing_address      = $order->get_formatted_billing_address();
$columns                        = $invoice->get_columns();
$color                          = $templater->get_option( 'bewpi_color_theme' );
$terms                          = $templater->get_option( 'bewpi_terms' );


$order_id = trim(str_replace('#', '', $order->get_order_number()));
$the_prods = array();
foreach($line_items as $item){

	$the_prods_data[]=array(get_the_title($item->get_product_id()), $item->get_subtotal(), $item->get_quantity());
	$the_prods[sanitize_title(get_the_title($item->get_product_id()))] = array("unit_price"=>($item->get_subtotal()/$item->get_quantity()), "tax"=>$item->get_total_tax(), "total"=>$item->get_total()+$item->get_total_tax(), "subtotal"=>$item->get_subtotal());

}


?>

<div class="title">
	<div>
		<h2><?php echo esc_html( $templater->get_option( 'bewpi_title' ) )." #".get_post_meta($order_id,"_bewpi_invoice_number",true); ?></h2>
		<h5 style="margin-top:-20px;">Invoice Date: <?php echo date("d/m/Y",strtotime($order->order_date)); ?></h5>
	</div>
	<div class="watermark">
		<?php
		if ( $templater->get_option( 'bewpi_show_payment_status' ) && $order->is_paid() ) {
			printf( '<h2 class="green">%s</h2>', esc_html__( 'Paid', 'woocommerce-pdf-invoices' ) );
		}

		do_action( 'wpi_watermark_end', $order, $invoice );
		?>
	</div>
</div>
<table cellpadding="0" cellspacing="0">
	<tr class="information">
		<td width="50%">
			<?php echo nl2br( $templater->get_option( 'bewpi_company_address' ) ); ?>
		</td>

		<td>
			<?php
			if ( $templater->get_option( 'bewpi_show_ship_to' ) && ! empty( $formatted_shipping_address ) && $formatted_shipping_address !== $formatted_billing_address && ! $templater->has_only_virtual_products( $line_items ) ) {
				printf( '<strong>%s</strong><br />', esc_html__( 'Ship to:', 'woocommerce-pdf-invoices' ) );
				echo $formatted_shipping_address;
			}
			?>
		</td>

		<td>
			<?php echo $formatted_billing_address; ?>
		</td>
	</tr>
</table>

<?php
foreach($columns as $key=>$data){
	if($key=="total"){
		$new_columns["unit"]=__("Per Unit (excl. tax)");
		$new_columns["total"]=__("Subtotal (excl. tax)");
		$new_columns["tax"]=__("Tax");
		$new_columns["total_tax"]=__("Total (incl. tax)");

	}else{
		$new_columns[$key]=$data;
	}
}
$columns = $new_columns;
?>
<table cellpadding="0" cellspacing="0">
	<thead>
		<tr class="heading" bgcolor="<?php echo esc_attr( $color ); ?>;">
			<?php
			foreach ( $columns as $key => $data ) {
				$templater->display_header_recursive( $key, $data );
			}
			?>
		</tr>
	</thead>
	<tbody>
		<?php
		foreach ( $invoice->get_columns_data() as $index => $row ) {
			echo '<tr class="item">';

			// Display row data.
			foreach ( $row as $column_key => $data ) {
				if($column_key=="total"){
					echo '<td>';
					echo wc_price($the_prods[sanitize_title($row["description"])]["unit_price"]);
					echo '</td>';
					// $templater->display_data_recursive( $column_key, $data );
					echo '<td>';
					echo wc_price($the_prods[sanitize_title($row["description"])]["subtotal"]);
					echo '</td>';
					echo '<td>';
					echo wc_price($the_prods[sanitize_title($row["description"])]["tax"]);
					echo '</td>';
					echo '<td>';
					echo wc_price($the_prods[sanitize_title($row["description"])]["total"]);
					echo '</td>';
				}else{
					$templater->display_data_recursive( $column_key, $data );
				}
			}

			echo '</tr>';
		} // End foreach().
		?>

		<tr class="spacer">
			<td></td>
		</tr>

	</tbody>
</table>

<table cellpadding="0" cellspacing="0">
	<tbody>
		<tr class="total">
			<td width="50%"></td>

			<td width="25%" align="left" class="border <?php echo esc_attr( $class ); ?>">
				<?php _e("Subtotal (excl. tax)"); ?>
			</td>

			<td width="25%" align="right" class="border <?php echo esc_attr( $class ); ?>">
				<?php echo wc_price(str_replace( '&nbsp;', '', $invoice->order->subtotal )); ?>

			</td>
		</tr>
		<?php
		foreach ( $invoice->get_order_item_totals() as $key => $total ) {
			$class = str_replace( '_', '-', $key );
			?>
			<tr class="total">
				<td width="50%"></td>

				<td width="25%" align="left" class="border <?php echo esc_attr( $class ); ?>">
					<?php
					if($key=="discount"){
						echo "WingBoost";
					}elseif($key=="cart_subtotal"){
						echo str_replace(":","",$total['label'])." ".__("(incl. tax)");
					}else{
						echo $total['label'];
					}
					?>
				</td>

				<td width="25%" align="right" class="border <?php echo esc_attr( $class ); ?>">
					<?php echo str_replace( '&nbsp;', '', $total['value'] ); ?>
				</td>
			</tr>

		<?php } ?>
	</tbody>
</table>

<table class="notes" cellpadding="0" cellspacing="0">
	<tr>
		<td>
			<?php
			// Customer notes.
			if ( $templater->get_option( 'bewpi_show_customer_notes' ) ) {
				// Note added by customer.
				$customer_note = BEWPI_WC_Order_Compatibility::get_customer_note( $order );
				if ( $customer_note ) {
					printf( '<strong>' . __( 'Note from customer: %s', 'woocommerce-pdf-invoices' ) . '</strong><br />', nl2br( $customer_note ) );
				}

				// Notes added by administrator on 'Edit Order' page.
				foreach ( $order->get_customer_order_notes() as $custom_order_note ) {
					printf( '<strong>' . __( 'Note to customer: %s', 'woocommerce-pdf-invoices' ) . '</strong><br />', nl2br( $custom_order_note->comment_content ) );
				}
			}
			?>
		</td>
	</tr>

	<tr>
		<td>
			<?php
			// Zero Rated VAT message.
			if ( 'true' === $templater->get_meta( '_vat_number_is_valid' ) && count( $order->get_tax_totals() ) === 0 ) {
				_e( 'Zero rated for VAT as customer has supplied EU VAT number', 'woocommerce-pdf-invoices' );
				printf( '<br />' );
			}
			?>
		</td>
	</tr>
</table>

<?php if ( $terms ) { ?>
	<!-- Using div to position absolute the block. -->
	<div class="terms">
		<table>
			<tr>
				<td style="border: 1px solid #000;">
					<?php echo nl2br( $terms ); ?>
				</td>
			</tr>
		</table>
	</div>
<?php }
