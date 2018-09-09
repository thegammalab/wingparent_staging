<div class="container">
	<div class="row">
		<div class="col-md-3">
			<div id="filter_sidebar">
				<?php dynamic_sidebar("primary"); ?>
			</div>
		</div>
		<?php $term_id = get_queried_object()->term_id; $the_term = get_term_by("term_id",$term_id,"product_tag");  ?>
		<div class="col-md-9">




			<?php
			/**
			* The Template for displaying product archives, including the main shop page which is a post type archive
			*
			* This template can be overridden by copying it to yourtheme/woocommerce/archive-product.php.
			*
			* HOWEVER, on occasion WooCommerce will need to update template files and you
			* (the theme developer) will need to copy the new files to your theme to
			* maintain compatibility. We try to do this as little as possible, but it does
			* happen. When this occurs the version of the template file will be bumped and
			* the readme will list any important changes.
			*
			* @see https://docs.woocommerce.com/document/template-structure/
			* @package WooCommerce/Templates
			* @version 3.4.0
			*/

			defined( 'ABSPATH' ) || exit;

			//get_header( 'shop' );

			/**
			* Hook: woocommerce_before_main_content.
			*
			* @hooked woocommerce_output_content_wrapper - 10 (outputs opening divs for the content)
			* @hooked woocommerce_breadcrumb - 20
			* @hooked WC_Structured_Data::generate_website_data() - 30
			*/
			//do_action( 'woocommerce_before_main_content' );

			?>
			<div class="top_category_page">
				<?php if ( apply_filters( 'woocommerce_show_page_title', true ) ) : ?>
					<h2 class="category_page_title text-uppercase"><?php woocommerce_page_title(); ?></h2>
				<?php endif; ?>
				<?php
				/**
				* Hook: woocommerce_archive_description.
				*
				* @hooked woocommerce_taxonomy_archive_description - 10
				* @hooked woocommerce_product_archive_description - 10
				*/
				do_action( 'woocommerce_archive_description' );
				?>
			</div>
			<header class="woocommerce-products-header">



			</header>
			<?php
			if ( woocommerce_product_loop() ) {

				/**
				* Hook: woocommerce_before_shop_loop.
				*
				* @hooked wc_print_notices - 10
				* @hooked woocommerce_result_count - 20
				* @hooked woocommerce_catalog_ordering - 30
				*/
				do_action( 'woocommerce_before_shop_loop' );

				woocommerce_product_loop_start();

				if ( wc_get_loop_prop( 'total' ) ) {
					while ( have_posts() ) {
						the_post();

						/**
						* Hook: woocommerce_shop_loop.
						*
						* @hooked WC_Structured_Data::generate_product_data() - 10
						*/
						do_action( 'woocommerce_shop_loop' );

						wc_get_template_part( 'content', 'product' );
					}
				}

				woocommerce_product_loop_end();

				/**
				* Hook: woocommerce_after_shop_loop.
				*
				* @hooked woocommerce_pagination - 10
				*/

				$cats = array("product_cat", "product_tag");
				foreach($cats as $cat){
					$taxonomies[]=$cat;
					$term = get_term_by('slug', get_query_var($cat), $cat );
					$taxonomy_vals[]=$term->slug;
					$tax_in[]="IN";
				}
				foreach ($_GET as $f => $v) {
					if(substr($f,0,7)=="filter_"){
						$taxonomies[]="pa_".substr($f,7);
						$taxonomy_vals[]=$v;
						$tax_in[]="IN";
					}
				}

				if(isset($_GET["min_price"])){
					$meta_info = ' meta_key="_price" meta_value="'.$_GET["min_price"].','.$_GET["max_price"].'" meta_compare = "BETWEEN" meta_type = "NUMERIC"';
				}

				echo '<div class="woocommerce">'.do_shortcode('[ajax_load_more post_type="product" css_classes="products" posts_per_page="9" css_classes="products row" transition_container="false" taxonomy="'.implode(":",$taxonomies).'" taxonomy_terms="'.implode(":",$taxonomy_vals).'" taxonomy_operator="'.implode(":",$tax_in).'"  search = "'.get_query_var("s").'" '.$meta_info.']').'</div>';
				//do_action( 'woocommerce_after_shop_loop' );
			} else {
				/**
				* Hook: woocommerce_no_products_found.
				*
				* @hooked wc_no_products_found - 10
				*/
				do_action( 'woocommerce_no_products_found' );
			}

			/**
			* Hook: woocommerce_after_main_content.
			*
			* @hooked woocommerce_output_content_wrapper_end - 10 (outputs closing divs for the content)
			*/
			do_action( 'woocommerce_after_main_content' );

			/**
			* Hook: woocommerce_sidebar.
			*
			* @hooked woocommerce_get_sidebar - 10
			*/
			// do_action( 'woocommerce_sidebar' );

			//get_footer( 'shop' );

			?>
		</div>
	</div>
</div>

<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<?php
$tags_list = get_terms("product_tag");
$tags = array();
foreach($tags_list as $tags_item){
	$tags[]='"'.$tags_item->name.'"';
}
 ?>
<script>
jQuery( function() {
    var availableTags = [<?php echo implode(",",$tags); ?>];
    jQuery( "#woocommerce-product-search-field-0" ).autocomplete({
      source: availableTags
    });
  } );
</script>
