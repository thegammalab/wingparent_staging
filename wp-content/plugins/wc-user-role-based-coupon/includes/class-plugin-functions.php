<?php
/**
 * functionality of the plugin.
 *
 * @link       @TODO
 * @since      1.0
 *
 * @package    @TODO
 * @subpackage @TODO
 *
 * @package    @TODO
 * @subpackage @TODO
 * @author     Varun Sridharan <varunsridharan23@gmail.com>
 */
if ( ! defined( 'WPINC' ) ) { die; }

class WC_User_Role_Based_Coupon_Functions {
	/**
	 * Class Constructor
	 */
	public function __construct() {
		add_filter('woocommerce_coupon_is_valid',array($this,'check_coupon'),10,2);
	}
 
	public function check_coupon($status,$post){
		$id = method_exists($post,'get_id') ? $post->get_id() :  $post->id;
		$allowed = get_post_meta($id,WC_URBC_DB.'_allowed_roles',true);
        $blocked = get_post_meta($id,WC_URBC_DB.'_blocked_roles',true);
        
		if(!empty($allowed)){
            $current_role = WC_URBC()->current_role();
            if(! in_array($current_role, $allowed)){$status = false;}
        } else if(!empty($blocked)){
            $current_role = WC_URBC()->current_role();
            if(in_array($current_role, $blocked)){$status = false;}
        }		
		return $status;
	}
}
