<div class="container">
  <section class="browse_category text-center">
    <h2><?php _e("browse by category","wingparent"); ?></h2>
    <p><?php _e("Use the list below to filter by category","wingparent"); ?></p>
  </section>
  <div class="row">
    <?php
    $cats = get_terms("product_cat","hide_empty=0&parent=0");
    foreach($cats as $cat){
      if($cat->slug!="uncategorized"){
        $cat_thumb_id = get_woocommerce_term_meta( $cat->term_id, 'thumbnail_id', true );
                $cat_thumb_url = wp_get_attachment_image_src( $cat_thumb_id,"medium" );
        ?>
    <div class="col-md-4">
      <a href="<?php echo get_term_link($cat); ?>">
      <div class="category_box p-relative">
        <div class="category_name p-absolute">
        <?php echo $cat->name; ?>
        </div>
        <div class="category_image p-absolute">
          <img src="<?php echo $cat_thumb_url[0]; ?>" alt="feature1">

        </div>
      </div>
    </a>
    </div>
  <?php }
} ?>

  </div>

<div class="divider"></div>
  <div class="shop-above-carousel-content">
    <h3 class="text-uppercase text-center"><?php _e("Featured products","wingparent"); ?></h3>
    <?php $shop_id = get_option( 'woocommerce_shop_page_id' ); $the_post = get_post($shop_id); echo apply_filters("the_content",$the_post->post_content);  ?>

  </div>
  <section class="products_carousel pb-6">
        <div id="featured_carousel" class="carousel slide" data-ride="carousel">
          <div class="carousel-inner">
            <?php for($i=0;$i<3;$i++){
              if($i==0){
                $cl = 'active';
              }else{
                $cl = '';
              }
              $args = array(
                'post_type' => 'product',
                'posts_per_page' => 4,
                "paged" => 1+$i,
                'tax_query' => array(
                  array(
                    'taxonomy' => 'product_visibility',
                    'field'    => 'slug',
                    'terms'    => 'featured'
                  ),
                ),
              );
              $the_query = new WP_Query($args);

              // The Loop
              if ( $the_query->have_posts() ) {
                ?>
                <div class="carousel-item <?php echo $cl; ?>">
                  <div class="product_list row">
                    <?php
                    while ( $the_query->have_posts() ) {
                      $the_query->the_post();
                      include("woocommerce/content-product.php");
                    }

                    ?>
                  </div>
                </div>
              <?php }
            } ?>
          </div>
          <a class="carousel-control-prev" href="#featured_carousel" role="button" data-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="sr-only">Previous</span>
          </a>
          <a class="carousel-control-next" href="#featured_carousel" role="button" data-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="sr-only">Next</span>
          </a>
        </div>

    </section>
  </div>
