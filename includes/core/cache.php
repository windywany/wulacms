<?php
/*
 * kissgo framework that keep it simple and stupid, go go go ~~ @author Windywany @package kissgo @date 12-9-16 下午6:16 $Id$
 */

/**
 * cache基类.
 */
class Cache implements ArrayAccess {
	public $expire = 0;

	/**
	 * 取系统缓存管理器.
	 *
	 * @return Cache
	 */
	public static function getCache() {
		static $cache = false;
		if ($cache === false) {
			if (bcfg('develop_mode')) {
				$cache = new Cache ();
			} else {
				$cache = apply_filter('get_cache_manager', null);
				if (!$cache instanceof Cache) {
					$cache = new Cache ();
				}
			}
		}

		return $cache;
	}

	public function offsetExists($offset) {
		return $this->has_key($offset);
	}

	public function offsetGet($offset) {
		return $this->get($offset);
	}

	public function offsetSet($offset, $value) {
		$this->add($offset, $value, $this->expire);
	}

	public function offsetUnset($offset) {
		$this->delete($offset);
	}

	/**
	 * 缓存数据.
	 *
	 * @param string $key
	 *            缓存唯一键值
	 * @param mixed  $value
	 *            要缓存的数据
	 * @param int    $expire
	 *            缓存时间
	 */
	public function add($key, $value, $expire = 0) {
	}

	/**
	 * 从缓存中取数据.
	 *
	 * @param string $key
	 *            缓存唯一键值.
	 *
	 * @return mixed 缓存数据,如果未命中则返回null
	 */
	public function get($key) {
		return null;
	}

	/**
	 * 删除一个缓存.
	 *
	 * @param string $key
	 *            缓存唯一键值
	 */
	public function delete($key) {
	}

	/**
	 * 清空所有缓存.
	 *
	 * @param boolean $check
	 *            缓存组
	 */
	public function clear($check = true) {
	}

	/**
	 * key是否存在.
	 *
	 * @param string $key
	 *
	 * @return boolean
	 */
	public function has_key($key) {
		return false;
	}
}

/**
 * APC Cache.
 *
 * @author guangfeng.ning
 *
 */
class ApcCacher extends Cache {
	public function add($key, $data, $expire = 0) {
		apc_store($key, $data);

		return true;
	}

	public function delete($key) {
		apc_delete($key);

		return true;
	}

	public function get($key) {
		return apc_fetch($key);
	}

	public function clear($check = true) {
		return apc_clear_cache('user');
	}

	public function has_key($key) {
		return apc_exists($key);
	}
}

/**
 * XCache.
 *
 * @author guangfeng.ning
 *
 */
class XCacheCacher extends Cache {
	public function add($key, $data, $expire = 0) {
		@xcache_set($key, $data);

		return true;
	}

	public function delete($key) {
		@xcache_unset($key);

		return true;
	}

	public function get($key) {
		if (@xcache_isset($key)) {
			return @xcache_get($key);
		}

		return null;
	}

	public function clear($check = true) {
		return @xcache_clear_cache(XC_TYPE_VAR);
	}

	public function has_key($key) {
		return @xcache_isset($key);
	}
}

class RedisCacher extends Cache {
	private $redis;

	public function __construct() {
		$settings    = KissGoSetting::getSetting('cluster');
		$cnf         = $settings ['redis'];
		$cid         = $settings ['id'];
		$this->redis = KissGoSetting::getRedis($cnf, $settings ['redisDB'], $cid);
	}

	public function add($key, $data, $expire = 0) {
		if ($this->redis) {
			$xdata = serialize(array($data));
			$this->redis->set($key, $xdata, $expire);

			return true;
		}

		return false;
	}

	public function delete($key) {
		if ($this->redis) {
			$this->redis->delete($key);

			return true;
		}

		return false;
	}

	public function get($key) {
		if ($this->redis) {
			$xdata = $this->redis->get($key);
			if ($xdata !== false) {
				$xdata = @unserialize($xdata);
				if (is_array($xdata)) {
					return $xdata [0];
				}
			}
		}

		return null;
	}

	public function has_key($key) {
		if ($this->redis) {
			return $this->redis->exists($key);
		}

		return false;
	}

	public function clear($check = true) {
		if ($this->redis) {
			$this->redis->flushDB();

			return true;
		}

		return false;
	}
}

/**
 * Runtime Cache Wrapper.
 *
 * @author guangfeng.ning
 *
 */
class RtCache {
	// 缓存与路径无关的数据
	private static $CACHE;
	// 缓存与路径有关的数据
	private static $LOCAL_CACHE;
	private static $PRE;

	public static function init() {
		if (!file_exists(TMP_PATH . 'cache')) {
			@mkdir(TMP_PATH . 'cache', 0755);
		}
		if (RtCache::$CACHE == null) {
			if (KissGoSetting::hasSetting('cluster') && extension_loaded('redis') && KissGoSetting::getSetting('cluster')->get('enabled')) {
				$settings     = KissGoSetting::getSetting('cluster');
				$cid          = $settings ['id'];
				RtCache::$PRE = 'kis_cache_@';
			} else {
				RtCache::$PRE = APP_NAME . '@';
			}
			if (isset ($cid)) {
				if (function_exists('apc_store')) {
					RtCache::$LOCAL_CACHE = new ApcCacher ();
				} else if (function_exists('xcache_get')) {
					RtCache::$LOCAL_CACHE = new XCacheCacher ();
				} else {
					RtCache::$LOCAL_CACHE = new Cache ();
				}
				RtCache::$CACHE = new RedisCacher ();
			} else if (function_exists('apc_store')) {
				RtCache::$LOCAL_CACHE = RtCache::$CACHE = new ApcCacher ();
			} else if (function_exists('xcache_get')) {
				RtCache::$LOCAL_CACHE = RtCache::$CACHE = new XCacheCacher ();
			} else {
				RtCache::$LOCAL_CACHE = RtCache::$CACHE = new Cache ();
			}
			if (DEBUG == DEBUG_DEBUG) {
				RtCache::$LOCAL_CACHE = RtCache::$CACHE = new Cache ();
			}
		}
	}

	public static function add($key, $data, $local = false) {
		$key   = RtCache::$PRE . $key;
		$cache = $local ? RtCache::$LOCAL_CACHE : RtCache::$CACHE;

		return $cache->add($key, $data);
	}

	public static function get($key, $local = false) {
		$key   = RtCache::$PRE . $key;
		$cache = $local ? RtCache::$LOCAL_CACHE : RtCache::$CACHE;

		return $cache->get($key);
	}

	public static function delete($key, $local = false) {
		$key   = RtCache::$PRE . $key;
		$cache = $local ? RtCache::$LOCAL_CACHE : RtCache::$CACHE;

		return $cache->delete($key);
	}

	public static function clear($local = false) {
		$cache = $local ? RtCache::$LOCAL_CACHE : RtCache::$CACHE;
		$cache->clear();
	}

	public static function exists($key, $local = false) {
		$cache = $local ? RtCache::$LOCAL_CACHE : RtCache::$CACHE;

		return $cache->has_key($key);
	}

	public static function getInfo() {
		$clz = get_class(self::$CACHE);
		if ($clz != 'Cache') {
			return $clz;
		}

		return __('Unkown');
	}
}

RtCache::init();
// END OF FILE cache.php