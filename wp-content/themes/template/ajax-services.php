<?php
global $wpdb;
session_start();
if (is_admin()) {
    require_once('includes/file.php' );
    require_once('includes/image.php' );
} else {
    require_once( ABSPATH . 'wp-admin/includes/file.php' );
    require_once( ABSPATH . 'wp-admin/includes/image.php' );
}

if ($_REQUEST["logout"]) {
    wp_logout();
}

if ($_REQUEST["logmein"]) {
    $creds = array();
    $user_data = wp_signon();
    wp_set_current_user($user_data->ID, $user_data->user_login);
    wp_set_auth_cookie($user_data->ID);
    do_action('wp_login', $user_data->user_login);
}

if ($_GET["clear_filters"]) {
    $_SESSION["search"] = array();
}

if ($_GET["ajax_action"] == "check_username") {
    if (get_user_by("login", $_GET["vals"])) {
        echo 1;
    }
    die();
}

if ($_GET["ajax_action"] == "daily_tasks") {
//    RESET PENDING SWAPS
//    DELETE FOLLOW ROWS FOR REMOVED USERS
    die();
}

if ($_GET["ajax_action"] == "load_blogs") {
    unset($_GET["ajax_action"]);
    $per_page = 12;
    $page_no = $_GET["pageno"];
    if (!$page_no) {
        $page_no = 0;
    }
    $args = array();
    $args["post_type"] = "blog";
    $args["post_template"] = dirname(__FILE__) . "/templates/post-blog/content-item-small.php";
    $args["search"]["exclude"] = explode("|", $_GET["exclude"]);

    if ($_GET["blog_tax"]) {
        $args["search"]["tax_blogs"] = $_GET["blog_tax"];
    }
//    $args["paged"] = $page_no;
//    $args["per_page"] = $per_page;
    $args["no_results_html"] = '<h3 class="no_results">Sorry, no results</h3>';
    $args["page"] = $page_no;
    $args["per_page"] = $per_page;
    $results = gamma_get_posts($args);

    //print_r($args);

    echo $results["output"];
    echo '<input type="hidden" name="total_pages" id="total_pages" value="' . ceil($results["total_posts"] / $per_page) . '" />';
    echo '<input type="hidden" name="current_page" id="current_page" value="' . $page_no . '" />';
    if (ceil($results["total_posts"] / $per_page)>1) {
        ?>
        <div style="clear: both; padding-top: 30px;">
            <nav class="page-numbers">
                <?php echo gamma_get_pagination($results["total_posts"], $per_page); ?>
            </nav>
        </div>
        <script>
            jQuery(document).ready(function () {
                jQuery(".page-numbers a").unbind("click").click(function (event) {
                    $.ajax({
                        url: "<?php echo get_bloginfo("url"); ?>/?ajax_action=load_blogs&exclude=<?php echo $_GET["exclude"]; ?>&blog_tax=" + jQuery("#categories  .active .filter_category").attr("data-termid") + "&pageno=" + jQuery(this).attr("data-pgid"),
                        context: document.body
                    }).done(function (data) {
                        jQuery("#results_div").animate({opacity: 0}, function () {
                            jQuery("#results_div").html(data).animate({opacity: 1});
                            jQuery('html, body').animate({
                                scrollTop: jQuery("#results_div").offset().top - 50
                            }, 500);
                        });
                    })
                    event.preventDefault();
                    return false;
                })
            })
        </script>
        <?php
    }
    die();
}

if ($_GET["ajax_action"] == "load_portfolio") {
    unset($_GET["ajax_action"]);
    $per_page = 12;
    $page_no = $_GET["pageno"];
    if (!$page_no) {
        $page_no = 0;
    }
    $args = array();
    $args["post_type"] = "portfolio";
    $args["post_template"] = dirname(__FILE__) . "/templates/post-portfolio/content-item.php";

    if ($_GET["portfolio_tax"]) {
        $args["search"]["tax_portfoliocat"] = $_GET["portfolio_tax"];
    }
//    $args["paged"] = $page_no;
//    $args["per_page"] = $per_page;
    $args["no_results_html"] = '<h3 class="no_results">Sorry, no results</h3>';
    $args["page"] = $page_no;
    $args["per_page"] = $per_page;
    $results = gamma_get_posts($args);

    //print_r($args);

    echo $results["output"];
    echo '<input type="hidden" name="total_pages" id="total_pages" value="' . ceil($results["total_posts"] / $per_page) . '" />';
    echo '<input type="hidden" name="current_page" id="current_page" value="' . $page_no . '" />';
    if (ceil($results["total_posts"] / $per_page)>1) {
        ?>
        <div style="clear: both; padding-top: 30px;">
            <nav class="page-numbers">
                <?php echo gamma_get_pagination($results["total_posts"], $per_page); ?>
            </nav>
        </div>
        <script>
            jQuery(document).ready(function () {
                jQuery(".page-numbers a").unbind("click").click(function (event) {
                    $.ajax({
                        url: "<?php echo get_bloginfo("url"); ?>/?ajax_action=load_portfolio&portfolio_tax=" + jQuery("#categories  .active .filter_category").attr("data-termid") + "&pageno=" + jQuery(this).attr("data-pgid"),
                        context: document.body
                    }).done(function (data) {
                        jQuery("#results_div").animate({opacity: 0}, function () {
                            jQuery("#results_div").html(data).animate({opacity: 1});
                            jQuery('html, body').animate({
                                scrollTop: jQuery("#results_div").offset().top - 50
                            }, 500);
                        });
                    })
                    event.preventDefault();
                    return false;
                })
            })
        </script>
        <?php
    }
    die();
}

