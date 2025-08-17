<?php
namespace Crawler;

class HB {
		
	var $desc_str = '';
	
	function __construct() {
		$rss = simplexml_load_string(file_get_contents("https://b.hatena.ne.jp/hotentry.rss"));
		$json = json_encode($rss);
		$arr = json_decode($json, true);
		foreach ($arr['item'] as $item) {
			$this->desc_str .= $item['description'];
		}
	}
	
	function get_desc_str() {
		return $this->desc_str;
	}
}

class TT {
	var $desc_src = '';
	
	function __construct() {
		$html = file_get_contents('https://tsuiran.jp/trend/hourly');
		$this->desc_src = $html;
	}

	function get_desc_str() {
		return $this->desc_src;
	}
}
