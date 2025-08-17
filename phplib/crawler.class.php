<?php
namespace Crawler;

class HB {
	
	/**
     * @var string
     */
	public $desc_str = '';
	
	function __construct() {
		$rss = simplexml_load_string((string) file_get_contents("https://b.hatena.ne.jp/hotentry.rss"));
		$json = (string) json_encode($rss);
		$arr = json_decode($json, true);
		foreach ($arr['item'] as $item) {
			$this->desc_str .= $item['description'];
		}
	}
	
	/**
	* @return string
	*/
	function get_desc_str(): string {
		return $this->desc_str;
	}
}

class TT {
	/**
     * @var string
     */
	public $desc_src = '';
	
	function __construct() {
		$html = (string) file_get_contents('https://tsuiran.jp/trend/hourly');
		$this->desc_src = $html;
	}
	
	/**
	* @return string
	*/
	function get_desc_str(): string {
		return $this->desc_src;
	}
}
