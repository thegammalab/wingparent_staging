<?php
if(is_tax("product_cat")){
  include("taxonomy-product_cat.php");
}elseif(is_tax("product_tag")){
    include("taxonomy-product_tag.php");
  }else{
the_post(); ?>
<div class = "inner_page_bg">
    <div class="container">
        <div class="row">
            <div class="col-sm-12">
                <h1 class="text-center text-uppercase"><?php echo the_title(); ?></h1>
            </div>
        </div>
    </div>
</div>
<div class = "container">
    <div class = "row">
        <div class = "col-md-12">
            <?php the_content(); ?>
        </div>
    </div>
</div>
<?php } ?>
