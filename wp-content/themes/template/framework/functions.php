<?php
date_default_timezone_set('America/Montreal');
error_reporting(0);


$path = get_template_directory_uri();
$url = get_bloginfo('wpurl');

function gamma_get_login($args = array()) {
    if ($args["redirect"]) {
        $redirect = $args["redirect"];
    } else {
        $redirect = $_SERVER['REQUEST_URI'];
    }
    $args1 = array(
        'echo' => true,
        'redirect' => $redirect,
        'form_id' => 'loginform',
        'label_username' => __('Email'),
        'label_password' => __('Password'),
        'label_remember' => __('Remember Me'),
        'label_log_in' => __('Log In'),
        'id_username' => 'login_user_login',
        'id_password' => 'login_user_pass',
        'id_remember' => 'login_rememberme',
        'id_submit' => 'login_wp-submit',
        'remember' => true,
        'value_username' => NULL,
        'value_remember' => false
    );
    foreach ($args as $f => $v) {
        $args1[$f] = $v;
    }
    wp_login_form($args1);
}

add_action('wp_login_failed', 'pu_login_failed'); // hook failed login

function pu_login_failed($user) {
// check what page the login attempt is coming from
    $referrer = $_SERVER['HTTP_REFERER'];
    $referrer = str_replace("?login=failed", "", $referrer);
    $referrer = str_replace("?login=required", "", $referrer);
// check that were not on the default login page
    if (!empty($referrer) && !strstr($referrer, 'wp-login') && !strstr($referrer, 'wp-admin') && $user != null) {
// make sure we don't already have a failed login attempt
        if (!strstr($referrer, '?')) {
// Redirect to the login page and append a querystring of login failed
            wp_redirect($referrer . '?login=failed');
        } else {
            wp_redirect($referrer . '&login=failed');
        }
        exit;
    }
}

add_action('authenticate', 'pu_blank_login');

function pu_blank_login($user) {
// check what page the login attempt is coming from
    $referrer = $_SERVER['HTTP_REFERER'];
    $error = false;
    if ($_POST['log'] == '' || $_POST['pwd'] == '') {
        $error = true;
    }
// check that were not on the default login page
    if (!empty($referrer) && !strstr($referrer, 'wp-login') && !strstr($referrer, 'wp-admin') && $error) {
// make sure we don't already have a failed login attempt
        if (!strstr($referrer, '?login=failed')) {
// Redirect to the login page and append a querystring of login failed
            wp_redirect($referrer . '?login=failed');
        } else {
            wp_redirect($referrer);
        }
        exit;
    }
}

function theme_name_scripts() {
    wp_enqueue_style('bootstrap', 'https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/css/bootstrap.min.css');
}

add_action('wp_enqueue_scripts', 'theme_name_scripts');

//////////////////////////////////////////////////////////

$setting_items = json_encode($setting_items);
update_option('gamma_site_settings', $setting_items);
add_action('init', 'my_mailplugin_init');

function my_mailplugin_init() {
    wp_enqueue_script('word-count');
    wp_enqueue_script('post');
    if (user_can_richedit())
        wp_enqueue_script('editor');
    add_thickbox();
    wp_enqueue_script('media-upload');
}

add_action('admin_menu', 'my_mailplugin_menu');

function my_mailplugin_menu() {
    add_menu_page('Site Settings', 'Site Settings', 'level_10', 'site_settings', 'site_settings', "", 4);
}

function site_settings() {
    global $wpdb;
    $settings = get_option('gamma_site_settings');
    $settings = json_decode($settings);
    if ($_POST) {
        foreach ($_POST as $f => $v) {
            if (substr($f, 0, 4) == "opt_") {
                $field = substr($f, 4);
                if (is_array($v)) {
                    $value = implode("|", $v);
                } else {
                    $value = $v;
                }
                update_option('gamma_' . $field, stripslashes($value));
            }
        }
    }
    echo '<div class="wrap"><h2>Site Settings</h2><form method="post" action=""><table class="form-table"><tbody>';
    foreach ($settings as $item) {
        if ($item->type == "title") {
            echo '<tr valign="top"><th scope="row" colspan="2">';
            echo '<h2>' . $item->label . '</h2>';
            echo '</th></tr>';
        } else {
            echo '<tr valign="top"><th scope="row">';
            echo '<label for="blogname">' . $item->label . '</label>';
            echo '</th><td>';
        }
        if ($item->type == "textarea") {
            echo '<textarea name="opt_' . $item->name . '" id="opt_' . $item->name . '" class="regular-text" style="width:100%; min-height:150px;">' . get_option('gamma_' . $item->name) . '</textarea>';
        } elseif ($item->type == "radio") {
            foreach ($item->values as $val) {
                if (is_array($val)) {
                    $val1 = $val[0];
                    $label1 = $val[1];
                } else {
                    $val1 = $val;
                    $label1 = $val;
                }
                if ($val1 == get_option('gamma_' . $item->name)) {
                    $checked = 'checked="checked"';
                } else {
                    $checked = '';
                }
                echo '<fieldset><label for="users_can_register"><input name="opt_' . $item->name . '" type="radio" id="opt_' . $item->name . '" value="' . $val1 . '" ' . $checked . '>' . $label1 . '</label></fieldset>';
            }
        } elseif ($item->type == "checkbox") {
            $values = get_option('gamma_' . $item->name);
            $values = explode("|", $values);
            foreach ($item->values as $val) {
                if (is_array($val)) {
                    $val1 = $val[0];
                    $label1 = $val[1];
                } else {
                    $val1 = $val;
                    $label1 = $val;
                }
                if (in_array($val1, $values)) {
                    $checked = 'checked="checked"';
                } else {
                    $checked = '';
                }
                echo '<fieldset><label for="users_can_register"><input name="opt_' . $item->name . '[]" type="checkbox" id="opt_' . $item->name . '" value="' . $val1 . '" ' . $checked . '> ' . $label1 . '</label></fieldset>';
            }
        } elseif ($item->type == "select") {
            echo '<select name="opt_' . $item->name . '" id="opt_' . $item->name . '" style="width:100%;">';
            foreach ($item->values as $val) {
                if ($val == get_option('gamma_' . $item->name)) {
                    $checked = 'selected="selected"';
                } else {
                    $checked = '';
                }
                echo '<option value="' . $val . '" ' . $checked . '>' . $val . '</option>';
            }
            echo '</select>';
        } elseif ($item->type == "wysiwyg") {
            echo '<div class="settings_wysiwyg">';
            wp_editor(get_option('gamma_' . $item->name), "opt_" . $item->name);
            echo '</div>';
        } else {
            if ($item->type != "title") {
                echo '<input name="opt_' . $item->name . '" type="text" id="opt_' . $item->name . '" value="' . get_option('gamma_' . $item->name) . '" style="width:100%; padding:5px 10px;" class="regular-text">';
            }
        }
        echo '</td></tr>';
    }
    echo '</tbody></table><p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" value="Save Changes"></p></form></div>';
}

function gamma_register_widget($name, $title, $description, $function, $fields) {
    include_once('widget-class/EasyWidgets.class.php' );
    $prefix = $name . '_';
    $widgets = array();
// -----------------------------------------------
    /**
     * easyBox widget
     */
    $args1 = 'array(';
    foreach ($fields as $field) {
        $args1 .= '"' . $field["id"] . '"=>$' . $field["id"] . ',';
    }
    $args1 .= ")";
    $widgets[] = array(
        'id' => 'easyBox',
        'label' => $title,
        'title' => $title,
        'desc' => $description,
        'fields' => $fields,
        'output' => '<?php echo ' . $function . '(' . $args1 . '); ?>'
    );
// -----------------------------------------------
    /**
     * Iterate and register the widgets
     */
    if (class_exists('WidgetCreator')) {
        foreach ($widgets AS &$w) {
            $WC = new WidgetCreator($w);
            eval($WC->render());
        }
    } else
        trigger_error('WidgetCreator does not exist.', E_USER_ERROR);
}

function gamma_register_meta_box($name, $post_types, $fields, $args = array()) {
    require_once("meta-box-class/my-meta-box-class.php");
    if (!is_array($post_types)) {
        $post_types = array($post_types);
    }
    if ($args["name"]) {
        $label = $args["name"];
    } else {
        $label = str_replace("_", " ", ucfirst($name));
    }
    if ($args["location"]) {
        $location = $args["location"];
    } else {
        $location = "normal";
    }
    if (is_admin()) {
        if (($args["pid"] && in_array($_GET["post"], $args["pid"])) || ($_GET["post"] == $args["pid"]) || !$args["pid"] || !$_GET["post"]) {
            $prefix = '';
            $config = array(
                'id' => $name, // meta box id, unique per meta box
                'title' => $label, // meta box title
                'pages' => $post_types, // post types, accept custom post types as well, default is array('post'); optional
                'context' => $location, // where the meta box appear: normal (default), advanced, side; optional
                'priority' => 'high', // order of meta box: high (default), low; optional
                'fields' => array(), // list of meta fields (can be added by field arrays)
                'local_images' => false, // Use local or hosted images (meta box images for add/remove)
                'use_with_theme' => false          //change path if used with theme set to true, false for a plugin or anything else for a custom path(default false).
            );
            $my_meta = new AT_Meta_Box($config);
            if (is_array($fields)) {
                foreach ($fields as $f) {
                    $field = get_option('gamma_custom_post_' . $f);
                    $field = json_decode($field);
                    $values = array();
                    foreach ($field->values as $f1 => $v1) {
                        $values[$f1] = $v1;
                    }
// echo $f."<br>";
// print_r($field);
//  echo "<br><br>";
                    if ($field->type == "select") {
                        $my_meta->addSelect($prefix . $f, $values, array('name' => $field->label));
                    } elseif ($field->type == "radio") {
                        $values = $field->values;
                        $my_meta->addRadio($prefix . $f, $values, array('name' => $field->label));
                    } elseif ($field->type == "checkbox") {
                        $values = $field->values;
                        $res = $my_meta->addCheckboxList($prefix . $f, $values, array('name' => $field->label));
                    } elseif ($field->type == "file") {
                        $my_meta->addFile($prefix . $f, array('name' => $field->label));
                    } elseif ($field->type == "textarea") {
                        $my_meta->addTextarea($prefix . $f, array('name' => $field->label));
                    } elseif ($field->type == "wysiwyg") {
                        $my_meta->addWysiwyg($prefix . $f, array('name' => $field->label));
                    } elseif ($field->type == "repeater") {
                        /* $my_re_meta =  new AT_Meta_Box($config);
                          $repeater_fields=array();
                          foreach($args["repeater_fields"] as $re_field){
                          if($re_field->type=="select"){
                          $repeater_fields[]=$my_re_meta->addSelect($prefix.$re_field->name,$values,array('name'=> $re_field->label));
                          }elseif($re_field->type=="radio"){
                          $repeater_fields[]=$my_re_meta->addRadio($prefix.$re_field->name,$values,array('name'=> $re_field->label));
                          }elseif($re_field->type=="checkbox"){
                          $repeater_fields[]=$my_re_meta->addCheckbox($prefix.$re_field->name,$values,array('name'=> $re_field->label));
                          }elseif($re_field->type=="file"){
                          $repeater_fields[]=$my_re_meta->addFile($prefix.$re_field->name,array('name'=> $re_field->label));
                          }elseif($re_field->type=="textarea"){
                          $repeater_fields[]=$my_re_meta->addTextarea($prefix.$re_field->name,array('name'=> $re_field->label));
                          }elseif($field->type=="wysiwyg"){
                          $repeater_fields[]=$my_re_meta->addWysiwyg($prefix.$re_field->name,array('name'=> $re_field->label));
                          }
                          }
                          //$my_meta->addRepeaterBlock($prefix.$f,array('inline' => true, 'name' => $field->label,'fields' => $repeater_fields));
                         */
                        $repeater_fields = array();
                        $repeater_fields[] = $my_meta->addText('re_text_field_id', array('name' => 'Name'), true);
                        $repeater_fields[] = $my_meta->addFile('re_field_field_id', array('name' => 'File'), true);
                        $my_meta->addRepeaterBlock($prefix . $f, array('inline' => true, 'name' => 'This is a Repeater Block', 'fields' => $repeater_fields));
                    } else {
                        $my_meta->addText($prefix . $f, array('name' => $field->label));
                    }
                }
            }
            $my_meta->Finish();
        }
    }
}

function gamma_register_tax_meta_box($name, $terms, $fields, $args = array()) {
    require_once("tax-meta-class/Tax-meta-class.php");
    if ($args["name"]) {
        $label = $args["name"];
    } else {
        $label = str_replace("_", " ", ucfirst($name));
    }
    if ($args["location"]) {
        $location = $args["location"];
    } else {
        $location = "normal";
    }
    if (is_admin()) {
        $prefix = '';
        $config = array(
            'id' => $name, // meta box id, unique per meta box
            'title' => $label, // meta box title
            'pages' => $terms, // taxonomy name, accept categories, post_tag and custom taxonomies
            'context' => 'normal', // where the meta box appear: normal (default), advanced, side; optional
            'fields' => array(), // list of meta fields (can be added by field arrays)
            'local_images' => false, // Use local or hosted images (meta box images for add/remove)
            'use_with_theme' => false          //change path if used with theme set to true, false for a plugin or anything else for a custom path(default false).
        );
        $my_meta = new Tax_Meta_Class($config);
        if (is_array($fields)) {
            foreach ($fields as $f) {
                $field = get_option('gamma_custom_tax_' . $f);
                $field = json_decode($field);
                foreach ($field->values as $f1 => $v1) {
                    $values[$f1] = $v1;
                }
                if ($field->type == "select") {
                    $my_meta->addSelect($prefix . $f, $values, array('name' => $field->label));
                } elseif ($field->type == "radio") {
                    $values = $field->values;
                    $my_meta->addRadio($prefix . $f, $values, array('name' => $field->label));
                } elseif ($field->type == "checkbox") {
                    $values = $field->values;
                    $res = $my_meta->addCheckboxList($prefix . $f, $values, array('name' => $field->label));
                } elseif ($field->type == "file") {
                    $my_meta->addFile($prefix . $f, array('name' => $field->label));
                } elseif ($field->type == "textarea") {
                    $my_meta->addTextarea($prefix . $f, array('name' => $field->label));
                } elseif ($field->type == "wysiwyg") {
                    $my_meta->addWysiwyg($prefix . $f, array('name' => $field->label));
                } else {
                    $my_meta->addText($prefix . $f, array('name' => $field->label));
                }
            }
        }
        $my_meta->Finish();
    }
}

function gamma_register_user_meta_box($name, $terms, $fields, $args = array()) {
    require_once("user-meta-class/wp_user_fields.php");
    if ($args["name"]) {
        $label = $args["name"];
    } else {
        $label = str_replace("_", " ", ucfirst($name));
    }
    if ($args["location"]) {
        $location = $args["location"];
    } else {
        $location = "normal";
    }
    if (is_admin()) {
        $prefix = '';
        $fields112 = array(
            'publication' => array(
                'label' => $label,
                'type' => 'title',
                'single' => true
            ),
        );
        if ($_GET["user_id"] && !user_can($_GET["user_id"], $terms[0])) {

        } else {
            $bjm_user_fields = new Bjm_user_fields($fields112);
            if (is_array($fields)) {
                foreach ($fields as $f) {
                    $field = get_option('gamma_custom_user_' . $f);
                    $field = json_decode($field);
                    foreach ($field->values as $f1 => $v1) {
                        $values[$f1] = $v1;
                    }
                    $fields11 = array(
                        $f => array(
                            'label' => $field->label,
                            'type' => $field->type,
                            'options' => $values,
                        ),
                    );
                    $bjm_user_fields1 = new Bjm_user_fields($fields11);
                }
            }
        }
    }
}

function gamma_register_post_type($name, $args = array()) {
    if ($args["name"]) {
        $label_plural = $args["name"];
    } else {
        $label_plural = str_replace("_", " ", ucfirst($name));
    }
    if ($args["singular_name"]) {
        $label_singular = $args["singular_name"];
    } else {
        $label_singular = str_replace("_", " ", ucfirst($name));
    }
    if ($args["position"]) {
        $position = $args["position"];
    } else {
        $position = 3;
    }
    if ($args["slug"]) {
        $slug = $args["slug"];
    } else {
        $slug = $name;
    }
    register_post_type($name, array(
        'labels' => array(
            'name' => __($label_plural),
            'singular_name' => __($label_singular)
        ),
        'public' => true,
        'show_ui' => true,
        'show_in_menu' => true,
        'supports' => array('title', 'editor', "thumbnail", "author", "custom-fields", "comments"),
        'rewrite' => array('slug' => $slug, 'with_front' => true),
        'has_archive' => true,
        'menu_position' => $position
            )
    );
}

