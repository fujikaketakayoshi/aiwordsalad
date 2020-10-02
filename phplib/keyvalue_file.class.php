<?php
namespace KeyValueFile;

class KeyValueFile {
	
	var $lock = false;
	var $salt = 'kvfc';
	var $expires = false;
	var $expire_span = 0;
	var $path_depth = 2;
	var $path = './';
	var $tmp_path = null;
	var $key = '';
	var $key_path = './';
	var $hashing = true;
//	var $is_hashed = false;
//	var $mkdir_permission = 777;
//	var $tmp_path = '/var/tmp/';
	
	
	function __construct( $path, $option = null ) {
		$this->path = $path;
		// パスの書き込みチェック
		if ( ! file_exists($path) ) {
			echo "error: directory \"$path\" does not exists";
			exit;
		}
		elseif ( ! is_writable($path) ) {
			echo "error: directory \"$path\" is not writable";
			exit;
		}
		
		// オプションの処理
		if ( isset($option['plain']) === true ) {
			$this->hashing = false;
			$this->path_depth = 0;
		}
		if ( isset($option['umask']) ) {
			umask($option['umask']);
		}
		
		// 書き込みファイルのmvロック用ディレクトリ作成
		$this->tmp_path = $this->path . '/tmp_datum';
		if ( ! file_exists($this->tmp_path) ) {
			mkdir($this->tmp_path, 0777, true);
		}
		
	}
	
	function set_expire_span($span_time) {
		$this->expire_span = $span_time;
	}
	
	
	function set_salt($salt) {
		$this->salt = $salt;
	}
	
	function has_hash_key($hash_key) {
		$this->is_hashed = true;
		return $this->has_key($hash_key);
	}
	
	function has_key($key) {
		$this->conv_key_path($key);
		return file_exists($this->path_key_path . $this->key);
	}
	
	function conv_key_path($key) {
		$key = $this->hashing ? sha1($key . $this->salt) : $key;
		if ( $this->path_depth > 0 ) {
			$key_path = '';
			foreach ( range(0, $this->path_depth - 1 )as $i ) {
				$key_path .= substr($key, $i * 2, 2) . '/';
			}
		}
		
		$this->key = $key;
		$this->key_path = $key_path;
		$this->path_key_path = $this->path . '/' . $key_path;
		$this->key_fullpath = $this->path . '/' . $key_path . $key;
//		dde(get_object_vars($this));
	}
	
	function set_keyvalue($key, $value) {
		if ( $this->key == '' ) {
			$this->conv_key_path($key);
		}
		
//		$lock_dir = $this->path_key_path . '.lock';
//		if ( ! file_exists($lock_dir) ) {
//		}
		
		if ( ! file_exists($this->path_key_path) ) {
			// パーミッションはconstructorのumaskで調節する
			mkdir($this->path_key_path, 0777, true);
		}
		
//		if ( $this->has_key($key) ) return false;
		// serializedデータ作成
		$serialized_data = serialize(array(
			'value'   => $value,
			'expires' => $this->expires ? time() + $this->expire_span : 0,
		));
		
		// 一時データ保存
		$tmp_file = $this->tmp_path . '/' . $this->key;
		$writing = error_log($serialized_data, 3, $tmp_file);
		if ( ! $writing ) return false;
		
		// 一時データ移動
		$cmd = "mv -f $tmp_file $this->key_fullpath";
		exec($cmd, $arr, $res);
		if ( $res !== 0 ) return false;
		
		return true;
	}
	
	function get_hash_keyvalue($hash_key) {
		return $this->get_keyvalue($hash_key);
	}
	
	
	function get_keyvalue($key) {
//		$this->set_is_hashed(true);
		if ( $this->has_key($key) ) {
			$tmp = $this->get_raw_cache();
			return $tmp['value'];
		}
		else {
			return false;
		}
	}
	
	
	function is_cache_available($key) {
		$cache = [];
		if ( $this->has_key($key) ) {
			$cache = $this->get_raw_cache();
		} else {
			return false;
		}
		return time() > $cache['expires'] ? false : true;
	}
	
	
	function get_raw_cache() {
		return unserialize(file_get_contents($this->key_fullpath));
	}
	
	function get_cache_mtime() {
		return filemtime($this->key_fullpath);
	}
	
	function remove_file($key) {
		if ( ! $this->has_key($key) ) return false;
		
		// remove dirも入れるか？
		return unlink($this->key_fullpath);
	}
	
	function _keyError() {
		echo "Doesn't exist file. $this->key";
		return false;
	}
}

