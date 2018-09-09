<?php
/**
* Roots includes
*
* The $roots_includes array determines the code library included in your theme.
* Add or remove files to the array as needed. Supports child theme overrides.
*
* Please note that missing files will produce a fatal error.
*
* @link https://github.com/roots/roots/pull/1042
*/
add_action('wp_print_scripts', 'de_script', 100);

function de_script() {
  wp_dequeue_script('bp-legacy-js');
  wp_deregister_script('bp-legacy-js');
}

$roots_includes = array(
  '/framework/functions.php',
  '/site_setings.php',
  '/redirects.php',
  '/ajax-services.php',
  'lib/utils.php', // Utility functions
  'lib/init.php', // Initial theme setup and constants
  'lib/wrapper.php', // Theme wrapper class
  'lib/sidebar.php', // Sidebar class
  'lib/config.php', // Configuration
  'lib/activation.php', // Theme activation
  'lib/titles.php', // Page titles
  'lib/nav.php', // Custom nav modifications
  'lib/gallery.php', // Custom [gallery] modifications
  'lib/scripts.php', // Scripts and stylesheets
  'lib/extras.php', // Custom functions
);

add_theme_support('category-thumbnails');
add_theme_support('post-thumbnails', array('page'));
add_theme_support('excerpt', array('page'));
if ($_GET["logout"]) {
  wp_logout();
  header("Location:" . get_bloginfo("url"));
}
foreach ($roots_includes as $file) {
  if (!$filepath = locate_template($file)) {
    trigger_error(sprintf(__('Error locating %s for inclusion', 'roots'), $file), E_USER_ERROR);
  }
  require_once $filepath;
}
unset($file, $filepath);

function string_limit_words($string, $word_limit) {
  $words = explode(' ', $string, ($word_limit + 1));
  if (count($words) > $word_limit)
  array_pop($words);
  return implode(' ', $words);
}

if ($_GET["gift_card_div"]) {
  $content = file_get_contents('https://dustlessleague.launch27.com/giftcards/new?w');
  echo '<script src="https://dustlessleague.launch27.com/jsbundle"></script>';
  echo $content;
  die();
}
if (isset($_REQUEST["redirect_services"])) {
  global $wpdb;
  $zip_assoc = array();
  $res = $wpdb->get_results("SELECT * FROM `" . $wpdb->prefix . "termmeta` WHERE `meta_key`='zip_codes'");
  foreach ($res as $item) {
    $zips = $item->meta_value;
    $zips_list = explode(",", $zips);
    foreach ($zips_list as $zips_list_item) {
      $zip_assoc[trim($zips_list_item)] = $item->term_id;
    }
  }

  if (($zip_assoc[trim($_REQUEST["zipcode"])])) {
    $term_id = $zip_assoc[trim($_REQUEST["zipcode"])];
    $res = $wpdb->get_results("SELECT * FROM `" . $wpdb->prefix . "term_relationships` WHERE `term_taxonomy_id`='" . $term_id . "'");
    foreach ($res as $item) {
      $pid = $item->object_id;
      if (get_post_meta($pid, "service", true) == trim($_REQUEST["service"])) {
        header("Location:" . get_bloginfo("url") . "/" . get_post_meta($pid, "url", true) . "/?bedrooms=" . $_REQUEST["bedrooms"] . "&bathrooms=" . $_REQUEST["bathrooms"]);
      }
    }
    header("Location:" . get_permalink($_REQUEST["service"]) . "/?bedrooms=" . $_REQUEST["bedrooms"] . "&bathrooms=" . $_REQUEST["bathrooms"]);
  } else {
    header("Location:" . get_permalink($_REQUEST["service"]) . "/?bedrooms=" . $_REQUEST["bedrooms"] . "&bathrooms=" . $_REQUEST["bathrooms"]);
  }
  die();
}

