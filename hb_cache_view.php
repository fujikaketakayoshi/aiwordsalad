<?php
require('phplib/keyvalue_file.class.php');

use KeyValueFile\KeyValueFile;

$tou_array = [];
$ku_array = [];

$file = new KeyValueFile('phplib/tmp');	
$key = 'tou_ku_array';
if ( $file->is_cache_available($key) ) {
    /** @var array{tou: array<string>, ku: array<string>} $arr */
	$arr = $file->get_keyvalue($key);
	$tou_array = $arr['tou'];
	$ku_array = $arr['ku'];
} else {
	$file->remove_file($key);
}

var_dump($tou_array);
echo '<br>';
var_dump($ku_array);