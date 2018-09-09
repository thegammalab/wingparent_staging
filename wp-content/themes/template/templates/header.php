<?php if (is_page("home")) { ?>
  <header>
    <div class="border-top">  </div>
    <div class="container">
      <nav class="navbar navbar-expand-lg bg-none p-0">
        <a class="navbar-brand" href="<?php echo get_bloginfo("url"); ?>"><img src="<?php echo get_bloginfo("template_directory"); ?>/assets/images/logo.png"/></a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#main_menu" aria-controls="main_menu" aria-expanded="false" aria-label="Toggle navigation">
          <i class="fa fa-bars"></i>
        </button>

        <div class="collapse navbar-collapse justify-content-end" id="main_menu">
          <?php
          if (has_nav_menu('main_menu_header')) :
            wp_nav_menu(array('theme_location' => 'main_menu_header', 'walker' => new Roots_Nav_Walker(), 'menu_class' => 'navbar-nav header_menu', "depth" => 2));
          endif;
          ?>

        </div>
        <div class="language_button">
          <?php if(get_current_user_id()){ ?>
            <a class="login_button text-uppercase" href="<?php echo get_bloginfo("url"); ?>/my-account/"><?php _e("Account","wingparent"); ?></a>
          <?php }else{ ?>
            <a class="login_button text-uppercase" href="<?php echo get_bloginfo("url"); ?>/signup/"><?php _e("Login","wingparent"); ?></a>
          <?php } ?>
          <a href="<?php echo get_bloginfo("url")."/cart/"; ?>" class="cart_link"><i class="fa fa-shopping-cart"></i></a>

          <div class="lang_select">
            <div class="lang_current">

              <?php
              $languages = icl_get_languages('skip_missing=0&orderby=code');

              if(!empty($languages)){
                foreach($languages as $language){
                  if ($language['language_code'] == ICL_LANGUAGE_CODE) {
                    $flag_url = $language['country_flag_url'];
                  }
                }
              }
              ?>
              <img src="<?php echo $flag_url; ?>" />
            </div>
            <div class="lang_options">
              <?php do_action('wpml_add_language_selector'); ?>
            </div>
          </div>

        </div>
      </nav>
      <div class="row">
        <div class="col-md-6">
          <div class="bs-component">
            <div class="jumbotron">
              <?php the_content(); ?>
            </div>
          </div>

        </div>
        <div class="col-md-6">
        </div>
      </div>
    </div>
  </header>



<?php } else { ?>

  <header class="inner_header">
    <div class="border-top">  </div>
    <div class="container">
      <nav class="navbar navbar-expand-lg bg-none p-0 pb-6 border-bottom">
        <a class="navbar-brand" href="<?php echo get_bloginfo("url"); ?>"><img src="<?php echo get_bloginfo("template_directory"); ?>/assets/images/logo.png"/></a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#main_menu" aria-controls="main_menu" aria-expanded="false" aria-label="Toggle navigation">
          <i class="fa fa-bars"></i>
        </button>

        <div class="collapse navbar-collapse justify-content-end" id="main_menu">

          <?php
          if (has_nav_menu('main_menu_header')) :
            wp_nav_menu(array('theme_location' => 'main_menu_header', 'walker' => new Roots_Nav_Walker(), 'menu_class' => 'navbar-nav header_menu', "depth" => 2));
          endif;
          ?>

        </div>

        <div class="language_button">

          <?php if(get_current_user_id()){ ?>
            <a class="login_button text-uppercase" href="<?php echo get_bloginfo("url"); ?>/my-account/">account</a>
          <?php }else{ ?>
            <a class="login_button text-uppercase" href="<?php echo get_bloginfo("url"); ?>/signup/">login</a>
          <?php } ?>
          <a href="<?php echo get_bloginfo("url")."/cart/"; ?>" class="cart_link"><i class="fa fa-shopping-cart"></i></a>

          <div class="lang_select">
            <div class="lang_current">

              <?php
              $languages = icl_get_languages('skip_missing=0&orderby=code');

              if(!empty($languages)){
                foreach($languages as $language){
                  if ($language['language_code'] == ICL_LANGUAGE_CODE) {
                    $flag_url = $language['country_flag_url'];
                  }
                }
              }
              ?>
              <img src="<?php echo $flag_url; ?>" />
            </div>
            <div class="lang_options">
              <?php do_action('wpml_add_language_selector'); ?>
            </div>
          </div>

        </div>
      </nav>

    </div>
  </header>

<?php }
if(is_tax("product_cat") || is_tax("product_tag") || is_search() || is_singular("product")){
?>
<div class="container" style="margin-top:-30px; margin-bottom:20px;">
  <p id="breadcrumbs" style="font-size: 13px;"><span class="d-none d-md-inline"><?php _e("Home"); ?> <font style="vertical-align: inherit;"> » </font> </span><a href="<?php echo get_permalink( wc_get_page_id( 'shop' ) ); ?>"><?php _e("Shop"); ?></a> <font style="vertical-align: inherit;"> » </font>
  <?php
  if ( function_exists('yoast_breadcrumb') ) {
    echo yoast_breadcrumb( '','' );
  }
  ?>
  </p>
</div>
<?php } ?>