function pricing_meta_box($post) {
  global $wpdb;
  $pricing = get_post_meta($post->ID, "pricing", true);
  $discount = get_post_meta($post->ID, "discount", true);
  ?>
  <h2 style="padding: 20px 0; font-size: 20px;">Pricing Matrix</h2>
  <table style="width:100%; margin-bottom: 20px;">
    <thead>
      <tr>
        <th>Beds/Baths</th>
        <?php for ($i = 1; $i < 10; $i++) { ?>
          <th style="text-align: center;"><?php echo $i; ?></th>
        <?php } ?>
      </tr>
    </thead>
    <tbody>
      <?php for ($i = 1; $i < 10; $i++) { ?>
        <tr>
          <th style="padding: 5px;"><?php echo $i; ?></th>
          <?php for ($j = 1; $j < 10; $j++) { ?>
            <td style="padding: 5px;"><input type="text" style="width:100%;" name="pricing[<?php echo $i; ?>][<?php echo $j; ?>]" value="<?php echo $pricing[$i][$j]; ?>" /></td>
          <?php } ?>
        </tr>
      <?php } ?>
    </tbody>
  </table>
  <hr />
  <h2 style="padding: 20px 0; font-size: 20px;">Frequency Discount</h2>
  <table style="width:100%">
    <thead>
      <tr>
        <th>Frequency</th>
        <th style="text-align: center;">One time</th>
        <th style="text-align: center;">Every Week</th>
        <th style="text-align: center;">Every 2 Weeks</th>
        <th style="text-align: center;">Every 4 Weeks</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <th style="padding: 5px;">Discount</th>
        <td style="padding: 5px;"><input type="number" style="width:100%;" name="discount[0]" value="<?php echo $discount[0]; ?>" /></td>
        <td style="padding: 5px;"><input type="number" style="width:100%;" name="discount[1]" value="<?php echo $discount[1]; ?>" /></td>
        <td style="padding: 5px;"><input type="number" style="width:100%;" name="discount[2]" value="<?php echo $discount[2]; ?>" /></td>
        <td style="padding: 5px;"><input type="number" style="width:100%;" name="discount[4]" value="<?php echo $discount[4]; ?>" /></td>
      </tr>
    </tbody>
  </table>
  <?php
}

function add_pricing_meta_box() {
  add_meta_box("pricing-meta-box", "Pricing", "pricing_meta_box", "services", "side", "high", null);
}

add_action("add_meta_boxes", "add_pricing_meta_box");

function pricing_save_meta_box($post_id, $post, $update) {
  if (!current_user_can("edit_post", $post_id))
  return $post_id;
  if (defined("DOING_AUTOSAVE") && DOING_AUTOSAVE)
  return $post_id;

  if (isset($_POST["pricing"])) {
    update_post_meta($post_id, "pricing", $_REQUEST["pricing"]);
  }
  if (isset($_POST["discount"])) {
    update_post_meta($post_id, "discount", $_REQUEST["discount"]);
  }
}

add_action("save_post", "pricing_save_meta_box", 100, 3);

function custom_excerpt_length($length) {
  return 20;
}

add_filter('excerpt_length', 'custom_excerpt_length', 999);

function add_mce_markup($initArray) {
  $ext = '*[*]';
  $initArray['extended_valid_elements'] = $ext;
  return $initArray;
}

add_filter('tiny_mce_before_init', 'add_mce_markup');

add_action('wp_ajax_later_interview', 'later_interview');

function later_interview() {
  global $wpdb;
  $job_id = $_REQUEST["job_id"];
  $profile_id = get_user_meta(get_current_user_id(), "profile_id", true);

  $empl_count = get_post_meta($job_id, "selected_employees", true);
  for ($i = 0; $i < $empl_count; $i++) {
    if ($profile_id == get_post_meta($job_id, "selected_employees_" . $i . "_profile", true)) {
      update_post_meta($job_id, "selected_employees_" . $i . "_seeker_status", 3);
      $interview_id = get_post_meta($job_id, "selected_employees_" . $i . "_interview_id", true);
      update_post_meta($interview_id, "status", 3);
    }
  }

  echo 1;
  wp_die();
}

