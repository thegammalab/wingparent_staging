<?php
global $wp_query;
$term_id = $wp_query->get_queried_object()->term_id;

if(get_option("product_cat_".$term_id."_type")=="external_links"){

  (get_template_part('archive', 'external_links'));

}else{
  (get_template_part('woocommerce/archive', 'product'));
}
