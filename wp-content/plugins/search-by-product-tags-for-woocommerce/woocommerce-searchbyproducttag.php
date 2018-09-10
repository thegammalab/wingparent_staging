<?php

/*
  Plugin Name: Search By Product tag - for Woocommerce
  Plugin URI: http://www.mattyl.co.uk/2012/12/14/woocommerce-wordpress-plugin-to-search-for-products-by-tag/
  Description: The search functionality in woocommerce doesn't search by product tags by default. This simple plugin adds this functionality to both the admin site and regular search
  Author: Matthew Lawson
  Version: 0.3.1
  Author URI: http://www.mattyl.co.uk/
 */


add_filter('the_posts', 'search_by_product_tag');

function search_by_product_tag($posts, $query = false) {
    if (is_search()) {
        //get_search_query does sanitization
        
        $tags = explode(',', get_search_query());
        //var_dump($tags);
        foreach($tags as $tag)
        {
            //Ignore already found posts from query..
            $ignoreIds = array(0);
            foreach($posts as $post)
            {
                $ignoreIds[] = $post->ID;
            }
        
            $matchedTags = get_post_by_tag(trim($tag), $ignoreIds);
            //var_dump($ignoreIds,$matchedTags);
            //die();
            if ($matchedTags) 
            {
                foreach($matchedTags as $product_id)
                {   
                    $posts[] = get_post($product_id->post_id);
                }

            }
        }
        
        //var_dump($posts);
        return $posts;
    }

    return $posts;
}

function get_post_by_tag($tag, $ignoreIds) {
    //Check for 
    global $wpdb, $wp_query;
    
    //$ordering_args = WC()->query->get_catalog_ordering_args( $orderby, $order );
    
    
    /**
     * Special code for removing duplicates if WPML plugin is installed
     * Its popular translation plugin that causes lots of product duplicate products that have different product ids
     * We need to check the wpml meta data of the product to work out the duplicates
     */
    
    $wmplEnabled = false;

    if(defined('WPML_TM_VERSION') && defined('WPML_ST_VERSION') && class_exists("woocommerce_wpml")){
        $wmplEnabled = true;
        //What language should we search for...
        $languageCode = ICL_LANGUAGE_CODE;
    }
   //  $wmplEnabled = false;
    $ignoreIdsForMySql = implode(",", $ignoreIds);
    //var_dump($ignoreIdsForMySql);
    $query = "
            select p.ID as post_id from $wpdb->terms t
            join $wpdb->term_taxonomy tt
            on t.term_id = tt.term_id
            join $wpdb->term_relationships tr
            on tt.term_taxonomy_id = tr.term_taxonomy_id
            join $wpdb->posts p
            on p.ID = tr.object_id
            join $wpdb->postmeta visibility
            on p.ID = visibility.post_id    
            and visibility.meta_key = '_visibility'
            and visibility.meta_value <> 'hidden'
            ";
            
    //IF WPML Plugin is enabled join and get correct language product.
    if($wmplEnabled)
    {
        $query .=
        "join ".$wpdb->prefix."icl_translations trans on
         trans.element_id = p.ID
         and trans.element_type = 'post_product'
         and trans.language_code = '$languageCode'";
         ;
    }
    
    
    $query .= "
            WHERE 
            tt.taxonomy = 'product_tag' and
            t.name LIKE '%$tag%'
            and p.post_status = 'publish'
            and p.post_type = 'product'
            and (p.post_parent = 0 or p.post_parent is null)
            and p.ID not in ($ignoreIdsForMySql)
            group by p.ID
             ;
";
    
    //Search for the sku of a variation and return the parent.
    $matchedProducts = $wpdb->get_results($query) ;
    
    //var_dump($matchedProducts);
    if(is_array($matchedProducts) && !empty($matchedProducts))
    {
        //var_dump($matchedProducts);
        $wp_query->found_posts += sizeof($matchedProducts);
        //var_dump($wp_query->found_posts, sizeof($matchedProducts));
        return $matchedProducts;
    
    }
   
    //return get_post($product_id);
    
    return null;
}

?>
