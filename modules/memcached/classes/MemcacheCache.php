<?php
class MemcacheCache extends Cache {
	private $cache = null;
	private $compress_enabled = null;
	private $prefix;
	
	/**
	 *
	 * @param Memcache $cache
	 */
	public function __construct($cache, $compress_enabled = false) {
		$this->cache = $cache;
		if ($compress_enabled) {
			$this->compress_enabled = MEMCACHE_COMPRESSED;
		}
		
		$this->prefix = md5 ( MCACHE_PREFIX ) . ':';
	}
	/*
	 * (non-PHPdoc) @see Cache::add()
	 */
	public function add($key, $value, $expire = 0) {
		if($expire>0){
			$expire = time () + $expire;
		}
		$this->cache->set ( $this->prefix . $key, $value, $this->compress_enabled,  $expire);
	}
	
	/*
	 * (non-PHPdoc) @see Cache::clear()
	 */
	public function clear($check = true) {
		$this->cache->flush ();
	}
	
	/*
	 * (non-PHPdoc) @see Cache::delete()
	 */
	public function delete($key) {
		$this->cache->delete ( $this->prefix . $key );
	}
	
	/*
	 * (non-PHPdoc) @see Cache::get()
	 */
	public function get($key) {
		return $this->cache->get ( $this->prefix . $key );
	}
}