<?php
defined ( 'KISSGO' ) or exit ( 'No direct script access allowed' );
function hook_for_get_memcached_cache_manager($cache) {
	if ($cache == null) {
		if (bcfg ( 'enabled@mem' ) && (extension_loaded ( 'memcache' ) || extension_loaded ( 'memcached' ))) {
			$servers = cfg ( 'servers@mem' );
			if ($servers) {
				$servers = explode ( "\n", $servers );
				if (! empty ( $servers )) {
					$cache = extension_loaded ( 'memcached' ) ? new Memcached () : new Memcache ();
					$count = 0;
					foreach ( $servers as $server ) {
						$addr = explode ( ':', trim ( $server ) );
						if (! isset ( $addr [1] )) {
							$addr [1] = 11211;
						}
						if (! isset ( $addr [2] )) {
							$addr [2] = 1;
						}
						if ($cache->addserver ( $addr [0], intval ( $addr [1] ), intval ( $addr [2] ) )) {
							$count ++;
						} else {
							log_warn ( '无法连接缓存服务器:' . $server );
						}
					}
					if ($count > 0) {
						if (extension_loaded ( 'memcached' )) {
							$cache = new MemcachedCache ( $cache );
						} else {
							$cache = new MemcacheCache ( $cache, bcfg ( 'compress_enabled@mem' ) );
						}
					}
				}
			}
		}
	}
	return $cache;
}
function hook_for_show_system_info_memcache($html) {
	$cache = Cache::getCache ();
	$info [] = '<p>缓存扩展：';
	$info [] = extension_loaded ( 'memcached' ) ? 'memcached' : (extension_loaded ( 'memcache' ) ? 'memcache' : '无');
	$info [] = '&nbsp;&nbsp;&nbsp;';
	$info [] = ',实现：' . get_class ( $cache );
	$info [] = ', 状态：' . (!bcfg ( 'develop_mode' ) && bcfg ( 'enabled@mem' ) && (extension_loaded ( 'memcache' ) || extension_loaded ( 'memcached' )) ? '已启用' : '未启用');
	$info [] = ', 压缩：' . (bcfg ( 'compress_enabled@mem' ) ? '已启用' : '未启用');
	$info [] = ', 默认缓存时间：' . cfg ( 'cache_expire@mem' );
	
	$cache->add ( 'test_con', '11' );
	$rst = $cache->get ( 'test_con' );
	$info [] = '秒, 测试：' . ($rst == '11' ? '成功' : '失败');
	$info [] = '</p>';
	return $html . implode ( '', $info );
}
function hook_for_on_render_homepage_memcache($data) {
	$data ['expire'] = icfg ( 'index_expire@mem', 0 );
	return $data;
}