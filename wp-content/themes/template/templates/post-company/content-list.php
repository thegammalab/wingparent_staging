<script type="text/javascript" src="//s7.addthis.com/js/300/addthis_widget.js#pubid=ra-5a0456f2178193a2"></script> <div class="article_page_top_bg">    <div class="container">        <div class="row">            <div class="col-sm-12">                <h2 class="rainbow" style="display: block;">POS Knowledge Center</h2>            </div>        </div>    </div></div><div class="container">    <div class="row">        <div class="col-md-8">            <div class="row">                <?php                $page_no = 0;                $per_page = 12;                $post_type = "articles";                $args = array();                $args["post_type"] = $post_type;                $args["post_template"] = dirname(__FILE__) . "/content-item.php";                $args["search"] = array();                if ($term) {                    $args["search"]["tax_slug_" . $tax_name] = $term;                }                $args["page"] = $page_no;                $args["per_page"] = $per_page;                $args["no_results_html"] = '<h3 class="no_results">Sorry, no results</h3>';                $results = gamma_get_posts($args);                echo $results["output"];                ?>            </div>            <nav aria-label="...">                <?php echo gamma_get_pagination($results["total_posts"], $per_page); ?>            </nav>        </div>        <div class="col-md-4">            <?php dynamic_sidebar("article_page_sidebar"); ?>        </div>    </div></div><hr /><div class="container">    <div class="send_article_box">        <?php echo do_shortcode("[smartblock id=119]"); ?>     </div></div>