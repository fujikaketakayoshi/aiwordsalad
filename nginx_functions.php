<?php

$funcs = get_defined_functions();

$arr = $funcs['internal'];
sort($arr);

var_dump($arr);