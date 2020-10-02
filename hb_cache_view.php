<?php
require('phplib/keyvalue_file.class.php');

use KeyValueFile\KeyValueFile;

$index_tou = [];
$index_ku = [];

$file = new KeyValueFile('phplib/tmp');	
$key = 'index_tou_array';
if ( $file->is_cache_available($key) ) {
	$index_tou = $file->get_keyvalue($key);
} else {
	$file->remove_file($key);
}

$file = new KeyValueFile('phplib/tmp');
$key = 'index_ku_array';
if ( $file->is_cache_available($key) ) {
	$index_ku = $file->get_keyvalue($key);
} else {
	$file->remove_file($key);
}

var_dump($index_tou);
echo '<br>';
var_dump($index_ku);
