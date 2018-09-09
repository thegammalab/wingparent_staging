<?php
if (!defined('ABSPATH')) {
    exit;
}
class MA_Multi_Cus_Add {
	const VERSION = MA_MULTI_ADD_VERSION;
	protected static $plugin_slug = 'multiple-customer-addresses-for-woo';
	protected static $instance = null;
	private function __construct() {
                add_action( 'init', array($this,'manage_address_endpoints') );
                add_filter( 'query_vars', array($this,'manage_address_query_vars'), 0 );
                add_filter( 'woocommerce_account_menu_items', array($this,'add_manage_address_link_my_account') );
                add_action( 'woocommerce_account_ma-manage-address_endpoint', array($this,'multiple_shipping_addresses') );
                add_filter( 'the_title', array($this,'manage_address_endpoint_title') );
		add_action( 'wpmu_new_blog', array( $this, 'activate_new_site' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		add_action( 'woocommerce_before_my_account', array( $this, 'rewrite_edit_url_on_my_account' ), 25 );
		add_shortcode( 'woo_multiple_customer_address', array( $this, 'multiple_shipping_addresses' ) );
		add_action( 'template_redirect', array( $this, 'save_multiple_shipping_addresses' ) );
		add_action( 'woocommerce_before_checkout_form', array( $this, 'before_checkout_form' ) );
		add_action( 'woocommerce_created_customer', array( $this, 'created_customer_save_shipping_as_default' ) );
		add_filter( 'woocommerce_checkout_fields', array( $this, 'add_dd_to_checkout_fields' ) );
		add_action( 'wp_ajax_alt_change', array( $this, 'ajax_checkout_change_shipping_address' ) );
		add_action( 'wp_ajax_nopriv_alt_change', array( $this, 'ajax_checkout_change_shipping_address' ) );
		add_filter( 'woocommerce_checkout_get_value', array( $this, 'wma_checkout_get_value' ), 10, 2 );
	}
        public function manage_address_endpoints()
        {
            add_rewrite_endpoint( 'ma-manage-address', EP_ROOT | EP_PAGES );
        }

        public function manage_address_query_vars($vars)
        {
            $vars[] = 'ma-manage-address';
            return $vars;
        }
        public function add_manage_address_link_my_account($items)
        {
            $new_items = array();
            $new_items['ma-manage-address'] = __('Manage Addresses','ma-multiple-address');
            return self::my_custom_insert_after_helper( $items, $new_items, 'edit-address' );
        }
        public function my_custom_insert_after_helper($items, $new_items, $after)
        {
            $position = array_search( $after, array_keys( $items ) ) + 1;
            $array = array_slice( $items, 0, $position, true );
            $array += $new_items;
            $array += array_slice( $items, $position, count( $items ) - $position, true );

            return $array;
        }
        public function manage_address_endpoint_title($title)
        {
            global $wp_query;
            $is_endpoint = isset( $wp_query->query_vars['ma-manage-address'] );

            if ( $is_endpoint && ! is_admin() && is_main_query() && in_the_loop() && is_account_page() ) {
                    // New page title.
                    $title = __( 'Manage Addresses', 'ma-multiple-address' );
                    remove_filter( 'the_title', array($this,'manage_address_endpoint_title') );
            }

            return $title;
        }
        public static function activate( $network_wide ) {

		if ( function_exists( 'is_multisite' ) && is_multisite() ) {

			if ( $network_wide ) {

				// Get all blog ids
				$blog_ids = self::get_blog_ids();

				foreach ( $blog_ids as $blog_id ) {

					switch_to_blog( $blog_id );
					self::single_activate();
				}

				restore_current_blog();

			} else {
				self::single_activate();
			}

		} else {
			self::single_activate();
		}

	}
	public static function deactivate( $network_wide ) {

		if ( function_exists( 'is_multisite' ) && is_multisite() ) {

			if ( $network_wide ) {

				// Get all blog ids
				$blog_ids = self::get_blog_ids();

				foreach ( $blog_ids as $blog_id ) {

					switch_to_blog( $blog_id );
					self::single_deactivate();

				}

				restore_current_blog();

			} else {
				self::single_deactivate();
			}

		} else {
			self::single_deactivate();
		}

	}
	public function activate_new_site( $blog_id ) {

		if ( 1 !== did_action( 'wpmu_new_blog' ) ) {
			return;
		}

		switch_to_blog( $blog_id );
		self::single_activate();
		restore_current_blog();

	}
	private static function get_blog_ids() {

		global $wpdb;

		// get an array of blog ids
		$sql = "SELECT blog_id FROM $wpdb->blogs
			WHERE archived = '0' AND spam = '0'
			AND deleted = '0'";

		return $wpdb->get_col( $sql );

	}
	private static function single_activate() {
		global $woocommerce;

		$page_id = woocommerce_get_page_id( 'multiple_shipping_addresses' );

		if ( $page_id == - 1 ) {
			// get the checkout page
			$account_id = woocommerce_get_page_id( 'myaccount' );

			// add page and assign
			$page = array(
				'menu_order'     => 0,
				'comment_status' => 'closed',
				'ping_status'    => 'closed',
				'post_author'    => 1,
				'post_content'   => '[woo_multiple_customer_address]',
				'post_name'      => 'multiple-shipping-addresses',
				'post_parent'    => $account_id,
				'post_title'     => __( 'Manage Your Addresses', self::$plugin_slug ),
				'post_type'      => 'page',
				'post_status'    => 'publish',
				'post_category'  => array( 1 )
			);

			$page_id = wp_insert_post( $page );

			update_option( 'woo_multiple_customer_address_page_id', $page_id );
		}
	}
	public static function get_instance() {
		if ( null == self::$instance ) {
			self::$instance = new self;
		}
		return self::$instance;
	}
	public function enqueue_styles() {
		wp_enqueue_style( self::$plugin_slug . '-plugin-styles', MA_MULTI_ADD_MAIN_URL.'assets/css/public.css', array());
	}
	public function enqueue_scripts() {
		wp_enqueue_script( 'wc-country-select', WP_CONTENT_URL . '/plugins/woocommerce/assets/js/frontend/country-select.min.js', array( 'jquery' ));
		wp_enqueue_script( self::$plugin_slug . '-plugin-script', MA_MULTI_ADD_MAIN_URL.'assets/js/public.js', array( 'jquery' ));
		wp_localize_script( self::$plugin_slug . '-plugin-script', 'MAMCA_Ajax', array(
				'ajaxurl'               => admin_url( 'admin-ajax.php' ),
				'id'                    => 0,
				'wc_multiple_addresses' => wp_create_nonce( 'wc-multiple-addresses-ajax-nonce' )
			)
		);
	}
	public function rewrite_edit_url_on_my_account() {
		$page_id  = wc_get_page_id( 'multiple_shipping_addresses' );
		$page_url = get_permalink( $page_id );
		?>
		<script type="text/javascript">
			jQuery(document).ready(function ($) {
				$('.woocommerce-account .col-2.address .title a').attr('href', '<?php echo $page_url; ?>');
			});
		</script>
	<?php
	}
	public function wma_checkout_get_value($null, $input) {
		global $wma_current_address;

		if ( ! empty( $wma_current_address ) ) {
			foreach ($wma_current_address as $key => $value) {
				if ( $input == $key ) {
					return $value;
				}
			}
		}
	}
	public function multiple_shipping_addresses() {
		global $woocommerce;

		$GLOBALS['wma_current_address'] = '';

		if ( version_compare( WOOCOMMERCE_VERSION, '2.1', '<' ) ) {
			require_once $woocommerce->plugin_path() .'/classes/class-wc-checkout.php';
		} else {
			require_once $woocommerce->plugin_path() . '/includes/class-wc-checkout.php';
		}

		$user     = wp_get_current_user();
		$checkout = WC()->checkout();
		$shipFields = $checkout->checkout_fields['shipping'];

		if ( $user->ID == 0 ) {
			return;
		}

		$otherAddr = get_user_meta( $user->ID, 'wc_multiple_shipping_addresses', true );
		echo '<div class="woocommerce">';
		echo '<form action="" method="post" id="address_form">';
		if ( ! empty( $otherAddr ) ) {
			echo '<div id="addresses">';

			global $wma_current_address;
			foreach ( $otherAddr as $idx => $address ) {
				$wma_current_address = $address;
				echo '<div class="shipping_address address_block" id="shipping_address_' . $idx . '">';
				echo '<div style="margin-bottom:20px;"><h3 style="display: inline;">'.$address['label'].'</h3><a href="#" class="delete button" style="float: right;">'.__( "delete", self::$plugin_slug ).'</a></div>';
				do_action( 'woocommerce_before_checkout_shipping_form', $checkout );

                                $label['id'] = 'label';
                                $label['label'] = __( 'Label', self::$plugin_slug );
                                woocommerce_form_field( 'label[]', $label, $address['label'] );

				foreach ( $shipFields as $key => $field ) {

					if ( 'shipping_alt' == $key ) {
						continue;
					}

					$val = '';
					if ( isset( $address[ $key ] ) ) {
						$val = $address[ $key ];
					}

					$field['id'] = $key;
					$key .= '[]';
					woocommerce_form_field( $key, $field, $val );
				}

				if ( ! wc_ship_to_billing_address_only() && get_option( 'woocommerce_calc_shipping' ) !== 'no' ) {
					$is_checked = $address['shipping_address_is_default'] == 'true' ? "checked" : "";
					echo '<input type="checkbox" class="default_shipping_address" ' . $is_checked . ' value="' . $address['shipping_address_is_default'] . '"> ' . __( 'Mark this shipping address as default', self::$plugin_slug );
					echo '<input type="hidden" class="hidden_default_shipping_address" name="shipping_address_is_default[]" value="' . $address['shipping_address_is_default'] . '" />';
				}

				do_action( 'woocommerce_after_checkout_shipping_form', $checkout );
				echo '</div>';
			}
			echo '</div>';
		} else {

			echo '<div id="addresses">';

			foreach ( $shipFields as $key => $field ) :
				$field['id'] = $key;
				$key .= '[]';
				woocommerce_form_field( $key, $field, $checkout->get_value( $field['id'] ) );
			endforeach;

			if ( ! wc_ship_to_billing_address_only() && get_option( 'woocommerce_calc_shipping' ) !== 'no' ) {
				echo '<input type="checkbox" class="default_shipping_address" checked value="true"> ' . __( 'Mark this shipping address as default', self::$plugin_slug );
				echo '<input type="hidden" class="hidden_default_shipping_address" name="shipping_address_is_default[]" value="true" />';
			}

			echo '</div>';
		}
		echo '<div class="form-row">
                <input type="hidden" name="shipping_account_address_action" value="save" />
                <input type="submit" name="set_addresses" value="' . __( 'Save Addresses', self::$plugin_slug ) . '" class="button alt" />
                <a class="add_address button button-info" href="#">' . __( 'Add another', self::$plugin_slug ) . '</a>
            </div>';
		echo '</form>';
		echo '</div>';
		?>
		<script type="text/javascript">
			var tmpl = '<div class="shipping_address address_block"><div style="margin-bottom:20px;"><h3 style="display: inline;">Add new Address</h3><a href="#" class="delete button" style="float: right;"><?php _e( "delete", self::$plugin_slug ); ?></a></div>';

                tmpl += '<?php $label['id'] = 'label';
                $label['label'] = __( 'Address Label', self::$plugin_slug );
                $row = woocommerce_form_field( 'label[]', $label, '' );
                echo str_replace("\n", "\\\n", str_replace("'", "\'", $row));
                ?>';

			tmpl += '<?php foreach ($shipFields as $key => $field) :
				if ( 'shipping_alt' == $key ) {
					continue;
				}
				$field['return'] = true;
				$val = '';
				$field['id'] = $key;
				$key .= '[]';
				$row = woocommerce_form_field( $key, $field, $val );
				echo str_replace("\n", "\\\n", str_replace("'", "\'", $row));
			endforeach; ?>';

			<?php if ( ! wc_ship_to_billing_address_only() && get_option( 'woocommerce_calc_shipping' ) !== 'no' ) : ?>
				tmpl += '<input type="checkbox" class="default_shipping_address" value="false"> <?php _e( "Mark this shipping address as default", self::$plugin_slug ); ?>';
				tmpl += '<input type="hidden" class="hidden_default_shipping_address" name="shipping_address_is_default[]" value="false" />';
			<?php endif; ?>

			tmpl += '</div>';
			jQuery(".add_address").click(function (e) {
				e.preventDefault();

				jQuery("#addresses").append(tmpl);

				jQuery('html,body').animate({
						scrollTop: jQuery('#addresses .shipping_address:last').offset().top},
					'slow');
			});

			jQuery(".delete").live("click", function (e) {
				e.preventDefault();
				jQuery(this).parents("div.address_block").remove();
			});

			jQuery(document).ready(function () {

				jQuery(document).on("click", ".default_shipping_address", function () {
					if (this.checked) {
						jQuery("input.default_shipping_address").not(this).removeAttr("checked");
						jQuery("input.default_shipping_address").not(this).val("false");
						jQuery("input.hidden_default_shipping_address").val("false");
						jQuery(this).next().val('true');
						jQuery(this).val('true');
					}
					else {
						jQuery("input.default_shipping_address").val("false");
						jQuery("input.hidden_default_shipping_address").val("false");
					}
				});

				jQuery("#address_form").submit(function () {
					var valid = true;
					jQuery("input[type=text],select").each(function () {
						if (jQuery(this).prev("label").children("abbr").length == 1 && jQuery(this).val() == "") {
							jQuery(this).focus();
							valid = false;
							return false;
						}
					});
					return valid;
				});
			});
		</script>
	<?php
	}
	public function save_multiple_shipping_addresses() {

		if ( isset( $_POST['shipping_account_address_action'] ) && $_POST['shipping_account_address_action'] == 'save' ) {
			unset( $_POST['shipping_account_address_action'] );

			$addresses  = array();
			$is_default = false;
			foreach ( $_POST as $key => $values ) {
				if ( $key == 'shipping_address_is_default' ) {
					foreach ( $values as $idx => $val ) {
						if ( $val == 'true' ) {
							$is_default = $idx;
						}
					}
				}
				if ( ! is_array( $values ) ) {
					continue;
				}

				foreach ( $values as $idx => $val ) {
					$addresses[ $idx ][ $key ] = $val;
				}
			}

			$user = wp_get_current_user();

			if ( $is_default !== false ) {
				$default_address = $addresses[ $is_default ];
				foreach ( $default_address as $key => $field ) :
					if ( $key == 'shipping_address_is_default' ) {
						continue;
					}
					update_user_meta( $user->ID, $key, $field );
				endforeach;
			}

			update_user_meta( $user->ID, 'wc_multiple_shipping_addresses', $addresses );

			if ( version_compare( WOOCOMMERCE_VERSION, '2.1', '<' ) ) {
				global $woocommerce;
				$woocommerce->add_message( __( 'Addresses have been saved', self::$plugin_slug ) );
			} else {
				wc_add_notice( __( 'Addresses have been saved', self::$plugin_slug ), $notice_type = 'success' );
			}

			$page_id = woocommerce_get_page_id( 'myaccount' );
			wp_redirect( get_permalink( $page_id ) );
			exit;
		}
	}
	public function before_checkout_form() {
		global $woocommerce;

		$page_id = woocommerce_get_page_id( 'multiple_shipping_addresses' );
		if ( is_user_logged_in() ) {
			echo '<p class="woocommerce-info woocommerce_message">
	                ' . __( 'If you have more than one shipping address, then you may choose a default one here.', self::$plugin_slug ) . '
	                <a class="button" href="' . get_permalink( $page_id ) . '">' . __( 'Configure Address', self::$plugin_slug ) . '</a>
	              </p>';
		}
	}

	public function array_unshift_assoc( &$arr, $key, $val ) {
		$arr         = array_reverse( $arr, true );
		$arr[ $key ] = $val;

		return array_reverse( $arr, true );
	}

	public function created_customer_save_shipping_as_default( $current_user_id ) {
		global $woocommerce;
		if ( $current_user_id == 0 ) {
			return;
		}

		$checkout        = $woocommerce->checkout->posted;
		$default_address = array();
		if ( $checkout['shiptobilling'] == 0 ) {
			$default_address[0]['shipping_country']    = $checkout['shipping_country'];
			$default_address[0]['shipping_first_name'] = $checkout['shipping_first_name'];
			$default_address[0]['shipping_last_name']  = $checkout['shipping_last_name'];
			$default_address[0]['shipping_company']    = $checkout['shipping_company'];
			$default_address[0]['shipping_address_1']  = $checkout['shipping_address_1'];
			$default_address[0]['shipping_address_2']  = $checkout['shipping_address_2'];
			$default_address[0]['shipping_city']       = $checkout['shipping_city'];
			$default_address[0]['shipping_state']      = $checkout['shipping_state'];
			$default_address[0]['shipping_postcode']   = $checkout['shipping_postcode'];
		} elseif ( $checkout['shiptobilling'] == 1 ) {
			$default_address[0]['shipping_country']    = $checkout['billing_country'];
			$default_address[0]['shipping_first_name'] = $checkout['billing_first_name'];
			$default_address[0]['shipping_last_name']  = $checkout['billing_last_name'];
			$default_address[0]['shipping_company']    = $checkout['billing_company'];
			$default_address[0]['shipping_address_1']  = $checkout['billing_address_1'];
			$default_address[0]['shipping_address_2']  = $checkout['billing_address_2'];
			$default_address[0]['shipping_city']       = $checkout['billing_city'];
			$default_address[0]['shipping_state']      = $checkout['billing_state'];
			$default_address[0]['shipping_postcode']   = $checkout['billing_postcode'];
		}
		$default_address[0]['shipping_address_is_default'] = 'true';
		update_user_meta( $current_user_id, 'wc_multiple_shipping_addresses', $default_address );
	}

	public function add_dd_to_checkout_fields( $fields ) {
		global $current_user;

		$otherAddrs = get_user_meta( $current_user->ID, 'wc_multiple_shipping_addresses', true );
		if ( ! $otherAddrs ) {
			return $fields;
		}

		$addresses    = array();
		$addresses[0] = __( 'Choose an address...', self::$plugin_slug );
		for ( $i = 1; $i <= count( $otherAddrs ); ++$i ) {
                    if (!empty($otherAddrs[$i - 1]['label'])) {
                        $addresses[ $i ] = $otherAddrs[$i - 1]['label'];
                    } else {
                        $addresses[ $i ] = $otherAddrs[ $i - 1 ]['shipping_first_name'] . ' ' . $otherAddrs[ $i - 1 ]['shipping_last_name'] . ', ' . $otherAddrs[ $i - 1 ]['shipping_postcode'] . ' ' . $otherAddrs[ $i - 1 ]['shipping_city'];
                    }
		}

		$alt_field = array(
			'label'    => __( 'Your addresses', self::$plugin_slug ),
			'required' => false,
			'class'    => array( 'form-row' ),
			'clear'    => true,
			'type'     => 'select',
			'options'  => $addresses
		);

		$fields['shipping'] = $this->array_unshift_assoc( $fields['shipping'], 'shipping_alt', $alt_field );
		$fields['billing'] = $this->array_unshift_assoc( $fields['billing'], 'billing_alt', $alt_field );

		return $fields;
	}

	public function ajax_checkout_change_shipping_address() {

		// check nonce
		$nonce = $_POST['wc_multiple_addresses'];
		if ( ! wp_verify_nonce( $nonce, 'wc-multiple-addresses-ajax-nonce' ) ) {
			die ( 'Busted!' );
		}

		$address_id = $_POST['id'] - 1;
		if ( $address_id < 0 ) {
			return;
		}

		// get address
		global $current_user;
		$otherAddr = get_user_meta( $current_user->ID, 'wc_multiple_shipping_addresses', true );

		global $woocommerce;
		$addr                          = $otherAddr[ $address_id ];
		$addr['shipping_country_text'] = $woocommerce->countries->countries[ $addr['shipping_country'] ];
		$response                      = json_encode( $addr );

		// response output
		header( "Content-Type: application/json" );
		echo $response;

		exit;
	}
}