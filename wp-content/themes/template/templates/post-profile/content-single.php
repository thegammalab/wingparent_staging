<?php
the_post();
$results = gamma_get_posts(array("post_type" => "profile", "search" => array("pid" => array(get_the_ID()))));
$item = $results["items"][0];
$profile_id = $item["post_id"];
$job_id = $_GET["job_id"];

$empl_count = get_post_meta($job_id, "selected_employees", true);
for ($i = 0; $i < $empl_count; $i++) {
    if ($profile_id == get_post_meta($job_id, "selected_employees_" . $i . "_profile", true)) {
        $match_perc = get_post_meta($job_id, "selected_employees_" . $i . "_match_perc", true);
    }
}

$empl_count = get_post_meta($job_id, "selected_employees", true);
for ($i = 0; $i < $empl_count; $i++) {
    if ($profile_id == get_post_meta($job_id, "selected_employees_" . $i . "_profile", true)) {
        $match_perc = get_post_meta($job_id, "selected_employees_" . $i . "_match_perc", true);
        $the_status = get_post_meta($job_id, "selected_employees_" . $i . "_company_status", true);
        $seeker_status = get_post_meta($job_id, "selected_employees_" . $i . "_seeker_status", true);
        $interview_id = get_post_meta($job_id, "selected_employees_" . $i . "_interview_id", true);

        $job_statuses = array("match_perc" => $match_perc, "status" => $the_status, "seeker_status" => $seeker_status);
    }
}
?>
<div class="inner_page_bg">
    <div class="container">

        <div class="row">
            <div class="col-md-12">
                <div class="row">
                    <div class="col-lg-2 col-md-3">
                        <div class="employee_profile_image">
                            <?php echo get_the_post_thumbnail($profile_id, "medium"); ?>
                        </div>
                    </div>
                    <div class="col-lg-10 col-md-9">
                        <div class="row " style="margin-bottom: 20px; align-items: center;">
                            <div class="col-lg-9">
                                <div class="candidate_name">
                                    <?php echo get_post_meta($profile_id, "anonymous_id", true); ?>
                                </div>

                            </div>
                            <div class="col-lg-3">
                                <a href="<?php echo get_bloginfo("url") . "/my-account/job-candidates/?job_id=" . ($job_id); ?>" class="go_back_btn"><i class="fa fa-chevron-left"></i>Go Back</a>
                            </div> 
                        </div>
                        <div class="whya_good_match">
                            Why they are a good match for "<?php echo get_the_title($job_id); ?>" 
                        </div>
                        <div class="row  mt-2" style="align-items: center;">
                            <?php if ($match_perc) { ?>
                                <div class="col-lg-4">
                                    <div class="match_score">
                                        <div class="match_stars_empty">
                                            <div class="match_stars_full" style="width:<?php echo ($match_perc / 100) * 100; ?>%;"></div>
                                        </div>
                                        <div class="candidate_match_score">
                                            <span><b><?php echo $match_perc; ?>%</b><br/>Match</span>  
                                        </div>
                                    </div>
                                </div>
                            <?php } ?>
                            <div class="col-lg-8">
                                <ul class="candidates_details_box whiter">
                                    <?php
                                    $terms = wp_get_post_terms($profile_id, "years_experience");
                                    if (count($terms)) {
                                        $candidate_term = $terms[0]->name;
                                        $exp_term = $terms[0];
                                        $terms = wp_get_post_terms($job_id, "years_experience");
                                        $job_term = $terms[0]->name;

                                        if ($job_term == $candidate_term) {
                                            $act = 'checked';
                                        } else {
                                            $act = '';
                                        }
                                        ?>
                                        <li class="<?php echo $act; ?>">
                                            <b><?php echo $candidate_term; ?>yrs</b>experience
                                        </li>
                                        <?php
                                    }
                                    $terms = wp_get_post_terms($profile_id, "base_salary");
                                    if (count($terms)) {
                                        $candidate_term = $terms[0]->name;
                                        $salary_term = $terms[0];
                                        $terms = wp_get_post_terms($job_id, "base_salary");
                                        $job_term = $terms[0]->name;

                                        if ($job_term == $candidate_term) {
                                            $act = 'checked';
                                        } else {
                                            $act = '';
                                        }
                                        ?>
                                        <li class="<?php echo $act; ?>">
                                            <b><?php echo $candidate_term; ?></b>base salary
                                        </li>
                                        <?php
                                    }
                                    $terms = wp_get_post_terms($profile_id, "ote");
                                    if (count($terms)) {
                                        $candidate_term = $terms[0]->name;
                                        $ote_term = $terms[0];
                                        $terms = wp_get_post_terms($job_id, "ote");
                                        $job_term = $terms[0]->name;

                                        if ($job_term == $candidate_term) {
                                            $act = 'checked';
                                        } else {
                                            $act = '';
                                        }
                                        ?>
                                        <li class="<?php echo $act; ?>">
                                            <b><?php echo $candidate_term; ?></b>OTE
                                        </li> 
                                    <?php } ?>
                                </ul>

                            </div>
                        </div>
                    </div>

                </div>
                <hr/>
            </div>
        </div>
        <div class="row mt-4">
            <div class="col-lg-4">
                <div class="next_goals_section">
                    <?php
                    $terms = wp_get_post_terms($profile_id, "next_career_goal");
                    if (count($terms)) {
                        ?>
                        <h3>My Next Goals:</h3>
                        <ul>
                            <?php
                            foreach ($terms as $term) {
                                ?>
                                <li><span><?php echo the_term_thumbnail($term->term_id); ?></span><b><?php echo $term->name; ?></b></li>
                            <?php } ?>
                        </ul>
                        <hr/>
                    <?php } ?>
                    <?php if (!$job_statuses["status"] || $job_statuses["status"] == 3) { ?>
                        <a href="#" class="download_resume invite_comp_interview_link" data-job_id="<?php echo $job_id; ?>" data-profile_id="<?php echo $item["post_id"]; ?>">Invite to Interview</a>
                    <?php } elseif ($job_statuses["status"] == 1) { ?>
                        <?php if ($job_statuses["seeker_status"] == 1) { ?>
                            <div class="alert alert-success">You have invited them to interview and they have accepted</div>
                            <a href="#" class="download_resume" data-toggle="modal" data-target="#send_offer">Send Offer</a>
                            <div class="modal fade" id="send_offer" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="exampleModalLabel">Send an Offer</h5>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                            <form action="" method="POST">
                                                <textarea name="offer" class="form-control" style="min-height:150px;"></textarea>
                                                <hr />
                                                <input type="submit" name="send_offer" class="download_resume" value="Send Offer" />
                                                <input type="hidden" name="job_id" value="<?php echo $job_id; ?>" />
                                                <input type="hidden" name="profile_id" value="<?php echo $profile_id; ?>" />
                                            </form>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        <?php } else { ?>
                            <div class="alert alert-success">You have invited them to interview and they have not yet responded</div>
                        <?php } ?>
                    <?php } elseif ($job_statuses["status"] == 2) { ?>
                        <div class="alert alert-danger">You have rejected this candidate</div>
                    <?php } elseif ($job_statuses["status"] == 4) { ?>
                        <div class="alert alert-success">You have sent them an offer</div>
                    <?php } ?>
                </div>
            </div>
            <div class="col-lg-5">
                <?php
                $terms = wp_get_post_terms($profile_id, "years_experience");
                if (count($terms)) {
                    ?>
                    <div class="candidate_exp">
                        <h3>Years of experience</h3>
                        <span><?php echo $terms[0]->name; ?>yrs</span>
                    </div>
                    <ul class="candidate_increments">
                        <?php
                        $terms = get_terms("years_experience", "hide_empty=0");
                        $act = 'active';
                        foreach ($terms as $term) {
                            ?>
                            <li class="<?php echo $act; ?>"><b><?php echo $term->name; ?></b><span></span></li>
                            <?php
                            if ($term->term_id == $exp_term->term_id) {
                                $act = '';
                            }
                        }
                        ?>
                    </ul>
                    <?php
                }
                $terms = wp_get_post_terms($profile_id, "base_salary");
                if (count($terms)) {
                    ?>
                    <div class="candidate_exp">
                        <h3>desired base salary</h3>
                        <span><?php echo $terms[0]->name; ?></span>
                    </div>
                    <ul class="candidate_increments">
                        <?php
                        $terms = get_terms("base_salary", "hide_empty=0");
                        $act = 'active';
                        foreach ($terms as $term) {
                            ?>
                            <li class="<?php echo $act; ?>"><b><?php echo $term->name; ?></b><span></span></li>
                            <?php
                            if ($term->term_id == $salary_term->term_id) {
                                $act = '';
                            }
                        }
                        ?>

                    </ul>
                    <?php
                }
                $terms = wp_get_post_terms($profile_id, "ote");
                if (count($terms)) {
                    ?>
                    <div class="candidate_exp">
                        <h3>desired OTE</h3>
                        <span><?php echo $terms[0]->name; ?></span>
                    </div>
                    <ul class="candidate_increments">
                        <?php
                        $terms = get_terms("ote", "hide_empty=0");
                        $act = 'active';
                        foreach ($terms as $term) {
                            ?>
                            <li class="<?php echo $act; ?>"><b><?php echo $term->name; ?></b><span></span></li>
                            <?php
                            if ($term->term_id == $ote_term->term_id) {
                                $act = '';
                            }
                        }
                        ?>
                    </ul>
                <?php } ?>
            </div>
            <div class="col-lg-3">
                <ul class="candidate_page_tags">
                    <?php
                    $terms = wp_get_post_terms($profile_id, "technology_experience");
                    if (count($terms)) {
                        ?>
                        <li>
                            <b>Technolody Experience </b> 
                            <?php foreach ($terms as $term) { ?>
                                <span><?php echo $term->name; ?></span>
                            <?php } ?>
                        </li>
                        <?php
                    }
                    $terms = wp_get_post_terms($profile_id, "sales_methodologies");
                    if (count($terms)) {
                        ?>
                        <li>
                            <b>Sales Methodologies</b>
                            <?php foreach ($terms as $term) { ?>
                                <span><?php echo $term->name; ?></span>
                            <?php } ?>
                        </li>
                        <?php
                    }
                    $terms = wp_get_post_terms($profile_id, "vertical_experience");
                    if (count($terms)) {
                        ?>
                        <li>
                            <b>Vertical Experience</b>
                            <?php foreach ($terms as $term) { ?>
                                <span><?php echo $term->name; ?></span>
                            <?php } ?>
                        </li>
                    <?php } ?>
                </ul>
            </div>
        </div>
        <div class="candidate_score_bg">
            <div class="row">
                <div class="col-lg-7">
                    <div class="candidate_score">
                        <div class="row" style="align-items:center;">
                            <?php
                            $perc = get_post_meta($profile_id, "percentage", true);
                            if ($perc) {
                                ?>
                                <div class="col-sm-5">
                                    <canvas id="myChart" style="width:100%;"></canvas>
                                    <script src="http://bernii.github.io/gauge.js/dist/gauge.min.js"></script>
                                    <script>
                                        var opts = {
                                            angle: -0.01, // The span of the gauge arc
                                            lineWidth: 0.35, // The line thickness
                                            radiusScale: 1, // Relative radius
                                            pointer: {
                                                length: 0.6, // // Relative to gauge radius
                                                strokeWidth: 0.084, // The thickness
                                                color: '#000000' // Fill color
                                            },
                                            limitMax: false, // If false, max value increases automatically if value > maxValue
                                            limitMin: false, // If true, the min value of the gauge will be fixed
                                            colorStart: '#17D895', // Colors
                                            colorStop: '#17D895', // just experiment with them
                                            strokeColor: '#EEEEEE', // to see which ones work best for you
                                            generateGradient: true,
                                            highDpiSupport: true, // High resolution support

                                        };
                                        var target = document.getElementById('myChart'); // your canvas element
                                        var gauge = new Gauge(target).setOptions(opts); // create sexy gauge!
                                        gauge.maxValue = 100; // set max gauge value
                                        gauge.setMinValue(0);  // Prefer setter over gauge.minValue = 0
                                        gauge.animationSpeed = 35; // set animation speed (32 is default value)
                                        gauge.set(<?php echo $perc; ?>); // set actual value
                                    </script>
                                </div>
                            <?php } ?>
                            <div class="col-sm-7">
                                <ul>
                                    <li>
                                        <?php if ($perc) { ?>
                                            <b><?php echo $perc; ?>%</b> attainment
                                            <?php
                                        }
                                        $terms = wp_get_post_terms($profile_id, "sales_quota");
                                        if (count($terms)) {
                                            if ($perc) {
                                                echo ' of a <br>';
                                            }
                                            ?>
                                            <b><?php echo $terms[0]->name; ?></b> sales quota
                                        <?php } ?>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-5">
                    <ul class="attaiment_right_section">
                        <?php
                        $terms = wp_get_post_terms($profile_id, "average_deal_size");
                        if (count($terms)) {
                            ?>
                            <li>
                                <b>Average Deal Size </b>
                                <?php
                                foreach ($terms as $term) {
                                    ?>
                                    <span><?php echo $term->name; ?></span>
                                <?php } ?>
                            </li>
                            <?php
                        }
                        $terms = wp_get_post_terms($profile_id, "segment_experiences");
                        if (count($terms)) {
                            ?>
                            <li>
                                <b>Segment Experience</b>
                                <?php
                                foreach ($terms as $term) {
                                    ?>
                                    <span><?php echo $term->name; ?></span>
                                <?php } ?>
                            </li>
                            <?php
                        }
                        $terms = wp_get_post_terms($profile_id, "source_of_leads");
                        if (count($terms)) {
                            ?>
                            <li>
                                <b>Main Source of Leads</b>
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
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="next_goals_section">
                    <?php if ($item["post_content"]) { ?>
                        <h3>About the Candidate</h3>
                        <?php echo $item["post_content"]; ?>
                    <?php } ?>
                    <hr/>
                    <?php if (!$job_statuses["status"]) { ?>
                        <a href="#" class="later_interview_link later_comp_interview_link" data-job_id="<?php echo $job_id; ?>" data-profile_id="<?php echo $item["post_id"]; ?>" style="padding: 25px 0; text-transform: uppercase; display: inline-block; font-size: 12px;" data-job_id="">Not Right Now</a>
                    <?php } ?>
                    <div class="interview_or_not">
                        <?php if (!$job_statuses["status"] || $job_statuses["status"] == 2 || $job_statuses["status"] == 3) { ?> { ?>
                            <a href="#" class="see_more_btn invite_comp_interview_link pull-right" data-job_id="<?php echo $job_id; ?>" data-profile_id="<?php echo $item["post_id"]; ?>">ask to interview</a>  
                            <a href="#" class="not_now_btn reject_comp_interview_link pull-right" data-job_id="<?php echo $job_id; ?>" data-profile_id="<?php echo $item["post_id"]; ?>">not interested</a>  
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    </div>  
</div>