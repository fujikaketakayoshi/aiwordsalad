<?php
require_once('phplib/keyvalue_file.class.php');
require_once('phplib/html.class.php');

use KeyValueFile\KeyValueFile;


var_dump($_SERVER);

$protocol = isset($_SERVER["HTTP_X_FORWARDED_PROTO"]) ? $_SERVER["HTTP_X_FORWARDED_PROTO"] : 'http';
$url = $protocol . "://" . $_SERVER["HTTP_HOST"] . $_SERVER["SCRIPT_NAME"];
$index_url = str_replace("index.php", "", $url);

$file = new KeyValueFile('phplib/tmp');
$recent_key = 'recent_keywords';
$recent_keywords = [];
if ( $file->has_key($recent_key) ) {
	$recent_keywords = $file->get_keyvalue($recent_key);
}

$file = new KeyValueFile('phplib/tmp');
// 過去１週間のdate_keyがあるか確認して日付を取得
$dates = [];
foreach ( range(0, 6) as $day ) {
	$date_key = date("Y年m月d日", time() - 24*60*60*$day);
	if ( $file->has_key($date_key) ) {
		$dates[] = $date_key;
	}
}

Html\header('AI Wordsalad', $index_url);
?>
	<div class="container">
		<div class="row">
			<div class="col col-md-offset-1 col-md-10">
				<nav class="panel panel-default">
					<div class="panel-heading">AIワードサラダを検索する</div>
					<div class="panel-body">
						<form action="<?= $index_url ?>search.php" method="GET">
							<div class="form-group">
								<label for="keyword">検索したい言葉</label>
								<input type="text" class="form-control" name="keyword" id="keyword" value="" />
							</div>
							<div class="text-right">
								<button type="submit" class="btn btn-primary">検索</button>
							</div>
						</form>
					</div>
				</nav>
			</div>
		</div>
		<div class="row">
			<div class="col col-md-offset-1 col-md-10">
				<nav class="panel panel-default">
					<div class="panel-heading">最新の検索履歴</div>
<!--					<div class="panel-body">
					</div>-->
					<div class="list-group">
						<?php foreach( $recent_keywords as $word ) { ?>
							<a href="<?= $index_url?>search.php/<?= htmlspecialchars($word) ?>" class="list-group-item">
								<?= htmlspecialchars($word) ?>
							</a>
						<?php } ?>
					</div>
				</nav>
			</div>
			<div class="col col-md-offset-1 col-md-10">
				<nav class="panel panel-default">
					<div class="panel-heading">最近の検索履歴</div>
<!--					<div class="panel-body">
					</div>-->
					<div class="list-group">
						<?php foreach( $dates as $d ) { ?>
							<a href="<?= $index_url?>archive.php?date=<?= htmlspecialchars($d) ?>" class="list-group-item">
								<?= htmlspecialchars($d) ?>
							</a>
						<?php } ?>
					</div>
				</nav>
			</div>
		</div>

	</div>

<?php
Html\footer();
