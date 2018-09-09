<?php
the_post();
$results = gamma_get_posts(array("post_type" => "job", "search" => array("pid" => array(get_the_ID()))));
$item = $results["items"][0];

$profile_id = get_user_meta(get_current_user_id(), "profile_id", true);
$comp_id = $item["meta_company_profile"];
$url = get_post_meta($comp_id, "url", true);

$empl_count = get_post_meta($item["post_id"], "selected_employees", true);
for ($i = 0; $i < $empl_count; $i++) {
    if ($profile_id == get_post_meta($item["post_id"], "selected_employees_" . $i . "_profile", true)) {
        $the_status = get_post_meta($item["post_id"], "selected_employees_" . $i . "_company_status", true);
        $seeker_status = get_post_meta($item["post_id"], "selected_employees_" . $i . "_seeker_status", true);
        $interview_id = get_post_meta($item["post_id"], "selected_employees_" . $i . "_interview_id", true);

    }
}
?>


<div class="inner_page_bg">
    <div class="container">
        <div class="row">
            <div class="col-lg-9">
                <div class="candidates_top_details">
                    <h1><?php echo $item["post_title"] ?></h1>
                    <div class="job_location float-left pt-0">
                        <i class="fas fa-map-marker-alt"></i> <?php echo $item["meta_location"] ?>
                    </div>
                    <a href="<?php echo $url; ?>" target="_blank" class="company_url"><?php echo $url; ?></a>
                </div>
            </div>
            <div class="col-lg-3">
                <a href="<?php echo get_bloginfo("url") . "/my-account/"; ?>" class="go_back_btn"><i class="fa fa-chevron-left"></i>Go Back</a>
            </div>
        </div>
        <hr />
        <div class="row">
            <div class="col-lg-5">
                <ul class="job_candidates_page_box">
                    <?php
                    $terms = wp_get_post_terms($item["post_id"], "years_experience");
                    if (count($terms)) {
                        ?>
                        <li><b><?php echo $terms[0]->name; ?>yrs</b>experience</li>
                        <?php
                    }
                    $terms = wp_get_post_terms($item["post_id"], "base_salary");
                    if (count($terms)) {
                        ?>
                        <li><b><?php echo $terms[0]->name; ?></b>base salary</li>
                        <?php
                    }
                    $terms = wp_get_post_terms($item["post_id"], "ote");
                    if (count($terms)) {
                        ?>
                        <li><b><?php echo $terms[0]->name; ?></b>OTE</li> 
                    <?php } ?>
                </ul>
            </div>
            <div class="col-lg-7">
                <ul class="job_tags">
                    <?php
                    $terms = wp_get_post_terms($item["post_id"], "sales_methodologies");
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
                    $terms = wp_get_post_terms($item["post_id"], "technology_experience");
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
                    <?php } ?>
                </ul>
            </div>
        </div>


        <hr />
        <div class="row">
            <div class="col-md-7">
                <div class="job_section_content">
                    <h2>Job Overview</h2>
                    <?php echo apply_filters('the_content', $item["post_content"]); ?>
                    <h3>Responsibilities</h3>
                    <?php echo $item["meta_responsabilities"]; ?>
                    <div class="job_actions_item">
                        <?php if (!$seeker_status || $seeker_status == 3) { ?>
                            <a href="#" class="later_interview_link" data-job_id="<?php echo $item["post_id"]; ?>" style="padding: 25px 0; text-transform: uppercase; display: inline-block; font-size: 12px;" data-job_id="<?php echo $job->ID; ?>">Not Now</a>
                            <a href="#" class="see_more_btn pull-right accept_interview_link" data-job_id="<?php echo $item["post_id"]; ?>">Accept Interview</a>  
                            <a href="#" class="not_now_btn pull-right reject_interview_link" data-job_id="<?php echo $item["post_id"]; ?>">not interested</a>  
                        <?php } elseif ($seeker_status == 1) { ?>
                            <?php if ($the_status == 4) { ?>
                                <div class="alert alert-success">You have Received an offer</div>
                                <p><?php echo get_post_meta($interview_id, "offer_description", true); ?></p>
                                <a href="#" class="download_resume accept_offer_link" data-job_id="<?php echo $item["post_id"]; ?>" data-interview_id="<?php echo $interview_id; ?>">Accept Offer</a>
                                
                            <?php } else { ?>
                                <div class="alert alert-success">You have accepted this interview request</div>
                            <?php } ?>
                        <?php } elseif ($seeker_status == 2) { ?>
                            <div class="alert alert-danger">You have rejected this interview request</div>
                        <?php } elseif ($seeker_status == 4) { ?>
                            <div class="alert alert-success">You have accepted the offer</div>
                        <?php } ?>


                    </div>
                </div>
            </div>

            <div class="col-md-5">
                <div class="job_sidebar">
                    <h2>Company Profile</h2>
                </div>
                <div class="company_match_box">
                    <a href="<?php echo get_permalink($comp_id); ?>" class="company_logo"><?php echo get_the_post_thumbnail($comp_id, "medium"); ?></a>
                    <hr>
                    <h2><?php echo get_the_title($comp_id); ?></h2>
                    <div class="job_location_sidebar float-left pt-0">
                        <i class="fas fa-map-marker-alt"></i> <?php echo get_post_meta($comp_id, "location", true); ?>
                    </div>
                    <?php ?>
                    <a href="<?php echo $url; ?>" target="_blank" class="company_url"><?php echo $url; ?></a>
                    <hr>
                    <div class="company_size_tag">
                        <?php
                        $terms = wp_get_post_terms($comp_id, "company_size");
                        if (count($terms)) {
                            ?>
                            <h4>Company size: <span><?php echo $terms[0]->name; ?></span></h4>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>

    </div>  
</div>