<?php
if (is_tax("review_category")) {
  $post_type = "reviews";
}elseif ($_GET["post_type"]) {
  $post_type = $_GET["post_type"];
} else {
  $elem = (get_queried_object());
  $post_type = get_query_var('post_type');
}
if (file_exists(dirname(__FILE__) . "/templates/post-" . $post_type . "/content-list.php")) {
  (get_template_part('templates/post-' . $post_type . "/content", "list"));
} else {
  //(get_template_part('templates/content', 'list'));
}
?>
