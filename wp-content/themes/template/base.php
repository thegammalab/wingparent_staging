<?php
if(is_wc_endpoint_url( 'lost-password' )){
  header("Location:https://www.wingparent.be/wp-login.php?action=lostpassword");
}
if(!get_current_user_id()){
  if(is_shop() || is_singular("product") || is_tax("product_cat") || is_cart() || is_checkout()){
    header("Location:".get_bloginfo("url")."/signup/");
  }
}


get_template_part('templates/head'); ?>
<body <?php body_class(); ?> data-spy="scroll" data-target="#category_content_nav">
    <!--[if lt IE 8]>
      <div class="alert alert-warning">
    <?php _e('You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.', 'roots'); ?>
      </div>
    <![endif]-->
    <?php
    do_action('get_header');
    get_template_part('templates/header');
    if (is_page("how-it-works") || is_page("home") || !is_page()) {
        include roots_template_path();
    } else {
        include roots_template_path();
    }
    ?>
    <?php get_template_part('templates/footer'); ?>
    <?php wp_footer(); ?>
    <script>
      wc_add_to_cart_params.i18n_view_cart= "<?php _e("Added to the cart"); ?>! &nbsp;&nbsp;<?php _e("View cart"); ?>.";

      jQuery(document).ready(function(){
        jQuery(".widget.widget_layered_nav").addClass("collapsed").find("h3").append('<span class="expand"></span>');
        jQuery(".widget.widget_layered_nav .expand").click(function(){
          jQuery(this).closest(".widget").toggleClass("collapsed");
        })
        jQuery(".prodcat_list .cat_expand").click(function(){
          jQuery(this).closest("li").toggleClass("is_open");
        })

        jQuery("#address_form>.form-row").append('<a class="pull-right button button-info" href="<?php echo get_permalink( woocommerce_get_page_id( 'checkout' ) ); ?>" style="margin-left: auto"><?php _e("Back to Checkout"); ?></a>')

        setInterval(function(){
          jQuery("li.chosen").closest(".widget").removeClass("collapsed");
        },100);
      })
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.3/umd/popper.min.js" integrity="sha384-vFJXuSJphROIrBnz7yo7oB41mKfc8JzQZiCq4NCceLEaO4IHwicKwpJf9c9IpFgh" crossorigin="anonymous"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta/js/bootstrap.min.js"></script>
</body>
</html>