if ($_GET["ajax_action"] == "load_blog") {
    unset($_GET["ajax_action"]);
    $search = $_GET;
    unset($search["tags"]);
    if (strlen($_GET["tags"]) > 2) {
        $tags = explode("|", $_GET["tags"]);
    }
    foreach ($tags as $f => $v) {
        $search["tax_slug_box_tags"][] = sanitize_title($v);
    }
    $per_page = 9;
    $page_no = $_GET["page_no"];
    if (!$page_no) {
        $page_no = 0;
    }
    $post_type = "blog";
    $term = get_query_var('term');
    $tax_name = get_query_var('taxonomy');
    $args = array();
    $args["post_type"] = $post_type;
    $args["post_template"] = dirname(__FILE__) . "/templates/post-blog/content-item.php";
    $args["search"] = $search;
    $args["page"] = $page_no;
    $args["per_page"] = $per_page;
    $args["no_results_html"] = '<h3 class="no_results">No results</h3>';
    $results = gamma_get_posts($args);
    echo $results["output"];
    echo '<input type="hidden" name="total_pages" id="total_pages" value="' . ceil($results["total_posts"] / $per_page) . '" />';
    echo '<input type="hidden" name="current_page" id="current_page" value="' . $page_no . '" />';

    die();
}


if ($_GET["ajax_action"] == "upload_image") {
//echo "aaaaaaaaaa";
//print_r($_POST);
    $wp_upload_dir = wp_upload_dir();
    $uploadedfile = $_FILES["file"];
    for ($i = 0; $i < count($uploadedfile['name']); $i++) {
        $upload_overrides = array('test_form' => false);
        $wp_filetype = $uploadedfile['type'][$i];
        $fname = $uploadedfile['name'][$i];
        $filename = sanitize_file_name(rand(100, 999) . "_" . $uploadedfile['name'][$i]);
        $movefile = move_uploaded_file($uploadedfile['tmp_name'][$i], $wp_upload_dir['path'] . '/' . basename($filename));
        if ($movefile) {
            $attachment = array(
                'guid' => $wp_upload_dir['url'] . '/' . basename($filename),
                'post_mime_type' => $wp_filetype,
                'post_title' => preg_replace('/\.[^.]+$/', '', basename($filename)),
                'post_content' => '',
                'post_status' => 'inherit'
            );
            $attach_id = wp_insert_attachment($attachment, $wp_upload_dir['path'] . '/' . basename($filename));
            $attach_data = wp_generate_attachment_metadata($attach_id, $wp_upload_dir['path'] . '/' . basename($filename));
            wp_update_attachment_metadata($attach_id, $attach_data);
        }
        echo '<input type="hidden" name="post_attach[]" data_file="' . $fname . '" value="' . $attach_id . '" />';
    }
    die();
}

