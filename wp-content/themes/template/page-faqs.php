<?php
global $wpdb;
?>
<section class="inner_page_bg">
    <div class="container">
        <div class = "row">
            <div class = "col-sm-12">
                <h1 style="margin-bottom:20px;"><?php echo the_title(); ?></h1>
              <?php the_content(); ?>

            </div>
        </div>
        <hr />
        <?php
$the_terms = get_terms("faq_types","hide_empty=0");
foreach($the_terms as $term){

        $the_query = new WP_Query(array(
            "post_type" => "faqs",
            'orderby' => 'date',
            'order' => 'ASC',
            'posts_per_page' => -1,
        ));
        $faq_items = array();
        if ($the_query->have_posts()) {
            while ($the_query->have_posts()) {
                $the_query->the_post();
                $faq_items[get_the_ID()] = get_the_content();
            }
        }
        ?>

        <div class="faq_item">
          <div class="row">
            <div class="col-sm-3">
            <h3><?php echo $term->name; ?></h3>
          </div>
          <div class="col-sm-9">
            <div class="row">
                <div class="col-sm-12">
                    <ul class="faq_list" id="<?php echo $term->slug; ?>">
                        <?php
                        $i = 0;
                        foreach ($faq_items as $post_id=>$post_content) {
                            $i++;
                            ?>
                            <li>
                                <div class="faq_title"><?php echo get_the_title($post_id); ?></div>
                                <div class="faq_body"><?php  echo $post_content;  ?></div>
                            </li>
                            <?php
                        }
                        ?>
                    </ul>
                </div>
            </div>
            </div>
            </div>
        </div>
        <hr />
          <?php } ?>
    </div>

    <script>
        jQuery(document).ready(function () {
            jQuery(".faq_list li").click(function () {
                var th = jQuery(this);
                if (jQuery(this).is(".active")) {
                    jQuery(this).find(".faq_body").slideUp(500, function () {
                        th.removeClass("active");
                    });
                } else {
                    jQuery(this).find(".faq_body").slideDown(500, function () {
                        th.addClass("active");
                    });
                }
            })
        });

    </script>
</section>
