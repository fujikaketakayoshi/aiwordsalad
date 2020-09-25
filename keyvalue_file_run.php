<?php

require('keyvalue_file.class.php');

use KeyValueFile\KeyValueFile;
//use KeyValueFile;

$file = new KeyValueFile('tmp');	
$file->set_keyvalue('０１２', 'あマナ家人fがおがlがあgsぁsdさl');

$file = new KeyValueFile('tmp');	
$file->set_keyvalue('あいう', array(1,2,3,4));


var_dump($file->get_keyvalue('０１２'));



