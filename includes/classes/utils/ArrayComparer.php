<?php
/**
 * 自定义数组排序器。
 * @author ngf
 *
 */
class ArrayComparer {
	private $key;
	private $order;
	private function __construct($key, $order = 'a') {
		if ($order != 'a') {
			$this->order = 'd';
		} else {
			$this->order = 'a';
		}
		$this->key = $key;
	}
	public function doCompare($v1, $v2) {
		$key = $this->key;
		if (! isset ( $v1 [$this->key] )) {
			return 0;
		}
		if (! isset ( $v2 [$key] )) {
			return 0;
		}
		if ($v1 [$key] < $v2 [$key]) {
			return $this->order == 'd' ? 1 : - 1;
		} else if ($v1 [$key] > $v2 [$key]) {
			return $this->order == 'd' ? - 1 : 1;
		}
		return 0;
	}
	public function doStrCompare($v1, $v2) {
		$key = $this->key;
		if (! isset ( $v1 [$this->key] )) {
			return 0;
		}
		if (! isset ( $v2 [$key] )) {
			return 0;
		}
		$rst = strcmp ( $v1 [$key], $v2 [$key] );
		if ($rst < 0) {
			return $this->order == 'd' ? 1 : - 1;
		} else if ($rst > 0) {
			return $this->order == 'd' ? - 1 : 1;
		}
		return 0;
	}
	public function doBoolCompare($v1, $v2) {
		$key = $this->key;
		if (! isset ( $v1 [$this->key] )) {
			return 0;
		}
		if (! isset ( $v2 [$key] )) {
			return 0;
		}
		if (! $v1 && $v2) {
			$rst = 1;
		} else if ($v1 && ! $v2) {
			$rst = - 1;
		} else {
			$rst = 0;
		}
		
		if ($rst < 0) {
			return $this->order == 'd' ? 1 : - 1;
		} else if ($rst > 0) {
			return $this->order == 'd' ? - 1 : 1;
		}
		return 0;
	}
	public static function compare($key, $order = 'a') {
		return array (new ArrayComparer ( $key, $order ),'doCompare' );
	}
	public static function str($key, $order = 'a') {
		return array (new ArrayComparer ( $key, $order ),'doStrCompare' );
	}
	public static function bool($key, $order = 'a') {
		return array (new ArrayComparer ( $key, $order ),'doBoolCompare' );
	}
}

?>