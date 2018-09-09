<?php
/**
* Related Products
*
* This template can be overridden by copying it to yourtheme/woocommerce/single-product/related.php.
*
* HOWEVER, on occasion WooCommerce will need to update template files and you
* (the theme developer) will need to copy the new files to your theme to
* maintain compatibility. We try to do this as little as possible, but it does
* happen. When this occurs the version of the template file will be bumped and
* the readme will list any important changes.
*
* @see 	    https://docs.woocommerce.com/document/template-structure/
* @author 		WooThemes
* @package 	WooCommerce/Templates
* @version     3.0.0
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( $related_products ) : ?>

<section class="pt-4">

	<h3 style="font-weight: 400;    text-transform: uppercase;    font-size: 21px;    letter-spacing: 1px;    margin-bottom: 5px;"><?php esc_html_e( 'Related Products', 'woocommerce' ); ?></h3>
	<p style="font-weight: 300; font-size:16px;"><?php _e('You may also be interested in these products','wingparent'); ?></p>
	<hr />

	<?php woocommerce_product_loop_start(); ?>

	<?php

	foreach ( $related_products as $related_product ) : ?>

	<?php
	$rid = $related_product->get_id();

	?>
	<li class="col-lg-3 col-md-4 col-6">
		<div class="product_item">
			<a href="<?php echo get_the_permalink($rid); ?>" class="woocommerce-LoopProduct-link woocommerce-loop-product__link">
				<h2 class="woocommerce-loop-product__title"><?php echo get_the_title($rid); ?></h2>
				<img width="300" height="300" src="<?php echo get_the_post_thumbnail_url($rid, "thumbnail"); ?>" class="attachment-woocommerce_thumbnail size-woocommerce_thumbnail wp-post-image" alt="" sizes="(max-width: 300px) 100vw, 300px"></a>
				<div class="product_footer">
				<span class="price"><?php echo $related_product->get_price_html(); ?></span>
				<a href="<?php echo get_bloginfo("url"); ?>/cart/?add-to-cart=<?php echo $rid; ?>" data-quantity="1" class="button product_type_simple add_to_cart_button ajax_add_to_cart" data-product_id="<?php echo $rid; ?>" data-product_sku="<?php echo get_the_title($rid); ?>" aria-label="Add “Dotties Maat 3” to your cart" rel="nofollow"><?php _e('Add to cart','woocommerce'); ?></a>	</div>
			</div>
		</li>

	<?php endforeach; ?>

	<?php woocommerce_product_loop_end(); ?>

</section>

<?php endif;

wp_reset_postdata();