function gamma_register_taxonomy($name, $post_types, $args = array()) {
    if ($args["name"]) {
        $label_plural = $args["name"];
    } else {
        $label_plural = str_replace("_", " ", ucfirst($name)) . "s";
    }
    if ($args["singular_name"]) {
        $label_singular = $args["singular_name"];
    } else {
        $label_singular = str_replace("_", " ", ucfirst($name));
    }
    if ($args["slug"]) {
        $slug = $args["slug"];
    } else {
        $slug = $name;
    }
    $labels = array(
        'name' => $label_plural,
        'singular_name' => $label_singular,
        'search_items' => 'Search ' . $label_plural,
        'popular_items' => 'Popular ' . $label_plural,
        'all_items' => 'All ' . $label_plural,
        'parent_item' => 'Parent ' . $label_singular,
        'edit_item' => 'Edit ' . $label_plural,
        'update_item' => 'Update ' . $label_singular,
        'add_new_item' => 'Add New ' . $label_singular,
        'new_item_name' => 'New ' . $label_singular,
        'separate_items_with_commas' => 'Separate ' . $label_plural . ' with commas',
        'add_or_remove_items' => 'Add or remove ' . $label_plural,
        'choose_from_most_used' => 'Choose from most used ' . $label_plural
    );


    if ($args["tag_type"]) {
        $hier = false;
    } else {
        $hier = true;
    }

    $tax_args = array(
        'label' => $label_singular,
        'labels' => $labels,
        'public' => true,
        'hierarchical' => $hier,
        'show_ui' => true,
        'show_in_nav_menus' => true,
        'args' => array('orderby' => 'term_order'),
        'rewrite' => array('slug' => $slug, 'with_front' => false),
        'query_var' => true
    );
    register_taxonomy($name, $post_types, $tax_args);
}

function gamma_add_taxonomy($tax, $post_type) {
    if (is_array($post_type)) {
        foreach ($post_type as $type) {
            register_taxonomy_for_object_type($tax, $type);
        }
    } else {
        register_taxonomy_for_object_type($tax, $post_type);
    }
}

function gamma_register_tax_field($name, $label, $type = "", $values = "") {
    if (!$type) {
        $type = "text";
    }
    if (!is_array($values)) {
        $values = explode("|", $values);
    }
    $options = array(
        "label" => $label,
        "type" => $type,
        "values" => $values
    );
    update_option('gamma_custom_tax_' . $name, json_encode($options));
}

function gamma_get_tax_meta($term_id) {
    $values = get_option('tax_meta_' . $term_id, true);
    return ($values);
}

function gamma_add_menu($id, $args = "") {
    if ($args["description"]) {
        $label = $args["description"];
    } else {
        $label = str_replace("_", " ", ucfirst($id));
    }
    register_nav_menu($id, $label);
}

function gamma_add_widget_area($id, $args = "") {
    if ($args["name"]) {
        $label = $args["name"];
    } else {
        $label = str_replace("_", " ", ucfirst($id));
    }
    if ($args["description"]) {
        $description = $args["description"];
    } else {
        $description = ucfirst($menu) . "Widgets in this area will show in the " . str_replace("_", " ", ucfirst($id));
    }
    register_sidebar(array(
        'name' => $label,
        'id' => $id,
        'description' => $description,
        'before_widget' => '<div id="%1$s" class="widget %2$s"><div class="inner_body">',
        'after_widget' => '</div></div>',
        'before_title' => '<h2 class="widgettitle">',
        'after_title' => '</h2>'
    ));
}

function gamma_get_terms($tax, $search, $term_search, $type = "list", $args = "") {
    /*
      array(
      post_search: array(
      pid: array(1,2,3),
      post_type: array("product","post"),
      tax_slug_category: array(aaa,bbb,ccc)
      tax_tags: array(1,2,3)
      meta_price: 15
      meta_price: array(1,2,3)
      meta_price: array(
      compare: >
      value: 5
      )
      date: mm/dd/yyyy
      date_start: mm/dd/yyyy
      date_end: mm/dd/yyyy
      author_name: user_nicename
      author: array(1,2,3)
      author_not: array(1,2,3)
      author_role: array(subscriber,author)
      )
      term_search: array(
     *                              term_id: array(1,2,3)
      parent: 1,
      name: "aaaa"
      )
      order: title_asc/title_desc/date_asc/date_desc/comments_asc/comments_desc/rand/meta_asc_FIELD/meta_desc_FIELD/custom_asc_FUNCTION/custom_desc_FUNCTION
      )
     */
    global $wpdb;
    $post_list = array();
    if ($search) {
        if ($search["post_type"]) {
            if (count($search["post_type"]) > 1) {
                $post_types = "(";
                foreach ($search["post_type"] as $val) {
                    $post_types .= "`post_type`='" . $val . "' OR ";
                }
                $post_types = substr($post_types, 0, strlen($post_types) - 4);
                $post_types .= ")";
            } else {
                $post_types .= "`post_type`='" . $search["post_type"] . "' ";
            }
            $results = $wpdb->get_results("SELECT `wp_posts`.`ID` FROM `wp_posts` WHERE " . $post_types . " AND `post_status`='publish'");
            foreach ($results as $item) {
                $post_list[] = $item->ID;
            }
        } else {
            $results = $wpdb->get_results("SELECT `wp_posts`.`ID` FROM `wp_posts` WHERE `post_type`!='attachment' AND `post_type`!='nav_menu_item' AND `post_status`='publish'");
            foreach ($results as $item) {
                $post_list[] = $item->ID;
            }
        }
        foreach ($search as $f => $v) {
            if (substr($f, 0, 9) == "pid") {
                $posts0 = $v;
            } elseif (substr($f, 0, 9) == "tax_slug_") {
                $tax = substr($f, 9);
                $posts1 = array();
                $term_slugs = "(";
                foreach ($v as $val) {
                    $term_slugs .= "`wp_terms`.`slug`='" . $val . "' OR ";
                }
                $term_slugs = substr($term_slugs, 0, strlen($term_slugs) - 4);
                $term_slugs .= ")";
                $results = $wpdb->get_results("SELECT `wp_term_relationships`.`object_id` FROM `wp_terms`,`wp_term_relationships`,`wp_term_taxonomy` WHERE `wp_term_taxonomy`.`term_id`=`wp_terms`.`term_id` AND `wp_term_taxonomy`.`term_taxonomy_id`=`wp_term_relationships`.`term_taxonomy_id` AND " . $term_slugs . " GROUP BY `wp_term_relationships`.`object_id`");
                foreach ($results as $item) {
                    $posts1[] = $item->object_id;
                }
            } elseif (substr($f, 0, 4) == "tax_") {
                $tax = substr($f, 4);
                $posts2 = array();
                $term_slugs = "(";
                foreach ($v as $val) {
                    $term_slugs .= "`wp_terms`.`term_id`='" . $val . "' OR ";
                }
                $term_slugs = substr($term_slugs, 0, strlen($term_slugs) - 4);
                $term_slugs .= ")";
                $results = $wpdb->get_results("SELECT `wp_term_relationships`.`object_id` FROM `wp_terms`,`wp_term_relationships`,`wp_term_taxonomy` WHERE `wp_term_taxonomy`.`term_id`=`wp_terms`.`term_id` AND `wp_term_taxonomy`.`term_taxonomy_id`=`wp_term_relationships`.`term_taxonomy_id` AND " . $term_slugs . " GROUP BY `wp_term_relationships`.`object_id`");
                foreach ($results as $item) {
                    $posts2[] = $item->object_id;
                }
            } elseif (substr($f, 0, 5) == "meta_") {
                $field = substr($f, 5);
                $tax = substr($f, 9);
                $posts3 = array();
                $term_slugs = "(";
                foreach ($v as $val) {
                    $term_slugs .= "`wp_postmeta`.`meta_value`='" . $val . "' OR ";
                }
                $term_slugs = substr($term_slugs, 0, strlen($term_slugs) - 4);
                $term_slugs .= ")";
                $results = $wpdb->get_results("SELECT `wp_postmeta`.`post_id` FROM `wp_postmeta` WHERE `wp_postmeta`.`meta_key`='" . $field . "' AND " . $term_slugs . " GROUP BY `wp_postmeta`.`post_id`");
                foreach ($results as $item) {
                    $posts3[] = $item->post_id;
                }
            } elseif ($f == "date") {
                $posts4 = array();
                $results = $wpdb->get_results("SELECT `wp_posts`.`ID` FROM `wp_posts` WHERE `wp_posts`.`post_date`>'" . date("Y-m-d H:i:s", $v) . "' AND `wp_posts`.`post_date`<'" . date("Y-m-d H:i:s", $v + 24 * 3600) . "'");
                foreach ($results as $item) {
                    $posts4[] = $item->ID;
                }
            } elseif ($f == "date_start") {
                $posts5 = array();
                $results = $wpdb->get_results("SELECT `wp_posts`.`ID` FROM `wp_posts` WHERE `wp_posts`.`post_date`>'" . date("Y-m-d H:i:s", $v) . "'");
                foreach ($results as $item) {
                    $posts5[] = $item->ID;
                }
            } elseif ($f == "date_end") {
                $posts6 = array();
                $results = $wpdb->get_results("SELECT `wp_posts`.`ID` FROM `wp_posts` WHERE `wp_posts`.`post_date`<'" . date("Y-m-d H:i:s", $v + 24 * 3600) . "'");
                foreach ($results as $item) {
                    $posts6[] = $item->ID;
                }
            } elseif ($f == "pid") {
                $posts7 = array();
                foreach ($v as $val) {
                    $posts7[] = $val;
                }
            } elseif ($f == "author") {
                $user_list = "(";
                foreach ($v as $author) {
                    $user_list .= "`wp_posts`.`post_author`='" . $author . " OR ";
                }
                $user_list = substr($user_list, 0, strlen($user_list) - 4);
                $user_list .= ")";
                $posts8 = array();
                $results = $wpdb->get_results("SELECT `wp_posts`.`ID` FROM `wp_posts` WHERE " . $user_list);
                foreach ($results as $item) {
                    $posts8[] = $item->ID;
                }
            } elseif ($f == "author_not") {
                foreach ($v as $author) {
                    $user_list .= "`wp_posts`.`post_author`!='" . $author . " AND ";
                }
                $user_list = substr($user_list, 0, strlen($user_list) - 5);
                $posts9 = array();
                $results = $wpdb->get_results("SELECT `wp_posts`.`ID` FROM `wp_posts` WHERE " . $user_list);
                foreach ($results as $item) {
                    $posts9[] = $item->ID;
                }
            } elseif ($f == "author_role") {
                $user_list = "(";
                foreach ($v as $role) {
                    $user_query = new WP_User_Query(array('role' => $role));
                    $authors = $author_query->get_results();
                    foreach ($authors as $author) {
                        $user_list .= "`wp_posts`.`post_author`='" . $author->ID . " OR ";
                    }
                }
                $user_list = substr($user_list, 0, strlen($user_list) - 4);
                $user_list .= ")";
                $posts10 = array();
                $results = $wpdb->get_results("SELECT `wp_posts`.`ID` FROM `wp_posts` WHERE " . $user_list);
                foreach ($results as $item) {
                    $posts10[] = $item->ID;
                }
            } else {

            }
        }
        if (count($posts0)) {
            $post_list = array_intersect($post_list, $posts0);
        }
        if (count($posts1)) {
            $post_list = array_intersect($post_list, $posts1);
        }
        if (count($posts2)) {
            $post_list = array_intersect($post_list, $posts2);
        }
        if (count($posts3)) {
            $post_list = array_intersect($post_list, $posts3);
        }
        if (count($posts4)) {
            $post_list = array_intersect($post_list, $posts4);
        }
        if (count($posts5)) {
            $post_list = array_intersect($post_list, $posts5);
        }
        if (count($posts6)) {
            $post_list = array_intersect($post_list, $posts6);
        }
        if (count($posts7)) {
            $post_list = array_intersect($post_list, $posts7);
        }
        if (count($posts8)) {
            $post_list = array_intersect($post_list, $posts8);
        }
        if (count($posts9)) {
            $post_list = array_intersect($post_list, $posts9);
        }
        if (count($posts10)) {
            $post_list = array_intersect($post_list, $posts10);
        }
        $term_slugs = "(";
        foreach ($post_list as $val) {
            $term_slugs .= "`wp_term_relationships`.`object_id`='" . $val . "' OR ";
        }
        $term_slugs = substr($term_slugs, 0, strlen($term_slugs) - 4);
        $term_slugs .= ")";
        $results = $wpdb->get_results("SELECT `wp_terms`.*, COUNT(`wp_term_relationships`.`object_id`) AS `count` FROM `wp_terms`,`wp_term_relationships`,`wp_term_taxonomy` WHERE `wp_term_taxonomy`.`term_id`=`wp_terms`.`term_id` AND `wp_term_taxonomy`.`term_taxonomy_id`=`wp_term_relationships`.`term_taxonomy_id` AND `wp_term_taxonomy`.`taxonomy`='" . $tax . "' AND " . $term_slugs . " GROUP BY `wp_terms`.`term_id`");
    } else {
        $results = get_terms($tax, "hide_empty=0");
    }
    $term_list = array();
    $max = 0;
    $min = 999999999;
    foreach ($results as $item) {
        if ($item->count > $max) {
            $max = $item->count;
        }
        if ($min > $item->count) {
            $min = $item->count;
        }
        $term_list[] = $item;
    }
    foreach ($term_search as $item => $val) {
        if ($item == "keyword") {
            foreach ($term_list as $f => $v) {
                if (!strpos("--" . $v->name, $val) && !strpos("--" . $v->slug, $val)) {
                    unset($term_list[$f]);
                }
            }
        }
        if ($item == "term_id") {
            foreach ($term_list as $f => $v) {
                if (!in_array($v->term_id, $val)) {
                    unset($term_list[$f]);
                }
            }
        }
        if ($item == "parent") {
            foreach ($term_list as $f => $v) {
                if ($val != $v->parent) {
                    unset($term_list[$f]);
                }
            }
        }
        if (substr($item, 0, 5) == "meta_") {
            $meta_name = substr($item, 5);
            foreach ($term_list as $f => $v) {
                $meta = get_option('tax_meta_' . $v->term_id);
                $found = 0;
                foreach ($meta as $meta_f => $meta_v) {
                    if ($meta_f == $meta_name) {
                        if ($meta_v != $val) {
                            unset($term_list[$f]);
                        }
                        $found = 1;
                    }
                }
                if (!$found) {
                    unset($term_list[$f]);
                }
            }
        }
    }
    if ($args["limit"]) {
        for ($i = $args["limit"]; $i < count($term_list); $i++) {
            unset($term_list[$i]);
        }
    }
    if ($type == "list") {
        $return = "<ul class='cf'>";
        foreach ($term_list as $term) {
            if ($link) {
                $term_link = $link . $term->term_id;
            } else {
                $term_link = get_term_link($term->slug, $tax);
            }
            $return .= '<li><a href="' . $term_link . '">' . $term->name . "</a> (" . $term->count . ")</li>";
        }
        $return .= "</ul>";
        return $return;
    } elseif ($type == "checkbox") {
        $return = "<ul>";
        foreach ($term_list as $term) {
            if (in_array($term->term_id, $_GET["tax_" . $tax])) {
                $selected = 'checked="checked"';
                $active = 'activated';
            } else {
                $selected = '';
                $active = '';
            }
            $return .= '<li ><input type="checkbox" name="tax_' . $tax . '[]" value="' . $term->term_id . '" id="' . $term->term_id . '" ' . $selected . ' /> <span class="selectit ' . $active . '">' . $term->name . " (" . $term->count . ")</span></li>";
        }
        $return .= "</ul>";
        return $return;
    } elseif ($type == "radio") {
        $return = "<ul>";
        foreach ($term_list as $term) {
            if (in_array($term->term_id, $_GET["tax_" . $tax])) {
                $selected = 'checked="checked"';
            } else {
                $selected = '';
            }
            $return .= '<li><input type="radio" name="tax_' . $tax . '" value="' . $term->term_id . '" ' . $selected . ' /> ' . $term->name . " (" . $term->count . ")</li>";
        }
        $return .= "</ul>";
        return $return;
    } elseif ($type == "select") {
        $return = '<select name="tax_' . $tax . '" id="tax_' . $tax . '">';
        foreach ($term_list as $term) {
            if (in_array($term->term_id, $_GET["tax_" . $tax])) {
                $selected = 'selected="selected"';
            } else {
                $selected = '';
            }
            $return .= '<option value="' . $term->term_id . '" ' . $selected . '>' . $term->name . " (" . $term->count . ")</option>";
        }
        $return .= "</select>";
        return $return;
    } elseif ($type == "cloud") {
        $min_font = 8;
        $max_font = 20;
        $med_font = 14;
        $med = ($max + $min) / 2;
        $range_font = $max_font - $min_font;
        $range_count = $max - $min;
        if ($range_font && $range_count) {
            $countunit = $range_font / $range_count;
        } else {
            $countunit = $range_font / 2;
        }
        shuffle($term_list);
        $return = '<div class="tag-cloud">';
        foreach ($term_list as $term) {
            if ($link) {
                $term_link = $link . $term->term_id;
            } else {
                $term_link = get_term_link($term->slug, $tax);
            }
            $return .= '<a href="' . $term_link . '" rel="' . $term->count . '" style="font-size:' . $countunit . 'px;">' . $term->name . "</a>";
        }
        $return .= "</div>";
        return $return;
    } else {
        return $term_list;
    }
}

