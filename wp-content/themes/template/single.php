<?php

$elem = (get_queried_object());
$post_type = $elem->post_type;

if (file_exists(dirname(__FILE__) . "/templates/post-" . $post_type . "/content-single.php")) {
    include(locate_template('templates/post-' . $post_type . "/content-single.php"));
} else {
    include(locate_template('templates/content-single.php'));
}
?>
