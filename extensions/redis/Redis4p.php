<?php
namespace redis;

class Redis4p {

	private static $instance = [];

	private static $db = false;

	private static $host = false;

	private static $port = false;

	private static $auth = false;

	private function __construct() {
	}

	public static function select($db) {
		self::$db = $db;
	}

	/**
	 * get redis instance.
	 *
	 * @param int $db 数据库.
	 *
	 * @return \Redis Redis实例.
	 * @throws \Exception when cannot connect to the redis server
	 */
	public static function getRedis($db = null) {
		if (self::$db === false) {
			self::$db = cfg('db@redis', '8');
		}
		if (self::$host === false) {
			self::$host = cfg('host@redis', 'localhost');
			self::$port = cfg('port@redis', '6379');
			self::$auth = cfg('@redis');
		}
		$db = $db ? $db : self::$db;
		if (!isset (self::$instance [ $db ])) {
			$libRedis = new \Redis ();
			if ($libRedis->connect(self::$host, self::$port)) {
				if (self::$auth) {
					$libRedis->auth(self::$auth);
				}
				$libRedis->select($db);
				self::$instance [ $db ] = $libRedis;
			} else {
				throw new \Exception ('cannot connect to the redis db');
			}
		}

		return self::$instance [ $db ];
	}

	/**
	 * @param string       $key
	 * @param string|array $data
	 *
	 * @return bool
	 */
	public static function set($key, $data) {
		try {
			$redis = self::getRedis();
			if (is_array($data)) {
				$data = json_encode($data);
			}
			$redis->set($key, $data);

			return true;
		} catch (\Exception $e) {
			return false;
		}
	}

	/**
	 * @param string $key
	 *
	 * @return bool|null|string
	 */
	public static function get($key) {
		try {
			$redis = self::getRedis();
			$data  = $redis->get($key);

			return $data;
		} catch (\Exception $e) {
			return null;
		}
	}

	/**
	 * @param string $key
	 *
	 * @return array
	 */
	public static function getJSON($key) {
		$data = self::get($key);
		if ($data) {
			return @json_decode($data, true);
		}

		return [];
	}

	/**
	 * 加锁
	 *
	 * @param string $key     锁名
	 * @param int    $timeout 超时.
	 *
	 * @return bool true 可用
	 *
	 */
	public static function lock($key = '', $timeout = 600) {
		try {
			$redis = self::getRedis();
			$key   = trim($key);
			if (empty ($key)) {
				return false;
			}
			if ($redis->exists($key) == true) {
				return false;
			}

			return $redis->set($key, 1, $timeout);
		} catch (\Exception $e) {
			return false;
		}
	}

	/**
	 * 解锁.
	 *
	 * @param string $key 锁名.
	 */
	public static function unlock($key = '') {
		try {
			$redis = self::getRedis();
			$redis->delete($key);
		} catch (\Exception $e) {

		}
	}
}