function gamma_get_search_field($name, $type = "", $value = "", $args = "") {
    if (!$value) {
        $value = $_GET[$name];
    }
    if (substr($name, 0, 4) == "tax_") {
        if (!$type) {
            $type = "select";
        }
        $sel_term = get_query_var('term');
        $field = substr($name, 4);
        if ($type == "checkbox") {
            echo '<ul id="tax_' . $field . '" class="' . $args["classes"] . '">';
            $terms = get_terms($field, 'hide_empty=0&parent=0');
            foreach ($terms as $term) {
                if (is_array($value)) {
                    if (in_array($term->term_id, $value) || $sel_term == $term->slug || in_array($term->slug, $sel_term)) {
                        $selected = 'checked="checked"';
                    } else {
                        $selected = "";
                    }
                } else {
                    if ($term->term_id == $value || $sel_term == $term->slug || in_array($term->slug, $sel_term)) {
                        $selected = 'checked="checked"';
                    } else {
                        $selected = "";
                    }
                }
//                echo '<li><span><input type="checkbox" name="tax_' . $field . '[]" value="' . $term->term_id . '" ' . $selected . '/></span> ' . $term->name . "</li>";
                echo '<li><label><input type="checkbox" name="tax_' . $field . '[]" value="' . $term->term_id . '" ' . $selected . '/> ' . $term->name . "</label></li>";
            }
            echo "</ul>";
        } elseif ($type == "radio") {
            echo '<ul id="tax_' . $field . '" class="' . $args["classes"] . '">';
            $terms = get_terms($field, 'hide_empty=0');
            foreach ($terms as $term) {
                if (is_array($value)) {
                    if (in_array($term->term_id, $value) || $sel_term == $term->slug || in_array($term->slug, $sel_term)) {
                        $selected = 'checked="checked"';
                    } else {
                        $selected = "";
                    }
                } else {
                    if ($term->term_id == $value || $sel_term == $term->slug || in_array($term->slug, $sel_term)) {
                        $selected = 'checked="checked"';
                    } else {
                        $selected = "";
                    }
                }
                echo '<li><span><input type="radio" name="tax_' . $field . '" value="' . $term->term_id . '" ' . $selected . '/></span> ' . $term->name . "</li>";
            }
            echo "</ul>";
        } elseif ($type == "select") {
            echo '<select name="tax_' . $field . '" id="tax_' . $field . '" class="' . $args["classes"] . '">';
            if (isset($args["select_label"])) {
                $select_label = $args["select_label"];
            } else {
                $select_label = " -- select -- ";
            }
            echo '<option value="0">' . $select_label . '</option>';
            $terms = get_terms($field, 'hide_empty=0&parent=0');
            foreach ($terms as $term) {
                if (is_array($value)) {
                    if (in_array($term->term_id, $value) || $sel_term == $term->slug || in_array($term->slug, $sel_term)) {
                        $selected = 'selected="selected"';
                    } else {
                        $selected = "";
                    }
                } else {
                    if ($term->term_id == $value || $sel_term == $term->slug || in_array($term->slug, $sel_term)) {
                        $selected = 'selected="selected"';
                    } else {
                        $selected = "";
                    }
                }

                $terms1 = get_terms($field, 'hide_empty=0&parent=' . $term->term_id);
                if (count($terms1)) {
                    echo ' <optgroup label="' . $term->name . '" data-parent="' . $term->slug . '">';
                } else {
                    echo '<option value="' . $term->term_id . '" ' . $selected . '>' . $term->name . "</option>";
                }
                foreach ($terms1 as $term1) {
                    if (is_array($value)) {
                        if (in_array($term1->term_id, $value) || $sel_term == $term1->slug || in_array($term1->slug, $sel_term)) {
                            $selected1 = 'selected="selected"';
                        } else {
                            $selected1 = "";
                        }
                    } else {
                        if ($term1->term_id == $value || $sel_term == $term1->slug || in_array($term1->slug, $sel_term)) {
                            $selected1 = 'selected="selected"';
                        } else {
                            $selected1 = "";
                        }
                    }
                    echo '<option value="' . $term1->term_id . '" ' . $selected1 . '>' . $term1->name . "</option>";
                }
                if (count($terms1)) {
                    echo '</optgroup>';
                }
            }
            echo "</select>";
        } elseif ($type == "cloud") {
            $min_font = 8;
            $max_font = 20;
            $med_font = 14;
            $med = ($max + $min) / 2;
            $range_font = $max_font - $min_font;
            $terms = get_terms($field, 'hide_empty=1');
            foreach ($terms as $term) {
                if ($term->count) {
                    $counts[] = $term->count;
                }
            }
            $min = min($counts);
            $max = max($counts);
            $range_count = $max - $min;
            shuffle($terms);
            echo '<div class="tag-cloud">';
            foreach ($terms as $term) {
                if ($link) {
                    $term_link = $link . $term->term_id;
                } else {
                    $term_link = get_term_link($term->slug, $field);
                }
                if ($range_font && $range_count) {
                    $countunit = $min_font + ($term->count - $min) * ($range_font / $range_count);
                } else {
                    $countunit = $min_font + $range_font / 2;
                }
                echo '<a href="' . $term_link . '" rel="' . $term->count . '" style="font-size:' . $countunit . 'px;">' . $term->name . "</a>";
            }
            echo"</div>";
        }
    }
    if (substr($name, 0, 5) == "meta_") {
        $field = substr($name, 5);
        $info = get_option('gamma_custom_post_' . $field);
        $info = json_decode($info);
        if (!$type) {
            $type = $info->type;
        }
        $vals = $info->values;
        if ($type == "textarea") {
            echo '<textarea name="meta_' . $field . '" id="meta_' . $field . '" class="' . $args["classes"] . '">' . $value . '</textarea>';
        } elseif ($type == "checkbox") {
            echo '<ul id="meta_' . $field . '" class="' . $args["classes"] . '">';
            foreach ($vals as $fs => $v) {
                if (is_array($value)) {
                    if (in_array($fs, $value)) {
                        $selected = 'checked="checked"';
                    } else {
                        $selected = "";
                    }
                } else {
                    if ($v == $value) {
                        $selected = 'checked="checked"';
                    } else {
                        $selected = "";
                    }
                }
                echo '<li><span><input type="checkbox" name="meta_' . $field . '[]" value="' . $fs . '" ' . $selected . '/></span> ' . $v . "</li>";
            }
            echo "</ul>";
        } elseif ($type == "radio") {
            echo '<ul id="meta_' . $field . '" class="' . $args["classes"] . '">';
            foreach ($vals as $fs => $v) {
                if (is_array($value)) {
                    if (in_array($fs, $value)) {
                        $selected = 'checked="checked"';
                    } else {
                        $selected = "";
                    }
                } else {
                    if ($v == $value) {
                        $selected = 'checked="checked"';
                    } else {
                        $selected = "";
                    }
                }
                echo '<li><span><input type="radio" name="meta_' . $field . '" value="' . $fs . '" ' . $selected . '/></span> ' . $v . "</li>";
            }
            echo "</ul>";
        } elseif ($type == "select") {
            echo '<select name="meta_' . $field . '" id="meta_' . $field . '" class="' . $args["classes"] . '">';
            echo '<option value="0"> -- select -- </option>';
            foreach ($vals as $fs => $v) {
                if (is_array($value)) {
                    if (in_array($fs, $value)) {
                        $selected = 'selected="selected"';
                    } else {
                        $selected = "";
                    }
                } else {
                    if ($fs == $value) {
                        $selected = 'selected="selected"';
                    } else {
                        $selected = "";
                    }
                }
                echo '<option value="' . $fs . '" ' . $selected . '>' . $v . "</option>";
            }
            echo "</select>";
        } else {
            echo '<input type="text" name="meta_' . $field . '" id="meta_' . $field . '" value="' . $value . '" class="' . $args["classes"] . '" />';
        }
    }
    if ($name == "search") {
        if ($_GET["key"] && !$value) {
            $value = $_GET["key"];
        }
        echo '<input type="text" name="key" id="core_key" value="' . $value . '" class="' . $args["classes"] . '" />';
    }
    /*
      if($name=="excerpt"){
      echo '<textarea name="core_'.$name.'" id="core_'.$name.'">'.$value.'</textarea>';
      }
      if($name=="content"){
      wp_editor( $value, "core_content" );
      //echo '<textarea name="core_'.$name.'" id="core_'.$name.'">'.$value.'</textarea>';
      }
      if($name=="content"){
      //wp_editor( $value, "core_content" );
      //echo '<textarea name="core_'.$name.'" id="core_'.$name.'">'.$value.'</textarea>';
      }
     */
}

function gamma_get_add_user_field($name, $type = "", $args = "") {
    if (substr($name, 0, 5) == "meta_") {
        $field = substr($name, 5);
        $info = get_option('gamma_custom_user_' . $field);
        $info = json_decode($info);
        if (!$type) {
            $type = $info->type;
        }
        $vals = $info->values;
        if ($type == "textarea") {
            echo '<textarea name="user_meta_' . $field . '" id="user_meta_' . $field . '" class="' . $args["classes"] . '" placeholder="' . $args["placeholder"] . '">' . $value . '</textarea>';
        } elseif ($type == "checkbox") {
            echo '<ul id="user_meta_' . $field . '">';
            foreach ($vals as $f => $v) {
                echo '<li><span><input type="checkbox" name="user_meta_' . $field . '[]" value="' . $f . '" class="' . $args["classes"] . '" /></span>' . $v . "</li>";
            }
            echo "</ul>";
        } elseif ($type == "radio") {
            echo '<ul id="user_meta_' . $field . '">';
            foreach ($vals as $f => $v) {
                echo '<li><span><input type="radio" name="user_meta_' . $field . '" value="' . $f . '" class="' . $args["classes"] . '" /></span> ' . $v . "</li>";
            }
            echo "</ul>";
        } elseif ($type == "select") {
            echo '<select name="user_meta_' . $field . '" id="user_meta_' . $field . '" class="' . $args["classes"] . '">';
            echo '<option value="0"> -- select -- </option>';
            foreach ($vals as $f => $v) {
                echo '<option value="' . $f . '">' . $v . "</option>";
            }
            echo "</select>";
        } elseif ($type == "file") {
            echo '<input type="file" name="user_file_' . $field . '" id="user_file_' . $field . '" value="' . $value . '" class="' . $args["classes"] . '" />';
        } else {
            echo '<input type="text" name="user_meta_' . $field . '" placeholder="' . $args["placeholder"] . '"  id="user_meta_' . $field . '" value="' . $value . '" class="' . $args["classes"] . '" />';
        }
    }
    if ($name == "password") {
        echo '<input type="password" name="user_core_' . $name . '" id="user_core_' . $name . '" class="' . $args["classes"] . '" placeholder="' . $args["placeholder"] . '" />';
    }
    if ($name == "password_confirm") {
        echo '<input type="password" name="user_core_' . $name . '2" id="user_core_' . $name . '2" class="' . $args["classes"] . '" placeholder="' . $args["placeholder"] . '" />';
    }
    if ($name == "login" || $name == "display_name" || $name == "nickname") {
        echo '<input type="text" name="user_core_' . $name . '" id="user_core_' . $name . '" class="' . $args["classes"] . '" placeholder="' . $args["placeholder"] . '" />';
    }
    if ($name == "first_name" || $name == "last_name") {
        echo '<input type="text" name="user_meta_' . $name . '" id="user_meta_' . $name . '" class="' . $args["classes"] . '" placeholder="' . $args["placeholder"] . '" />';
    }
    if ($name == "description") {
        echo '<textarea name="user_core_' . $name . '" id="user_core_' . $name . '" class="' . $args["classes"] . '" placeholder="' . $args["placeholder"] . '"></textarea>';
    }
    if ($name == "email") {
        echo '<input type="email" name="user_core_' . $name . '" id="user_core_' . $name . '" class="' . $args["classes"] . '" placeholder="' . $args["placeholder"] . '" />';
    }
    if ($name == "avatar") {
        echo '<input type="file" name="user_core_avatar" id="user_core_avatar" class="' . $args["classes"] . '" />';
    }
    if ($name == "image_gallery") {
//echo '<div class="upload_div"><label>'.$args["label"].'</label><input type="file" name="post_core_gallery" id="post_core_gallery" multiple /></div>';
        $sel = '[data_file="+file.name+"]';
        ?>
        <script>
            $(document).ready(function () {
                Dropzone.options.myAwesomeDropzoneUser = {
                    url: "<?php echo get_bloginfo("url"); ?>/?ajax_action=upload_image_user",
                    previewsContainer: ".dropzone-previews_user",
                    uploadMultiple: true,
                    parallelUploads: 1,
                    maxFiles: 100,
                    addRemoveLinks: true,
                    init: function () {
                        this.on("success", function (file, response) {
                            $("#attachment_inputs").append(response);
                        });
                        this.on("removedfile", function (file, response) {
                            var rem = $('#attachment_inputs input[data_file="' + file.name + '"]').attr("value");
                            $('input[data_file="' + file.name + '"]').remove();
                            $.ajax({
                                url: '<?php bloginfo("url"); ?>/?ajax_action=delete_img&img_id=' + rem,
                                context: document.body
                            })
                        });
                    }
                }
            })
        </script>
        <div class="dropzone-previews_user dropzone" id="my-awesome-dropzone-user" style="clear:both; overflow:auto; margin-bottom:20px;"></div>
        <div id="attachment_inputs" class="hidden"></div>
        <?php
    }
}

function gamma_get_update_user_field($name, $pid, $type = "", $args = "") {
    $user = get_user_by("id", $pid);
    $user_vals = array();
    foreach ($user->data as $f => $v) {
        $user_vals[$f] = $v;
    }
    if (substr($name, 0, 5) == "meta_") {
        $field = substr($name, 5);
        $info = get_option('gamma_custom_user_' . $field);
        $info = json_decode($info);
        if (!$type) {
            $type = $info->type;
        }
        $vals = $info->values;
        $value = get_user_meta($pid, $field, true);
        if (count($value1 = explode("|", $value)) > 1) {
            $value = explode("|", $value);
        }
        if ($type == "textarea") {
            echo '<textarea name="user_meta_' . $field . '" id="user_meta_' . $field . '" class="' . $args["classes"] . '">' . $value . '</textarea>';
        } elseif ($type == "checkbox") {
            echo '<ul id="user_meta_' . $field . '">';
            foreach ($vals as $v) {
                if (is_array($value)) {
                    if (in_array($v, $value)) {
                        $selected = 'checked="checked"';
                    } else {
                        $selected = "";
                    }
                } else {
                    if ($v == $value) {
                        $selected = 'checked="checked"';
                    } else {
                        $selected = "";
                    }
                }
                echo '<li><span><input type="checkbox" name="user_meta_' . $field . '[]" value="' . $f . '" ' . $selected . ' class="' . $args["classes"] . '"/></span>' . $v . "</li>";
            }
            echo "</ul>";
        } elseif ($type == "radio") {
            echo '<ul id="user_meta_' . $field . '">';
            foreach ($vals as $v) {
                if (is_array($value)) {
                    if (in_array($v, $value)) {
                        $selected = 'checked="checked"';
                    } else {
                        $selected = "";
                    }
                } else {
                    if ($v == $value) {
                        $selected = 'checked="checked"';
                    } else {
                        $selected = "";
                    }
                }
                echo '<li><span><input type="radio" name="user_meta_' . $field . '" value="' . $f . '" ' . $selected . ' class="' . $args["classes"] . '"/></span> ' . $v . "</li>";
            }
            echo "</ul>";
        } elseif ($type == "select") {
            echo '<select name="user_meta_' . $field . '" id="user_meta_' . $field . '" class="' . $args["classes"] . '">';
            echo '<option value="0"> -- select -- </option>';
            foreach ($vals as $f => $v) {
                if (is_array($value)) {
                    if (in_array($f, $value)) {
                        $selected = 'selected="selected"';
                    } else {
                        $selected = "";
                    }
                } else {
                    if ($f == $value) {
                        $selected = 'selected="selected"';
                    } else {
                        $selected = "";
                    }
                }
                echo '<option value="' . $f . '" ' . $selected . '>' . $v . "</option>";
            }
            echo "</select>";
        } elseif ($type == "file") {
            echo '<input type="file" name="user_file_' . $field . '" id="user_file_' . $field . '" value="' . $value . '" class="' . $args["classes"] . '" />';
            echo "<div>Current file: ";
            echo '<a href="' . wp_get_attachment_url($value) . '" target="_blank">' . get_the_title($value) . "</a>";
            echo "</div>";
        } else {
            echo '<input type="text" name="user_meta_' . $field . '" id="user_meta_' . $field . '" value="' . $value . '" class="' . $args["classes"] . '" />';
        }
    }
    if ($name == "password") {
        echo '<input type="password" name="user_core_' . $name . '" id="user_core_' . $name . '" class="' . $args["classes"] . '" />';
    }
    if ($name == "password_confirm") {
        echo '<input type="password" name="user_core_' . $name . '2" id="user_core_' . $name . '2" class="' . $args["classes"] . '" />';
    }
    if ($name == "login" || $name == "display_name" || $name == "nickname") {
        if ($name == "login") {
            $name1 = "user_login";
        } else {
            $name1 = $name;
        }
        echo '<input type="text" name="user_core_' . $name . '" id="user_core_' . $name . '" class="' . $args["classes"] . '" value="' . $user_vals[$name1] . '" />';
    }
    if ($name == "first_name" || $name == "last_name") {
        $value = get_user_meta($pid, $name, true);
        echo '<input type="text" name="user_meta_' . $name . '" id="user_meta_' . $name . '" value="' . $value . '" class="' . $args["classes"] . '" placeholder="' . $args["placeholder"] . '" />';
    }
    if ($name == "description") {
        $value = get_user_meta($pid, "description", true);
        echo '<textarea name="user_core_' . $name . '" id="user_core_' . $name . '" class="' . $args["classes"] . '">' . $value . '</textarea>';
    }
    if ($name == "email") {
        echo '<input type="email" name="user_core_' . $name . '" id="user_core_' . $name . '" class="' . $args["classes"] . '" value="' . $user_vals["user_email"] . '" />';
    }
    if ($name == "avatar") {
        $av = get_user_meta($pid, "avatar_id", true);
        echo '<div style="float:left; width:20%;" class="img_max_width">';
        if (wp_get_attachment_image($av)) {
            echo wp_get_attachment_image($av);
        } else {
            echo "<img alt='image_alt' src='" . get_bloginfo("template_url") . "/assets/img/defaults/no_user.png' class='avatar avatar-{$size} photo avatar-default' height='{$size}' width='{$size}' />";
        }
        echo '</div><div style="float:right; width:75%;">';
        echo '<input type="file" name="user_core_avatar" id="user_core_avatar" />';
        echo '</div>';
    }
    if ($name == "image_gallery") {
        $images2 = array();
        if (get_user_meta($pid, "avatar_id", true)) {
            $images2[] = get_user_meta($pid, "avatar_id", true);
        }
        $images_list = get_user_meta($pid, "image_gallery", true);
        foreach ($images_list as $im) {
            $images2[] = $im;
        }
        if ($images2) {
            $gal = '<ul class="gallery_list row" style="list-style:none; padding:0; margin:0; margin-bottom:20px; margin-left:-10px;">';
            foreach ($images2 as $image) {
                $gal .= '<li class="col-lg-2 no-padding img_max_width" style="margin-bottom:10px;">' . wp_get_attachment_image($image, "thumbnail", false) . '<div style="position:absolute; bottom:0; background:#FFF; width:100%; padding:3px 5px; opacity:0.95;"><a href="' . get_bloginfo("url") . '/edit-profile/edit-image/?img_id=' . $image . '" class="pull-left" style="display:block; background:#FFF; padding:3px;">edit</a><a href="' . get_bloginfo("url") . '/?ajax_action=delete_img_user&img_id=' . $image . '" class="del_img_link pull-right" style="display:block; background:#FFF; padding:3px; text-align:right;">delete</a></div></li>';
            }
            $gal .= "</ul>";
            ?>
            <script>$(document).ready(function () {
                    $('.del_img_link').click(function () {
                        var lnk = $(this).attr('href');
                        var txt;
                        var r = confirm("Are you sure you want to delete?");
                        if (r == true) {
                            $.ajax({url: lnk});
                            $(this).parent().remove();
                        } else {
                        }
                        return false;
                    })
                });</script>
            <?php
        }
        $sel = '[data_file="+file.name+"]';
        echo $gal;
        ?>
        <script>
            $(document).ready(function () {
                Dropzone.options.myAwesomeDropzoneUser = {
                    url: "<?php echo get_bloginfo("url"); ?>/?ajax_action=upload_image_user",
                    previewsContainer: ".dropzone-previews_user",
                    uploadMultiple: true,
                    parallelUploads: 1,
                    maxFiles: 100,
                    addRemoveLinks: true,
                    init: function () {
                        this.on("success", function (file, response) {
                            $("#attachment_inputs").append(response);
                        });
                        this.on("removedfile", function (file, response) {
                            var rem = $('#attachment_inputs input[data_file="' + file.name + '"]').attr("value");
                            $('input[data_file="' + file.name + '"]').remove();
                            $.ajax({
                                url: '<?php bloginfo("url"); ?>/?ajax_action=delete_img&img_id=' + rem,
                                context: document.body
                            })
                        });
                    }
                }
            })
        </script>
        <div class="dropzone-previews_user dropzone" id="my-awesome-dropzone-user" style="clear:both; overflow:auto; margin-bottom:20px;"></div>
        <div id="attachment_inputs" class="hidden"></div>
        <?php
    }
}

