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
	$str = str_replace(" ", "。", $str);
	$str = str_replace("＝", "。", $str);
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

shuffle($tou_array);
shuffle($ku_array);

$wordsalads = [];

// 7個のワードサラダブロックを作る
foreach ( range(1, 7) as $i ) {
	$line_words = [];
	
	// 9句読点を配列に入れて最後は句点を入れる、20%の確率で各文にキーワードを挿入
	foreach ( range(1, 9) as $ii ) {
		if ( rand(0, 1) === 0 ) {
			$tmp = array_shift($tou_array);
			$tmp = rand(0, 5) === 0 ? $keyword . $tmp : $tmp;
			$line_words[] = $tmp . "、";
		} else {
			$tmp = array_shift($ku_array);
			$tmp = rand(0, 5) === 0 ? $keyword . $tmp : $tmp;
			$line_words[] =  $tmp . "。";
		}
	}
	
	$line_words[] = array_shift($ku_array) . "。";
	$wordsalads[] = implode("", $line_words);
	
	if ( count($ku_array) === 0 ) break;
}



var_dump($wordsalads);
echo "<br>";
var_dump($tou_array);
echo "<br>";
var_dump($ku_array);
