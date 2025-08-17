<?php
namespace KeyValueFile;

class KeyValueFile {
	
	/**
     * @var bool
     */
	var $lock = false;

	/**
     * @var string
     */
	var $salt = 'kvfc';

	/**
     * @var int|bool
     */
	var $expires = false;

	/**
     * @var int
     */
	var $expire_span = 0;

	/**
     * @var int
     */
	var $path_depth = 2;

	/**
     * @var string
     */
	var $path = './';

	/**
     * @var string|null
     */
	var $tmp_path = null;

	/**
     * @var string
     */
	var $key = '';

	/**
     * @var string
     */
	var $key_path = './';

	/**
     * @var bool
     */
	var $hashing = true;

	/**
     * @var string|null
     */
	var $path_key_path;

	/**
     * @var string|null
     */
	var $key_fullpath;

	/**
     * @var bool
     */
	var $is_hashed = false;
	
	/**
	 * @param string $path
	 * @param array<string, string|int|bool> $option
	 */
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
			umask((int) $option['umask']);
		}
		if ( isset($option['expires']) && $option['expires'] !== false) {
			$this->expires = (int) $option['expires'];
		}
		
		// 書き込みファイルのmvロック用ディレクトリ作成
		$this->tmp_path = $this->path . '/tmp_datum';
		if ( ! file_exists($this->tmp_path) ) {
			mkdir($this->tmp_path, 0777, true);
		}
		
	}
	
	/**
	 * @param int $span_time
	 * @return void
	 */
	function set_expire_span($span_time) {
		$this->expire_span = $span_time;
	}
	
	/**
	 * @param string $salt
	 * @return void
	 */
	function set_salt($salt) {
		$this->salt = $salt;
	}
	
	/**
	 * @param string $hash_key
	 * @return bool
	 */
	function has_hash_key($hash_key) {
		$this->is_hashed = true;
		return $this->has_key($hash_key);
	}
	
	/**
	 * @param string $key
	 * @return bool
	 */
	function has_key($key) {
		$this->conv_key_path($key);
		return file_exists($this->path_key_path . $this->key);
	}
	
	/**
	 * @param string $key
	 * @return void
	 */
	function conv_key_path($key) {
		$key = $this->hashing ? sha1($key . $this->salt) : $key;
		$key_path = '';
		if ( $this->path_depth > 0 ) {
			foreach ( range(0, $this->path_depth - 1 )as $i ) {
				$key_path .= substr($key, (int) $i * 2, 2) . '/';
			}
		}
		
		$this->key = $key;
		$this->key_path = $key_path;
		$this->path_key_path = $this->path . '/' . $key_path;
		$this->key_fullpath = $this->path . '/' . $key_path . $key;
	}
	
	/**
	 * @param string $key
	 * @param mixed $value
	 * @return bool
	 */
	function set_keyvalue($key, $value) {
		if ( $this->key == '' ) {
			$this->conv_key_path($key);
		}
		
//		$lock_dir = $this->path_key_path . '.lock';
//		if ( ! file_exists($lock_dir) ) {
//		}
		
		if ( $this->path_key_path !== null && !file_exists($this->path_key_path) ) {
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
	
	/**
	 * @param string $hash_key
	 * @return mixed
	 */
	function get_hash_keyvalue($hash_key) {
		return $this->get_keyvalue($hash_key);
	}
	
	/**
	 * @param string $key
	 * @return mixed|bool
	 */
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
	
	/**
	 * @param string $key
	 * @return bool
	 */
	function is_cache_available($key) {
		$cache = [];
		if ( $this->has_key($key) ) {
			$cache = $this->get_raw_cache();
		} else {
			return false;
		}
		return time() > $cache['expires'] ? false : true;
	}
	
	/**
	 * @return mixed
	 */
	function get_raw_cache() {
		if ($this->key_fullpath) {
			return unserialize((string) file_get_contents($this->key_fullpath));
		}
	}
	
	/**
	 * @return int|false
	 */
	function get_cache_mtime() {
		if ($this->key_fullpath) {
			return filemtime($this->key_fullpath);
		} else {
			return false;
		}
		
	}
	
	/**
	 * @param string $key
	 * @return bool
	 */
	function remove_file($key) {
		if ( !$this->has_key($key) || !$this->key_fullpath) return false;
		// remove dirも入れるか？
		return unlink($this->key_fullpath);
	}
	
	/**
	 * @return false
	 */
	function _keyError() {
		echo "Doesn't exist file. $this->key";
		return false;
	}
}