if ($_GET["ajax_action"] == "upload_image_user") {
//echo "aaaaaaaaaa";
//print_r($_POST);
    $wp_upload_dir = wp_upload_dir();
    $uploadedfile = $_FILES["file"];
    for ($i = 0; $i < count($uploadedfile['name']); $i++) {
        $upload_overrides = array('test_form' => false);
        $wp_filetype = $uploadedfile['type'][$i];
        $fname = $uploadedfile['name'][$i];
        $filename = sanitize_file_name(rand(100, 999) . "_" . $uploadedfile['name'][$i]);
        $movefile = move_uploaded_file($uploadedfile['tmp_name'][$i], $wp_upload_dir['path'] . '/' . basename($filename));
        if ($movefile) {
            $attachment = array(
                'guid' => $wp_upload_dir['url'] . '/' . basename($filename),
                'post_mime_type' => $wp_filetype,
                'post_title' => preg_replace('/\.[^.]+$/', '', basename($filename)),
                'post_content' => '',
                'post_status' => 'inherit'
            );
            $attach_id = wp_insert_attachment($attachment, $wp_upload_dir['path'] . '/' . basename($filename));
            $attach_data = wp_generate_attachment_metadata($attach_id, $wp_upload_dir['path'] . '/' . basename($filename));
            wp_update_attachment_metadata($attach_id, $attach_data);
        }
        echo '<input type="hidden" name="user_attach[]" data_file="' . $fname . '" value="' . $attach_id . '" />';
    }
    die();
}
if ($_GET["ajax_action"] == "delete_img") {
    wp_delete_attachment($_GET["img_id"]);
    die();
}
if ($_GET["ajax_action"] == "delete_img_user") {
    $args = array();
    $user_id = get_current_user_id();
    $field_id = $_REQUEST['img_id'];
    $new_gal = array();
    $gal = get_user_meta($user_id, "image_gallery", true);
    foreach ($gal as $v) {
        if ($v != $field_id) {
            $new_gal[] = $v;
        }
    }
    $gal = update_user_meta($user_id, "image_gallery", $new_gal);
    $delete_img_post = $wpdb->query("DELETE FROM `wp_posts` WHERE `id`='" . $_REQUEST['img_id'] . "'");
    $delete_img_post = $wpdb->query("DELETE FROM `wp_postmeta` WHERE `post_id`='" . $_REQUEST['img_id'] . "'");
    if ($delete_img_post == 1) {
        echo 1;
    } else {
        echo 0;
    }
    die();
}
/* END UNFOLLOW */
if ($_REQUEST["gamma_save_user_post"]) {
    if ($cid = $_POST["user_core_id"]) {
        $fields = array("user_login" => "user_core_login", "user_nicename" => "user_core_nicename", "user_email" => "user_core_email", "display_name" => "user_core_display_name", "nickname" => "user_core_nickname", "first_name" => "user_core_first_name", "last_name" => "user_core_last_name", "description" => "user_core_description", "user_registered" => "user_core_registered", "role" => "user_core_role");
        $data = array();
        $data["ID"] = $cid;
        foreach ($fields as $f => $v) {
            if ($value = $_POST[$v]) {
                $data[$f] = $value;
            }
        }
        if ($_POST["user_core_password"]) {
            $data["user_pass"] = $_POST["user_core_password"];
        }
        (wp_update_user($data));
    } else {
        $fields = array("user_pass" => "user_core_password", "user_login" => "user_core_login", "user_nicename" => "user_core_nicename", "user_email" => "user_core_email", "display_name" => "user_core_display_name", "nickname" => "user_core_nickname", "first_name" => "user_core_first_name", "last_name" => "user_core_last_name", "description" => "user_core_description", "user_registered" => "user_core_registered", "role" => "user_core_role");
        $data = array();
        foreach ($fields as $f => $v) {
            if ($value = $_POST[$v]) {
                $data[$f] = $value;
            }
        }
        if (!$data["user_login"] && $_REQUEST["user_core_email"]) {
            $data["user_login"] = $_REQUEST["user_core_email"];
        }
        if (!$data["user_email"] && $_REQUEST["user_core_password"]) {
            $data["user_email"] = $_REQUEST["user_core_password"];
        }
        $cid = wp_insert_user($data);
        if ($cid && !is_wp_error($cid)) {
            $code = randomstring(16);
            update_user_meta($cid, "confirmation_code", $code);
            send_user_email($cid, "confirm_register", array("profile_id" => $cid, "code" => $code));
        }
    }
    if ($cid) {
        if ($file = basename($_FILES["user_core_avatar"]["name"])) {

            $uploadedfile = $_FILES['user_core_avatar'];
            $upload_overrides = array('test_form' => false);
            $movefile = wp_handle_upload($uploadedfile, $upload_overrides);
            if ($movefile) {
                $wp_filetype = $movefile['type'];
                $filename = $movefile['file'];
                $wp_upload_dir = wp_upload_dir();
                $attachment = array(
                    'guid' => $wp_upload_dir['url'] . '/' . basename($filename),
                    'post_mime_type' => $wp_filetype,
                    'post_title' => preg_replace('/\.[^.]+$/', '', basename($filename)),
                    'post_content' => '',
                    'post_status' => 'inherit'
                );
                $attach_id = wp_insert_attachment($attachment, $filename);
                $attach_data = wp_generate_attachment_metadata($attach_id, $filename);
                wp_update_attachment_metadata($attach_id, $attach_data);
                update_user_meta($cid, "avatar_id", $attach_id);
            }
        }
        foreach ($_FILES as $f => $v) {
            if (substr($f, 0, 10) == "user_file_") {
                $field = substr($f, 10);
                $uploadedfile = $_FILES[$f];
                if ($uploadedfile['name']) {
                    $upload_overrides = array('test_form' => false);
                    $movefile = wp_handle_upload($uploadedfile, $upload_overrides);
                    if ($movefile) {
                        $wp_filetype = $movefile['type'];
                        $filename = $movefile['file'];
                        $wp_upload_dir = wp_upload_dir();
                        $attachment = array(
                            'guid' => $wp_upload_dir['url'] . '/' . basename($filename),
                            'post_mime_type' => $wp_filetype,
                            'post_title' => preg_replace('/\.[^.]+$/', '', basename($filename)),
                            'post_content' => '',
                            'post_status' => 'inherit'
                        );
                        $attach_id = wp_insert_attachment($attachment, $filename);
                        $attach_data = wp_generate_attachment_metadata($attach_id, $filename);
                        wp_update_attachment_metadata($attach_id, $attach_data);
                    }
                }
                delete_user_meta($cid, $field);
                add_user_meta($cid, $field, $attach_id);
            }
        }
        foreach ($_POST as $f => $v) {
            if (substr($f, 0, 10) == "user_meta_") {
                $field = substr($f, 10);
                if (is_array($v)) {
                    delete_user_meta($cid, $field);
                    foreach ($v as $val) {
                        add_user_meta($cid, $field, $val);
                    }
                } else {
                    update_user_meta($cid, $field, $v);
                }
            }
        }
        if ($_POST["user_attach"]) {
            $attach_list = $_POST["user_attach"];
            $attach_list = array_unique($attach_list);
            foreach ($attach_list as $aid) {
                wp_update_post(
                        array(
                            'ID' => $aid,
                            'post_parent' => $cid
                        )
                );
            }
        }
    }
    $uid = $cid;
    if ($cid = $_POST["post_core_id"]) {
        $fields = array("post_content" => "post_core_content", "post_name" => "post_core_name", "post_title" => "post_core_title", "post_status" => "post_core_status", "post_type" => "post_core_type", "post_author" => "post_core_author", "post_date" => "post_core_date");
        $data = array();
        $data["ID"] = $cid;
        foreach ($fields as $f => $v) {
            if ($value = $_POST[$v]) {
                $data[$f] = $value;
            }
        }
        $cid = (wp_update_post($data));
    } else {
        $fields = array("post_content" => "post_core_content", "post_name" => "post_core_name", "post_title" => "post_core_title", "post_status" => "post_core_status", "post_type" => "post_core_type", "post_author" => "post_core_author", "post_date" => "post_core_date");
        $data = array();
        foreach ($fields as $f => $v) {
            if ($value = $_POST[$v]) {
                $data[$f] = $value;
            }
        }
        $data["post_author"] = $uid;
        $cid = wp_insert_post($data);
    }

    if ($file = basename($_FILES["post_core_image"]["name"])) {
        $uploadedfile = $_FILES['post_core_image'];
        $upload_overrides = array('test_form' => false);
        $movefile = wp_handle_upload($uploadedfile, $upload_overrides);
        if ($movefile) {
            $wp_filetype = $movefile['type'];
            $filename = $movefile['file'];
            $wp_upload_dir = wp_upload_dir();
            $attachment = array(
                'guid' => $wp_upload_dir['url'] . '/' . basename($filename),
                'post_mime_type' => $wp_filetype,
                'post_title' => preg_replace('/\.[^.]+$/', '', basename($filename)),
                'post_content' => '',
                'post_status' => 'inherit'
            );
            $attach_id = wp_insert_attachment($attachment, $filename, $cid);
            $attach_data = wp_generate_attachment_metadata($attach_id, $filename);
            wp_update_attachment_metadata($attach_id, $attach_data);
            set_post_thumbnail($cid, $attach_id);
        }
    }
    if ($_POST["post_attach"]) {
        $attach_list = $_POST["post_attach"];
        $attach_list = array_unique($attach_list);
        foreach ($attach_list as $aid) {
            wp_update_post(
                    array(
                        'ID' => $aid,
                        'post_parent' => $cid
                    )
            );
        }
    }
    foreach ($_FILES as $f => $v) {
        if (substr($f, 0, 11) == "post_media_") {
            $uploadedfile = $_FILES[$f];
            if ($uploadedfile['name']) {
                $upload_overrides = array('test_form' => false);
                $movefile = wp_handle_upload($uploadedfile, $upload_overrides);
                if ($movefile) {
                    $wp_filetype = $movefile['type'];
                    $filename = $movefile['file'];
                    $wp_upload_dir = wp_upload_dir();
                    $attachment = array(
                        'guid' => $wp_upload_dir['url'] . '/' . basename($filename),
                        'post_mime_type' => $wp_filetype,
                        'post_title' => preg_replace('/\.[^.]+$/', '', basename($filename)),
                        'post_content' => '',
                        'post_status' => 'inherit'
                    );
                    $attach_id = wp_insert_attachment($attachment, $filename, $cid);
                    $attach_data = wp_generate_attachment_metadata($attach_id, $filename);
                    wp_update_attachment_metadata($attach_id, $attach_data);
                }
            }
        }
        if (substr($f, 0, 10) == "post_file_") {
            $field = substr($f, 10);
            $uploadedfile = $_FILES[$f];
            if ($uploadedfile['name']) {
                $upload_overrides = array('test_form' => false);
                $movefile = wp_handle_upload($uploadedfile, $upload_overrides);
                if ($movefile) {
                    $wp_filetype = $movefile['type'];
                    $filename = $movefile['file'];
                    $wp_upload_dir = wp_upload_dir();
                    $attachment = array(
                        'guid' => $wp_upload_dir['url'] . '/' . basename($filename),
                        'post_mime_type' => $wp_filetype,
                        'post_title' => preg_replace('/\.[^.]+$/', '', basename($filename)),
                        'post_content' => '',
                        'post_status' => 'inherit'
                    );
                    $attach_id = wp_insert_attachment($attachment, $filename);
                    $attach_data = wp_generate_attachment_metadata($attach_id, $filename);
                    wp_update_attachment_metadata($attach_id, $attach_data);
                }
            }
            update_post_meta($cid, $field, $attach_id);
        }
    }
//    print_r($_POST);
    foreach ($_POST as $f => $v) {
        if (substr($f, 0, 9) == "post_tax_") {
//            print_r($v);
            $field = substr($f, 9);
            $values = array();
            if (is_array($v)) {
                foreach ($v as $vs) {
                    $values[] = intval($vs);
                }
            } else {
                $values[] = $v;
            }
            (wp_set_post_terms($cid, $values, $field, false));
        }
        if (substr($f, 0, 10) == "post_meta_") {
            $field = substr($f, 10);
//            print_r($v);
            if ($field == "price") {
                $field = "_price";
            }
            if ($field == "stock") {
                $field = "_stock";
            }
            if (is_array($v)) {
                delete_post_meta($cid, $field);
                foreach ($v as $val) {
                    add_post_meta($cid, $field, $val);
                }
            } else {
                update_post_meta($cid, $field, $v);
            }
        }
    }
    $user = get_user_by("id", $uid);
    wp_set_current_user($uid, $user->user_login);
    wp_set_auth_cookie($uid);
    do_action('wp_login', $user->user_login);
    if (!$_POST["post_core_id"]) {
//header("Location:" . get_bloginfo("url") . "/email-confirmation/");
    }
}

