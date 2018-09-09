<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

$author = get_user_by('slug', get_query_var('author_name'));

$per_page = 9;
$page_no = $_GET["page_no"];
if (!$page_no) {
    $page_no = 0;
}
$post_type = "boxes";
$term = get_query_var('term');
$tax_name = get_query_var('taxonomy');
$args = array();
$args["post_type"] = $post_type;
$args["post_template"] = dirname(__FILE__) . "/templates/post-boxes/content-item.php";
$args["search"] = $_GET;
$args["search"]["author"] = array($author->ID);
$args["search"]["post_status"] = array("publish");
$args["page"] = $page_no;
$args["per_page"] = $per_page;
$args["no_results_html"] = '<h3 class="no_results">No results</h3>';
$str = "ajax_action=load_posts&post_type=" . $post_type . "&per_page=" . $per_page;
foreach ($_GET as $f => $v) {
    if (is_array($v)) {
        foreach ($v as $vs) {
            $str.="&" . $f . "[]=" . $vs;
        }
    } else {
        $str.="&" . $f . "=" . $v;
    }
}
if ($term) {
    $str.="&tax_slug_" . $tax_name . "=" . $term;
}
$results = gamma_get_posts($args);
?>
<div class="background-listings">
    <div class="container">
        <div class="row" style="">
            <div class="col-xs-4 col-sm-2 col-md-1 product-picture avatar-picture no-padding" style="margin: auto; float: none; padding-top: 30px;">
                <?php echo get_avatar($author->ID); ?>
            </div>
            <div class="col-xs-12" style="text-align: center;margin-top: 10px;">

                <h1 class="title-margin" style="margin-bottom: 0;"><span style="background: #FFF; padding: 0 20px;"><?php echo get_display_name($author->ID); ?></span></h1>
                <div style="text-align:center;">
                    <div class="review_div"><div class="review_div_active" style="width: <?php echo 100 * get_user_score($author->ID) / 5; ?>%;"></div></div>
                </div>
                <div style="padding-top: 10px; margin-bottom: 10px;">
                    <p style=""><?php echo get_user_meta($author->ID, "description", true); ?></p>
                </div>
                <div style="margin-bottom: 40px; overflow: hidden; text-align: center;">
                    <?php
                    if ($author->ID != get_current_user_id()) {
                        if (is_following($author->ID)) {
                            ?>
                            <a href="#" class="button-unfollow">Unfollow this User</a>
                        <?php } elseif (get_current_user_id()) { ?>
                            <a href="#" class="button-follow">Follow this User</a>
                            <?php
                        }
                    }
                    ?>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="row" id="listing_box">
                    <?php
                    echo $results["output"];
                    echo '<input type="hidden" name="total_pages" id="total_pages" value="' . ceil($results["total_posts"] / $per_page) . '" />';
                    echo '<input type="hidden" name="current_page" id="current_page" value="' . $page_no . '" />';
                    ?>
                </div>
                <div class="col-xs-12 more-listings">
                    <a href=""><img src="<?php echo get_bloginfo("template_directory"); ?>/assets/images/more-listings.png"/>
                        <h3 class=""> More Listings</h3></a>
                </div>
            </div>
        </div>
        <div style="padding: 20px 0 40px; overflow: hidden;">
            <h1 class="title-margin" style="margin-bottom: 0; clear: both; text-align: center; margin-bottom: 40px; font-size: 30px; color: #999;"><span style="padding: 0 20px;">User Reviews</span></h1>
            <?php
            $follow_ids = array();
            $results = $wpdb->get_results("SELECT * FROM `wp_reviews` WHERE `for_id`='" . $author->ID . "'");
            foreach ($results as $item) {
                $uid = $item->for_id;
                $user_info = get_userdata($uid);
                $follow_ids[] = $uid;
                ?>
                <div style="overflow:hidden; clear: both; padding: 20px 0;">
                    <div class="col-xs-4 review_item">
                        <div class="row">
                            <div class="col-xs-4 product-picture no-padding avatar-picture">
                                <a href="<?php echo get_author_posts_url($uid); ?>"><?php echo get_avatar($uid); ?></a>
                            </div>
                            <div class="col-xs-8" style="text-align: left;">
                                <h3 class="user-name" style="padding-left: 0;"><a href="<?php echo get_author_posts_url($uid); ?>"><?php echo get_display_name($uid); ?></a></h3>
                                <h3 class="count-listings" style="padding: 0;"><a href=""><?php echo get_posted_boxes($uid); ?></a></h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-xs-8" style="text-align: left; border-left: 1px solid #EEE;">
                        <div style="color: #000; font-weight: bold;">Rating: <div class="review_div"><div class="review_div_active" style="width: <?php echo 100 * $item->score / 5; ?>%;"></div></div> </div>
                        <hr />
                        <div><?php echo $item->description; ?></div>
                    </div>
                </div>
                <?php
            }
            if (!count($results)) {
                echo '<h3 style="text-align:center; color: #CCC; height:90px; font-size:30px; font-weight:100;">Nobody has left a review for this user yet</h3>';
            }
            ?>
        </div>
    </div>
</div>
<script>
    function set_follow() {
        jQuery(".button-follow").unbind("click").click(function () {
            var thi = jQuery(this);
            $.ajax({
                url: "<?php echo get_bloginfo("url") . "/?ajax_action=follow_user&user_id=" . $author->ID; ?>",
                context: document.body,
            }).done(function (data) {
                thi.animate({opacity: 0}, 300, function () {
                    thi.html("Unfollow this user").removeClass("button-follow").addClass("button-unfollow").animate({opacity: 1}, 300, function () {
                        set_unfollow();
                    });
                });
            });
            return false;
        });

    }

    function set_unfollow() {
        jQuery(".button-unfollow").unbind("click").click(function () {
            var thi = jQuery(this);
            $.ajax({
                url: "<?php echo get_bloginfo("url") . "/?ajax_action=unfollow_user&user_id=" . $author->ID; ?>",
                context: document.body,
            }).done(function (data) {
                thi.animate({opacity: 0}, 300, function () {
                    thi.html("Follow this user").removeClass("button-unfollow").addClass("button-follow").animate({opacity: 1}, 300, function () {
                        set_follow();
                    });

                });

            });
            return false;
        });
    }

    jQuery(document).ready(function () {
        set_follow();
        set_unfollow();
    });
</script>