add_action('wp_ajax_reject_interview', 'reject_interview');

function reject_interview() {
  global $wpdb;
  $job_id = $_REQUEST["job_id"];
  $profile_id = get_user_meta(get_current_user_id(), "profile_id", true);

  $empl_count = get_post_meta($job_id, "selected_employees", true);
  for ($i = 0; $i < $empl_count; $i++) {
    if ($profile_id == get_post_meta($job_id, "selected_employees_" . $i . "_profile", true)) {
      update_post_meta($job_id, "selected_employees_" . $i . "_seeker_status", 2);
      $interview_id = get_post_meta($job_id, "selected_employees_" . $i . "_interview_id", true);
      update_post_meta($interview_id, "status", 2);
    }
  }

  echo 1;
  wp_die();
}

add_action('wp_ajax_accept_interview', 'accept_interview');

function accept_interview() {
  global $wpdb;
  $job_id = $_REQUEST["job_id"];
  $profile_id = get_user_meta(get_current_user_id(), "profile_id", true);

  $empl_count = get_post_meta($job_id, "selected_employees", true);
  for ($i = 0; $i < $empl_count; $i++) {
    if ($profile_id == get_post_meta($job_id, "selected_employees_" . $i . "_profile", true)) {
      update_post_meta($job_id, "selected_employees_" . $i . "_seeker_status", 1);
      $interview_id = get_post_meta($job_id, "selected_employees_" . $i . "_interview_id", true);
      update_post_meta($interview_id, "status", 1);
    }
  }

  echo 1;
  wp_die();
}

add_action('wp_ajax_accept_offer', 'accept_offer');

function accept_offer() {
  global $wpdb;
  $job_id = $_REQUEST["job_id"];
  $interview_id = $_REQUEST["interview_id"];
  $profile_id = get_user_meta(get_current_user_id(), "profile_id", true);

  print_r($_REQUEST);

  if (get_post_meta($interview_id, "profile", true) == $profile_id) {
    update_post_meta($interview_id, "status", 5);

    $empl_count = get_post_meta($job_id, "selected_employees", true);
    for ($i = 0; $i < $empl_count; $i++) {
      if ($profile_id == get_post_meta($job_id, "selected_employees_" . $i . "_profile", true)) {
        update_post_meta($job_id, "selected_employees_" . $i . "_seeker_status", 4);
      }
    }
  }



  echo 1;
  wp_die();
}

add_action('wp_ajax_company_mark_later', 'company_mark_later');

function company_mark_later() {
  global $wpdb;
  $job_id = $_REQUEST["job_id"];
  $profile_id = $_REQUEST["profile_id"];
  $company_id = get_user_meta(get_current_user_id(), "profile_id", true);

  if (get_post_meta($job_id, "company_profile", true) == $company_id) {
    $empl_count = get_post_meta($job_id, "selected_employees", true);
    for ($i = 0; $i < $empl_count; $i++) {
      if ($profile_id == get_post_meta($job_id, "selected_employees_" . $i . "_profile", true)) {
        update_post_meta($job_id, "selected_employees_" . $i . "_company_status", 3);
      }
    }
  }

  echo 1;
  wp_die();
}

add_action('wp_ajax_company_mark_interview', 'company_mark_interview');