function gamma_register_user_field($name, $label, $type = "", $values = "") {
    if (!$type) {
        $type = "text";
    }
    if (!is_array($values)) {
        $values = explode("|", $values);
    }
    foreach ($values as $f => $v) {
        if (is_int($f)) {
            $vals[$f] = $v;
        } else {
            $vals[$f] = $v;
        }
    }
    $options = array(
        "label" => $label,
        "type" => $type,
        "values" => $vals
    );
    update_option('gamma_custom_user_' . $name, json_encode($options));
}

function gamma_get_add_post_field($name, $type = "", $args = "") {
    if (substr($name, 0, 4) == "tax_") {
        if (!$type) {
            $type = "select";
        }
        $field = substr($name, 4);
        if ($type == "checkbox") {
            echo '<ul id="tax_' . $field . '" class="' . $args["classes"] . '">';
            $terms = get_terms($field, 'hide_empty=0');
            foreach ($terms as $term) {
                echo '<li><label><input type="checkbox" name="post_tax_' . $field . '[]" value="' . $term->term_id . '" />' . $term->name . "</span></li>";
            }
            echo "</ul>";
        } elseif ($type == "radio") {
            echo '<ul id="tax_' . $field . '" class="' . $args["classes"] . '">';
            $terms = get_terms($field, 'hide_empty=0');
            foreach ($terms as $term) {
                echo '<li><span><input type="radio" name="post_tax_' . $field . '" value="' . $term->term_id . '"/></span> ' . $term->name . "</li>";
            }
            echo "</ul>";
        } elseif ($type == "select") {
            echo '<select name="post_tax_' . $field . '" id="post_tax_' . $field . '" class="' . $args["classes"] . '">';
            echo '<option value="0"> -- select -- </option>';
            $terms = get_terms($field, 'hide_empty=0&parent=0');
            foreach ($terms as $term) {
                $terms1 = get_terms($field, 'hide_empty=0&parent=' . $term->term_id);
                if (count($terms1)) {
                    echo ' <optgroup label="' . $term->name . '" data-parent="' . $term->slug . '">';
                } else {
                    echo '<option value="' . $term->term_id . '" ' . $selected . '>' . $term->name . "</option>";
                }
                foreach ($terms1 as $term1) {
                    $terms1 = get_terms($field, 'hide_empty=0&parent=' . $term->term_id);
                    echo '<option value="' . $term1->term_id . '">' . $term1->name . "</option>";
                }
                if (count($terms1)) {
                    echo ' </optgroup>';
                }
            }
            echo "</select>";
        } elseif ($type == "tags") {
            echo '<input type="text" name="post_tax_' . $field . '" id="post_tax_tags_' . $field . '" value="' . $value . '" class="' . $args["classes"] . '" />';
        }
    }
    if (substr($name, 0, 5) == "meta_") {
        $field = substr($name, 5);
        $info = get_option('gamma_custom_post_' . $field);
        $info = json_decode($info);
        if (!$type) {
            $type = $info->type;
        }
        $vals = $info->values;
        if ($type == "textarea") {
            echo '<textarea name="post_meta_' . $field . '" id="post_meta_' . $field . '" class="' . $args["classes"] . '">' . $value . '</textarea>';
        } elseif ($type == "checkbox") {
            echo '<ul id="meta_' . $field . '" class="' . $args["classes"] . '">';
            foreach ($vals as $f => $v) {
                echo '<li><span><input type="checkbox" name="post_meta_' . $field . '[]" value="' . $f . '" /></span>' . $v . "</li>";
            }
            echo "</ul>";
        } elseif ($type == "radio") {
            echo '<ul id="tax_' . $field . '" class="' . $args["classes"] . '" >';
            foreach ($vals as $f => $v) {
                echo '<li><span><input type="radio" name="post_meta_' . $field . '" value="' . $f . '" /></span> ' . $v . "</li>";
            }
            echo "</ul>";
        } elseif ($type == "select") {
            echo '<select name="post_meta_' . $field . '" id="post_meta_' . $field . '" class="' . $args["classes"] . '">';
            echo '<option value="0"> -- select -- </option>';
            foreach ($vals as $f => $v) {
                echo '<option value="' . $f . '">' . $v . "</option>";
            }
            echo "</select>";
        } elseif ($type == "file") {
            echo '<input type="file" name="post_file_' . $field . '" id="post_file_' . $field . '" value="' . $value . '" class="' . $args["classes"] . '" />';
        } else {
            echo '<input type="text" name="post_meta_' . $field . '" id="post_meta_' . $field . '" value="' . $value . '" class="' . $args["classes"] . '" />';
        }
    }
    if ($name == "title") {
        echo '<input type="text" name="post_core_' . $name . '" id="post_core_' . $name . '" class="' . $args["classes"] . '" />';
    }
    if ($name == "excerpt") {
        echo '<textarea name="post_core_' . $name . '" id="post_core_' . $name . '" class="' . $args["classes"] . '"></textarea>';
    }
    if ($name == "content") {
        echo '<textarea name="post_core_content" id="post_core_content" class="' . $args["classes"] . '">' . $value . '</textarea>';
    }
    if ($name == "featured_image") {
        echo '<div class="' . $args["classes"] . '">' . $args["label"] . ' <input type="file" name="post_core_image" id="post_core_image" /></div>';
    }
    if ($name == "image_gallery") {
        $sel = '[data_file="+file.name+"]';
        ?>
        <script>
            $(document).ready(function () {
                Dropzone.options.myAwesomeDropzone = {
                    url: "<?php echo get_bloginfo("url"); ?>/?ajax_action=upload_image",
                    previewsContainer: ".dropzone-previews",
                    uploadMultiple: true,
                    parallelUploads: 1,
                    maxFiles: 100,
                    addRemoveLinks: true,
                    init: function () {
                        this.on("success", function (file, response) {
                            $("#attachment_inputs_post").append(response);
                        });
                        this.on("removedfile", function (file, response) {
                            var rem = $('#attachment_inputs_post input[data_file="' + file.name + '"]').attr("value");
                            $('input[data_file="' + file.name + '"]').remove();
                            $.ajax({
                                url: '<?php bloginfo("url"); ?>/?ajax_action=delete_img&img_id=' + rem,
                                context: document.body
                            })
                        });
                    }
                }
            })
        </script>
        <div class="dropzone-previews dropzone" id="my-awesome-dropzone" style="clear:both; overflow:auto; margin-bottom:20px;"></div>
        <div id="attachment_inputs_post" class="hidden"></div>
        <?php
    }
}

function gamma_get_update_post_field($name, $pid, $type = "", $args = "") {
    if (substr($name, 0, 4) == "tax_") {
        if (!$type) {
            $type = "select";
        }
        $field = substr($name, 4);
        $value = array();
        $value_val = array();
        $terms = get_the_terms($pid, $field);
        foreach ($terms as $term) {
            $value[] = $term->term_id;
            $value_val[] = $term->name;
        }
        if ($type == "checkbox") {
            echo '<ul id="tax_' . $field . '" class="' . $args["classes"] . '" >';
            $terms = get_terms($field, 'hide_empty=0');
            foreach ($terms as $term) {
                if (is_array($value)) {
                    if (in_array($term->term_id, $value)) {
                        $selected = 'checked="checked"';
                    } else {
                        $selected = "";
                    }
                } else {
                    if ($term->term_id == $value) {
                        $selected = 'checked="checked"';
                    } else {
                        $selected = "";
                    }
                }
                echo '<li><label><input type="checkbox" name="post_tax_' . $field . '[]" value="' . $term->term_id . '" ' . $selected . ' class="' . $args["classes"] . '" /> ' . $term->name . "</span></li>";
            }
            echo "</ul>";
        } elseif ($type == "radio") {
            echo '<ul id="tax_' . $field . '" class="' . $args["classes"] . '" >';
            $terms = get_terms($field, 'hide_empty=0');
            foreach ($terms as $term) {
                if (is_array($value)) {
                    if (in_array($term->term_id, $value)) {
                        $selected = 'checked="checked"';
                    } else {
                        $selected = "";
                    }
                } else {
                    if ($term->term_id == $value) {
                        $selected = 'checked="checked"';
                    } else {
                        $selected = "";
                    }
                }
                echo '<li><label><input type="radio" name="post_tax_' . $field . '" value="' . $term->term_id . '" ' . $selected . ' class="' . $args["classes"] . '" /> ' . $term->name . "</label></li>";
            }
            echo "</ul>";
        } elseif ($type == "select") {
            echo '<select name="post_tax_' . $field . '" id="post_tax_' . $field . '" class="' . $args["classes"] . '">';
            echo '<option value="0"> -- select -- </option>';
            $terms = get_terms($field, 'hide_empty=0&parent=0');
            foreach ($terms as $term) {
                if (is_array($value)) {
                    if (in_array($term->term_id, $value)) {
                        $selected = 'selected="selected"';
                    } else {
                        $selected = "";
                    }
                } else {
                    if ($term->term_id == $value) {
                        $selected = 'selected="selected"';
                    } else {
                        $selected = "";
                    }
                }
                $terms1 = get_terms($field, 'hide_empty=0&parent=' . $term->term_id);
                if (count($terms1)) {
                    echo ' <optgroup label="' . $term->name . '" data-parent="' . $term->slug . '">';
                } else {
                    echo '<option value="' . $term->term_id . '" ' . $selected . '>' . $term->name . "</option>";
                }
                foreach ($terms1 as $term1) {
                    if (is_array($value)) {
                        if (in_array($term1->term_id, $value)) {
                            $selected1 = 'selected="selected"';
                        } else {
                            $selected1 = "";
                        }
                    } else {
                        if ($term1->term_id == $value) {
                            $selected1 = 'selected="selected"';
                        } else {
                            $selected1 = "";
                        }
                    }
                    echo '<option value="' . $term1->term_id . '" ' . $selected1 . '>' . $term1->name . "</option>";
                }
                if (count($terms1)) {
                    echo ' </optgroup>';
                }
            }
            echo "</select>";
        } elseif ($type == "tags") {
            echo '<input type="text" name="post_tax_' . $field . '" id="post_tax_' . $field . '" value="' . implode(", ", $value_val) . '" class="' . $args["classes"] . '" />';
        }
    }
    if (substr($name, 0, 5) == "meta_") {
        $field = substr($name, 5);
        $info = get_option('gamma_custom_post_' . $field);
        $info = json_decode($info);
        if (!$type) {
            $type = $info->type;
        }
        $vals = $info->values;
        $value = get_post_meta($pid, $field);
        if (count($value) > 1) {
            $value = $value;
        } else {
            $value = $value[0];
        }
        if (count(explode("|", $value)) > 1) {
            $value = explode("|", $value);
        }
        if ($type == "textarea") {
            echo '<textarea name="post_meta_' . $field . '" id="post_meta_' . $field . '" class="' . $args["classes"] . '">' . $value . '</textarea>';
        } elseif ($type == "checkbox") {
            echo '<ul id="meta_' . $field . '" class="' . $args["classes"] . '" >';
            foreach ($vals as $f => $v) {
                if (is_array($value)) {
                    if (in_array($f, $value)) {
                        $selected = 'checked="checked"';
                    } else {
                        $selected = "";
                    }
                } else {
                    if ($f == $value) {
                        $selected = 'checked="checked"';
                    } else {
                        $selected = "";
                    }
                }
                echo '<li><label><input type="checkbox" name="post_meta_' . $field . '[]" value="' . $f . '" ' . $selected . ' class="' . $args["classes"] . '"/> ' . $v . "</label></li>";
            }
            echo "</ul>";
        } elseif ($type == "radio") {
            echo '<ul id="post_meta_' . $field . '" class="' . $args["classes"] . '" >';
            foreach ($vals as $f => $v) {
                if (is_array($value)) {
                    if (in_array($f, $value)) {
                        $selected = 'checked="checked"';
                    } else {
                        $selected = "";
                    }
                } else {
                    if ($f == $value) {
                        $selected = 'checked="checked"';
                    } else {
                        $selected = "";
                    }
                }
                echo '<li><label><input type="radio" name="post_meta_' . $field . '" value="' . $f . '" ' . $selected . ' class="' . $args["classes"] . '"/> ' . $v . "</label></li>";
            }
            echo "</ul>";
        } elseif ($type == "select") {
            echo '<select name="post_meta_' . $field . '" id="post_meta_' . $field . '" class="' . $args["classes"] . '">';
            echo '<option value="0"> -- select -- </option>';
            foreach ($vals as $f => $v) {
                if (is_array($value)) {
                    if (in_array($f, $value)) {
                        $selected = 'selected="selected"';
                    } else {
                        $selected = "";
                    }
                } else {
                    if ($f == $value) {
                        $selected = 'selected="selected"';
                    } else {
                        $selected = "";
                    }
                }
                echo '<option value="' . $f . '" ' . $selected . '>' . $v . "</option>";
            }
            echo "</select>";
        } elseif ($type == "file") {
            echo '<input type="file" name="post_file_' . $field . '" id="post_file_' . $field . '" value="' . $value . '" class="' . $args["classes"] . '" />';
            echo "<div>Current file: ";
            echo '<a href="' . wp_get_attachment_url($value) . '" target="_blank">' . get_the_title($value) . "</a>";
            echo "</div>";
        } else {
            echo '<input type="text" name="post_meta_' . $field . '" id="post_meta_' . $field . '" value="' . $value . '" class="' . $args["classes"] . '" />';
        }
    }
    if ($name == "title") {
        echo '<input type="text" name="post_core_' . $name . '" id="post_core_' . $name . '" value="' . get_the_title($pid) . '" class="' . $args["classes"] . '" />';
    }
    if ($name == "excerpt") {
        echo '<textarea name="post_core_' . $name . '" id="post_core_' . $name . '" class="' . $args["classes"] . '">' . get_the_excerpt($pid) . '</textarea>';
    }
    if ($name == "price") {
        $value = get_post_meta($pid, "_price", true);
        echo '<input type="text" name="post_meta_' . $name . '" id="post_meta_' . $name . '" value="' . $value . '" class="' . $args["classes"] . '" />';
    }
    if ($name == "stock") {
        $value = get_post_meta($pid, "_stock", true);
        echo '<input type="text" name="post_meta_' . $name . '" id="post_meta_' . $name . '" value="' . $value . '" class="' . $args["classes"] . '" />';
    }
    if ($name == "content") {
        $page_data = get_page($pid);  //gets all page data
        $content = apply_filters('the_content', $page_data->post_content);
        echo '<textarea name="post_core_content" id="post_core_content" class="' . $args["classes"] . '">' . $content . '</textarea>';
    }
    if ($name == "featured_image") {
        $av = get_post_thumbnail_id($pid);
        echo '<div style="float:left; width:10%; margin-left:0;" class="img_max_width">';
        if (wp_get_attachment_image($av)) {
            echo wp_get_attachment_image($av);
        } else {
            echo "<img alt='image_alt' style='width:100%; height:auto;' src='" . get_bloginfo("template_url") . "/assets/img/defaults/no_user.png' class='avatar avatar-{$size} photo avatar-default' height='{$size}' width='{$size}' />";
        }
        echo '</div><div style="float:right; width:85%;">';
        echo '<label style="text-align:left; width:100%; padding-top:0;">Update the image:</label><br><input type="file" name="post_core_image" id="post_core_image" />';
        echo '</div>';
    }
    if ($name == "image_gallery") {
        $images2 = get_children('post_type=attachment&post_mime_type=image&output=ARRAY_N&orderby=menu_order&order=ASC&post_parent=' . $pid);
//print_r($images2);
        if ($images2) {
            $gal = '<ul class="gallery_list row" style="list-style:none; padding:0; margin:0; margin-bottom:20px; margin-left:-10px;">';
            $av = get_post_thumbnail_id($pid);
            if ($av) {
                $src = wp_get_attachment_image_src($av, "thumbnail");
                $gal .= '<li class="col-lg-2 no-padding img_max_width" style="margin:10px;"><img src="' . $src[0] . '?rand=' . rand(1000, 9999) . '" /><div style="position:absolute; bottom:0; background:#FFF; width:100%; padding:3px 5px; opacity:0.95;"><a href="' . get_bloginfo("url") . '/edit-profile/edit-image/?img_id=' . $av . '" class="pull-left" style="display:block; background:#FFF; padding:3px;" >edit</a><a href="' . get_bloginfo("url") . '/?ajax_action=delete_img&img_id=' . $av . '" class="del_img_link pull-right" style="display:block; background:#FFF; padding:3px; text-align:right;">delete</a></div></li>';
            }
            foreach ($images2 as $image) {
                if (get_post_thumbnail_id($pid) != $image->ID) {
                    $src = wp_get_attachment_image_src($image->ID, "thumbnail");
                    $gal .= '<li class="col-lg-2 no-padding img_max_width" style="margin:10px;"><img src="' . $src[0] . '?rand=' . rand(1000, 9999) . '" /><div style="position:absolute; bottom:0; background:#FFF; width:100%; padding:3px 5px; opacity:0.95;"><a href="' . get_bloginfo("url") . '/edit-profile/edit-image/?img_id=' . $image->ID . '" class="pull-left" style="display:block; background:#FFF; padding:3px;" >edit</a><a href="' . get_bloginfo("url") . '/?ajax_action=delete_img&img_id=' . $image->ID . '" class="del_img_link pull-right" style="display:block; background:#FFF; padding:3px; text-align:right;">delete</a></div></li>';
                }
            }
            $gal .= "</ul>";
            ?>
            <script>$(document).ready(function () {
                    $('.del_img_link').click(function () {
                        var lnk = $(this).attr('href');
                        var txt;
                        var r = confirm("Are you sure you want to delete?");
                        if (r == true) {
                            $.ajax({url: lnk});
                            $(this).parent().parent().remove();
                        } else {
                        }
                        return false;
                    })
                });</script>
            <?php
        }
        echo $gal;
        $sel = '[data_file="+file.name+"]';
        ?>
        <script>
            $(document).ready(function () {
                Dropzone.options.myAwesomeDropzone = {
                    url: "<?php echo get_bloginfo("url"); ?>/?ajax_action=upload_image",
                    previewsContainer: ".dropzone-previews",
                    uploadMultiple: true,
                    parallelUploads: 1,
                    maxFiles: 100,
                    addRemoveLinks: true,
                    init: function () {
                        this.on("success", function (file, response) {
                            $("#attachment_inputs_post").append(response);
                        });
                        this.on("removedfile", function (file, response) {
                            var rem = $('#attachment_inputs_post input[data_file="' + file.name + '"]').attr("value");
                            $('input[data_file="' + file.name + '"]').remove();
                            $.ajax({
                                url: '<?php bloginfo("url"); ?>/?ajax_action=delete_img&img_id=' + rem,
                                context: document.body
                            })
                        });
                    }
                }
            })
        </script>
        <div class="dropzone-previews dropzone" id="my-awesome-dropzone" style="clear:both; overflow:auto; margin-bottom:20px;"></div>
        <div id="attachment_inputs_post" class="hidden"></div>
        <?php
    }
}

