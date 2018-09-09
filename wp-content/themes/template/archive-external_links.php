<?php
$term_id = $wp_query->get_queried_object()->term_id;
$the_term = get_term_by("id",$term_id,"product_cat");
$usr_id = get_current_user_id();
$daycares =get_user_meta($usr_id,"daycare",true);
$daycare = $daycares[0];

$args  = array(
  "post_type" => "external_links"
);
?>

<div class="container">
  <div class="top_category_page">
    <h2 class="category_page_title text-uppercase"><?php echo $the_term->name; ?></h2>
    <?php echo $the_term->description; ?>
  </div>
  <?php
  $the_query = new WP_Query( $args );

  // The Loop
  if ( $the_query->have_posts() ) {
    ?>
    <ul class="alm-listing alm-ajax product_list products" style="margin:0 -15px;">
      <?php
      while ( $the_query->have_posts() ) {
        $the_query->the_post();
        $uid = get_the_ID();
        ?>
        <li class="col-sm-3">
          <div class="product_item">
            <a href="<?php echo str_replace("variableCrecheUserID",$daycare."-".$usr_id,get_post_meta($uid,"link",true)); ?>" target="_blank" class="woocommerce-LoopProduct-link woocommerce-loop-product__link">
              <h2 class="woocommerce-loop-product__title" style="font-weight:bold;"><?php the_title(); ?></h2>
              <?php the_post_thumbnail(); ?>
              <p><?php echo get_post_meta($uid,"description",true); ?></p>
              <div class="product_footer">
                <button style="padding: 10px; text-align: center; border-radius: 30px; color: #FFF; background-color: #ffc000; text-transform: uppercase; width: 100%; border: 0;"><?php _e("Go to website"); ?></button>
              </div>
            </a>
          </div>
        </li>
        <?php
      }
      wp_reset_postdata();
      ?>
    </ul>
    <?php
  } else {
  }
  ?>
</div>