function company_mark_interview() {
  global $wpdb;
  $job_id = $_REQUEST["job_id"];
  $profile_id = $_REQUEST["profile_id"];
  $company_id = get_user_meta(get_current_user_id(), "profile_id", true);
  $interview_id = get_option("interview_id");
  if (!$interview_id) {
    $interview_id = 0;
  }
  update_option("interview_id", $interview_id++);

  if (get_post_meta($job_id, "company_profile", true) == $company_id) {
    $empl_count = get_post_meta($job_id, "selected_employees", true);
    for ($i = 0; $i < $empl_count; $i++) {
      if ($profile_id == get_post_meta($job_id, "selected_employees_" . $i . "_profile", true)) {
        update_post_meta($job_id, "selected_employees_" . $i . "_company_status", 1);

        $my_post = array(
          'post_title' => "Interview #" . str_pad($interview_id, 8, "0", STR_PAD_LEFT),
          'post_status' => 'publish',
          'post_type' => 'interviews',
          'post_author' => get_current_user_id(),
        );

        $interview_id = wp_insert_post($my_post);

        update_post_meta($interview_id, "job", $job_id);
        update_post_meta($interview_id, "profile", $profile_id);
        update_post_meta($interview_id, "status", 0);

        update_post_meta($job_id, "selected_employees_" . $i . "_interview_id", $interview_id);
        update_post_meta($job_id, "selected_employees_" . $i . "_interview", (array("title" => "", "url" => get_bloginfo("url") . "/wp-admin/post.php?post=" . $interview_id . "&action=edit", "target" => "_blank")));
      }
    }
  }

  echo 1;
  wp_die();
}

add_action('wp_ajax_company_mark_reject', 'company_mark_reject');

function company_mark_reject() {
  global $wpdb;
  $job_id = $_REQUEST["job_id"];
  $profile_id = $_REQUEST["profile_id"];
  $company_id = get_user_meta(get_current_user_id(), "profile_id", true);

  if (get_post_meta($job_id, "company_profile", true) == $company_id) {
    $empl_count = get_post_meta($job_id, "selected_employees", true);
    for ($i = 0; $i < $empl_count; $i++) {
      if ($profile_id == get_post_meta($job_id, "selected_employees_" . $i . "_profile", true)) {
        update_post_meta($job_id, "selected_employees_" . $i . "_company_status", 2);
      }
    }
  }

  echo 1;
  wp_die();
}

add_action('init', 'process_post');

function process_post() {

  $usr_id = get_current_user_id();
  $daycares =get_user_meta($usr_id,"daycare",true);
  $daycare = $daycares[0];

  $addresses = get_user_meta($usr_id,"wc_multiple_shipping_addresses",true);
  if(!count($addresses) || !is_array($addresses)){
    $addresses = array();
    $addresses[]=array(
      "label" => get_the_title($daycare),
      "shipping_first_name" => "",
      "shipping_last_name" => get_the_title($daycare),
      "shipping_company" => get_user_meta($usr_id,"company",true),
      "shipping_country" => "BE",
      "shipping_address_1" => get_post_meta($daycare,"address",true),
      "shipping_address_2" => "",
      "shipping_city" => get_post_meta($daycare,"town/city",true),
      "shipping_state" => get_post_meta($daycare,"state",true),
      "shipping_postcode" => get_post_meta($daycare,"zip",true),
      "shipping_address_is_default" => true
    );
  }else{
    $addresses[0]=array(
      "label" => get_the_title($daycare),
      "shipping_first_name" => "",
      "shipping_last_name" => get_the_title($daycare),
      "shipping_company" => get_user_meta($usr_id,"company",true),
      "shipping_country" => "BE",
      "shipping_address_1" => get_post_meta($daycare,"address",true),
      "shipping_address_2" => "",
      "shipping_city" => get_post_meta($daycare,"town/city",true),
      "shipping_state" => get_post_meta($daycare,"state",true),
      "shipping_postcode" => get_post_meta($daycare,"zip",true),
      "shipping_address_is_default" => true
    );
  }

  foreach($new_address as $f=>$v){
    update_user_meta($usr_id,$f,$v);
  }

  update_user_meta($usr_id,"wc_multiple_shipping_addresses",$addresses);

  if (isset($_POST['send_offer'])) {
    $job_id = $_REQUEST["job_id"];
    $profile_id = $_REQUEST["profile_id"];
    $company_id = get_user_meta(get_current_user_id(), "profile_id", true);

    if (get_post_meta($job_id, "company_profile", true) == $company_id) {
      $empl_count = get_post_meta($job_id, "selected_employees", true);
      for ($i = 0; $i < $empl_count; $i++) {
        if ($profile_id == get_post_meta($job_id, "selected_employees_" . $i . "_profile", true)) {
          $interview_id = get_post_meta($job_id, "selected_employees_" . $i . "_interview_id", true);

          update_post_meta($job_id, "selected_employees_" . $i . "_company_status", 4);

          update_post_meta($interview_id, "status", 4);
          update_post_meta($interview_id, "has_offer", 1);
          update_post_meta($interview_id, "offer_date", date("d/m/Y"));
          update_post_meta($interview_id, "offer_description", $_REQUEST["offer"]);
        }
      }
    }
    // process $_POST data here
  }
}

remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_title', 5 );
remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_price', 10 );
remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_meta', 40 );

remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_product_data_tabs', 10 );

remove_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_price', 10 );
add_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_product_link_close', 50 );

add_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_price', 7 );
remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_product_link_close', 5 );




add_filter('woocommerce_form_field_args','wc_form_field_args',10,3);

function wc_form_field_args( $args, $key, $value = null ) {
  $args['class'][] = 'form-group';
  $args['input_class'] = array('form-control', 'input-lg');
  $args['label_class'] = array('control-label');

  return $args;
}

add_action( 'wp_enqueue_scripts', 'wsis_dequeue_stylesandscripts_select2', 100 );

function wsis_dequeue_stylesandscripts_select2() {
  if ( class_exists( 'woocommerce' ) ) {
    wp_dequeue_style( 'selectWoo' );
    wp_deregister_style( 'selectWoo' );

    wp_dequeue_script( 'selectWoo');
    wp_deregister_script('selectWoo');
  }
}


function return_custom_price($price, $product) {
  $prod_id =($product->get_id());
  $user = wp_get_current_user();
  $role = ( array ) $user->roles;
  if($custom_price = get_post_meta($prod_id,$role[0]."_price",true)){
    return $custom_price;
  }else{
    return $price;
  }
}
add_filter('woocommerce_get_price', 'return_custom_price', 10, 2);

add_filter( 'woocommerce_before_checkout_billing_form' , 'kia_checkout_field_defaults', 20 );
function kia_checkout_field_defaults( $fields ) {
  $usr_id = get_current_user_id();
  $daycares =get_user_meta($usr_id,"daycare",true);
  $daycare = $daycares[0];

  $addresses = get_user_meta($usr_id,"wc_multiple_shipping_addresses",true);
  if(!count($addresses) || !is_array($addresses)){
    $addresses = array();
    $addresses[]=array(
      "label" => get_the_title($daycare),
      "shipping_first_name" => "",
      "shipping_last_name" => get_the_title($daycare),
      "shipping_company" => get_user_meta($usr_id,"company",true),
      "shipping_country" => "BE",
      "shipping_address_1" => get_post_meta($daycare,"address",true),
      "shipping_address_2" => "",
      "shipping_city" => get_post_meta($daycare,"town/city",true),
      "shipping_state" => get_post_meta($daycare,"state",true),
      "shipping_postcode" => get_post_meta($daycare,"zip",true),
      "shipping_address_is_default" => true
    );
  }else{
    $addresses[0]=array(
      "label" => get_the_title($daycare),
      "shipping_first_name" => "",
      "shipping_last_name" => get_the_title($daycare),
      "shipping_company" => get_user_meta($usr_id,"company",true),
      "shipping_country" => "BE",
      "shipping_address_1" => get_post_meta($daycare,"address",true),
      "shipping_address_2" => "",
      "shipping_city" => get_post_meta($daycare,"town/city",true),
      "shipping_state" => get_post_meta($daycare,"state",true),
      "shipping_postcode" => get_post_meta($daycare,"zip",true),
      "shipping_address_is_default" => true
    );
  }

  foreach(  $addresses[0] as $f=>$v){
    update_user_meta($usr_id,$f,$v);
  }

  update_user_meta($usr_id,"wc_multiple_shipping_addresses",$addresses);

  WC()->customer->set_shipping_first_name(" ");
  WC()->customer->set_shipping_last_name(get_the_title($daycare));
  WC()->customer->set_shipping_address_1(get_post_meta($daycare,"address",true));
  WC()->customer->set_shipping_address_2(" ");
  WC()->customer->set_shipping_city(get_post_meta($daycare,"town/city",true));
  WC()->customer->set_shipping_state(get_post_meta($daycare,"state",true));
  WC()->customer->set_shipping_postcode(get_post_meta($daycare,"zip",true));
}

