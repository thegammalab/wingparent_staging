=== Search by Product Tag for Woocommerce ===
Contributors: mattsgarage
Donate link: http://www.mattyl.co.uk/donate/
Tags: search, tag, tags, product tag, product, woocommerce, ecommerce, e-commerce, commerce, woothemes, wordpress ecommerce
Requires at least: 3.0.1
Tested up to: 3.8.1
Stable tag: 0.3.1
License: GPLv3
License URI: http://www.gnu.org/licenses/gpl-3.0.html

Extend the search functionality of woocommerce to include searching of product tags.

== Description ==

The search functionality in woocommerce doesn't search by product tags by default.  
This simple plugin adds this functionality to both the admin site and regular search.  
You can search by multiple tags by splitting your search with a comma. For example to search for all products tagged with toys and helicopter enter into the search box: “toys, helicopter” and all products with those tags will be returned in the search.
Tested with Woocommerce 1.5.6, 2.0.7 and 2.1.5 
See the [Accompanying blog post](http://www.mattyl.co.uk/2012/12/14/woocommerce-wordpress-plugin-to-search-for-products-by-tag/ "accompanying blog post") for more info.


== Installation ==

1. Upload `woocommerce-searchbyproducttag` directory to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress 3.8.1 and woocommerce 

== Changelog ==
= 0.3.1 =
* Removing debug being printed on a query (Whoops! - that was part of the work for v0.4, respecting ordering and layered nav settings - coming soon!)
= 0.3 =
* Bug causing duplicate search results fixed
* Support for sites using WPML plugin
* Tested with woocommerce 2.1.5
* Tested with wordpress 3.8.1
= 0.2 =
* Compatibility with Woocommerce 2.0
* Respect visibility options on products
* Remove duplicates from search results
* Partial matching on tags
* Various bug fixes reported via [the accompanying blog post](http://www.mattyl.co.uk/2012/12/14/woocommerce-wordpress-plugin-to-search-for-products-by-tag/ "accompanying blog post")
* Releasing to wordpress.org