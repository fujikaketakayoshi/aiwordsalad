<?php
require_once('phplib/keyvalue_file.class.php');
require_once('phplib/html.class.php');

use KeyValueFile\KeyValueFile;

$protocol = isset($_SERVER["HTTP_X_FORWARDED_PROTO"]) ? $_SERVER["HTTP_X_FORWARDED_PROTO"] : 'http';
$url = $protocol . "://" . $_SERVER["HTTP_HOST"] . $_SERVER["SCRIPT_NAME"];
$index_url = str_replace("archive.php", "", $url);

if ( !isset($_GET['date'])) header("Location: " . $index_url);
$date = $_GET['date'];

$file = new KeyValueFile('phplib/tmp');
$archive_key = $date;
$archives = [];
if ( $file->has_key($archive_key) ) {
	$archives = $file->get_keyvalue($archive_key);
}

Html\header('AI Wordsalad', $index_url);
?>
	<div class="container">
		<div class="row">
			<div class="col col-md-offset-1 col-md-10">
				<nav class="panel panel-default">
					<div class="panel-heading"><?= htmlspecialchars($date) ?>のAIワードサラダ</div>
<!--					<div class="panel-body">
					</div>-->
					<div class="list-group">
						<?php foreach( $archives as $word ) { ?>
							<a href="<?= $index_url?>search.php/<?= htmlspecialchars($word) ?>" class="list-group-item">
								<?= htmlspecialchars($word) ?>
							</a>
						<?php } ?>
					</div>
				</nav>
			</div>
		</div>

	</div>

<?php
Html\footer();
