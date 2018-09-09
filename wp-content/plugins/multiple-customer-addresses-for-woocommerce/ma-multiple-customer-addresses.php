<?php
/*
 * Plugin Name: WooCommerce Multiple Customer Addresses
 * Plugin URI:  https://wordpress.org
 * Description: The plugin allows customers have more than one shipping or billing addresses. Customers can switch one to another on checkout or setup a default one in My Account.
 * Version:     1.0.1
 * Author:      MoreAddons
 * Author URI:  https://moreaddons.com
 * License:     GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 */

// If this file is called directly, abort.
if (!defined('ABSPATH')) {
    exit;
}
if (!defined('MA_MULTI_ADD_MAIN_URL')) {
    define('MA_MULTI_ADD_MAIN_URL', plugin_dir_url(__FILE__));
}
if (!defined('MA_MULTI_ADD_MAIN_PATH')) {
    define('MA_MULTI_ADD_MAIN_PATH', plugin_dir_path(__FILE__));
}
if (!defined('MA_MULTI_ADD_VERSION')) {
    define('MA_MULTI_ADD_VERSION', '1.0.1');
}
if (!defined('MA_MULTI_ADD_MAIN_IMG')) {
    define('MA_MULTI_ADD_MAIN_IMG', MA_MULTI_ADD_MAIN_URL . "assets/img/");
}
if ( ! in_array('woocommerce/woocommerce.php', get_option('active_plugins'))) {
	deactivate_plugins( MA_MULTI_ADD_MAIN_PATH . 'ma-multiple-customer-addresses.php', false );
	die ( 'Please activate WooCommerce plugin.' );
}

/**
 * Require plugin class
 **/
require_once( MA_MULTI_ADD_MAIN_PATH . 'includes/class-ma-multiple-customer-addresses.php' );

/**
 * Flush rewrite rules on plugin activation.
 */
function ma_multiple_address_flush_rewrite_rules() {
    add_rewrite_endpoint( 'ma-manage-address', EP_ROOT | EP_PAGES );
    flush_rewrite_rules();
}

add_filter('plugin_action_links_' . plugin_basename(__FILE__), 'ma_multiple_addresses_action_link');
function ma_multiple_addresses_action_link($links) {
    if ( array_key_exists( 'deactivate', $links ) ) {
        $links['deactivate'] = str_replace( '<a', '<a class="multiple-customer-addresses-for-woocommerce-deactivate-link"', $links['deactivate'] );
    }
    return $links;
}

if (!class_exists('MoreAddons_Uninstall_feedback_Listener')) {
    require_once (MA_MULTI_ADD_MAIN_PATH . "includes/class-moreaddons-uninstall.php");
}

$qvar = array(
    'name' => 'WooCommerce Multiple Customer Addresses',
    'version' => MA_MULTI_ADD_VERSION,
    'slug' => 'multiple-customer-addresses-for-woocommerce',
    'lang' => 'ma-multiple-address',
    'logo' => MA_MULTI_ADD_MAIN_IMG.'logo.png'
);
new MoreAddons_Uninstall_feedback_Listener($qvar);

register_activation_hook( __FILE__, 'ma_multiple_address_flush_rewrite_rules' );
register_deactivation_hook( __FILE__, 'ma_multiple_address_flush_rewrite_rules' );

MA_Multi_Cus_Add::get_instance();