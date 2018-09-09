<?php
function serialize_string($string) {
  $string_pieces = explode(",","administrator,".$string);
	return serialize($string_pieces);
}
?>