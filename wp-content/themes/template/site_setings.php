<?php
$setting_items = array(
        //social links
//    array("name" => "check", "label" => "Social Links", "type" => "title"),
//    array("name" => "linkedin_link", "label" => "LinkedIn url", "type" => "text"),
//    array("name" => "facebook_link", "label" => "Facebook url", "type" => "text"),
//    array("name" => "twitter_link", "label" => "Twitter url", "type" => "text"),
//    array("name" => "google_link", "label" => "Google url", "type" => "text"),
//    array("name" => "check", "label" => "Category settings", "type" => "title"),
//    array("name" => "icon_class", "label" => "Default category icon", "type" => "text"),
);
$setting_items = json_encode($setting_items);
update_option('gamma_site_settings', $setting_items);
show_admin_bar(false);
/* ==============================================================
  HOME METABOX
  ============================================================== */
/* BANNER */
//
gamma_register_post_type("daycares", array("name" => "Daycares", "singular_name" => "Daycare", "position" => 6, "slug" => "daycare"));
gamma_register_taxonomy("region", array("daycares"), array("name" => "Regions", "singular_name" => "Region", "slug" => "region"));
gamma_register_post_type("external_links", array("name" => "External Links", "singular_name" => "External Link", "position" => 6, "slug" => "external_links"));

// gamma_register_taxonomy("segmentation", array("product"), array("name" => "Segments", "singular_name" => "Segment", "slug" => "segment"));



// gamma_register_post_type("company", array("name" => "Companies", "singular_name" => "Company", "position" => 6, "slug" => "company"));
// gamma_register_post_type("job", array("name" => "Jobs", "singular_name" => "Job", "position" => 6, "slug" => "job"));
// gamma_register_post_type("interviews", array("name" => "Interviews", "singular_name" => "Interview", "position" => 6, "slug" => "interview"));
// gamma_register_post_type("testimonials", array("name" => "Testimonials", "singular_name" => "Testimonial", "position" => 6, "slug" => "testimonials"));
// gamma_register_post_type("faqs", array("name" => "FAQs", "singular_name" => "FAQ", "position" => 6, "slug" => "faq"));
//
// gamma_register_taxonomy("faq_types", array("faqs"), array("name" => "FAQ Types", "singular_name" => "FAQ Type", "slug" => "faqs"));
//
// gamma_register_taxonomy("years_experience", array("profile", "job"), array("name" => "Years of Experience", "singular_name" => "Years of Experience", "slug" => "years_experience"));
// gamma_register_taxonomy("technology_experience", array("profile", "job"), array("name" => "Technology Experience", "singular_name" => "Technology Experience", "slug" => "technology_experience"));
// gamma_register_taxonomy("sales_methodologies", array("profile", "job"), array("name" => "Sales Methodologies", "singular_name" => "Sales Methodology", "slug" => "sales_methodologies"));
// gamma_register_taxonomy("base_salary", array("profile", "job"), array("name" => "Desired Base Salary", "singular_name" => "Desired Base Salary", "slug" => "base_salary"));
// gamma_register_taxonomy("ote", array("profile", "job"), array("name" => "OTE", "singular_name" => "OTE", "slug" => "ote"));
//
// gamma_register_taxonomy("next_career_goal", array("profile"), array("name" => "Next Career Goals", "singular_name" => "Next Career Goal", "slug" => "next_career_goal"));
// gamma_register_taxonomy("sales_quota", array("profile"), array("name" => "Most Recent Sales Quotas", "singular_name" => "Most Recent Sales Quota", "slug" => "sales_quota"));
// gamma_register_taxonomy("average_deal_size", array("profile"), array("name" => "Average Deal Sizes", "singular_name" => "Average Deal Size", "slug" => "average_deal_size"));
// gamma_register_taxonomy("segment_experiences", array("profile"), array("name" => "Segment Experiences", "singular_name" => "Segment Experience", "slug" => "segment_experiences"));
// gamma_register_taxonomy("source_of_leads", array("profile"), array("name" => "Sources of Leads", "singular_name" => "Source of Leads", "slug" => "source_of_leads"));
// gamma_register_taxonomy("vertical_experience", array("profile"), array("name" => "Vertical Experiences", "singular_name" => "Vertical Experience", "slug" => "vertical_experience"));
//
// gamma_register_taxonomy("company_size", array("company"), array("name" => "Company Sizes", "singular_name" => "Company Size", "slug" => "company_size"));



//gamma_register_post_type("faq", array("name" => "FAQ Items", "singular_name" => "FAQ Item", "position" => 6, "slug" => "faq"));
//gamma_register_post_type("services", array("name" => "Services", "singular_name" => "Service", "position" => 6, "slug" => "service"));
//gamma_register_post_type("testimonials", array("name" => "Testimonials", "singular_name" => "Testimonial", "position" => 6, "slug" => "testimonial"));
//gamma_register_post_type("seo_pages", array("name" => "SEO Pages", "singular_name" => "SEO Pages", "position" => 6, "slug" => "seo_page"));
//
//gamma_register_post_type("research", array("name" => "Research", "singular_name" => "Research", "position" => 6, "slug" => "research-article"));
//
//
//gamma_register_taxonomy("FAQ_categories", array("faq"));
//
//
//gamma_register_taxonomy("included_service", array("services"));
//gamma_register_taxonomy("checklist_service", array("services"));
//gamma_register_taxonomy("addon_service", array("services"));
//
//
//gamma_register_taxonomy("location", array("seo_pages"));
///////////////////////////////////// REGISTER WIDGETS  //////////////////////////////////////

