<?php

require('phplib/keyvalue_file.class.php');

use KeyValueFile\KeyValueFile;

$file = new KeyValueFile('phplib/tmp');	
$file->set_keyvalue('０１２', 'あマナ家人fがおがlがあgsぁsdさl');

$file = new KeyValueFile('phplib/tmp');	
$file->set_keyvalue('あいう', array(1,2,3,4));


$file->get_keyvalue('あいう');