add_filter("woocommerce_checkout_fields", "order_fields");

function order_fields($fields) {
  $order = array(
    "billing_first_name",
    "billing_last_name",
    "billing_email",
    "billing_phone",
    "billing_company",
    // "vat_number",
    "billing_address_1",
    "billing_address_2",
    "billing_city",
    "billing_state",
    "billing_postcode",
    "billing_country",
  );
  foreach($order as $field){
    $ordered_fields[$field] = $fields["billing"][$field];
  }

  $fields["billing"] = $ordered_fields;
  $fields["shipping"]["shipping_first_name"]["required"]=0;
  return $fields;

}


function custom_my_account_menu_items( $items ) {
  unset($items['downloads']);
  unset($items['vat-number']);
  return $items;
}
add_filter( 'woocommerce_account_menu_items', 'custom_my_account_menu_items' );

function mysite_box_discount( $cart ){

  global $woocommerce;

  foreach($woocommerce->cart->coupon_discount_tax_totals as $f=>$v){
    $woocommerce->cart->coupon_discount_tax_totals[$f]=0;

  }
  //print_r(WC()->cart);
  $total_tax=0;
  $taxes = array();
  foreach($woocommerce->cart->cart_contents as $f=>$v){
    $woocommerce->cart->cart_contents[$f]["line_tax"]=$woocommerce->cart->cart_contents[$f]["line_subtotal_tax"];
    $woocommerce->cart->cart_contents[$f]["line_tax_data"]["total"]=$woocommerce->cart->cart_contents[$f]["line_tax_data"]["subtotal"];
    foreach($woocommerce->cart->cart_contents[$f]["line_tax_data"]["total"] as $f1=>$v1){
      //print_r($woocommerce->cart->cart_contents[$f]);
      $taxes[$f1] +=$woocommerce->cart->cart_contents[$f]["line_subtotal_tax"];
    }

    $total_tax+=$woocommerce->cart->cart_contents[$f]["line_subtotal_tax"];
  }
  WC()->cart->set_discount_tax(0);
  WC()->cart->set_cart_contents_tax($total_tax);
  WC()->cart->set_total_tax($total_tax);
  WC()->cart->set_cart_contents_taxes($taxes);



  return $cart;
}
add_action('woocommerce_calculate_totals', 'mysite_box_discount');

add_action( 'woocommerce_after_calculate_totals', 'woocommerce_after_calculate_totals', 30 );
function woocommerce_after_calculate_totals( $cart ) {
  global $wpdb;


  // make magic happen here...
  // use $cart object to set or calculate anything.
  $totals = ($cart->get_totals());
  $cart->total = $cart->cart_contents_total + $totals["subtotal_tax"] + $totals["shipping_total"] + $totals["shipping_tax"];


}


function check_category($slug){
  $args = array(
    'post_type' => 'product',
    'fields' => 'ids',
    'tax_query' => array(
      array(
        'taxonomy' => 'product_cat',
        'field'    => 'slug',
        'terms'    => $slug,
      ),
    ),
  );
  $query = new WP_Query( $args );
  $ok = 0;
  foreach($query->posts as $pid){
    $roles = get_post_meta($pid,"_alg_wc_pvbur_visible",true);

    foreach($roles as $role){
      if(current_user_can($role)){
        $ok = 1;
      }
      if($role=="parent"){
        $ok = 1;
      }
    }
  }

  return $ok;
}

