<?php
require_once('phplib/crawler.class.php');

use Crawler\HB;

$hb = new HB();

// 不適切記号を句読点化または消去
$str = str_replace("...", "、", $hb->get_desc_str());
$str = str_replace("、。", "。", $str);
$str = str_replace("。、", "。", $str);
$str = str_replace("？", "。", $str);
$str = str_replace("！", "。", $str);
$str = str_replace("■", "。", $str);
$str = str_replace("➡︎", "。", $str);
$str = str_replace("「", "", $str);
$str = str_replace("」", "", $str);


preg_match_all("/([^、。].*?)、/u", $str, $tou_match_arr);

$tou_array = [];
$ku_array = [];

foreach ( $tou_match_arr[1] as $m ) {
	$arr = explode("。", $m);
	if ( count($arr) == 1 ) {
		$tou_array[] = $arr[0];
	} else {
		$tmparr = array_slice($arr, 0, count($arr) - 1);
		$ku_array[] = $tmparr[0];
	}
}


var_dump($ku_array);
echo "<br>";
var_dump($tou_array);