<?php
$profile_id = get_user_meta(get_current_user_id(), "profile_id", true);
$empl_count = get_post_meta($item["post_id"], "selected_employees", true);
for ($i = 0; $i < $empl_count; $i++) {
    if ($profile_id == get_post_meta($item["post_id"], "selected_employees_" . $i . "_profile", true)) {
        $the_status = get_post_meta($item["post_id"], "selected_employees_" . $i . "_company_status", true);
        $seeker_status = get_post_meta($item["post_id"], "selected_employees_" . $i . "_seeker_status", true);
        $interview_id = get_post_meta($item["post_id"], "selected_employees_" . $i . "_interview_id", true);
    }
}
?>
<div class="active_job_box">
    <div class="row">
        <div class="col-md-12">
            <div class="row ">
                <div class="col-md-9">
                    <div class="job_title">
                        <a href="<?php echo $item["post_permalink"] ?>"><?php echo $item["post_title"] ?></a>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="job_location">
                        <i class="fas fa-map-marker-alt"></i> <?php echo $item["meta_location"] ?>
                    </div>
                </div>
            </div>
            <div class="border-bottom"></div>
            <div class="row">
                <div class="col-lg-7">
                    <ul class="candidates_details_box ">
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
                <div class="col-lg-5">
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
            <?php $comp_id = $item["meta_company_profile"]; ?>
            <div class="border-bottom"></div>
            <div class="row" style="align-items: center;margin-bottom: -10px;">
                <div class="col-md-7">
                    <div class="new_job_box">
                        <a href="<?php echo get_permalink($comp_id); ?>" class="company_logo"><?php echo get_the_post_thumbnail($comp_id, "small"); ?></a> 
                        <div class="company_name_new_job"><h4><?php echo get_the_title($comp_id); ?></h4>
                            <h5><?php echo get_post_meta($comp_id, "location", true); ?></h5>
                        </div>
                    </div>
                </div>
                <div class="col-md-5">
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
            </div>
        </div>
    </div>
</div>