// Register and load the widget
function prodcat_load_widget() {
  register_widget( 'prodcat_widget' );
}
add_action( 'widgets_init', 'prodcat_load_widget' );

// Creating the widget
class prodcat_widget extends WP_Widget {

  function __construct() {
    parent::__construct(

      // Base ID of your widget
      'prodcat_widget',

      // Widget name will appear in UI
      __('Product Category Tree', 'prodcat_widget_domain'),

      // Widget description
      array( 'description' => __( 'Product page filtering Category Tree', 'prodcat_widget_domain' ), )
    );
  }

  // Creating widget front-end

  public function widget( $args, $instance ) {
    $title = apply_filters( 'widget_title', $instance['title'] );
    $all_cats = get_terms("product_cat","hide_empty=0&parent=0");
    $cats = array();
    foreach($all_cats as $cat){
      if(get_option("product_cat_".$cat->term_id."_type")=="external_links"){
        $cats[]=array("cat"=>$cat,"children"=>array(),"target"=>"_blank");
}else{
      if($cat->slug!="uncategorized"){
        if(check_category($cat->slug)){
          $children = array();
          $sub_cats = get_terms("product_cat","hide_empty=1&parent=".$cat->term_id);
          foreach($sub_cats as $sub_cat){
            if(check_category($sub_cat->slug)){
              $sub_children = array();
              $sub_sub_cats = get_terms("product_cat","hide_empty=1&parent=".$sub_cat->term_id);
              foreach($sub_sub_cats as $sub_sub_cat){
                if(check_category($sub_sub_cat->slug)){
                  $sub_children[]=$sub_sub_cat;
                }
              }
              $children[]=array("subcat"=>$sub_cat,"sub_children"=>$sub_children);
            }
          }
          $cats[]=array("cat"=>$cat,"children"=>$children,"target"=>"_parent");
        }
      }
      }
    }
    if(count($cats)){
      // before and after widget arguments are defined by themes
      echo $args['before_widget'];
      if ( ! empty( $title ) )
      echo $args['before_title'] . $title . $args['after_title'];

      $sel_cat = get_term_by("slug",get_query_var("product_cat"),"product_cat");
      echo '<ul class="prodcat_list">';
      foreach($cats as $the_cat){
        $cat=$the_cat["cat"];
        if($sel_cat->term_id==$cat->term_id){
          $cl='active is_open';
        }elseif($sel_cat->parent==$cat->term_id){
          $cl='is_open';
        }else{
          $cl = '';
        }

        echo '<li class="'.$cl.'"><a href="'.get_term_link($cat).'" target="'.$the_cat["target"].'">'.$cat->name.'</a><span class="cat_expand"></span>';
        if(count($the_cat["children"])){
          if($sel_cat->term_id==$cat->term_id || $sel_cat->parent==$cat->term_id){
            $cl='active';
          }else{
            $cl = '';
          }
          echo '<ul class="'.$cl.'">';
          foreach($the_cat["children"] as $sub_cat){
            if($sel_cat->term_id==$sub_cat["subcat"]->term_id){
              $cl='active';
            }else{
              $cl = '';
            }
            echo '<li class="'.$cl.'"><a href="'.get_term_link($sub_cat["subcat"]).'">'.$sub_cat["subcat"]->name.'</a>';
            if(count($sub_cat["sub_children"])){
              echo '<ul>';
              foreach($sub_cat["sub_children"] as $sub_sub_cat){
                echo '<li><a href="'.get_term_link($sub_sub_cat).'"> - '.$sub_sub_cat->name.'</a></li>';
              }
              echo '</ul>';
            }
            echo '</li>';
          }
          echo '</ul>';
        }
        echo '</li>';
      }
      echo '</ul>';
      // This is where you run the code and display the output

      echo $args['after_widget'];
    }
  }

