<?php
global $job_id, $match_score, $status_id;
?>
<div class="candidate_box">
    <div class="row">
        <div class="col-lg-2 col-md-3">
            <div class="employee_profile_image">
                <?php echo get_the_post_thumbnail($item["post_id"], "medium"); ?>
            </div>
        </div>
        <div class="col-lg-10 col-md-9">
            <div class="row " style="margin-bottom: 20px; align-items: center;">
                <div class="col-lg-6">
                    <div class="candidate_name">
                        <a href="<?php echo get_the_permalink($item["post_id"]); ?>/?job_id=<?php echo $job_id; ?>"><?php echo get_post_meta($item["post_id"], "anonymous_id", true); ?></a>
                    </div>
                </div>
                <div class="col-lg-6">
                    <?php if (!$status_id || $status_id == 3) { ?>
                        <div class="buttons_float">
                            <a href="#" class="not_now_btn later_comp_interview_link" data-job_id="<?php echo $job_id; ?>" data-profile_id="<?php echo $item["post_id"]; ?>">not right now</a>  
                            <a href="<?php echo get_the_permalink($item["post_id"]); ?>/?job_id=<?php echo $job_id; ?>" class="see_more_btn">See more info</a>
                        </div>                   
                    <?php } elseif ($status_id == 1) { ?>
                        <?php if ($seeker_status_id == 1) { ?>
                            <div class="alert alert-success">You have invited them to interview and they have accepted</div>
                        <?php } else { ?>
                            <div class="alert alert-success">You have invited them to interview and they have not yet responded</div>
                        <?php } ?>
                    <?php } elseif ($status_id == 2) { ?>
                        <div class="alert alert-danger">You have rejected this candidate</div>
                    <?php } elseif ($status_id == 4) { ?>
                        <div class="alert alert-success">You have sent them an offer</div>
                    <?php } ?>

                </div>
            </div>
            <div class="whya_good_match">
                Why they are a good match for "<?php echo get_the_title($job_id); ?>" 
            </div>
            <div class="row  mt-2" style="align-items: center;">
                <?php if ($match_perc) { ?>
                    <div class="col-lg-5">
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
                <div class="col-lg-7">
                    <ul class="candidates_details_box ">
                        <?php
                        $terms = wp_get_post_terms($item["post_id"], "years_experience");
                        if (count($terms)) {
                            $candidate_term = $terms[0]->name;
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
                        $terms = wp_get_post_terms($item["post_id"], "base_salary");
                        if (count($terms)) {
                            $candidate_term = $terms[0]->name;
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
                        $terms = wp_get_post_terms($item["post_id"], "ote");
                        if (count($terms)) {
                            $candidate_term = $terms[0]->name;
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

</div>
