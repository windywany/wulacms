<?php
class CachedController extends NonSessionController {
	public function index($file) {
		$res_file = TMP_PATH . 'cache' . DS . $file;
		if (! file_exists ( $res_file )) {
			Response::respond ( 404 );
		}
		$time = filemtime ( $res_file );
		$type = pathinfo ( $file, PATHINFO_EXTENSION );
		if ($type == 'js') {
			@header ( 'Content-Type: application/javascript' );
		} else if ($type == 'css') {
			@header ( 'Content-Type: text/css' );
		} else {
			@header ( 'Content-Type: text/plain' );
		}
		$expire = icfg ( 'resource_expire@mem', 31536000 );
		if (! bcfg ( 'develop_mode' )) {
			out_cache_header ( $time, $expire );
		}
		if (isset ( $_SERVER ['HTTP_IF_MODIFIED_SINCE'] ) && strtotime ( $_SERVER ['HTTP_IF_MODIFIED_SINCE'] ) == $time) {
			$protocol = $_SERVER ["SERVER_PROTOCOL"];
			if ('HTTP/1.1' != $protocol && 'HTTP/1.0' != $protocol) {
				$protocol = 'HTTP/1.0';
			}
			$status_header = "$protocol 304 Not Modified";
			@header ( $status_header, true, 304 );
			if (php_sapi_name () == 'cgi-fcgi') {
				@header ( "Status: 304 Not Modified" );
			}
		} else {
			echo $this->getContent ( $res_file, $time );
		}
		exit ();
	}
	private function getContent($file, $time) {
		$key = md5 ( $file . $time );
		$cache = Cache::getCache ();
		$content = $cache->get ( $key );
		if (! $content) {
			$content = file_get_contents ( $file );
			$expire = icfg ( 'resource_expire@mem', 31536000 );
			$cache->add ( $key, $content, $expire );
		}
		return $content;
	}
}