gamma_add_menu("main_menu_header");
// gamma_add_menu("main_menu_header_logged");

gamma_add_menu("footer_menu1");
gamma_add_menu("footer_menu2");
gamma_add_menu("footer_menu3");


/////////////////////////// Admin Colors & Logos ///////////////////////////

update_option("admin_logo_height", 90); // The height of the logo that is shown in the admin sidebar
update_option("login_logo_height", 90); // The height of the logo that is shown on the admin login page
update_option("color1", "#1b488e"); // main color 1
update_option("color2", "#2ea3e0"); // main color 2
update_option("color3", "#FFFFFF"); // The height of the logo that is shown on the admin login page
update_option("color4", "#333"); // The height of the logo that is shown on the admin login page
update_option("color5", "#FFFFFF"); // sidebar background
update_option("color6", "#333"); // sidebar background

gamma_add_widget_area("article_sidebar");
gamma_add_widget_area("article_page_sidebar");


gamma_register_widget("reviews_widget", "Reviews Widget", "", "reviews_widget", array());

function reviews_widget() {
    global $post;

    $cat_id = get_post_meta($post->ID, "review_cat_id", true);
    $cat_term = get_term_by('id', $cat_id, "review_category");
    ?>
    <div class = "article_sidebar_reviews">
        <div class = "sidebar_title">
            <h4><?php echo $cat_term->name; ?> Reviews</h4>
        </div>
        <?php
        $args = array();
        $args["post_type"] = "reviews";
        $args["search"]["tax_review_category"] = array($cat_id);
        $args["page"] = 0;
        $args["per_page"] = 4;

        $results = gamma_get_posts($args);
        foreach ($results["items"] as $item) {

            $values = get_field('site_reviews', $item["post_id"]);
            $total_reviews = 0;
            $star_rating = 0;
            $rating = 0;
            if (count($values)) {
                foreach ($values as $value) {
                    $total_reviews += $value["reviews_number"];
                    $star_rating += $value["reviews_number"] * $value["star_rating"];
                    $rating += $value["reviews_number"] * $value["rating"];
                }
            }
            $avg_rating = $rating / $total_reviews;
            $avg_stars = ($star_rating / $total_reviews) * 20;
            $rating_name = array(0 => "Horrible", 1 => "Horrible", 2 => "Horrible", 3 => "Terrible", 4 => "Bad", 5 => "OK", 6 => "Good", 7 => "Good", 8 => "Very Good", 8.5 => "Great", 9 => "Excellent", 10 => "Perfect");
            ?>
            <div class="item">
                <a href = "<?php echo $item["post_permalink"]; ?>" class = "reviews_sidebar">
                    <div class = "row">
                        <div class = "col-6">
                            <div class = "service_logo">
                                <div class = "service_img">
                                    <?php echo $item["featured_img_medium"]; ?>
                                </div>
                            </div>
                        </div>
                        <div class = "col-6">
                            <div class = "sidebar_review">
                                <div class = "review_name">
                                    <?php echo round($avg_rating, 1); ?> - <?php echo $rating_name[round($avg_rating)]; ?>
                                </div>
                                <div class = "review_stars_empty">
                                    <div class = "review_full">
                                    </div>
                                </div>
                                <div class = "read_review_onsidebar" style = "">Read review</div>

                            </div>
                        </div>
                    </div>
                </a>

            </div>
        <?php } ?>

    </div>
    <?php
}

gamma_register_widget("recent_articles_widget", "Recent Articles", "", "recent_articles_widget", array());

function recent_articles_widget() {
    ?>
    <div class="recent_posts">
        <h5>Recent Posts</h5>
        <ul>
            <?php
            $args = array();
            $args["post_type"] = "articles";
            $args["page"] = 0;
            $args["per_page"] = 5;

            $results = gamma_get_posts($args);
            foreach ($results["items"] as $item) {
                ?>
                <li><a href="<?php echo $item["post_permalink"]; ?>"><?php echo $item["post_title"]; ?></a></li>
            <?php } ?>
        </ul>
    </div>
    <?php
}

gamma_register_widget("recent_articles_widget2", "Category List", "", "recent_articles_widget2", array());

function recent_articles_widget2() {
    ?>
    <div class="recent_posts">
        <h5>Categories</h5>
        <ul>
            <?php
            $terms = get_terms("article_category");
            foreach ($terms as $term) {
                ?>
                <li><a href="<?php echo get_term_link($term); ?>"><?php echo $term->name; ?></a></li>
                <?php
            }
            ?>
        </ul>
    </div>
    <?php
}