function gamma_register_post_field($name, $label, $type = "", $values = "") {
    if (!$type) {
        $type = "text";
    }
    if (!is_array($values)) {
        $values = explode("|", $values);
    }
    foreach ($values as $f => $v) {
        if (is_int($f)) {
            $vals[$f] = $v;
        } else {
            $vals[$f] = $v;
        }
    }
    $options = array(
        "label" => $label,
        "type" => $type,
        "values" => $vals
    );
    update_option('gamma_custom_post_' . $name, json_encode($options));
}

function gamma_get_add_comment_field($name, $type = "", $args = "") {
    if ($name == "author" || $name == "author_email" || $name == "author_url") {
        echo '<input type="text" name="comment_core_' . $name . '" id="comment_core_' . $name . '" class="' . $args["classes"] . '" />';
    } elseif ($name == "pid") {
        echo '<input type="hidden" name="comment_core_' . $name . '" id="comment_core_' . $name . '" class="' . $args["classes"] . '" value="' . $args["pid"] . '" />';
    } elseif ($name == "content") {
        echo '<textarea name="comment_core_' . $name . '" id="comment_core_' . $name . '" class="' . $args["classes"] . '"></textarea>';
    } elseif ($name == "image") {
        echo '<div class="' . $args["classes"] . '">' . $args["label"] . ' <input type="file" name="core_featured_image" id="core_featured_image" /></div>';
    } elseif (substr($name, 0, 5) == "meta_") {
        $field = substr($name, 5);
        $info = get_option('gamma_custom_comment_' . $field);
        $info = json_decode($info);
        if (!$type) {
            $type = $info->type;
        }
        $vals = $info->values;
        if ($type == "textarea") {
            echo '<textarea name="comment_meta_' . $field . '" id="comment_meta_' . $field . '" class="' . $args["classes"] . '">' . $value . '</textarea>';
        } elseif ($type == "checkbox") {
            echo '<ul id="meta_' . $field . '">';
            foreach ($vals as $f => $v) {
                echo '<li><span><input type="checkbox" name="comment_meta_' . $field . '[]" value="' . $f . '" class="' . $args["classes"] . '" /></span> ' . $v . "</li>";
            }
            echo "</ul>";
        } elseif ($type == "radio") {
            echo '<ul id="tax_' . $field . '">';
            foreach ($vals as $f => $v) {
                echo '<li><span><input type="radio" name="comment_meta_' . $field . '" value="' . $f . '" class="' . $args["classes"] . '" /></span> ' . $v . "</li>";
            }
            echo "</ul>";
        } elseif ($type == "select") {
            echo '<select name="comment_meta_' . $field . '" id="comment_meta_' . $field . '" class="' . $args["classes"] . '">';
            echo '<option value="0"> -- select -- </option>';
            foreach ($vals as $f => $v) {
                echo '<option value="' . $f . '">' . $v . "</option>";
            }
            echo "</select>";
        } else {
            echo '<input type="text" name="comment_meta_' . $field . '" id="comment_meta_' . $field . '" value="' . $value . '" class="' . $args["classes"] . '" />';
        }
    }
}

function gamma_get_update_comment_field($name, $cid, $type = "", $args = "") {
    $comment = get_comment($cid);
    if ($name == "author") {
        echo '<input type="text" name="comment_core_' . $name . '" id="comment_core_' . $name . '" value="' . $comment->comment_author . '" class="' . $args["classes"] . '" />';
    } elseif ($name == "author_email") {
        echo '<input type="text" name="comment_core_' . $name . '" id="comment_core_' . $name . '" value="' . $comment->comment_author_email . '" class="' . $args["classes"] . '" />';
    } elseif ($name == "author_url") {
        echo '<input type="text" name="comment_core_' . $name . '" id="comment_core_' . $name . '" value="' . $comment->comment_author_url . '" class="' . $args["classes"] . '" />';
    } elseif ($name == "content") {
        echo '<textarea name="comment_core_' . $name . '" id="comment_core_' . $name . '" class="' . $args["classes"] . '">' . $comment->comment_content . '</textarea>';
    } elseif ($name == "cid") {
        echo '<input type="hidden" name="comment_core_' . $name . '" id="comment_core_' . $name . '" class="' . $args["classes"] . '" value="' . $args["cid"] . '" />';
    } elseif ($name == "image") {
        echo '<div class="' . $args["classes"] . '">' . $args["label"] . ' <input type="file" name="core_featured_image" id="core_featured_image" /></div>';
    } else {
        $field = $name;
        $info = get_option('gamma_custom_comment_' . $field);
        $info = json_decode($info);
        if (!$type) {
            $type = $info->type;
        }
        $vals = $info->values;
        $value = get_comment_meta($cid, $field, true);
        if (count($value1 = explode("|", $value)) > 1) {
            $value = explode("|", $value);
        }
        if ($type == "textarea") {
            echo '<textarea name="comment_meta_' . $field . '" id="comment_meta_' . $field . '" class="' . $args["classes"] . '">' . $value . '</textarea>';
        } elseif ($type == "checkbox") {
            echo '<ul id="meta_' . $field . '">';
            foreach ($vals as $f => $v) {
                if (is_array($value)) {
                    if (in_array($v, $value)) {
                        $selected = 'checked="checked"';
                    } else {
                        $selected = "";
                    }
                } else {
                    if ($v == $value) {
                        $selected = 'checked="checked"';
                    } else {
                        $selected = "";
                    }
                }
                echo '<li><span><input type="checkbox" name="comment_meta_' . $field . '[]" value="' . $f . '" class="' . $args["classes"] . '" ' . $selected . ' /></span> ' . $v . "</li>";
            }
            echo "</ul>";
        } elseif ($type == "radio") {
            echo '<ul id="tax_' . $field . '">';
            foreach ($vals as $f => $v) {
                if (is_array($value)) {
                    if (in_array($v, $value)) {
                        $selected = 'checked="checked"';
                    } else {
                        $selected = "";
                    }
                } else {
                    if ($v == $value) {
                        $selected = 'checked="checked"';
                    } else {
                        $selected = "";
                    }
                }
                echo '<li><span><input type="radio" name="comment_meta_' . $field . '" value="' . $f . '" class="' . $args["classes"] . '" ' . $selected . ' /></span> ' . $v . "</li>";
            }
            echo "</ul>";
        } elseif ($type == "select") {
            echo '<select name="comment_meta_' . $field . '" id="comment_meta_' . $field . '" class="' . $args["classes"] . '">';
            echo '<option value="0"> -- select -- </option>';
            foreach ($vals as $f => $v) {
                if (is_array($value)) {
                    if (in_array($v, $value)) {
                        $selected = 'selected="selected"';
                    } else {
                        $selected = "";
                    }
                } else {
                    if ($v == $value) {
                        $selected = 'selected="selected"';
                    } else {
                        $selected = "";
                    }
                }
                echo '<option value="' . $f . '" ' . $selected . '>' . $v . "</option>";
            }
            echo "</select>";
        } else {
            echo '<input type="text" name="comment_meta_' . $field . '" id="comment_meta_' . $field . '" value="' . $value . '" class="' . $args["classes"] . '" />';
        }
    }
}

function gamma_register_comment_field($name, $label, $type, $values) {
    if (!$type) {
        $type = "text";
    }
    if (!is_array($values)) {
        $values = explode("|", $values);
    }
    foreach ($values as $f => $v) {
        if (is_int($f)) {
            $vals[$f + 1] = $v;
        } else {
            $vals[$f] = $v;
        }
    }
    $options = array(
        "label" => $label,
        "type" => $type,
        "values" => $vals
    );
    update_option('gamma_custom_comment_' . $name, json_encode($options));
}

