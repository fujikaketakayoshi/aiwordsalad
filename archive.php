<?php
require_once('phplib/keyvalue_file.class.php');
require_once('phplib/html.class.php');

use KeyValueFile\KeyValueFile;

/** @var string $protocol */
$protocol = $_SERVER["REQUEST_SCHEME"] ?? 'http';
/** @var string $http_host */
$http_host = $_SERVER['HTTP_HOST'];
/** @var string $script_name */
$script_name = $_SERVER['SCRIPT_NAME'];
$url = $protocol . "://" . $http_host . $script_name;
$index_url = str_replace("archive.php", "", $url);

if ( !isset($_GET['date'])) header("Location: " . $index_url);
/** @var string $date */
$date = $_GET['date'];

$file = new KeyValueFile('phplib/tmp');
$archive_key = $date;
$archives = [];
if ( $file->has_key($archive_key) ) {
	/** @var array<string> $archives */
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
