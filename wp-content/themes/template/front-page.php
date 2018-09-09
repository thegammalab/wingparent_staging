<section class="feature-tabs">
  <div class="container">
    <ul class="nav nav-tabs nav-justified" id="nav-tab" role="tablist">
      <?php
      $all_cats = get_terms("product_cat","hide_empty=1&parent=0");
      foreach($all_cats as $cat){
        if($cat->slug!="uncategorized"){
          if(check_category($cat->slug)){
            $cats[]=$cat;
          }
        }
      }
      foreach($cats as $cat){
        if(!$act){
          $cl='active';
          $act=1;
        }else{
          $cl='';
        }
        ?>
        <li class="nav-item ">
          <a class="nav-item nav-link <?php echo $cl; ?>" id="nav-<?php echo $cat->slug;?>-tab" data-toggle="tab" href="#nav-<?php echo $cat->slug;?>" role="tab" aria-controls="nav-<?php echo $cat->slug;?>" aria-selected="true"><?php echo $cat->name;?></a>
        </li>
      <?php } ?>
    </ul>
  </div>
</section>

<div class="container">
  <section class="products_carousel pb-5">
    <div class="tab-content" id="nav-tabContent">
      <?php
      foreach($cats as $cat){
        if($cat->slug!="uncategorized"){
          if(!$act1){
            $cl='active';
            $act1=1;
          }else{
            $cl='';
          }
          ?>
          <div class="tab-pane fade show <?php echo $cl; ?>" id="nav-<?php echo $cat->slug;?>" role="tabpanel" aria-labelledby="nav-<?php echo $cat->slug;?>-tab">
            <h3 class="text-uppercase text-center"><?php echo $cat->name;?></h3>

            <div id="<?php echo $cat->slug;?>_carousel" class="carousel slide" data-ride="carousel">
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
                        'taxonomy' => 'product_cat',
                        'field'    => 'slug',
                        'terms'    =>$cat->slug,
                      ),
                    ),
                  );
                  $the_query = new WP_Query($args);

                  // The Loop
                  if ( $the_query->have_posts() ) {
                    ?>
                    <div class="carousel-item <?php echo $cl; ?>">
                      <div class="row">
                        <?php
                        while ( $the_query->have_posts() ) {
                          $the_query->the_post();
                          ?>
                          <div class="col-md-3 col-sm-6">
                            <div class="carousel_product_box text-justify">
                              <div class="d-block mx-auto"><a href="<?php echo get_bloginfo("url"); ?>/shop/"><?php the_post_thumbnail("medium"); ?></a></div>
                              <p class=" text-center text-uppercase">
                                <a href="<?php echo get_bloginfo("url"); ?>/shop/"><?php the_title(); ?></a>
                              </p>
                            </div>

                          </div>
                          <?php
                        }

                        ?>
                      </div>
                    </div>
                  <?php }
                } ?>
              </div>
              <a class="carousel-control-prev" href="#<?php echo $cat->slug;?>_carousel" role="button" data-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="sr-only">Previous</span>
              </a>
              <a class="carousel-control-next" href="#<?php echo $cat->slug;?>_carousel" role="button" data-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="sr-only">Next</span>
              </a>
            </div>
          </div>
        <?php }
      } ?>
    </div>
  </section>
</div>
<?php wp_reset_query(); ?>
<section class="what_you_need text-center">
  <div class="container">
    <?php echo get_post_meta(get_the_ID(),"what_you_need",true); ?>

  </div>
</section>
<div class="container">
  <section class="dna_features">
    <?php echo get_post_meta(get_the_ID(),"dna_content",true); ?>

  </section>
</div>