  // Widget Backend
  public function form( $instance ) {
    if ( isset( $instance[ 'title' ] ) ) {
      $title = $instance[ 'title' ];
    }
    else {
      $title = __( 'New title', 'prodcat_widget_domain' );
    }
    // Widget admin form
    ?>
    <p>
      <label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label>
      <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
    </p>
    <?php
  }

  // Updating widget replacing old instances with new
  public function update( $new_instance, $old_instance ) {
    $instance = array();
    $instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
    return $instance;
  }
} // Class prodcat_widget ends here



add_filter('woocommerce_package_rates', 'apply_static_rate', 10, 2);
function apply_static_rate($rates, $package)
{
  $ship_id = get_option("shipping_address_".get_current_user_id());
  if(!$ship_id || $ship_id==1 || $ship_id==0){
    foreach($rates as $key => $value) {
      $rates[$key]->cost  =   0;    // your amount
      $taxes= array();
      foreach($rates[$key]->taxes as $f1=>$v1){
        $taxes[$f1]=0;
      }
      $rates[$key]->taxes  =   $taxes;
    }
  }


  return $rates;

}


add_action('wp_ajax_update_ship_address', 'update_ship_address');

function update_ship_address() {
  if($_REQUEST["val"]!=get_option("shipping_address_".get_current_user_id())){
    echo 1;
  }

  if($_REQUEST["val"]>1){
   update_option("shipping_address_".get_current_user_id(),$_REQUEST["val"]);
 }
  wp_die();
}


add_action( 'woocommerce_before_single_product', 'cspl_change_single_product_layout' );
function cspl_change_single_product_layout() {
    // Disable the hooks so that their order can be changed.
    remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_excerpt', 20 );

}
function possibly_redirect(){
  global $pagenow;

  if( 'wp-login.php' == $pagenow ) {
    if (isset($_GET['action']) && $_GET['action']=='lostpassword' && $_GET["error"]){
      header("Location:". get_bloginfo("url").'/signup/?error='.$_GET["error"] );
      die();
    }


    if ( isset( $_POST['wp-submit'] ) ||   // in case of LOGIN
    (isset($_GET['action']) && $_GET['action']=='lostpassword') ||
      ( isset($_GET['action']) && $_GET['action']=='rp') ||   // in case of LOGOUT
      ( isset($_GET['action']) && $_GET['action']=='logout') ||   // in case of LOGOUT
      ( isset($_GET['checkemail']) && $_GET['checkemail']=='confirm') ||   // in case of LOST PASSWORD
      ( isset($_GET['checkemail']) && $_GET['checkemail']=='registered') ) {
        return;
      }else{    // in case of REGISTER
      wp_redirect( get_bloginfo("url").'/signup/' );
      die();
    }
    exit();
  }
}
add_action('init','possibly_redirect');

add_filter( 'login_errors', function( $error ) {
	global $errors;
	$err_codes = $errors->get_error_codes();

	// Invalid username.
	// Default: '<strong>ERROR</strong>: Invalid username. <a href="%s">Lost your password</a>?'
	if ( in_array( 'invalidkey', $err_codes ) ) {
		$error = __('Key is invalid or already used or gives an error. No worries, you can just reset a new password by entering your e-mail adress below.',"wingparent");
	}

  $error.=serialize($err_codes);

	return $error;
} );
function the_login_message( $message ) {
    if(strpos($_SERVER["REQUEST_URI"],"invalidkey")){
      return '<p class="error">'.__("Key is invalid or already used or gives an error. No worries, you can just reset a new password by entering your e-mail adress below.","wingparent","wingparent").'</p>'.$message;
    }else{
      return $message;
    }

}
add_filter( 'login_message', 'the_login_message' );
