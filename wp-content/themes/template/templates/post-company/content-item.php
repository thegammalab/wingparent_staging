<div class="col-md-6">
    <a href="<?php echo $item["post_permalink"]; ?>" class="list_blog_box_select">
        <div class="article_list_onepiece">
            <div class="article_header_image">
                <?php echo $item["featured_img_medium"]; ?>
            </div>
            <div class="article_box_content">
                <h3><?php echo $item["post_title"]; ?></h3> 
                <p><?php echo strip_tags($item["post_excerpt"]); ?></p>
            </div>
            <div class="read_more_article">
                Read more <i class="fa fa-long-arrow-right" aria-hidden="true"></i>
            </div>
        </div>
    </a>
</div>