function gamma_get_posts($args = array()) {
    /*
      array(
      post_type: array("product","post"),
      page: 1,
      per_page: 10,
      hide_author_info: 1,
      post_template: array(
      product: product_item.php
      post: post_item.php
      )
      no_results_html: '<p>No results</p>'
      no_results_file: 'no_results.php'
      search: array(
      pid: 15
      pid: array(15,16,17)
      tax:
      tax_slug_category: array(aaa,bbb,ccc)
      tax_category: array(1,2,3)
      meta_price: 15
      meta_price: array(1,2,3)
      meta_price: array(
      compare: >
      value: 5
      )
      key: "aaa"
      date: mm/dd/yyyy
      date_start: mm/dd/yyyy
      date_end: mm/dd/yyyy
      author_name: user_nicename
      author: array(1,2,3)
      author_not: array(1,2,3)
      author_role: array(subscriber,author)
      )
      order: title_asc/title_desc/date_asc/date_desc/comments_asc/comments_desc/rand/meta_asc_FIELD/meta_desc_FIELD/custom_asc_FUNCTION/custom_desc_FUNCTION
      )
     */
    $output = "";
    if (!$args["page"]) {
        $args["page"] = 1;
        $page_no = 0;
    } else {
        $page_no = $args["page"];
    }
    if (!$args["per_page"]) {
        $args["per_page"] = 10;
        $per_page = 10;
    } else {
        $per_page = $args["per_page"];
    }
    $query_args = array(
        'post_type' => $args["post_type"],
        'posts_per_page' => $per_page,
        'paged' => $page_no + 1
    );
    if ($args["order"] == "title_asc") {
        $query_args['orderby'] = 'title';
        $query_args['order'] = 'ASC';
    } elseif ($args["order"] == "title_desc") {
        $query_args['orderby'] = 'title';
        $query_args['order'] = 'DESC';
    } elseif ($args["order"] == "date_asc") {
        $query_args['orderby'] = 'date';
        $query_args['order'] = 'ASC';
    } elseif ($args["order"] == "date_desc") {
        $query_args['orderby'] = 'date';
        $query_args['order'] = 'DESC';
    } elseif ($args["order"] == "rand") {
        $query_args['orderby'] = 'rand';
    } elseif ($args["order"] == "comments_asc") {
        $query_args['orderby'] = 'comment_count';
        $query_args['order'] = 'ASC';
    } elseif ($args["order"] == "comments_desc") {
        $query_args['orderby'] = 'comment_count';
        $query_args['order'] = 'DESC';
    } elseif (substr($args["order"], 0, 9) == "meta_asc_") {
        $query_args['orderby'] = 'meta_value';
        $query_args['meta_key'] = substr($args["order"], 9);
        $query_args['order'] = 'ASC';
    } elseif (substr($args["order"], 0, 10) == "meta_desc_") {
        $query_args['orderby'] = 'meta_value';
        $query_args['meta_key'] = substr($args["order"], 10);
        $query_args['order'] = 'DESC';
    } elseif (substr($args["order"], 0, 11) == "custom_asc_") {

    } elseif (substr($args["order"], 0, 12) == "custom_desc_") {

    }
//print_r($args["search"]);
    foreach ($args["search"] as $f => $v) {
        if ($v || (is_array($v) && count($v))) {
            if (substr($f, 0, 9) == "tax_slug_") {
                $tax = substr($f, 9);
                if (!is_array($v)) {
                    $v = array($v);
                }
                $query_args["tax_query"][] = array(
                    'taxonomy' => $tax,
                    'field' => 'slug',
                    'terms' => $v,
                    'operator' => "IN"
                );
            } elseif (substr($f, 0, 4) == "tax_") {
                $tax = substr($f, 4);
                if (!is_array($v)) {
                    $v = array($v);
                }
                $query_args["tax_query"][] = array(
                    'taxonomy' => $tax,
                    'field' => 'id',
                    'terms' => $v,
                    'compare' => "IN"
                );
            } elseif (substr($f, 0, 5) == "meta_") {
                if ($v || count($v)) {
                    $field = substr($f, 5);
                    if (!is_array($v)) {
                        if (strpos($field, "_less")) {
                            if ($v) {
                                $field = str_replace("_less", "", $field);
                                $query_args["meta_query"][] = array(
                                    'key' => $field,
                                    'value' => $v,
                                    'type' => 'numeric',
                                    'compare' => "<="
                                );
                            }
                        } elseif (strpos($field, "_more")) {
                            if ($v) {
                                $field = str_replace("_more", "", $field);
                                $query_args["meta_query"][] = array(
                                    'key' => $field,
                                    'value' => $v,
                                    'type' => 'numeric',
                                    'compare' => ">="
                                );
                            }
                        } else {
                            if ($v) {
                                $query_args["meta_query"][] = array(
                                    'key' => $field,
                                    'value' => $v,
                                );
                            }
                        }
                    } else {
                        if (isset($v["compare"])) {
                            if ($v["value"]) {
                                $query_args["meta_query"][] = array(
                                    'key' => $field,
                                    'value' => $v["value"],
                                    'compare' => $v["compare"]
                                );
                            }
                        } else {
                            if ($v) {
                                $query_args["meta_query"][] = array(
                                    'key' => $field,
                                    'value' => $v,
                                    'compare' => "IN"
                                );
                            }
                        }
                    }
                }
            } elseif ($f == "key") {
                $query_args["s"] = $v;
            } elseif ($f == "date") {
                $query_args["date_query"][] = array(
                    'year' => intval(substr($v, 6, 4)),
                    'month' => intval(substr($v, 0, 2)),
                    'day' => intval(substr($v, 3, 2)),
                );
            } elseif ($f == "date_start") {
                $query_args["date_query"] = array(
                    'after' => $v,
                    'inclusive' => true,
                );
            } elseif ($f == "date_end") {
                $query_args["date_query"] = array(
                    'before' => $v,
                    'inclusive' => true,
                );
            } elseif ($f == "author_name") {
                $query_args["author_name"] = $v;
            } elseif ($f == "exclude") {
                $query_args["post__not_in"] = $v;
            } elseif ($f == "pid") {
                if (is_array($v)) {
                    $query_args["post__in"] = $v;
                } else {
                    $query_args["p"] = $v;
                }
                $query_args["post_status"] = "any";
            } elseif ($f == "post_status") {
                $query_args["post_status"] = $v;
            } elseif ($f == "author") {
                $query_args["author__in"] = $v;
            } elseif ($f == "author_not") {
                $query_args["author__not_in"] = $v;
            } elseif ($f == "post_status") {
                $query_args["post_status"] = $v;
            } elseif ($f == "author_role") {
                $user_list = array();
                foreach ($v as $role) {
                    $user_query = new WP_User_Query(array('role' => $role));
                    $authors = $author_query->get_results();
                    foreach ($authors as $author) {
                        $user_list[] = $author->ID;
                    }
                }
                $query_args["author__in"] = $user_list;
            } else {

            }
        }
    }
    $items = array();
//    print_r($args=array());
//    print_r($query_args);
    $my_query = new wp_query($query_args);
//    print_r($my_query);
    if ($my_query->have_posts()) {
        while ($my_query->have_posts()) {
            $post = $my_query->the_post();
            $item = array();
            $post_type = get_post_type();
            $pid = get_the_ID();
            $item["post_id"] = get_the_ID();
            $item["post_title"] = get_the_title();
            $item["post_permalink"] = get_permalink();
            $item["post_content"] = get_the_content();
            $item["post_excerpt"] = get_the_excerpt();
            $item["post_type"] = $post_type;
            $item["post_date"] = get_the_time('U');
            $item["author_id"] = get_the_author_meta("id");
            $item["price"] = get_post_meta($pid, "_price", true);
            $item["stock"] = get_post_meta($pid, "_stock", true);
            $item["sku"] = get_post_meta($pid, "_sku", true);
            foreach (get_post_meta($pid) AS $f => $v) {
                $options = json_decode(get_option('gamma_custom_post_' . $f));
                $vls1 = $options->values;
                $vls = array();
                foreach ($vls1 as $fv => $vv) {
                    $vls[$fv] = $vv;
                }
                if (count($v) > 1) {
                    $vl_list = array();
                    foreach ($v as $vs) {
                        if ($vls[$vs]) {
                            $vl_list[] = $vls[$vs];
                        } else {
                            $vl_list[] = $vs;
                        }
                    }
                    $item["meta_" . $f] = implode(", ", $vl_list);
                    $item["meta_array_" . $f] = $vl_list;
                } else {
                    if ($vls[($v[0])]) {
                        $item["meta_" . $f] = $vls[($v[0])];
                    } else {
                        $item["meta_" . $f] = $v[0];
                    }
                }
            }
            if ($post_type == "product") {
                $item["buy_now_button"] = '<a href="' . get_bloginfo("siteurl") . '?add-to-cart=' . $pid . '" rel="nofollow" data-product_id="' . $pid . '" data-product_sku="' . $item["sku"] . '" class="add_to_cart_button button product_type_simple">Add to cart</a>';
                $item["buy_now_link"] = '<a href="' . get_bloginfo("siteurl") . '?add-to-cart=' . $pid . '">Add to cart</a>';
            }
            $author_id = $post->post_author;
            if (!$args["hide_author_info"]) {
                $info = array("id", "user_login", "user_pass", "user_nicename", "user_email", "user_url", "user_registered", "user_activation_key", "user_status", "display_name", "nickname");
                foreach ($info as $field) {
                    $item["author_" . $field] = get_the_author_meta($field);
                }
                $author_meta = get_user_meta($item["author_id"]);
                foreach ($author_meta as $f => $v) {
                    if (count($v) > 1) {
                        $item["author_" . $f] = $v;
                    } else {
                        $item["author_" . $f] = $v[0];
                    }
                }
                if ($avatar = $author_meta["avatar_id"]) {
                    $item["author_avatar_thumbnail"] = wp_get_attachment_image($avatar, 'thumbnail');
                    $item["author_avatar_medium"] = wp_get_attachment_image($avatar, 'medium');
                    $item["author_avatar_large"] = wp_get_attachment_image($avatar, 'large');
                    $item["author_avatar_full"] = wp_get_attachment_image($avatar, 'full');
                }
                $item["author_name"] = $item["author_user_nicename"];
                $item["author_link"] = get_author_posts_url($author_id);
            }
            $post_meta = get_post_meta($pid);
            foreach ($post_meta as $field => $v) {
                $item["post_" . $field] = $v;
            }
            $sizes = array("thumbnail", "medium", "large", "full");
            foreach ($sizes as $size) {
                $item["featured_img_info_" . $size] = wp_get_attachment_image_src(get_post_thumbnail_id($pid), $size);
                $item["featured_img_width_" . $size] = $item["featured_img_info_" . $size][1];
                $item["featured_img_height_" . $size] = $item["featured_img_info_" . $size][1];
                $item["featured_img_src_" . $size] = $item["featured_img_info_" . $size][0];
                $item["featured_img_" . $size] = get_the_post_thumbnail($pid, $size);
            }
            $images2 = get_children('post_type=attachment&post_mime_type=image&output=ARRAY_N&orderby=menu_order&order=ASC&post_parent=' . $pid);
            if ($images2) {
                $item["gallery_thumbnail"] = "<ul>";
                $item["gallery_medium"] = "<ul>";
                $item["gallery_large"] = "<ul>";
                $item["gallery_full"] = "<ul>";
                $item["gallery_except_thumbnail"] = "<ul>";
                $item["gallery_except_medium"] = "<ul>";
                $item["gallery_except_large"] = "<ul>";
                $item["gallery_except_full"] = "<ul>";
                if ($imid = get_post_thumbnail_id($pid)) {
                    $img = array();
                    foreach ($sizes as $size) {
                        $full_link = wp_get_attachment_image_src($imid, "full");
                        $img["img_info_" . $size] = wp_get_attachment_image_src($imid, $size);
                        $img["img_src_" . $size] = $item["featured_img_info_" . $size][0];
                        $img["img_" . $size] = '<a href="' . $full_link[0] . '">' . wp_get_attachment_image($imid, $size, false) . '</a>';
                        $item["gallery_" . $size] .= '<li><a rel="gallery_' . $pid . '" href="' . $full_link[0] . '">' . wp_get_attachment_image($imid, $size, false) . '</a></li>';
                    }
                    $item["gallery_list"][] = $img;
                }
                foreach ($images2 as $image) {
                    if (get_post_thumbnail_id($pid) != $image->ID) {
                        $img = array();
                        foreach ($sizes as $size) {
                            $full_link = wp_get_attachment_image_src($image->ID, "full");
                            $img["img_info_" . $size] = wp_get_attachment_image_src($image->ID, $size);
                            $img["img_src_" . $size] = $item["featured_img_info_" . $size][0];
                            $img["img_" . $size] = '<a href="' . $full_link[0] . '">' . wp_get_attachment_image($image->ID, $size, false) . '</a>';
                            $item["gallery_" . $size] .= '<li><a rel="gallery_' . $pid . '" href="' . $full_link[0] . '">' . wp_get_attachment_image($image->ID, $size, false) . '</a></li>';
                            $item["gallery_except_" . $size] .= '<li><a rel="gallery_' . $pid . '" href="' . $full_link[0] . '">' . wp_get_attachment_image($image->ID, $size, false) . '</a></li>';
                        }
                        $item["gallery_list"][] = $img;
                    }
                }
                $item["gallery_thumbnail"] .= "</ul>";
                $item["gallery_medium"] .= "</ul>";
                $item["gallery_large"] .= "</ul>";
                $item["gallery_full"] .= "</ul>";
                $item["gallery_except_thumbnail"] .= "</ul>";
                $item["gallery_except_medium"] .= "</ul>";
                $item["gallery_except_large"] .= "</ul>";
                $item["gallery_except_full"] .= "</ul>";
            }
            $taxonomies = get_object_taxonomies($post_type, "objects");
            foreach ($taxonomies as $tax => $v) {
                $terms = get_the_terms($pid, $tax);
                if ($terms) {
                    $terms_links_ul = "<ul>";
                    $terms_string_ul = "<ul>";
                    $terms_list = array();
                    $terms_links = array();
                    foreach ($terms as $term) {
                        $terms_list[] = $term->name;
                        $terms_links[] = '<a href="' . get_term_link($term, $tax) . '">' . $term->name . '</a>';
                        $terms_links_ul .= '<li><a href="' . get_term_link($term, $tax) . '">' . $term->name . '</a></li>';
                        $terms_string_ul .= '<li>' . $term->name . '</li>';
                    }
                    $terms_links_ul .= "</ul>";
                    $terms_string_ul .= "</ul>";
                    $item["tax_string_" . $tax] = implode(", ", $terms_list);
                    $item["tax_list_" . $tax] = $terms_string_ul;
                    $item["tax_links_string_" . $tax] = implode(", ", $terms_links);
                    $item["tax_links_list_" . $tax] = $terms_links_ul;
                    $item["tax_array_" . $tax] = $terms;
                } else {
                    $item["tax_string_" . $tax] = "";
                    $item["tax_list_" . $tax] = "";
                    $item["tax_links_string_" . $tax] = array();
                    $item["tax_links_list_" . $tax] = "";
                    $item["tax_array_" . $tax] = array();
                }
            }
            if ($args["comment_template"]) {
                $item["comments"] = gamma_get_comments(array("comment_template" => $args["comment_template"], "hide_post_info" => 1, "search" => array("post_id" => array($pid))));
            } else {
                $item["comments"] = gamma_get_comments(array("hide_post_info" => 1, "search" => array("post_id" => array($pid))));
            }
            $ratingsum = 0;
            $ratingsum1 = 0;
            $ratingsum2 = 0;
            $ratingsum3 = 0;
            $ratingsum4 = 0;
            $ratingsum5 = 0;
            $ratingno = 0;
            foreach ($item["comments"]["items"] as $item1) {
                if ($item1["meta_rating"]) {
                    $ratingsum += $item1["meta_rating"];
                    $ratingsum1 += $item1["meta_rating1"];
                    $ratingsum2 += $item1["meta_rating2"];
                    $ratingsum3 += $item1["meta_rating3"];
                    $ratingsum4 += $item1["meta_rating4"];
                    $ratingsum5 += $item1["meta_rating5"];
                    $ratingno++;
                }
            }
            $item["rating_score"] = round($ratingsum / $ratingno, 2);
            $item["rating_stars"] = '<div class="outer_stars"><div class="inner_stars" style="width:' . round(round($ratingsum / $ratingno, 2) * 20) . '%">&nbsp;</div></div>';
            $item["rating_score1"] = round($ratingsum1 / $ratingno, 2);
            $item["rating_stars1"] = '<div class="outer_stars"><div class="inner_stars" style="width:' . round(round($ratingsum1 / $ratingno, 2) * 20) . '%">&nbsp;</div></div>';
            $item["rating_score2"] = round($ratingsum2 / $ratingno, 2);
            $item["rating_stars2"] = '<div class="outer_stars"><div class="inner_stars" style="width:' . round(round($ratingsum2 / $ratingno, 2) * 20) . '%">&nbsp;</div></div>';
            $item["rating_score3"] = round($ratingsum3 / $ratingno, 2);
            $item["rating_stars3"] = '<div class="outer_stars"><div class="inner_stars" style="width:' . round(round($ratingsum3 / $ratingno, 2) * 20) . '%">&nbsp;</div></div>';
            $item["rating_score4"] = round($ratingsum4 / $ratingno, 2);
            $item["rating_stars4"] = '<div class="outer_stars"><div class="inner_stars" style="width:' . round(round($ratingsum4 / $ratingno, 2) * 20) . '%">&nbsp;</div></div>';
            $item["rating_score5"] = round($ratingsum5 / $ratingno, 2);
            $item["rating_stars5"] = '<div class="outer_stars"><div class="inner_stars" style="width:' . round(round($ratingsum5 / $ratingno, 2) * 20) . '%">&nbsp;</div></div>';
            $item["rating_count"] = $ratingno;
            $item["date"] = get_the_time('U');
            if (is_array($args["post_template"])) {
                ob_start();
                include($args["post_template"][$post_type]);
                $output .= ob_get_contents();
                ob_end_clean();
            } elseif ($args["post_template"]) {
                ob_start();
                include($args["post_template"]);
                $output .= ob_get_contents();
                ob_end_clean();
            }
            $items[] = $item;
            ;
        }
    } else {
        if ($args["post_template"]) {
            if ($args["no_results_html"]) {
                $output .= $args["no_results_html"];
            } elseif ($args["no_results_file"]) {
                ob_start();
                include($args["no_results_file"]);
                $output .= ob_get_contents();
                ob_end_clean();
            }
        }
    }
    wp_reset_query();
    return array(
        "output" => $output,
        "total_posts" => $my_query->found_posts,
        "page_no" => $page_no,
        "per_page" => $per_page,
        "items" => $items
    );
}

