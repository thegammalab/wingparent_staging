<?php

session_start();
$uid = get_current_user_id();

if ($_REQUEST["referred_by"]) {
    $_SESSION["referred_by"] = $_REQUEST["referred_by"];
}

$base = "wingparent/";
$pieces = explode("?", $_SERVER["REQUEST_URI"]);
$path = str_replace($base, "", $pieces[0]);
if (substr($path, 0, 1) == "/") {
    $path = substr($path, 1);
}
if ($path=="my-account/" && !$uid) {
     header("Location:" . get_bloginfo("url")."/signup/");
     die();
}
if(strpos($path,"//ma-manage-address/")){
  header("Location:" . get_bloginfo("url")."/my-account/ma-manage-address/");
  die();
}
