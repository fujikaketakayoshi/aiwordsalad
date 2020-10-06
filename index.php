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
	</div>

<?php
Html\footer();
