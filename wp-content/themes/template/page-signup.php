<?php
$new_forms = array("nl"=>2,"fr"=>4,"en"=>5);
$existing_forms = array("nl"=>3,"fr"=>6,"en"=>7);
 ?>
<div class="container mt-6">
  <div class="row mb-5">
    <div class="col-lg-4 d-none d-lg-block">
      <div class="login_left_section">
        <img src="<?php echo get_bloginfo("template_directory"); ?>/assets/images/happy.png"/>
      </div>
    </div>
    <div class="col-lg-8">
      <div class="login_form mt-4 mb-4">
        <h2><?php _e("Login","wingparent"); ?></h2>
        <p><?php _e("Please enter your login info below","wingparent"); ?></p>
        <?php if($_GET["login"]=="failed"){ ?>
          <div class="alert alert-danger"><?php _e("Your login information does not match our records, please try again","wingparent"); ?></div>
        <?php } ?>
        <?php if($_GET["error"]=="invalidkey"){ ?>
          <div class="alert alert-danger"><?php _e("The key you have used has expired, or has already been used","wingparent"); ?></div>
        <?php } ?>
        <form name="loginform" id="loginform" action="<?php echo wp_login_url( ); ?> " method="post" class="form-inline">
          <div class="form-group w-100">
            <?php
            ?>
            <div class="input-group">
              <input type="text" name="log" id="user_login" autocomplete="off" class="form-input form-control-lg" placeholder="<?php _e("Email","wordpress"); ?>" id="email">
            </div>
            <div class="input-group">
              <input type="password" name="pwd" id="user_pass" autocomplete="off" class="form-input form-control-lg br-0" placeholder="<?php _e("Password","wordpress"); ?>" id="pass">

            </div>
            <div class="input-group">
              <input type="submit" name="wp-submit" id="wp-submit" class="btn btn-secondary" value="<?php _e("Login","wordpress"); ?>">
              <input type="hidden" name="redirect_to" value="<?php echo get_bloginfo("url");?>/my-account/">

            </div>
          </div>
        </div>
      </form>
      <p style="font-size:12px; padding-left: 5px; margin-top:-10px;"><a href="<?php echo get_bloginfo("url"); ?>/my-account/lost-password/"><?php _e("Forgot your password?"); ?></a></p>
    </div>
  </div>
  <div class="row_bg"></div>
  <div class="row mb-5">
    <div class="col-lg-4 d-none d-lg-block">
      <div class="login_left_section">
        <img src="<?php echo get_bloginfo("template_directory"); ?>/assets/images/mother.png"/>
      </div>
    </div>
    <div class="col-lg-8">
      <div class="login_form mt-4 mb-4">
        <h2><?php _e("Create Account","wingparent"); ?></h2>
        <p><?php _e("Please select your region and see if Wingparent is available for your daycare","wingparent"); ?></p>
          <form action="" id="daycare_search" class="form-inline">
            <div id="region_select_error" style="display:none;" class="alert alert-danger w-100"><?php _e("Please select a region first","wingparent"); ?></div>
            <div class="form-group w-100">
              <div class="input-group">
                <select name="Select your region" id="region_select">

                  <option value=""><?php _e("Select your region","wingparent"); ?></option>
                  <?php
                  $regions = get_terms("region","hide_empty=0");
                  foreach($regions as $region){ ?>
                    <option value="<?php echo $region->term_id; ?>"><?php echo $region->name; ?></option>
                  <?php } ?>
                </select>
              </div>
              <div class="input-group">
                <button class="btn btn-secondary" id="search_signup"><?php _e("Search","wordpress"); ?>></button>
              </div>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>

  <script>
  jQuery(document).ready(function(){
    jQuery("#daycare_search").submit(function(e){
      e.preventDefault();
      return false;
    })

    jQuery("#search_signup").click(function(){
      if(jQuery("#region_select").val()){
        jQuery("#region_select_error:visible").slideUp();
        jQuery(".available_daycares").slideDown();
        jQuery(".region_list").hide();
        jQuery("#region_"+jQuery("#region_select").val()).show();
      }else{
        jQuery("#region_select_error").slideDown();
      }
    })
  })
</script>

<section class="available_daycares" style="display:none;">
  <h3 class="text-center text-uppercase"><?php _e("Browse the available daycares in your area","wingparent"); ?></h3>
  <div class="container">
    <?php foreach($regions as $region){ ?>
      <div class="row region_list" id="region_<?php echo $region->term_id; ?>">
        <?php
        $args = array(
          'post_type' => 'daycares',
          'tax_query' => array(
            array(
              'taxonomy' => 'region',
              'field'    => 'slug',
              'terms'    =>  $region->slug,
            ),
          ),
        );
        $the_query = new WP_Query($args);
        if ( $the_query->have_posts() ) {
          while ( $the_query->have_posts() ) {
            $the_query->the_post();
            $did = get_the_ID();
            if(!get_post_meta($did,"hide_on_results")){
            ?>
            <div class="col-lg-3 col-md-6 mb-4">
              <div class="card">
                <div class="card-body">
                  <h4 class="card-title"><?php echo get_the_title($did);?></h4>
                  <h6 class="card-subtitle"<?php the_excerpt(); ?></h6>
                  <p class="card-text"><i class="fa fa-map-marker"></i> <?php echo get_post_meta($did,"address",true)." ".get_post_meta($did,"address_2",true)." ".get_post_meta($did,"town/city",true)." ".get_post_meta($did,"city",true)." ".get_post_meta($did,"state",true); ?></p>
                  <button class="card-link text-uppercase w-100 select_daycare" data-toggle="modal" data-daycare="<?php echo get_the_title($did);?>" data-target="#existing_signup">Select daycare</button>
                </div>
              </div>
            </div>
          <?php }
        }
        } ?>
<script>
jQuery(document).ready(function(){
  jQuery(".select_daycare").click(function(){
    jQuery("#nf-field-21-wrap").hide();
    jQuery("#nf-field-21").val(jQuery(this).attr("data-daycare"));
  })
});
</script>
        <div class="col-lg-3 col-md-6 mb-4">
          <div class="card yellow-card">
            <div class="card-body">
              <h3 class="card-title"><?php _e("Is your daycare not on the list?","wingparent"); ?></h3>
              <a href="#" data-toggle="modal" data-target="#contact_signup" class="card-link text-uppercase d-block"><?php _e("Contact us>","wingparent"); ?></a>
            </div>
          </div>
        </div>
      </div>
    <?php } ?>
  </div>
</section>

<div class="modal fade" id="existing_signup" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel"><?php _e("Start winging it","wingparent"); ?></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <?php echo do_shortcode('[ninja_form id='.$existing_forms[ICL_LANGUAGE_CODE].']'); ?>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="contact_signup" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel"><?php _e("Start winging it","wingparent"); ?></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <?php echo do_shortcode('[ninja_form id='.$new_forms[ICL_LANGUAGE_CODE].']'); ?>
      </div>
    </div>
  </div>
</div>