function gamma_get_users($args = array()) {
    /*
      array(
      user_roles: array("administrator","subscriber"),
      page: 1,
      per_page: 10,
      user_template: array(
      administrator: administrator_item.php
      subscriber: subscriber_item.php
      )
      no_results_html: '<p>No results</p>'
      no_results_file: 'no_results.php'
      search: array(
      uid: 15
      uid: array(15,16,17)
      meta_price: 15
      meta_price: array(1,2,3)
      meta_price: array(
      compare: >
      value: 5
      )
      key: "aaa"
      date: mm/dd/yyyy
      date_start: mm/dd/yyyy
      date_end: mm/dd/yyyy
      )
      posts: posts_array()
      order: name_asc/name_desc/id_asc/id_desc/date_asc/date_desc/post_count_asc/post_count_desc/meta_asc_FIELD/meta_desc_FIELD/custom_asc_FUNCTION/custom_desc_FUNCTION
      )
     */
    $query_args = array();
    if ($args["order"] == "name_asc") {
        $query_args['orderby'] = 'title';
        $query_args['order'] = 'ASC';
    } elseif ($args["order"] == "name_desc") {
        $query_args['orderby'] = 'title';
        $query_args['order'] = 'DESC';
    } elseif ($args["order"] == "id_asc") {
        $query_args['orderby'] = 'ID';
        $query_args['order'] = 'ASC';
    } elseif ($args["order"] == "id_desc") {
        $query_args['orderby'] = 'ID';
        $query_args['order'] = 'DESC';
    } elseif ($args["order"] == "date_asc") {
        $query_args['orderby'] = 'registered';
        $query_args['order'] = 'ASC';
    } elseif ($args["order"] == "date_desc") {
        $query_args['orderby'] = 'registered';
        $query_args['order'] = 'DESC';
    } elseif ($args["order"] == "post_count_asc") {
        $query_args['orderby'] = 'post_count';
        $query_args['order'] = 'ASC';
    } elseif ($args["order"] == "post_count_desc") {
        $query_args['orderby'] = 'post_count';
        $query_args['order'] = 'DESC';
    } elseif (substr($args["order"], 0, 9) == "meta_asc_") {
        $query_args['orderby'] = 'meta_value';
        $query_args['meta_key'] = substr($args["order"], 9);
        $query_args['order'] = 'ASC';
    } elseif (substr($args["order"], 0, 10) == "meta_desc_") {
        $query_args['orderby'] = 'meta_value';
        $query_args['meta_key'] = substr($args["order"], 10);
        $query_args['order'] = 'DESC';
    } elseif (substr($args["order"], 0, 11) == "custom_asc_") {

    } elseif (substr($args["order"], 0, 12) == "custom_desc_") {

    }
    if ($date = $args["search"]["date"]) {
        $datestart_time = mktime(0, 0, 0, intval(substr($date, 0, 2)), intval(substr($date, 3, 2)), intval(substr($date, 6, 4)));
        $dateend_time = mktime(23, 59, 0, intval(substr($date, 0, 2)), intval(substr($date, 3, 2)), intval(substr($date, 6, 4)));
    }
    if ($date = $args["search"]["date_start"]) {
        $datestart_time = mktime(0, 0, 0, intval(substr($date, 0, 2)), intval(substr($date, 3, 2)), intval(substr($date, 6, 4)));
    }
    if ($date = $args["search"]["date_end"]) {
        $dateend_time = mktime(0, 0, 0, intval(substr($date, 0, 2)), intval(substr($date, 3, 2)), intval(substr($date, 6, 4)));
    }
    if (!$datestart_time) {
        $datestart_time = 0;
    }
    if (!$dateend_time) {
        $dateend_time = time();
    }
    if ($args["per_page"]) {
        $query_args["number"] = $args["per_page"];
    } else {
        $query_args["number"] = 10;
    }
    if ($args["page"]) {
        $query_args["offset"] = $args["page"] * $args["per_page"];
    } else {
        $query_args["offset"] = 0;
    }
    $per_page = $args["per_page"];
    $page_no = $args["page"];
    foreach ($args["search"] as $f => $v) {
        if (substr($f, 0, 5) == "meta_") {
            $field = substr($f, 5);
            $options = json_decode(get_option('gamma_custom_user_' . $field));
            $vls1 = $options->values;
//print_r($vls1);
            $vls = array();
            foreach ($vls1 as $fv => $vv) {
                $vls[$fv] = $vv;
            }
            if (count($v) > 1) {
                $vl_list = array();
                foreach ($v as $vs) {
                    if ($vls[$vs]) {
                        $vl_list[] = $vls[$vs];
                    } else {
                        $vl_list[] = $vs;
                    }
                }
                $item["meta_val_" . $f] = implode(", ", $vl_list);
                $item["meta_val_array_" . $f] = $vl_list;
            } else {
                if ($vls[($v[0])]) {
                    $item["meta_val_" . $f] = $vls[($v[0])];
                } else {
                    $item["meta_val_" . $f] = $v[0];
                }
            }
            if (!is_array($v)) {
                $query_args["meta_query"][] = array(
                    'key' => $field,
                    'value' => $v
                );
            } else {
                if (isset($v["compare"])) {
                    $query_args["meta_query"][] = array(
                        'key' => $field,
                        'value' => $v["value"],
                        'compare' => $v["compare"]
                    );
                } else {
                    $query_args["meta_query"][] = array(
                        'key' => $field,
                        'value' => $v,
                        'compare' => "IN"
                    );
                }
            }
        } elseif ($f == "key") {
            $query_args["search"] = $v;
            $query_args["search_columns"] = array("ID", "login", "nicename", "email");
        } elseif ($f == "author_name") {
            $query_args["author_name"] = $v;
        } elseif ($f == "uid") {
            if (is_array($v)) {
                $query_args["include"] = $v;
            } else {
                $query_args["include"] = array($v);
            }
        } else {

        }
    }
    if ($args["user_roles"]) {
        $user_list = array();
        $exclude_list = array();
        foreach ($args["user_roles"] as $role) {
            $user_query = new WP_User_Query(array('role' => $role));
            $authors = $user_query->get_results();
            foreach ($authors as $author) {
                $reg_time = strtotime($author->user_registered);
                if ($reg_time > $datestart_time && $dateend_time > $reg_time) {
                    $user_list[] = $author->ID;
                } else {
                    $exclude_list[] = $author->ID;
                }
                if (is_array($args["search"]["uid"]) && in_array($author->ID, $args["search"]["uid"]) || !$args["search"]["uid"] || $args["search"]["uid"] == $author->ID) {
                    $user_list[] = $author->ID;
                } else {
                    $exclude_list[] = $author->ID;
                }
            }
        }
        $query_args["include"] = $user_list;
        $query_args["exclude"] = $exclude_list;
    }
    $query_args["include"][] = 99999;
    $items = array();
    $author_query = new WP_User_Query($query_args);
    if ($authors = $author_query->get_results()) {
        foreach ($authors as $author) {
//print_r($author);
            $item = array();
            foreach ($author->data as $f => $v) {
                $item["user_" . $f] = $v;
            }
            foreach ($author->allcaps as $cap => $v) {
                $item["capabilities"][] = $cap;
            }
            $item["roles"] = $author->roles;
            $user_role = $item["roles"][0];
            $author_meta = get_user_meta($author->ID);
            foreach ($author_meta as $f => $v) {
                if (count($v) > 1) {
                    $item["meta_" . $f] = $v;
                } else {
                    $item["meta_" . $f] = $v[0];
                }
            }
            $item["user_name"] = $item["user_nicename"];
            $post_args = $args["posts"];
            $post_args["author"] = $author->ID;
            $post_args["hide_author_info"] = 1;
            $item["posts"] = gamma_get_posts($post_args);
            if ($avatar = $author_meta["avatar_id"]) {
                $item["user_avatar_thumbnail"] = wp_get_attachment_image($avatar, 'thumbnail');
                $item["user_avatar_medium"] = wp_get_attachment_image($avatar, 'medium');
                $item["user_avatar_large"] = wp_get_attachment_image($avatar, 'large');
                $item["user_avatar_full"] = wp_get_attachment_image($avatar, 'full');
            }
            if (is_array($args["user_template"])) {
                ob_start();
                include($args["user_template"][$user_role]);
                $output .= ob_get_contents();
                ob_end_clean();
            } elseif ($args["user_template"]) {
                ob_start();
                include($args["user_template"]);
                $output .= ob_get_contents();
                ob_end_clean();
            }
            $items[] = $item;
            ;
        }
    } else {
        if ($args["post_template"]) {
            if ($args["no_results_html"]) {
                $output .= $args["no_results_html"];
            } elseif ($args["no_results_file"]) {
                ob_start();
                include($args["no_results_file"]);
                $output .= ob_get_contents();
                ob_end_clean();
            }
        }
    }
    return array(
        "output" => $output,
        "total_posts" => $author_query->total_users,
        "page_no" => $page_no,
        "per_page" => $per_page,
        "items" => $items
    );
}

function gamma_get_comments($args = array()) {
    /*
      array(
      page: 1,
      per_page: 10,
      hide_post_info: 1
      comment_template: administrator_item.php
      no_results_html: '<p>No results</p>'
      no_results_file: 'no_results.php'
      search: array(
      cid: 15
      post_type: array(post),
      post_id: 15,
      user_id: 15,
      user_email: office@thegammalab.com
      user_roles: array(administrator,subscriber)
      status: approve
      meta_price: 15
      meta_price: array(1,2,3)
      meta_price: array(
      compare: >
      value: 5
      )
      date: mm/dd/yyyy
      date_start: mm/dd/yyyy
      date_end: mm/dd/yyyy
      )
      posts: posts_array()
      order: name_asc/name_desc/id_asc/id_desc/date_asc/date_desc/post_count_asc/post_count_desc/meta_asc_FIELD/meta_desc_FIELD/custom_asc_FUNCTION/custom_desc_FUNCTION
      )
     */
    $query_args = array();
    $output = "";
    if ($args["order"] == "name_asc") {
        $query_args['orderby'] = 'title';
        $query_args['order'] = 'ASC';
    } elseif ($args["order"] == "name_desc") {
        $query_args['orderby'] = 'title';
        $query_args['order'] = 'DESC';
    } elseif ($args["order"] == "id_asc") {
        $query_args['orderby'] = 'ID';
        $query_args['order'] = 'ASC';
    } elseif ($args["order"] == "id_desc") {
        $query_args['orderby'] = 'ID';
        $query_args['order'] = 'DESC';
    } elseif ($args["order"] == "date_asc") {
        $query_args['orderby'] = '';
        $query_args['order'] = 'ASC';
    } elseif ($args["order"] == "date_desc") {
        $query_args['orderby'] = '';
        $query_args['order'] = 'DESC';
    } elseif (substr($args["order"], 0, 9) == "meta_asc_") {
        $query_args['orderby'] = 'meta_value';
        $query_args['meta_key'] = substr($args["order"], 9);
        $query_args['order'] = 'ASC';
    } elseif (substr($args["order"], 0, 10) == "meta_desc_") {
        $query_args['orderby'] = 'meta_value';
        $query_args['meta_key'] = substr($args["order"], 10);
        $query_args['order'] = 'DESC';
    } elseif (substr($args["order"], 0, 11) == "custom_asc_") {

    } elseif (substr($args["order"], 0, 12) == "custom_desc_") {

    }
    if ($date = $args["search"]["date"]) {
        $datestart_time = mktime(0, 0, 0, intval(substr($date, 0, 2)), intval(substr($date, 3, 2)), intval(substr($date, 6, 4)));
        $dateend_time = mktime(23, 59, 0, intval(substr($date, 0, 2)), intval(substr($date, 3, 2)), intval(substr($date, 6, 4)));
    }
    if ($date = $args["search"]["date_start"]) {
        $datestart_time = mktime(0, 0, 0, intval(substr($date, 0, 2)), intval(substr($date, 3, 2)), intval(substr($date, 6, 4)));
    }
    if ($date = $args["search"]["date_end"]) {
        $dateend_time = mktime(0, 0, 0, intval(substr($date, 0, 2)), intval(substr($date, 3, 2)), intval(substr($date, 6, 4)));
    }
    if (!$datestart_time) {
        $datestart_time = 0;
    }
    if (!$dateend_time) {
        $dateend_time = time();
    }
    if ($args["per_page"]) {
        $per_page = intval($args["per_page"]);
    } else {
        $per_page = 1000;
    }
    if ($args["page"]) {
        $page_no = $args["page"] * $per_page;
    } else {
        $page_no = 0;
    }
    $post_status_array = array();
    $post_id_array = array();
    $user_id_array = array();
    $comment_id_array = array();
    foreach ($args["search"] as $f => $v) {
        if (substr($f, 0, 5) == "meta_") {
            $field = substr($f, 5);
            if (!is_array($v)) {
                $query_args["meta_query"][] = array(
                    'key' => $field,
                    'value' => $v
                );
            } else {
                if (isset($v["compare"])) {
                    $query_args["meta_query"][] = array(
                        'key' => $field,
                        'value' => $v["value"],
                        'compare' => $v["compare"]
                    );
                } else {
                    $query_args["meta_query"][] = array(
                        'key' => $field,
                        'value' => $v,
                        'compare' => "IN"
                    );
                }
            }
        } elseif ($f == "status") {
            if (is_array($v)) {
                $post_status_array = $v;
            } else {
                $post_status_array = array($v);
            }
        } elseif ($f == "post_type") {
            if (is_array($v)) {
                $post_type_array = $v;
            } else {
                $post_type_array = array($v);
            }
        } elseif ($f == "post_id") {
            if (is_array($v)) {
                $post_id_array = $v;
            } else {
                $post_id_array = array($v);
            }
            if (count($post_id_array) == 1) {
                $query_args["post_id"] = $post_id_array[0];
            }
        } elseif ($f == "user_id") {
            if (is_array($v)) {
                $user_id_array = $v;
            } else {
                $user_id_array = array($v);
            }
            if (count($user_id_array) == 1) {
                $query_args["user_id"] = $user_id_array[0];
            }
        } elseif ($f == "user_email") {
            $author_email = $v;
        } elseif ($f == "cid") {
            $comment_id_array = $v;
        } else {

        }
    }
    if ($args["user_roles"]) {
        $user_list = array();
        foreach ($args["user_roles"] as $role) {
            $user_query = new WP_User_Query(array('role' => $role));
            $authors = $user_query->get_results();
            foreach ($authors as $author) {
                if (empty($user_id_array) || in_array($author->ID, $user_id_array)) {
                    $user_list[] = $author->ID;
                }
            }
        }
        $user_id_array = $user_list;
    }
//print_r($query_args);
    $items = array();
    $comments_query = new WP_Comment_Query;
    if ($comments = $comments_query->query($query_args)) {
//print_r($comments);
        foreach ($comments as $comment) {
            $item = array();
            $reg_time = strtotime($comment->comment_date);
            $status = wp_get_comment_status($comment->comment_ID);
            $valid = 1;
            if (!$post_status_array || in_array($status, $post_status_array)) {

            } else {
                $valid = 0;
            }
            if (!$user_id_array || in_array($comment->user_id, $user_id_array)) {

            } else {
                $valid = 0;
            }
            if (!$post_id_array || in_array($comment->comment_post_ID, $post_id_array)) {

            } else {
                $valid = 0;
            }
            if (!$post_type_array || in_array(get_post_type($comment->comment_post_ID), $post_type_array)) {

            } else {
                $valid = 0;
            }
            if (!$comment_id_array || in_array($comment->comment_ID, $comment_id_array)) {

            } else {
                $valid = 0;
            }
//if($reg_time>$datestart_time && $dateend_time>$reg_time){}else{$valid = 0;}
            if ($valid) {
//$items[]=$comment;
                foreach ($comment as $f => $v) {
                    $item[$f] = $v;
                }
                $pid = $comment->comment_post_ID;
                $item["comment_user_id"] = $comment->user_id;
                $item["comment_date"] = strtotime($comment->comment_date);
                $item["comment_status"] = $status;
                $meta = get_comment_meta($comment->comment_ID);
                foreach ($meta as $f => $v) {
                    if (count($v) > 1) {
                        $item["meta_" . $f] = $v;
                    } else {
                        $item["meta_" . $f] = $v[0];
                    }
                }
                $ratingsum = 0;
                $ratingsum1 = 0;
                $ratingsum2 = 0;
                $ratingsum3 = 0;
                $ratingsum4 = 0;
                $ratingsum5 = 0;
                $ratingno = 0;
                foreach ($item["comments"]["items"] as $item1) {
                    if ($item1["meta_rating"]) {
                        $ratingsum += $item1["meta_rating"];
                        $ratingsum1 += $item1["meta_rating1"];
                        $ratingsum2 += $item1["meta_rating2"];
                        $ratingsum3 += $item1["meta_rating3"];
                        $ratingsum4 += $item1["meta_rating4"];
                        $ratingsum5 += $item1["meta_rating5"];
                        $ratingno++;
                    }
                }
                $item["rating_score"] = round($item["meta_rating"], 2);
                $item["rating_stars"] = '<div class="outer_stars"><div class="inner_stars" style="width:' . round($item["meta_rating"] * 20) . '%">&nbsp;</div></div>';
                $item["rating_score1"] = round($item["meta_rating1"], 2);
                $item["rating_stars1"] = '<div class="outer_stars"><div class="inner_stars" style="width:' . round($item["meta_rating1"] * 20) . '%">&nbsp;</div></div>';
                $item["rating_score2"] = round($item["meta_rating2"], 2);
                $item["rating_stars2"] = '<div class="outer_stars"><div class="inner_stars" style="width:' . round($item["meta_rating2"] * 20) . '%">&nbsp;</div></div>';
                $item["rating_score3"] = round($item["meta_rating3"], 2);
                $item["rating_stars3"] = '<div class="outer_stars"><div class="inner_stars" style="width:' . round($item["meta_rating3"] * 20) . '%">&nbsp;</div></div>';
                $item["rating_score4"] = round($item["meta_rating4"], 2);
                $item["rating_stars4"] = '<div class="outer_stars"><div class="inner_stars" style="width:' . round($item["meta_rating4"] * 20) . '%">&nbsp;</div></div>';
                $item["rating_score5"] = round($item["meta_rating5"], 2);
                $item["rating_stars5"] = '<div class="outer_stars"><div class="inner_stars" style="width:' . round($item["meta_rating5"] * 20) . '%">&nbsp;</div></div>';
                if ($comment->user_id) {
                    $info = array("id", "user_login", "user_pass", "user_nicename", "user_email", "user_url", "user_registered", "user_activation_key", "user_status", "display_name", "nickname");
                    foreach ($info as $field) {
                        $item["author_" . $field] = get_the_author_meta($field, $comment->comment_post_ID);
                    }
                    $author_meta = get_user_meta($comment->user_id);
                    foreach ($author_meta as $f => $v) {
                        if (count($v) > 1) {
                            $item["author_" . $f] = $v;
                        } else {
                            $item["author_" . $f] = $v[0];
                        }
                    }
                    $item["author_name"] = $item["author_user_nicename"];
                    $item["author_link"] = get_author_posts_url($author_id);
                    if ($avatar = $author_meta["avatar_id"]) {
                        $item["author_avatar_thumbnail"] = wp_get_attachment_image($avatar, 'thumbnail');
                        $item["author_avatar_medium"] = wp_get_attachment_image($avatar, 'medium');
                        $item["author_avatar_large"] = wp_get_attachment_image($avatar, 'large');
                        $item["author_avatar_full"] = wp_get_attachment_image($avatar, 'full');
                    }
                } else {
                    $item["author_name"] = $comment->comment_author;
                    $item["author_email"] = $comment->comment_author_email;
                    $item["author_link"] = $comment->comment_author_url;
                }
                if (!$args["hide_post_info"]) {
                    $item["post_title"] = get_the_title($pid);
                    $item["post_content"] = get_the_content($pid);
                    $item["post_excerpt"] = get_the_excerpt($pid);
                    $item["post_date"] = get_the_time('U', $pid);
                    $post_type = get_post_type($pid);
                    $author_id = $post->post_author;
                    $post_meta = get_post_meta($pid);
                    foreach ($post_meta as $field => $v) {
                        $item["post_" . $field] = $v;
                    }
                    $sizes = array("thumbnail", "medium", "large", "full");
                    foreach ($sizes as $size) {
                        $item["featured_img_info_" . $size] = wp_get_attachment_image_src(get_post_thumbnail_id($pid), $size);
                        $item["featured_img_width_" . $size] = $item["featured_img_info_" . $size][1];
                        $item["featured_img_height_" . $size] = $item["featured_img_info_" . $size][1];
                        $item["featured_img_src_" . $size] = $item["featured_img_info_" . $size][0];
                        $item["featured_img_" . $size] = get_the_post_thumbnail($pid, $size);
                    }
                    $images2 = get_children('post_type=attachment&post_mime_type=image&output=ARRAY_N&orderby=menu_order&order=ASC&post_parent=' . $pid);
                    if ($images2) {
                        $item["gallery_thumbnail"] = "<ul>";
                        $item["gallery_medium"] = "<ul>";
                        $item["gallery_large"] = "<ul>";
                        $item["gallery_full"] = "<ul>";
                        if ($imid = get_post_thumbnail_id($pid)) {
                            $img = array();
                            foreach ($sizes as $size) {
                                $full_link = wp_get_attachment_image_src($imid, "full");
                                $img["img_info_" . $size] = wp_get_attachment_image_src($imid, $size);
                                $img["img_src_" . $size] = $item["featured_img_info_" . $size][0];
                                $img["img_" . $size] = '<a href="' . $full_link[0] . '">' . wp_get_attachment_image($imid, $size, false) . '</a>';
                                $item["gallery_" . $size] .= '<li><a href="' . $full_link[0] . '">' . wp_get_attachment_image($imid, $size, false) . '</a></li>';
                            }
                            $item["gallery_list"][] = $img;
                        }
                        foreach ($images2 as $image) {
                            if (get_post_thumbnail_id($pid) != $image->ID) {
                                $img = array();
                                foreach ($sizes as $size) {
                                    $full_link = wp_get_attachment_image_src($image->ID, "full");
                                    $img["img_info_" . $size] = wp_get_attachment_image_src($image->ID, $size);
                                    $img["img_src_" . $size] = $item["featured_img_info_" . $size][0];
                                    $img["img_" . $size] = '<a href="' . $full_link[0] . '">' . wp_get_attachment_image($image->ID, $size, false) . '</a>';
                                    $item["gallery_" . $size] .= '<li><a href="' . $full_link[0] . '">' . wp_get_attachment_image($image->ID, $size, false) . '</a></li>';
                                }
                                $item["gallery_list"][] = $img;
                            }
                        }
                        $item["gallery_thumbnail"] .= "</ul>";
                        $item["gallery_medium"] .= "</ul>";
                        $item["gallery_large"] .= "</ul>";
                        $item["gallery_full"] .= "</ul>";
                    }
                    $taxonomies = get_object_taxonomies($post_type, "objects");
                    foreach ($taxonomies as $tax => $v) {
                        $terms = get_the_terms($pid, $tax);
                        if ($terms) {
                            $terms_links_ul = "<ul>";
                            $terms_string_ul = "<ul>";
                            $terms_list = array();
                            $terms_links = array();
                            foreach ($terms as $term) {
                                $terms_list[] = $term->name;
                                $terms_links[] = '<a href="' . get_term_link($term, $tax) . '">' . $term->name . '</a>';
                                $terms_links_ul .= '<li><a href="' . get_term_link($term, $tax) . '">' . $term->name . '</a></li>';
                                $terms_string_ul .= '<li>' . $term->name . '</li>';
                            }
                            $terms_links_ul .= "</ul>";
                            $terms_string_ul .= "</ul>";
                            $item["tax_string_" . $tax] = implode(", ", $terms_list);
                            $item["tax_list_" . $tax] = $terms_string_ul;
                            $item["tax_links_string_" . $tax] = implode(", ", $terms_links);
                            $item["tax_links_list_" . $tax] = $terms_links_ul;
                            $item["tax_array_" . $tax] = $terms;
                        } else {
                            $item["tax_string_" . $tax] = "";
                            $item["tax_list_" . $tax] = "";
                            $item["tax_links_string_" . $tax] = array();
                            $item["tax_links_list_" . $tax] = "";
                            $item["tax_array_" . $tax] = array();
                        }
                    }
                }
                $items[] = $item;
            }
        }
    }
    $items2 = array();
    for ($g = $page_no; $g < ($page_no + $per_page); $g++) {
        $item = $items[$g];
        if ($item) {
            if ($args["comment_template"]) {
                ob_start();
                include($args["comment_template"]);
                $output .= ob_get_contents();
                ob_end_clean();
            }
            $items2[] = $item;
        }
    }
    if (!count($items2)) {
        if ($args["post_template"]) {
            if ($args["no_results_html"]) {
                $output .= $args["no_results_html"];
            } elseif ($args["no_results_file"]) {
                ob_start();
                include($args["no_results_file"]);
                $output .= ob_get_contents();
                ob_end_clean();
            }
        }
    }
    return array(
        "output" => $output,
        "total_posts" => count($items),
        "page_no" => $page_no,
        "per_page" => $per_page,
        "items" => $items2
    );
}

