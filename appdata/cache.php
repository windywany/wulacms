<?php
// do cache thing
$do = false;
if (isset ( $_SERVER ['PATH_INFO'] )) {
	$do = trim ( $_SERVER ['PATH_INFO'], '/' );
} else if (defined ( 'REQUEST_URL' )) {
	$do = trim ( REQUEST_URL, '/' );
} else if (isset ( $_GET ['_url'] )) {
	$do = $_GET ['_url'];
}
defined ( 'REQUEST_URL' ) or define ( 'REQUEST_URL', $do );
defined ( 'PCACHE_PREFIX' ) or define ( 'PCACHE_PREFIX', md5 ( WEB_ROOT ) );
$domain = $_SERVER ['HTTP_HOST'];
$cache = false;
if (! empty ( $do ) && ! isset ( $_GET ['preview'] )) {
	$qstr = get_query_string ();
	$cacher = Cache::getCache ();
	$cache_key = md5 ( PCACHE_PREFIX . $domain . ':' . $do . '?' . $qstr );
	$cache = $cacher->get ( $cache_key );
}
if ($cache) {
	function cache_ob_out_handler($content) {
		return $content;
	}
	if (@ob_get_status ()) {
		@ob_end_clean ();
	}
	@ob_start ( 'cache_ob_out_handler' );
	if (defined ( 'GZIP_ENABLED' ) && GZIP_ENABLED && extension_loaded ( "zlib" )) {
		$gzip = @ini_get ( 'zlib.output_compression' );
		if (! $gzip) {
			@ini_set ( 'zlib.output_compression', 1 );
		}
		@ini_set ( 'zlib.output_compression_level', 9 );
	} else {
		@ini_set ( 'zlib.output_compression', 0 );
		@ini_set ( 'zlib.output_compression_level', - 1 );
	}
	$cache = apply_filter ( 'alter_page_cache', $cache );
	list ( $time, $content, $gzipx, $expire ) = $cache;
	if (isset ( $_SERVER ['HTTP_IF_MODIFIED_SINCE'] ) && strtotime ( $_SERVER ['HTTP_IF_MODIFIED_SINCE'] ) === $time) {
		$protocol = $_SERVER ["SERVER_PROTOCOL"];
		if ('HTTP/1.1' != $protocol && 'HTTP/1.0' != $protocol) {
			$protocol = 'HTTP/1.0';
		}
		$status_header = "$protocol 304 Not Modified";
		out_cache_header ( $time, $expire );
		@header ( $status_header, true, 304 );
		if (php_sapi_name () == 'cgi-fcgi') {
			@header ( 'Status: 304 Not Modified' );
		}
	} else {
		if (! isset ( $_GET ['preview'] ) && $time !== 0) {
			out_cache_header ( $time, $expire );
		} else if ($time == 0) {
			Response::nocache ();
		}
		if (preg_match ( '#.+\.xml$#i', $do )) {
			@header ( "Content-Type: text/xml" );
		} else if (preg_match ( '#.+\.jsp$#i', $do )) {
			@header ( "Content-Type: application/javascript" );
		} else if (preg_match ( '#.+\.json$#i', $do )) {
			@header ( "Content-Type: application/json" );
		} else {
			@header ( "Content-Type: text/html" );
		}
		echo $content;
	}
	exit ();
} else {
	bind ( 'before_output_content', '_filter_output_content_for_cache', 100 );
	function _filter_output_content_for_cache($content) {
		$domain = $_SERVER ['HTTP_HOST'];
		$url = false;
		if (! isset ( $_GET ['preview'] )) {
			$router = Router::getRouter ();
			$url = $router->getCurrentURL ();
			if ($url && ! bcfg ( 'develop_mode' )) {
				$page = $router->getCurrentPage ();
				$expire2 = intval ( cfg ( 'cache_expire@mem' ) );
				$expire1 = isset ( $page ['expire'] ) ? intval ( $page ['expire'] ) : 0;
				if ($expire1 < 0) {
					$expire = - 1;
				} else if ($expire1 == 0) {
					$expire = $expire2;
				} else {
					$expire = $expire1;
				}
				if ($expire > 0) {
					$qstr = get_query_string ();
					$cacher = Cache::getCache ();
					$cache_key = md5 ( PCACHE_PREFIX . $domain . ':' . $url . '?' . $qstr );
					$time = apply_filter ( 'alter_page_modified_time', time () );
					$gzip_content = '';
					$cache = array ($time == 0 ? time () : $time,$content,$gzip_content,$expire );
					$cacher->add ( $cache_key, $cache, $expire );
					if ($time > 0) {
						out_cache_header ( $time, $expire );
					} else if ($time == 0) {
						Response::nocache ();
					}
				}
			}
		} else if (isset ( $_GET ['preview'] ) && $_GET ['preview'] == '_c2c_' && icando ( 'cmc:system' )) {
			$router = Router::getRouter ();
			$url = $router->getCurrentURL ();
			$qstr = get_query_string ();
			if ($url) {
				$cacher = Cache::getCache ();
				if ($domain != 'all') {
					$cache_key = md5 ( PCACHE_PREFIX . $domain . ':' . $url . '?' . $qstr );
					$cacher->delete ( $cache_key );
				}
				$cache_key = md5 ( 'all:' . $url . '?' . $qstr );
				$cacher->delete ( $cache_key );
			}
		}
		if ($url) {
			if (preg_match ( '#.+\.xml$#i', $url )) {
				@header ( "Content-Type: text/xml" );
			} else if (preg_match ( '#.+\.jsp$#i', $url )) {
				@header ( "Content-Type: application/javascript" );
			} else if (preg_match ( '#.+\.json$#i', $url )) {
				@header ( "Content-Type: application/json" );
			} else {
				@header ( "Content-Type: text/html" );
			}
		}
		return $content;
	}
}
unset ( $do );
// end of