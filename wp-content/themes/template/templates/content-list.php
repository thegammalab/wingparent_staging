<?phpglobal $wp_query;?>
<div id="content_section">
  <div class="container">
aaa
    <?php

    $category = get_the_category();

    if (is_category()) {


      ?>


      <h1><?php single_cat_title(); ?></h1>


      <?php echo category_description(); ?>


      <?php

    } else {


      ?>


      <h1>Our Blog</h1>


      <?php echo do_shortcode('[smartblock id=102]'); ?>


      <?php

    }
    ?>
  </div>
</div>
<div id="blog_section">
  <div class="container">
    <?php if (!have_posts()) : ?>
      <div class="alert alert-warning">
        <?php _e('Sorry, no results were found.', 'roots'); ?>
      </div>


      <?php get_search_form(); ?>

    <?php endif; ?>

    <ul id="blog_list">


      <?php while (have_posts()) : the_post(); ?>



        <?php



        include("content-post.php");



        ?>


      <?php endwhile; ?>

    </ul>

    <hr />

    <div class="pagination_links">


      <<?php echo paginate_links($args); ?>

    </div>
  </div></div>
