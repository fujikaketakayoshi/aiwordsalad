<!--<a href="./search.php">search</a>
<br>
<a href="./hb_cache_view.php">HB cache view</a>
<br>
<a href="./hatenabookmark_run.php">Hatenabookmark Test Run</a>
<br>
<a href="./keyvalue_file_run.php">KeyValueFile Test Run</a>
-->
<?php
require_once('phplib/keyvalue_file.class.php');
require_once('phplib/html.class.php');

use KeyValueFile\KeyValueFile;

$protocol = isset($_SERVER["HTTP_X_FORWARDED_PROTO"]) ? $_SERVER["HTTP_X_FORWARDED_PROTO"] : 'http';
$url = $protocol . "://" . $_SERVER["HTTP_HOST"] . $_SERVER["SCRIPT_NAME"];
$index_url = str_replace("index.php", "", $url);

$recent_keywords = ['バナナ', 'リンゴ', 'ゴリラ', 'ラッパ', 'パンツ'];

$archives = ['2020/10/06'];

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
					<div class="panel-heading">直近の検索履歴</div>
<!--					<div class="panel-body">
					</div>-->
					<div class="list-group">
						<?php foreach( $recent_keywords as $word ) { ?>
							<a href="<?= $index_url?>search.php/<?= $word ?>" class="list-group-item">
								<?= htmlspecialchars($word) ?>
							</a>
						<?php } ?>
					</div>
				</nav>
			</div>
			<div class="col col-md-offset-1 col-md-10">
				<nav class="panel panel-default">
					<div class="panel-heading">直近の検索履歴</div>
<!--					<div class="panel-body">
					</div>-->
					<div class="list-group">
						<?php foreach( $archives as $a ) { ?>
							<a href="<?= $index_url?>archive.php" class="list-group-item">
								<?= htmlspecialchars($a) ?>
							</a>
						<?php } ?>
					</div>
				</nav>
			</div>
		</div>

	</div>

<?php
Html\footer();
