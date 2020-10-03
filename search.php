<?php
require('phplib/keyvalue_file.class.php');
require_once('phplib/crawler.class.php');

use KeyValueFile\KeyValueFile;
use Crawler\HB;

$keyword = isset($_SERVER['PATH_INFO']) ? str_replace("/", "", $_SERVER['PATH_INFO']) : '';
if ( $keyword == '') {
	$url = $_SERVER["REQUEST_SCHEME"] . "://" . $_SERVER["HTTP_HOST"] . $_SERVER["SCRIPT_NAME"];
	$index_url = str_replace("search.php", "index.php", $url);
	header("Location: $index_url");
	exit;
}

$tou_array = [];
$ku_array = [];

$file = new KeyValueFile('phplib/tmp', ['expires' => true]);
$key = 'tou_ku_array';
if ( $file->is_cache_available($key) ) {
	$arr = $file->get_keyvalue($key);
	$tou_array = $arr['tou'];
	$ku_array = $arr['ku'];
} else {
	$hb = new HB();
	
	// 不適切記号を句読点化
	$str = str_replace("...", "、", $hb->get_desc_str());
	$str = str_replace("、。", "。", $str);
	$str = str_replace("。、", "。", $str);
	$str = str_replace("？", "。", $str);
	$str = str_replace("！", "。", $str);
	$str = str_replace("■", "。", $str);
	$str = str_replace("➡︎", "。", $str);
	
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
	
	$file = new KeyValueFile('phplib/tmp', ['expires' => true]);
	$file->set_expire_span(60*60);
	$arr = [];
	$arr['tou'] = $tou_array;
	$arr['ku'] = $ku_array;
	$file->set_keyvalue('tou_ku_array', $arr);
	$arr = $file->get_keyvalue($key);
	$tou_array = $arr['tou'];
	$ku_array = $arr['ku'];
}

var_dump($tou_array);
echo "<br>";
var_dump($ku_array);
