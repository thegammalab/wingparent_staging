<?php
global $wpdb;
$profile_id = get_user_meta(get_current_user_id(), "profile_id", true);

the_post();
$results = gamma_get_posts(array("post_type" => "company", "search" => array("pid" => array(get_the_ID()))));
$item = $results["items"][0];
?>
<div class="inner_page_bg">
    <div class="container">
        <div class="row mb-4">
            <div class="col-lg-9">
                <div class="row">
                    <?php if ($item["featured_img_medium"]) { ?>
                        <div class="col-md-3">
                            <div class="company_logo float-left">
                                <?php echo $item["featured_img_medium"]; ?>
                            </div>
                        </div>
                    <?php } ?>
                    <div class="col-md-9">
                        <div class="left_emp_acc_details">
                            <h2><?php echo $item["post_title"]; ?></h2>
                            <?php if ($item["meta_location"]) { ?>
                                <div class="job_location_sidebar float-left pt-0">
                                    <i class="fa fa-map-marker"></i> <?php echo $item["meta_location"]; ?>
                                </div>
                            <?php } ?>
                            <?php if ($item["meta_url"]) { ?>
                                <a href="<?php echo $item["meta_url"]; ?>" target="_blank" class="company_url"><?php echo $item["meta_url"]; ?></a>
                            <?php } ?>
                        </div>

                    </div>
                </div>
            </div>
            <div class="col-lg-3">
                <a href="#" class="go_back_btn" onclick="history.go(-1)"><i class="fa fa-chevron-left"></i>Go Back</a>
            </div>
        </div>
        <hr class="my-4"/>
        <div class="row">
            <div class="col-md-7">
                <div class="job_section_content">
                    <?php if ($item["tax_string_company_size"]) { ?>
                        <div class="company_size_tag mt-0"><h4>Company size:<span><?php echo $item["tax_string_company_size"]; ?></span></h4></div>
                        <hr />
                    <?php } ?>
                    <h2>Company Overview</h2>
                    <?php echo $item["post_content"]; ?>

                </div>
            </div>
            <div class="col-md-5">
              <?php
              $results = $wpdb->get_results("SELECT `post_id`,`meta_key` FROM `wp_postmeta` WHERE `meta_key` LIKE 'selected_employees_%_company_status' AND (`meta_value`='1' OR `meta_value`='4') AND `post_id` IN (SELECT `post_id` FROM `wp_postmeta` WHERE `meta_key` LIKE 'selected_employees_%_profile' AND `meta_value`='".$profile_id."')  AND `post_id` IN (SELECT `post_id` FROM `wp_postmeta` WHERE `meta_key`='company_profile' AND `meta_value`='".$item["post_id"]."')");
              if(count($results)){
               ?>
                <div class="job_sidebar">
                    <h2>Jobs I am a match for</h2>
                </div>
                <?php
                foreach($results as $item){
                  $job_id = $item->post_id;
                  $i_val_pieces = explode("_", $item->meta_key);
                  $i_val = $i_val_pieces[2];
                  $seeker_status = get_post_meta($job_id, "selected_employees_" . $i_val . "_seeker_status", true);
                  ?>
                  <div class="job_match_box">
                    <h2><a href="<?php echo get_the_permalink($job_id); ?>"><?php echo get_the_title($job_id); ?></a></h2>
                    <hr/>
                    <ul class="job_details_box"><?php
                        $terms = wp_get_post_terms($job_id, "years_experience");
                        if (count($terms)) {
                            ?>
                            <li><b><?php echo $terms[0]->name; ?>yrs</b>experience</li>
                            <?php
                        }
                        $terms = wp_get_post_terms($job_id, "base_salary");
                        if (count($terms)) {
                            ?>
                            <li><b><?php echo $terms[0]->name; ?></b>base salary</li>
                            <?php
                        }
                        $terms = wp_get_post_terms($job_id, "ote");
                        if (count($terms)) {
                            ?>
                            <li><b><?php echo $terms[0]->name; ?></b>OTE</li>
                        <?php } ?></ul>
                    <hr/>
                    <ul class="job_tags"><?php
                        $terms = wp_get_post_terms($job_id, "sales_methodologies");
                        if (count($terms)) {
                            ?>
                            <li>
                                <b>Sales Methodology: </b>
                                <?php
                                foreach ($terms as $term) {
                                    ?>
                                    <span><?php echo $term->name; ?></span>
                                <?php } ?>
                            </li>
                            <?php
                        }
                        $terms = wp_get_post_terms($job_id, "technology_experience");
                        if (count($terms)) {
                            ?>
                            <li>
                                <b>Tech Experience: </b>
                                <?php
                                foreach ($terms as $term) {
                                    ?>
                                    <span><?php echo $term->name; ?></span>
                                <?php } ?>
                            </li>
                        <?php } ?></ul>
                    <hr/>
                    <?php if (!$seeker_status || $seeker_status == 3) { ?>
                        <div class="buttons_float">
                            <a href="#" class="not_now_btn later_interview_link" data-job_id="<?php echo $item["post_id"]; ?>">not right now</a>
                            <a href="<?php echo $item["post_permalink"]; ?>" class="see_more_btn">See more info</a>
                        </div>
                    <?php } elseif ($seeker_status == 1) { ?>
                        <?php if ($the_status == 4) { ?>
                            <div class="alert alert-success">You have Received an offer</div>

                        <?php } else { ?>
                            <div class="alert alert-success">You have accepted this interview request</div>
                        <?php } ?>
                    <?php } elseif ($seeker_status == 2) { ?>
                        <div class="alert alert-danger">You have rejected this interview request</div>
                    <?php } elseif ($seeker_status == 4) { ?>
                        <div class="alert alert-success">You have accepted the offer</div>
                    <?php } ?>
                </div>
              <?php }
            } ?>
            </div>
        </div>

    </div>
</div>
