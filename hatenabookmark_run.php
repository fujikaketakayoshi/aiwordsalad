<?php
require_once('phplib/crawler.class.php');

use Crawler\HB;

$hb = new HB();

$str = str_replace("...", "、", $hb->get_desc_str());
$str = str_replace("、。", "。", $str);
$str = str_replace("。、", "。", $str);

//var_dump($str);

preg_match_all("/([^、。].*?)、/u", $str, $match_arr);

var_dump($match_arr[1]);
$tou_array;
$ku_array;
