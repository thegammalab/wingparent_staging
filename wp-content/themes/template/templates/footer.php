<section class="contact_section">
  <div class="container">
    <div class="row">
      <div class="col-lg-7">
        <h2 class="text-uppercase"><?php _e("Contact","wingparent"); ?></h2>
        <p><?php _e("Let us know how we can make your life easier!","wingparent"); ?></p>
        <ul class="">
          <li class="align-middle"><a href=""><i class="fa fa-map-marker"></i>Herk-de-Stad, België   </a></li>
          <li class="align-middle"><a href=""><i class="fa fa-mobile-alt"></i>+32 496 35 37 40   </a></li>
          <li class="align-middle"><a href=""><i class="fa fa-envelope"></i>karen@wingparent.be</a></li>
        </ul>
      <a href="<?php echo get_bloginfo("url"); ?>/contact">  <button class="btn btn-secondary text-uppercase">
          <?php _e("Get in touch with wingparent","wingparent"); ?>
        </button></a>
      </div>
      <div class="col-lg-5"></div>
    </div>
  </div>
</section>
<footer class="py-5">
  <div class="container">
    <section>
      <div class="row">
        <div class="col-lg-3">
        <div class="footer_logo mb-md-5 mt-md-0">
          <img src="<?php echo get_bloginfo("template_directory"); ?>/assets/images/footer_logo.png" alt="">
        </div>
        </div>
        <div class="col-lg-3 col-md-4 col-6">
          <?php
          if (has_nav_menu('footer_menu1')) :
              wp_nav_menu(array('theme_location' => 'footer_menu1', 'walker' => new Roots_Nav_Walker(), 'menu_class' => 'footer_menu', "depth" => 2));
          endif;
          ?>
      </div>
        <div class="col-lg-3 col-md-4 col-6">
          <div class="">
            <?php
            if (has_nav_menu('footer_menu2')) :
                wp_nav_menu(array('theme_location' => 'footer_menu2', 'walker' => new Roots_Nav_Walker(), 'menu_class' => 'footer_menu', "depth" => 2));
            endif;
            ?>
          </div>
        </div>
        <div class="col-lg-3 col-md-4 ">
            <p class="footer_lead text-uppercase mb-0">
              <?php _e("join our mailing list","wingparent"); ?>
            </p>
            <form action="" class="newsletter_form">
              <div class="form-group align-middle m-auto ">
                <div class="input-group">
                  <input class="form-control text-uppercase" type="text" placeholder="<?php _e("Your email address","wingparent"); ?>">
                  <div class="input-group-append" id="send_btn">
                    <button type="submit" class="input-group-text text-uppercase"><?php _e("Join","wingparent"); ?></button>
                  </div>
                </div>
              </div>
            </form>
        </div>
      </div>
    </section>
    <div class="second-footer mt-4">
      <div class="row">
        <div class="col-lg-3 col-md-4 col-12"><p class="copyright text-uppercase">Wingparent © 2018	</p></div>
        <div class="col-lg-5 col-md-5 col-6 float-md-left float-right">
          <?php
          if (has_nav_menu('footer_menu3')) :
              wp_nav_menu(array('theme_location' => 'footer_menu3', 'walker' => new Roots_Nav_Walker(), 'menu_class' => 'footer_menu', "depth" => 2));
          endif;
          ?>
      </div>
        <div class="col-lg-4 col-md-3 col-6">
          <ul class=" social float-md-right">
            <li><a href="https://www.facebook.com/wingparent/" target="_blank"><i class="fa fa-facebook-f"></i></a></li>
            <li><a href="https://twitter.com/Wingparent_be" target="_blank"><i class="fa fa-twitter"></i></a></li>
            <li><a href="https://www.instagram.com/wingparent.be/?hl=undefined" target="_blank"><i class="fa fa-instagram"></i></a></li>
          </ul>
        </div>
      </div>
    </div>
  </div>

</footer>

<script>
jQuery(document).ready(function(){
  jQuery(".lang_current").click(function(){
    jQuery(this).parent().toggleClass("active");
  });
  setInterval(function(){
  jQuery(".add_cart_mini input").unbind("change").change(function(){
    jQuery(this).parent().find("a").attr("data-quantity",jQuery(this).val());
  })
  },500);
})

</script>
