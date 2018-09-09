<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://wordpress.org/plugins/woocommerce-role-based-price/
 *
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    @TODO
 * @subpackage @TODO
 * @author     Varun Sridharan <varunsridharan23@gmail.com>
 */
if ( ! defined( 'WPINC' ) ) { die; }

class WC_User_Role_Based_Coupon_Admin extends WC_User_Role_Based_Coupon {

    /**
	 * Initialize the class and set its properties.
	 * @since      0.1
	 */
	public function __construct() {
        add_filter( 'plugin_row_meta', array($this, 'plugin_row_links' ), 10, 2 );
        add_action( 'admin_init', array( $this, 'admin_init' ));
        
	}

    /**
     * Inits Admin Sttings
     */
    public function admin_init(){
        add_action('woocommerce_coupon_options_usage_restriction',array($this,'add_box'));
		add_action('woocommerce_coupon_options_save',array($this,'save_restriction'));
    }
    
    public function add_box(){
		global $post;
		$allowed = get_post_meta($post->ID,WC_URBC_DB.'_allowed_roles',true);
        $blocked_roles = get_post_meta($post->ID,WC_URBC_DB.'_blocked_roles',true);
		if(empty($allowed)){$allowed = array();}
        if(empty($blocked_roles)){$blocked_roles = array();}
		$roles = WC_URBC()->get_registered_roles();
		echo '<div class="options_group"> ';
			echo '<p class="form-field "> ';
				echo '<label for="">'.__("Allowed Roles",WC_URBC_TXT).'</label>';
				echo '<select class="wc-enhanced-select" multiple="multiple" style="width:50%;" name="allowed_roles[]"> ';
				foreach($roles as $roleKey => $role){ 
					$selected = '';
					if(in_array($roleKey, $allowed)){$selected = 'selected';}
					echo '<option value="'.$roleKey.'" '.$selected.' > '.$role['name'].'</option>';
				}
				echo '<select > ';
			echo '</p>';
		echo '</div>';
        
        echo '<div class="options_group"> ';
			echo '<p class="form-field "> ';
				echo '<label for="">'.__("Blocked Roles",WC_URBC_TXT).'</label>';
				echo '<select class="wc-enhanced-select" multiple="multiple" style="width:50%;" name="blocked_roles[]"> ';
				foreach($roles as $roleKey => $role){ 
					$selected = '';
					if(in_array($roleKey, $blocked_roles)){$selected = 'selected';}
					echo '<option value="'.$roleKey.'" '.$selected.' > '.$role['name'].'</option>';
				}
				echo '<select > ';
			echo '</p>';
		echo '</div>';
		
	}
	 
	
	public function save_restriction($post_id){
        $allowed = '';
        $blocked_roles = '';
		if(isset($_POST['allowed_roles'])){
			$allowed = $_POST['allowed_roles'];
		}
        
        if(isset($_POST['blocked_roles'])){
            $blocked_roles = $_POST['blocked_roles'];
		}
        
        update_post_meta($post_id,WC_URBC_DB.'_allowed_roles',$allowed);
        update_post_meta($post_id,WC_URBC_DB.'_blocked_roles',$blocked_roles);
	}
    
    
    
    /**
	 * Adds Some Plugin Options
	 * @param  array  $plugin_meta
	 * @param  string $plugin_file
	 * @since 0.11
	 * @return array
	 */
	public function plugin_row_links( $plugin_meta, $plugin_file ) {
		if ( WC_URBC_FILE == $plugin_file ) { 
            $plugin_meta[] = sprintf('<a href="%s">%s</a>', 'https://wordpress.org/plugins/wc-user-role-based-coupon/', __('F.A.Q',WC_URBC_TXT) );
            $plugin_meta[] = sprintf('<a href="%s">%s</a>', 'https://github.com/technofreaky/wc-user-role-based-coupon', __('View On Github',WC_URBC_TXT) );
            $plugin_meta[] = sprintf('<a href="%s">%s</a>', 'https://github.com/technofreaky/wc-user-role-based-coupon/issues', __('Report Issue',WC_URBC_TXT) );
            $plugin_meta[] = sprintf('&hearts; <a href="%s">%s</a>', 'https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=LCUGYXGVY4ZUJ', __('Donate',WC_URBC_TXT) );
            $plugin_meta[] = sprintf('<a href="%s">%s</a>', 'http://varunsridharan.in/plugin-support/', __('Contact Author',WC_URBC_TXT) );
		}
		return $plugin_meta;
	}	    
}