function gamma_get_pagination($count, $per_page, $page = 0, $var = "page_no") {
    if ($page) {
        $pg = $page;
    } else {
        if ($_GET["page_no"]) {
            $pg = $_GET["page_no"];
        } else {
            $pg = 1;
        }
    }
    $total_pages = ceil($count / $per_page);

    $string = "page_no=%pgno";
    foreach ($_GET as $f => $v) {
        if ($f != $var) {
            if (is_array($v)) {
                foreach ($v as $val) {
                    $string .= "&" . $f . "[]=" . $val;
                }
            } else {
                $string .= "&" . $f . "=" . $v;
            }
        }
    }
    $path = current_page_url();
    $pieces_1 = explode("?", $path);
    $pieces = explode("/", $pieces_1[0]);
    for ($i = 0; $i < count($pieces); $i++) {
        if ($pieces[$i] == "page") {
            unset($pieces[$i]);
            unset($pieces[$i + 1]);
        }
        if (strpos("--" . $pieces[$i], "?")) {
            unset($pieces[$i]);
        }
    }
    if ($pg > $total_pages - 1) {
        $pg = $total_pages - 1;
    }
    $url = implode("/", $pieces);
    echo '<ul class="pagination justify-content-center pagination-md">';
    // echo '<li><a href="' . $url . '/?' . $var . '=0' . $string . '">First</a></li>';
    if ($pg > 1) {
        echo '<li class="page-item"><a class="page-link" href="' . $url . '/page/' . ($pg - 1) . '/?' . str_replace("%pgno", ($pg - 1), $string) . '">&lt;</a></li>';
    } else {
        //echo '<li><a href="' . $url . '/?' . $var . '=0' . $string . '">&lt;</a></li>';
    }
    if ($pg < 4) {
        for ($i = 0; $i <= $pg; $i++) {
            if (($i + 1) < $total_pages) {
                if ($pg == ($i + 1)) {
                    $class = 'class="current_page"';
                } else {
                    $class = "";
                }
                echo '<li class="page-item"><a class="page-link" href="' . $url . '/page/' . ($i + 1) . '/?' . str_replace("%pgno", ($i + 1), $string) . '" ' . $class . ' data-pgid="' . ($i ) . '">' . ($i + 1) . '</a></li>';
            }
        }
        if ($total_pages > 7) {
            for ($i = $pg + 1; $i < 7; $i++) {
                if ($pg == ($i + 1)) {
                    $class = 'class="current_page"';
                } else {
                    $class = "";
                }
                echo '<li class="page-item"><a class="page-link" href="' . $url . '/page/' . ($i + 1) . '/?' . str_replace("%pgno", ($i + 1), $string) . '" ' . $class . ' data-pgid="' . ($i ) . '">' . ($i + 1) . '</a></li>';
            }
        } else {
            for ($i = $pg + 1; $i < $total_pages - 1; $i++) {
                if ($pg == ($i + 1)) {
                    $class = 'class="current_page"';
                } else {
                    $class = "";
                }
                echo '<li class="page-item"><a class="page-link" href="' . $url . '/page/' . ($i + 1) . '/?' . str_replace("%pgno", ($i + 1), $string) . '" ' . $class . ' data-pgid="' . ($i ) . '">' . ($i + 1) . '</a></li>';
            }
        }
    } elseif ($pg > ($total_pages - 4)) {
        if ($pg > 3) {
            for ($i = $pg - 3; $i <= $pg; $i++) {
                if ($pg == ($i + 1)) {
                    $class = 'class="current_page"';
                } else {
                    $class = "";
                }
                echo '<li class="page-item"><a class="page-link" href="' . $url . '/page/' . ($i + 1) . '/?' . str_replace("%pgno", ($i + 1), $string) . '" ' . $class . ' data-pgid="' . ($i ) . '">' . ($i + 1) . '</a></li>';
            }
        } else {
            for ($i = $pg; $i >= 0; $i--) {
                if ($pg == ($i + 1)) {
                    $class = 'class="current_page"';
                } else {
                    $class = "";
                }
                echo '<li class="page-item"><a class="page-link" href="' . $url . '/page/' . ($i + 1) . '/?' . str_replace("%pgno", ($i + 1), $string) . '" ' . $class . ' data-pgid="' . ($i) . '">' . ($i + 1) . '</a></li>';
            }
        }
        if ($pg + 1 < $total_pages) {
            for ($i = $pg + 1; $i < $total_pages; $i++) {
                if ($pg == ($i + 1)) {
                    $class = 'class="current_page"';
                } else {
                    $class = "";
                }
                echo '<li class="page-item"><a class="page-link" href="' . $url . '/page/' . ($i + 1) . '/?' . str_replace("%pgno", ($i + 1), $string) . '" ' . $class . ' data-pgid="' . ($i ) . '">' . ($i + 1) . '</a></li>';
            }
        }
    } else {
        for ($i = $pg - 4; $i <= $pg; $i++) {
            if ($pg == ($i + 1)) {
                $class = 'class="current_page"';
            } else {
                $class = "";
            }
            echo '<li><a href="' . $url . '/page/' . ($i + 1) . '/?' . str_replace("%pgno", ($i + 1), $string) . '" ' . $class . ' data-pgid="' . ($i ) . '">' . ($i + 1) . '</a></li>';
        }
        for ($i = $pg + 1; $i < $pg + 3; $i++) {
            if ($pg == ($i)) {
                $class = 'class="current_page"';
            } else {
                $class = "";
            }
            echo '<li><a href="' . $url . '/page/' . ($i + 1) . '/?' . str_replace("%pgno", ($i + 1), $string) . '" ' . $class . ' data-pgid="' . ($i) . '">' . ($i + 1) . '</a></li>';
        }
    }
    if ($pg < $total_pages - 1) {
        if (!$pg) {
            $pg = 1;
        }
        echo '<li><a href="' . $url . '/page/' . ($pg + 1) . '/?' . str_replace("%pgno", ($pg + 1), $string) . '">&gt;</a></li>';
    } else {
        // echo '<li><a href="' . $url . '/?' . $var . '=' . ($total_pages - 1) . $string . '">&gt;</a></li>';
    }
    // echo '<li><a href="' . $url . '/?' . $var . '=' . ($total_pages - 1) . $string . '">Last</a></li>';
    echo '</ul>';
}

function admin_colors() {
    ?>
    <style type="text/css">
        #searchwp-missing-integrations-notice{
            display:none !important;
        }
        #wlcms_dashboard_logo{
            width:100%;
            margin-bottom:15px !important;
            display:block !important;
        }
        #adminmenuwrap {
            background:url(<?php echo get_bloginfo("template_url"); ?>/assets/img/defaults/admin_logo.png) center 15px !important;
            padding-top: <?php echo get_option("admin_logo_height"); ?>px;
            background-size: <?php echo (get_option("admin_logo_height") + 60); ?>px auto !important;
            background-repeat:no-repeat !important;
        }
        #wpfooter{
            display:none !important;
        }
        .settings_wysiwyg iframe{
            height:200px !important;
        }
        <?php
        $test = file_get_contents(get_bloginfo("template_url") . '/framework/css/admin_colors.css');
        $test = str_replace("#maincolor1", get_option("color1"), $test);
        $test = str_replace("#maincolor2", get_option("color2"), $test);
        $test = str_replace("#menuhover", get_option("color3"), $test);
        $test = str_replace("#menuhover2", get_option("color4"), $test);
        $test = str_replace("#background1", get_option("color5"), $test);
        $test = str_replace("#acolor", get_option("color6"), $test);
        echo $test;
        ?>
    </style>
    <?php
}

//add_action('admin_head', 'admin_colors');

function my_login_logo() {
    ?>
    <style type="text/css">
        .login h1 a {
            background:url(<?php echo get_bloginfo("template_url"); ?>/assets/img/defaults/login_logo.png) center !important;
            background-repeat:no-repeat !important;
            height: <?php echo get_option("login_logo_height"); ?>px !important;
            width:100%
        }
    </style>
    <?php
}

//add_action('login_enqueue_scripts', 'my_login_logo');
add_theme_support('post-thumbnails');

function current_page_url() {
    $pageURL = 'http';
    if ($_SERVER["HTTPS"] == "on") {
        $pageURL .= "s";
    }
    $pageURL .= "://";
    if ($_SERVER["SERVER_PORT"] != "80") {
        $pageURL .= $_SERVER["SERVER_NAME"] . ":" . $_SERVER["SERVER_PORT"] . $_SERVER["REQUEST_URI"];
    } else {
        $pageURL .= $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"];
    }
    return $pageURL;
}

function mytheme_get_avatar($avatar, $id_or_email, $size) {
    if (intval($id_or_email)) {
        $uid = $id_or_email;
    } elseif (strpos($id_or_email, "@")) {
        $user = get_user_by('email', $id_or_email);
        $uid = $user->ID;
    } else {
        $user = get_user_by('login', $id_or_email);
//        print_r($user);
        $uid = $user->ID;
    }
    global $wpdb;
    if (user_can($uid, "job_seeker")) {
        $bfe = $wpdb->get_var("SELECT `post_id` FROM `wp_posts`,`wp_postmeta` WHERE `wp_posts`.`ID`=`wp_postmeta`.`post_id` AND `wp_posts`.`post_type`='profile' AND `meta_value`='" . $uid . "' AND `meta_key`='user' LIMIT 0,1");
        update_user_meta($uid, "profile_id", $bfe);
        $av = get_post_thumbnail_id($bfe);
    } elseif (user_can($uid, "job_employer")) {
        $bfe = $wpdb->get_var("SELECT `post_id` FROM `wp_postmeta` WHERE `meta_value`='" . $uid . "' AND `meta_key`='company_owner' LIMIT 0,1");
        update_user_meta($uid, "profile_id", $bfe);
        $av = get_post_thumbnail_id($bfe);
    } else {
        $av = get_user_meta($uid, "avatar_id", true);
    }
    if ($avatar_img = wp_get_attachment_image($av, array($size, $size))) {
        $avatar = $avatar_img;
    } else {
        $avatar = "<img alt='image_alt' src='" . get_bloginfo("template_url") . "/assets/img/defaults/no_user.png' class='avatar avatar-{$size} photo avatar-default' height='{$size}' width='{$size}' />";
    }
    return $avatar;
}

add_filter('get_avatar', 'mytheme_get_avatar', 10, 3);

function get_display_name($author_id, $annon = 0) {
    global $wpdb;

    if ($annon) {
        $anon_id = get_user_meta($author_id, "anon_id", true);
        if (!$anon_id) {
            $anon_id = rand(100000, 999999);
            update_user_meta($author_id, "anon_id", $anon_id);
        }
        if (user_can($author_id, "job_employer")) {
            return "Company" . $anon_id;
        } else {
            return "Annon" . $anon_id;
        }
    } else {
        if (user_can($author_id, "job_seeker")) {
            $bfe = $wpdb->get_var("SELECT `post_id` FROM `wp_postmeta` WHERE `meta_value`='" . $author_id . "' AND `meta_key`='user' LIMIT 0,1");
            return get_the_title($bfe);
        } elseif (user_can($author_id, "job_employer")) {
            $bfe = $wpdb->get_var("SELECT `post_id` FROM `wp_postmeta` WHERE `meta_value`='" . $author_id . "' AND `meta_key`='company_owner' LIMIT 0,1");
            return get_the_title($bfe);
        } else {
            return ucfirst(get_user_meta($author_id, "first_name", true)) . " " . substr(ucfirst(get_user_meta($author_id, "last_name", true)), 0, 1);
        }
    }
}

add_filter('post_thumbnail_html', 'my_post_thumbnail_html');

function my_post_thumbnail_html($html) {
    if (empty($html))
        $html = '<img src="' . trailingslashit(get_stylesheet_directory_uri()) . '/assets/img/defaults/no_thumb.png' . '" alt="" style="max-width:100%; height:auto;" />';
    return $html;
}

function gamma_redirect($url, $type = "js") {
    if ($type == "js") {
        echo '<script>window.location="' . $url . '"; </script>';
    } else {
        header("Location:" . $url);
    }
    die();
}

function gamma_page_link($page) {
    return get_bloginfo("url") . "/" . $page;
}

function send_email($to, $subject, $body) {
//$to = get_bloginfo("admin_email");
    $headers = "From: " . get_option("gamma_email_from_name") . " <" . get_option("gamma_email_from_email") . ">\r\n";
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/html; charset=utf-8\r\n";
    $headers .= "Content-Transfer-Encoding: quoted-printable";
    $msg = stripslashes(get_option("gamma_email_header"));
    $msg .= $body;
    $msg .= stripslashes(get_option("gamma_email_footer"));
    if (wp_mail($to, $subject, $msg, $headers)) {
        return true;
    } else {
        return false;
    }
}

function randomstring($length = 10) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $randstring = '';
    for ($i = 0; $i < $length; $i++) {
        $randstring .= $characters[rand(0, strlen($characters))];
    }
    return $randstring;
}