//////////////////////////////////////
function tg_validate_url() {
    global $post;
    $page_url = get_bloginfo('url') . "/login/forgot-pass/";
    $urlget = strpos($page_url, "?");
    if ($urlget === false) {
        $concate = "?";
    } else {
        $concate = "&";
    }
    return $page_url . $concate;
}

if ($_GET["ajax_action"] == "load_posts") {
    $args = array();
    $post_type = $_GET["post_type"];
    $args["post_type"] = $post_type;
    $args["post_template"] = dirname(__FILE__) . "/templates/post-" . $post_type . "/content-item.php";
    $args["search"] = $_GET;
    $args["page"] = $_GET["page_no"];
    $args["per_page"] = $_GET["per_page"];
    $results = gamma_get_posts($args);
    echo $results["output"];
    die();
}
if (isset($_GET['key']) && $_GET['action'] == "reset_pwd") {
    $reset_key = $_GET['key'];
    $user_login = $_GET['login'];
    $user_data = $wpdb->get_row($wpdb->prepare("SELECT ID, user_login, user_email FROM $wpdb->users WHERE user_activation_key = %s AND user_login = %s", $reset_key, $user_login));
    $user_login = $user_data->user_login;
    $user_email = $user_data->user_email;
    if (!empty($reset_key) && !empty($user_data)) {
//        $new_password = wp_generate_password(7, false);
////echo $new_password; exit();
//        wp_set_password($new_password, $user_data->ID);
////mailing reset details to the user
//        $body = stripslashes(get_option("gamma_email_body_reset"));
//        $subject = get_option("gamma_email_subject_reset");
//        $body = str_replace("{username}", $user_login, $body);
//        $body = str_replace("{password}", $new_password, $body);
//        $message = '<table rules="all" cellpadding="10">';
////$message .= get_option("gamma_body_regis");
////        $message .= "<tr><td>You have received a review from " . $args["who"] . ".</td></tr>";
////        $message .= "<tr><td>Please go to <a href='" . get_bloginfo("url") . "/my-bookings/'>" . get_bloginfo("url") . "/my-bookings/</a> to see it.</td></tr>";
//        $message .= "<tr><td>" . $body . "</td></tr>";
//        $message .= "</table></body></html>";
//        if (!send_email($user_email, $subject, $message)) {
//            echo "<div class='alert alert-danger'>Email failed to send for some unknown reason</div>";
//            exit();
//        } else {
        $user = get_user_by("id", $cid);
        wp_set_current_user($user_data->ID, $user_data->user_login);
        wp_set_auth_cookie($user_data->ID);
        do_action('wp_login', $user_data->user_login);
        $redirect_to = get_bloginfo('url') . "/edit-account/?action=reset_pass";
        wp_safe_redirect($redirect_to);
        exit();
// }
    } else {
        exit('Not a Valid Key.');
    }
    die();
}
//exit();
if ($_POST['action'] == "tg_pwd_reset") {
    if (!wp_verify_nonce($_POST['tg_pwd_nonce'], "tg_pwd_nonce")) {
        exit("No trick please");
    }
    if (empty($_POST['user_input'])) {
        echo "<div class='alert alert-danger'>Please enter your Username or E-mail address</div>";
        exit();
    }
//We shall SQL escape the input
    $user_input = $wpdb->escape(trim($_POST['user_input']));
    if (strpos($user_input, '@')) {
        $user_data = get_user_by_email($user_input);
        if (empty($user_data)) { //delete the condition $user_data->caps[administrator] == 1, if you want to allow password reset for admins also
            echo "<div class='alert alert-danger'>Invalid E-mail address!</div>";
            exit();
        }
    } else {
        $user_data = get_userdatabylogin($user_input);
        if (empty($user_data)) { //delete the condition $user_data->caps[administrator] == 1, if you want to allow password reset for admins also
            echo "<div class='alert alert-danger'>Invalid Username!</div>";
            exit();
        }
    }
    $user_login = $user_data->user_login;
    $user_email = $user_data->user_email;
    $key = $wpdb->get_var($wpdb->prepare("SELECT user_activation_key FROM $wpdb->users WHERE user_login = %s", $user_login));
    if (empty($key)) {
//generate reset key
        $key = wp_generate_password(20, false);
        $wpdb->update($wpdb->users, array('user_activation_key' => $key), array('user_login' => $user_login));
    }
    $body = stripslashes(get_option("gamma_email_body_forgot"));
    $subject = get_option("gamma_email_subject_forgot");
    $body = str_replace("{username}", $user_login, $body);
    $body = str_replace("{reset_link}", get_bloginfo('url') . "/login/forgot-pass/?action=reset_pwd&key=$key&login=" . rawurlencode($user_login), $body);
    $message = '<table rules="all" cellpadding="10">';
    $message .= "<tr><td>" . $body . "</td></tr>";
    $message .= "</table></body></html>";
    if (!send_email($user_email, $subject, $message)) {
        echo "<div class='alert alert-danger'>Email failed to send for some unknown reason.</div>";
        exit();
    } else {
        echo "<div class='alert alert-success'>We have just sent you an email with Password reset instructions.</div>";
        exit();
    }
    die();
}
if ($_REQUEST["gamma_save_comment"]) {
    if ($cid = $_POST["comment_core_id"]) {
        $fields = array("comment_post_ID" => "comment_core_post_id", "comment_author" => "comment_core_author", "comment_author_email" => "comment_core_author_email", "comment_author_url" => "comment_core_author_url", "comment_content" => "comment_core_content");
        $data = array();
        $data["comment_ID"] = $cid;
        foreach ($fields as $f => $v) {
            if ($value = $_POST[$v]) {
                $data[$f] = $value;
            }
        }
        if ($_POST["gamma_save_comment"] == "Save as Draft") {
            $data['comment_approved'] = 0;
        } else {
            $data['comment_approved'] = 1;
        }
        wp_update_comment($data);
    } else {
        if ($_POST["gamma_save_comment"] == "Save as Draft") {
            $app = 0;
        } else {
            $app = 1;
        }
        $data = array(
            'comment_post_ID' => $_POST["comment_core_post_id"],
            'comment_author' => $_POST["comment_core_author"],
            'comment_author_email' => $_POST["comment_core_author_email"],
            'comment_author_url' => $_POST["comment_core_author_url"],
            'comment_content' => $_POST["comment_core_content"],
            'user_id' => get_current_user_id(),
            'comment_approved' => $app,
        );
        $cid = wp_insert_comment($data);
    }
    foreach ($_POST as $f => $v) {
        if (substr($f, 0, 13) == "comment_meta_") {
            $field = substr($f, 13);
            if (is_array($v)) {
                delete_post_meta($cid, $field);
                foreach ($v as $val) {
                    add_comment_meta($cid, $field, $val);
                }
            } else {
                update_comment_meta($cid, $field, $v);
            }
        }
    }
    $pid = $_POST["comment_core_post_id"];
    $comments = gamma_get_comments(array("hide_post_info" => 1, "search" => array("post_id" => array($pid))));
    $ratingsum = 0;
    $ratingsum1 = 0;
    $ratingsum2 = 0;
    $ratingsum3 = 0;
    $ratingsum4 = 0;
    $ratingno = 0;
    foreach ($comments["items"] as $item1) {
        $ratingsum+=$item1["meta_rating"];
        $ratingsum1+=$item1["meta_rating1"];
        $ratingsum2+=$item1["meta_rating2"];
        $ratingsum3+=$item1["meta_rating3"];
        $ratingsum4+=$item1["meta_rating4"];
        $ratingno++;
    }
    update_post_meta($pid, "rating", $ratingsum / $ratingno);
    update_post_meta($pid, "rating1", $ratingsum1 / $ratingno);
    update_post_meta($pid, "rating2", $ratingsum2 / $ratingno);
    update_post_meta($pid, "rating3", $ratingsum3 / $ratingno);
    update_post_meta($pid, "rating4", $ratingsum4 / $ratingno);
    update_post_meta($pid, "rating_count", $ratingno);
}
/////////////////////////////////////////////////////////////
if ($_REQUEST["gamma_save_post"]) {
    if ($cid = $_POST["post_core_id"]) {
        $fields = array("post_content" => "post_core_content", "post_name" => "post_core_name", "post_title" => "post_core_title", "post_status" => "post_core_status", "post_type" => "post_core_type", "post_author" => "post_core_author", "post_date" => "post_core_date");
        $data = array();
        $data["ID"] = $cid;
        foreach ($fields as $f => $v) {
            if ($value = $_POST[$v]) {
                $data[$f] = $value;
            }
        }
        $cid = (wp_update_post($data));
    } else {
        $fields = array("post_content" => "post_core_content", "post_name" => "post_core_name", "post_title" => "post_core_title", "post_status" => "post_core_status", "post_type" => "post_core_type", "post_author" => "post_core_author", "post_date" => "post_core_date");
        $data = array();
        foreach ($fields as $f => $v) {
            if ($value = $_POST[$v]) {
                $data[$f] = $value;
            }
        }
        $cid = wp_insert_post($data);
    }
    if ($file = basename($_FILES["post_core_image"]["name"])) {
        $uploadedfile = $_FILES['post_core_image'];
        $upload_overrides = array('test_form' => false);
        $movefile = wp_handle_upload($uploadedfile, $upload_overrides);
        if ($movefile) {
            $wp_filetype = $movefile['type'];
            $filename = $movefile['file'];
            $wp_upload_dir = wp_upload_dir();
            $attachment = array(
                'guid' => $wp_upload_dir['url'] . '/' . basename($filename),
                'post_mime_type' => $wp_filetype,
                'post_title' => preg_replace('/\.[^.]+$/', '', basename($filename)),
                'post_content' => '',
                'post_status' => 'inherit'
            );
            $attach_id = wp_insert_attachment($attachment, $filename, $cid);
            $attach_data = wp_generate_attachment_metadata($attach_id, $filename);
            wp_update_attachment_metadata($attach_id, $attach_data);
            set_post_thumbnail($cid, $attach_id);
        }
    }
    if ($_POST["post_attach"]) {
        $attach_list = $_POST["post_attach"];
        $attach_list = array_unique($attach_list);
        foreach ($attach_list as $aid) {
            wp_update_post(
                    array(
                        'ID' => $aid,
                        'post_parent' => $cid
                    )
            );
        }
    }
    foreach ($_FILES as $f => $v) {
        if (substr($f, 0, 11) == "post_media_") {
            $uploadedfile = $_FILES[$f];
            if ($uploadedfile['name']) {
                $upload_overrides = array('test_form' => false);
                $movefile = wp_handle_upload($uploadedfile, $upload_overrides);
                if ($movefile) {
                    $wp_filetype = $movefile['type'];
                    $filename = $movefile['file'];
                    $wp_upload_dir = wp_upload_dir();
                    $attachment = array(
                        'guid' => $wp_upload_dir['url'] . '/' . basename($filename),
                        'post_mime_type' => $wp_filetype,
                        'post_title' => preg_replace('/\.[^.]+$/', '', basename($filename)),
                        'post_content' => '',
                        'post_status' => 'inherit'
                    );
                    $attach_id = wp_insert_attachment($attachment, $filename, $cid);
                    $attach_data = wp_generate_attachment_metadata($attach_id, $filename);
                    wp_update_attachment_metadata($attach_id, $attach_data);
                }
            }
        }
        if (substr($f, 0, 10) == "post_file_") {
            $field = substr($f, 10);
            $uploadedfile = $_FILES[$f];
            if ($uploadedfile['name']) {
                $upload_overrides = array('test_form' => false);
                $movefile = wp_handle_upload($uploadedfile, $upload_overrides);
                if ($movefile) {
                    $wp_filetype = $movefile['type'];
                    $filename = $movefile['file'];
                    $wp_upload_dir = wp_upload_dir();
                    $attachment = array(
                        'guid' => $wp_upload_dir['url'] . '/' . basename($filename),
                        'post_mime_type' => $wp_filetype,
                        'post_title' => preg_replace('/\.[^.]+$/', '', basename($filename)),
                        'post_content' => '',
                        'post_status' => 'inherit'
                    );
                    $attach_id = wp_insert_attachment($attachment, $filename);
                    $attach_data = wp_generate_attachment_metadata($attach_id, $filename);
                    wp_update_attachment_metadata($attach_id, $attach_data);
                }
            }
            update_post_meta($cid, $field, $attach_id);
        }
    }
    foreach ($_POST as $f => $v) {
        if (substr($f, 0, 9) == "post_tax_") {
            $field = substr($f, 9);

            $values = array();
            if (is_array($v)) {
                foreach ($v as $vs) {
                    $values[] = intval($vs);
                }
            } else {
                $values[] = $v;
            }
            (wp_set_post_terms($cid, $values, $field, false));
        }
        if (substr($f, 0, 10) == "post_meta_") {
            $field = substr($f, 10);
            if ($field == "price") {
                $field = "_price";
            }
            if ($field == "stock") {
                $field = "_stock";
            }
            if (is_array($v)) {
                delete_post_meta($cid, $field);
                foreach ($v as $val) {
                    add_post_meta($cid, $field, $val);
                }
            } else {
                update_post_meta($cid, $field, $v);
            }
        }
    }
    if ($_POST["return_url"]) {
        header("Location:" . $_POST["return_url"] . $cid);
    }
}
/////////////////////////////////////////////////////////////
if ($_REQUEST["gamma_delete_user"]) {
    wp_logout();
    $uid = $_REQUEST["user_id"];
    $author_item = gamma_get_posts(array("post_type" => "boyfriend", "search" => array("author" => $uid, "post_status" => array("publish", "draft"))));
    $item = $author_item['items'][0];
    require('./wp-admin/includes/user.php');
    (wp_delete_user($uid));
    $data["ID"] = $item["post_id"];
    $data["post_status"] = "trash";
    (wp_update_post($data));
    header("Location:" . get_bloginfo("url") . "/account-deleted/");
    die();
}
if ($_REQUEST["gamma_save_user"]) {

    if ($cid = $_POST["user_core_id"]) {
        $fields = array("user_login" => "user_core_login", "user_nicename" => "user_core_nicename", "user_email" => "user_core_email", "display_name" => "user_core_display_name", "nickname" => "user_core_nickname", "first_name" => "user_core_first_name", "last_name" => "user_core_last_name", "description" => "user_core_description", "user_registered" => "user_core_registered", "role" => "user_core_role");
        $data = array();
        $data["ID"] = $cid;
        foreach ($fields as $f => $v) {
            if ($value = $_POST[$v]) {
                $data[$f] = $value;
            }
        }
        if ($_POST["user_core_password"]) {
            if ($cid == get_current_user_id()) {
                $update_cookie = 1;
            }
            $data["user_pass"] = $_POST["user_core_password"];
        }
        $cid = (wp_update_user($data));
    } else {
        $fields = array("user_pass" => "user_core_password", "user_login" => "user_core_login", "user_nicename" => "user_core_nicename", "user_email" => "user_core_email", "display_name" => "user_core_display_name", "nickname" => "user_core_nickname", "first_name" => "user_core_first_name", "last_name" => "user_core_last_name", "description" => "user_core_description", "user_registered" => "user_core_registered", "role" => "user_core_role");
        $data = array();
        foreach ($fields as $f => $v) {
            if ($value = $_POST[$v]) {
                $data[$f] = $value;
            }
        }

        if (!$data["user_login"] && $_REQUEST["user_core_email"]) {
            $data["user_login"] = $_REQUEST["user_core_email"];
        }
        if (!$data["user_email"] && $_REQUEST["user_core_login"]) {
            $data["user_email"] = $_REQUEST["user_core_login"];
        }
//
        $cid = wp_insert_user($data);
// send registration email to admin
        if ($cid && !is_wp_error($cid)) {
            $code = randomstring(16);
            update_user_meta($cid, "credit_to", $_SESSION["referred_by"]);
            update_user_meta($cid, "confirmation_code", $code);
            send_user_email($cid, "confirm_register", array("profile_id" => $cid, "code" => $code));
        } else {
            $error_msg = "";
            foreach ($cid->errors as $f => $v) {
                $error_msg.=$v[0] . "<br>";
            }
        }
    }
    if ($cid && !is_wp_error($cid)) {
        /*
          if ($_POST["autologin"]) {
          $creds = array();
          $creds['user_login'] = $_POST["user_core_login"];
          $creds['user_password'] = $_POST["user_core_password"];
          $creds['remember'] = true;
          $user = wp_signon($creds, false);
          }
         */
        if ($file = basename($_FILES["user_core_avatar"]["name"])) {
            $uploadedfile = $_FILES['user_core_avatar'];
            $upload_overrides = array('test_form' => false);
            $movefile = wp_handle_upload($uploadedfile, $upload_overrides);
//print_r($movefile);
            if ($movefile) {
                $wp_filetype = $movefile['type'];
                $filename = $movefile['file'];
                $wp_upload_dir = wp_upload_dir();
                $attachment = array(
                    'guid' => $wp_upload_dir['url'] . '/' . basename($filename),
                    'post_mime_type' => $wp_filetype,
                    'post_title' => preg_replace('/\.[^.]+$/', '', basename($filename)),
                    'post_content' => '',
                    'post_status' => 'inherit'
                );
                $attach_id = wp_insert_attachment($attachment, $filename);
                $attach_data = wp_generate_attachment_metadata($attach_id, $filename);
                wp_update_attachment_metadata($attach_id, $attach_data);
                update_user_meta($cid, "avatar_id", $attach_id);
            }
        }
        foreach ($_FILES as $f => $v) {
            if (substr($f, 0, 10) == "user_file_") {
                $field = substr($f, 10);
                $uploadedfile = $_FILES[$f];
                if ($uploadedfile['name']) {
                    $upload_overrides = array('test_form' => false);
                    $movefile = wp_handle_upload($uploadedfile, $upload_overrides);
                    if ($movefile) {
                        $wp_filetype = $movefile['type'];
                        $filename = $movefile['file'];
                        $wp_upload_dir = wp_upload_dir();
                        $attachment = array(
                            'guid' => $wp_upload_dir['url'] . '/' . basename($filename),
                            'post_mime_type' => $wp_filetype,
                            'post_title' => preg_replace('/\.[^.]+$/', '', basename($filename)),
                            'post_content' => '',
                            'post_status' => 'inherit'
                        );
                        $attach_id = wp_insert_attachment($attachment, $filename);
                        $attach_data = wp_generate_attachment_metadata($attach_id, $filename);
                        wp_update_attachment_metadata($attach_id, $attach_data);
                    }
                }
                update_user_meta($cid, $field, $attach_id);
            }
        }
        if ($_POST["user_attach"]) {
            $attach_list = $_POST["user_attach"];
            $attach_list = array_unique($attach_list);
            $vals = get_user_meta($cid, "image_gallery", true);
            foreach ($attach_list as $aid) {
                $vals[] = $aid;
            }
            update_user_meta($cid, "image_gallery", $vals);
        }
        foreach ($_POST as $f => $v) {
            if (substr($f, 0, 10) == "user_meta_") {
                $field = substr($f, 10);
                if (is_array($v)) {
                    delete_user_meta($cid, $field);
                    foreach ($v as $val) {
                        add_user_meta($cid, $field, $val);
                    }
                } else {
                    update_user_meta($cid, $field, $v);
                }
            }
        }
        if ($_POST["autologin"]) {
            $user = get_user_by("id", $cid);
            wp_set_current_user($cid, $user->user_login);
            wp_set_auth_cookie($cid);
            do_action('wp_login', $user->user_login);
        }
        if ($_POST["return_url"]) {
            $url = $_POST["return_url"] . $cid;
        } else {
            $url = "http://" . $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"] . "?user_id=" . $cid;
        }
        header("Location:" . $url);
    } else {
        header("Location:" . get_bloginfo("url") . "/login/?signup_error=1&error_msg=" . $error_msg);
    }
}

