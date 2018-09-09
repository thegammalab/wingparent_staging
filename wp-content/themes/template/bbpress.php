<div class="col-xs-12">
    <div class="row">
        <div class="col-sm-8 col-xs-12">
            <div class="page-title"><h2><?php echo the_title(); ?></h2></div>
            <div class="page-content ">
                <?php get_template_part('templates/content', 'page'); ?>
            </div>
        </div>
        <div class="col-sm-4 col-xs-12" id="forum_sidebar">
            <?php dynamic_sidebar('forum_page_sidebar'); ?>
        </div>

    </div> 
</div>