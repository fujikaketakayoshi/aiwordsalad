<?php
require_once('phplib/crawler.class.php');

use Crawler\TT;

$protocol = isset($_SERVER["HTTP_X_FORWARDED_PROTO"]) ? $_SERVER["HTTP_X_FORWARDED_PROTO"] : 'http';
$url = $protocol . "://" . $_SERVER["HTTP_HOST"] . $_SERVER["SCRIPT_NAME"];
$index_url = str_replace("twittertrend_scraping.php", "", $url);

$tt = new TT();

preg_match_all("/<h2><span>(.*?)<\/span><\/h2>/", $tt->get_desc_str(), $match);		
$trend_words = $match[1];

foreach ( $trend_words as $w ) {
	$search = $index_url . "search.php/" . $w;
	file_get_contents($search);
}