function send_user_email($user_id = 0, $type, $args = array()) {
    global $wpdb;
    if (!$user_id) {
        $user_id = get_current_user_id();
    }
    $user_info = get_userdata($user_id);
    $to = $user_info->user_email;
    $user_from = get_userdata($args["who"]);
    $from = get_display_name($user_from->ID);
    $to.=", office@thegammalab.com";
    $admin_to = get_option("admin_email");
    if ($type == "confirm_register") {
// Start Email to user after registration //
//$subject = get_option("gamma_email_subject");
        $body = stripslashes(get_option("gamma_email_body_reg"));
        $body = str_replace("{confirm_link}", "<a href='" . get_bloginfo("url") . "/login/?confirm_email=" . $args["code"] . "'>" . get_bloginfo("url") . "/login/?confirm_email=" . $args["code"] . "</a>", $body);
        $subject = get_option("gamma_email_subject_reg");
        $message = '<table rules="all" cellpadding="10">';
        $message .= "<tr><td>" . $body . "</td></tr>";
        $message .= "</table></body></html>";
        send_email($to, $subject, $message);
    } elseif ($type == "confirm_email") {
        $body = stripslashes(get_option("gamma_email_body_confirm"));
        $subject = get_option("gamma_email_subject_confirm");
        $body = str_replace("{confirm_link}", "<a href='" . get_bloginfo("url") . "/login/?confirm_email=" . $args["code"] . "'>" . get_bloginfo("url") . "/login/?confirm_email=" . $args["code"] . "</a>", $body);
        $message = '<table rules="all" cellpadding="10">';
        $message .= "<tr><td>" . $body . "</td></tr>";
        $message .= "</table></body></html>";
        send_email($to, $subject, $message);
    } elseif ($type == "swap_buyer") {
        $body = stripslashes(get_option("gamma_email_body_swap_buyer"));
        $body = str_replace("{box_name}", get_the_title($args["box_id"]), $body);
        $body = str_replace("{user_name}", $from, $body);
        $body = str_replace("{user_link}", get_author_posts_url($args["who"]), $body);

        $subject = get_option("gamma_email_subject_swap_buyer");
        $subject = str_replace("{user_name}", $from, $subject);
        $subject = str_replace("{box_name}", get_the_title($args["box_id"]), $subject);

        $message = '<table rules="all" cellpadding="10" width="100%">';
        $message .= "<tr><td>" . $body . "</td></tr>";
        $message .= "</table></body></html>";

        send_email($to, $subject, $message);
    } elseif ($type == "swap_seller") {
        $body = stripslashes(get_option("gamma_email_body_swap_seller"));
        $body = str_replace("{box_name}", get_the_title($args["box_id"]), $body);
        $body = str_replace("{user_name}", $from, $body);
        $body = str_replace("{user_link}", get_author_posts_url($args["who"]), $body);

        $subject = get_option("gamma_email_subject_swap_seller");
        $subject = str_replace("{user_name}", $from, $subject);
        $subject = str_replace("{box_name}", get_the_title($args["box_id"]), $subject);

        $message = '<table rules="all" cellpadding="10" width="100%">';
        $message .= "<tr><td>" . $body . "</td></tr>";
        $message .= "</table></body></html>";

        send_email($to, $subject, $message);
    } elseif ($type == "box_shipped") {
        $body = stripslashes(get_option("gamma_email_body_shipped"));
        $body = str_replace("{box_name}", get_the_title($args["box_id"]), $body);
        $body = str_replace("{user_name}", $from, $body);
        $body = str_replace("{user_link}", get_author_posts_url($args["who"]), $body);

        $subject = get_option("gamma_email_subject_shipped");
        $subject = str_replace("{user_name}", $from, $subject);
        $subject = str_replace("{box_name}", get_the_title($args["box_id"]), $subject);

        $message = '<table rules="all" cellpadding="10" width="100%">';
        $message .= "<tr><td>" . $body . "</td></tr>";
        $message .= "</table></body></html>";

        send_email($to, $subject, $message);
    } elseif ($type == "box_received") {
        $body = stripslashes(get_option("gamma_email_body_received"));
        $body = str_replace("{box_name}", get_the_title($args["box_id"]), $body);
        $body = str_replace("{user_name}", $from, $body);
        $body = str_replace("{user_link}", get_author_posts_url($args["who"]), $body);

        $subject = get_option("gamma_email_subject_received");
        $subject = str_replace("{user_name}", $from, $subject);
        $subject = str_replace("{box_name}", get_the_title($args["box_id"]), $subject);

        $message = '<table rules="all" cellpadding="10" width="100%">';
        $message .= "<tr><td>" . $body . "</td></tr>";
        $message .= "</table></body></html>";

        send_email($to, $subject, $message);
    } elseif ($type == "complaint_posted") {
        $body = stripslashes(get_option("gamma_email_body_complaint"));
        $body = str_replace("{box_name}", get_the_title($args["box_id"]), $body);
        $body = str_replace("{user_name}", $from, $body);
        $body = str_replace("{user_link}", get_author_posts_url($args["who"]), $body);

        $subject = get_option("gamma_email_subject_complaint");
        $subject = str_replace("{user_name}", $from, $subject);
        $subject = str_replace("{box_name}", get_the_title($args["box_id"]), $subject);

        $message = '<table rules="all" cellpadding="10" width="100%">';
        $message .= "<tr><td>" . $body . "</td></tr>";
        $message .= "</table></body></html>";

        send_email($to, $subject, $message);
    } elseif ($type == "review_posted") {
        $body = stripslashes(get_option("gamma_email_body_review"));
        $body = str_replace("{box_name}", get_the_title($args["box_id"]), $body);
        $body = str_replace("{user_name}", $from, $body);
        $body = str_replace("{user_link}", get_author_posts_url($args["who"]), $body);

        $subject = get_option("gamma_email_subject_review");
        $subject = str_replace("{user_name}", $from, $subject);
        $subject = str_replace("{box_name}", get_the_title($args["box_id"]), $subject);

        $message = '<table rules="all" cellpadding="10" width="100%">';
        $message .= "<tr><td>" . $body . "</td></tr>";
        $message .= "</table></body></html>";

        send_email($to, $subject, $message);
    }
}
