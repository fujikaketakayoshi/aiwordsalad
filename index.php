<a href="./hb_cache_view.php">HB cache view</a>
<br>
<a href="./hatenabookmark_run.php">Hatenabookmark Test Run</a>
<br>
<a href="./keyvalue_file_run.php">KeyValueFile Test Run</a>

<?php
require_once('phplib/crawler.class.php');
require_once('phplib/keyvalue_file.class.php');

use Crawler\HB;
use KeyValueFile\KeyValueFile;

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

$file = new KeyValueFile('phplib/tmp');
$file->set_expire_span(60*60);
$file->set_keyvalue('index_tou_array', $tou_array);

$file = new KeyValueFile('phplib/tmp');
$file->set_expire_span(60*60);
$file->set_keyvalue('index_ku_array', $ku_array);


