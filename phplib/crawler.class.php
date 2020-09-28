<?php
namespace HB;
class HB {
		
	var $desc_str = '';
	
	function __construct() {
		$rss = simplexml_load_string(file_get_contents("http://b.hatena.ne.jp/hotentry.rss"));
		$json = json_encode($rss);
		$arr = json_decode($json, true);
		foreach ($arr['item'] as $item) {
			$this->desc_str .= $item['description'];
		}
		
		var_Dump($this->desc_str);
	}
}

