<?php
require_once('phplib/keyvalue_file.class.php');
require_once('phplib/crawler.class.php');
require_once('phplib/html.class.php');

use KeyValueFile\KeyValueFile;
use Crawler\HB;


$protocol = isset($_SERVER["HTTP_X_FORWARDED_PROTO"]) ? $_SERVER["HTTP_X_FORWARDED_PROTO"] : 'http';
$url = $protocol . "://" . $_SERVER["HTTP_HOST"] . $_SERVER["SCRIPT_NAME"];
$index_url = str_replace("search.php", "", $url);

$keyword = isset($_SERVER['PATH_INFO']) ? str_replace("/", "", $_SERVER['PATH_INFO']) : '';

// まず、indexのフォームからの入力を自身のPATH_INFO変数に渡す
if ( isset($_GET['keyword']) ) {
	$search_url = $url . '/' . $_GET['keyword'];
	header("Location: $search_url");
// $keywordが作られていない場合は、indexに戻る
} elseif ( $keyword == '' ) {
	header("Location: $index_url");
}

$wordsalads = [];
$file = new KeyValueFile('phplib/tmp');

// ワードサラダが保存されていれば取り出し、無ければ作成
if ( $file->has_key($keyword) ) {
	$wordsalads = $file->get_keyvalue($keyword);
} else {
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
		$str = str_replace("•", "。", $str);
		$str = str_replace("◦", "。", $str);
		$str = str_replace("■", "。", $str);
		$str = str_replace("➡︎", "。", $str);
		$str = str_replace("(", "、", $str);
		$str = str_replace(")", "。", $str);
		$str = str_replace("（", "、", $str);
		$str = str_replace("）", "。", $str);
		$str = str_replace(" ", "", $str);
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
		
	// ワードサラダを保存
	$file = new KeyValueFile('phplib/tmp');
	$file->set_keyvalue($keyword, $wordsalads);	
}

// 直近の検索履歴
$file = new KeyValueFile('phplib/tmp');
$recent_key = 'recent_keywords';
$recent_keywords = [];
if ( $file->has_key($recent_key) ) {
	$recent_keywords = $file->get_keyvalue($recent_key);
}
array_unshift($recent_keywords, $keyword);
$recent_keywords = array_unique($recent_keywords);
$tmp = [];
foreach ( $recent_keywords as $rw ) {
	if ( $rw !== "" ) $tmp[] = $rw;
}
$recent_keywords = $tmp;
// 検索履歴の表示数8
$recent_keywords_max = 8;
if ( count($recent_keywords) > $recent_keywords_max ) {
	$recent_keywords = array_slice($recent_keywords, 0, $recent_keywords_max);
}
$file->set_keyvalue($recent_key, $recent_keywords);

// 日付の検索履歴
$file = new KeyValueFile('phplib/tmp');
$date_key = date("Y年m月d日");
$date_keywords = [];
if ( $file->has_key($date_key) ) {
	$date_keywords = $file->get_keyvalue($date_key);
}
array_unshift($date_keywords, $keyword);
$date_keywords = array_unique($date_keywords);
$file->set_keyvalue($date_key, $date_keywords);

Html\header($keyword . 'のAI Wordsalad', $index_url);
?>
	<div class="container">
		<div class="row">
			<div class="col col-md-offset-1 col-md-10">
				<nav class="panel panel-default">
					<div class="panel-heading"><?= htmlspecialchars($keyword) ?>のAIワードサラダ</div>
<!--
					<div class="panel-body">
						<a href="{{ route('folders.create') }}" class="btn btn-default btn-block">
							フォルダを追加する
						</a>
					</div>
-->
					<div class="list-group">
					<?php foreach ($wordsalads as $ws) { ?>
						<div class="list-group-item">
								<?= $ws ?>
						</div>
					<?php } ?>
					</div>
				</nav>
			</div>
		</div>
	</div>
<?php
Html\footer();

