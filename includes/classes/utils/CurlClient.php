<?php
class CurlClient {
	private static $agent = 'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:30.0) Gecko/20100101 Firefox/30.0';
	private $ch;
	private $domain = '';
	private $encoding = 'UTF-8';
	private $proxy = null;
	private $mcallback = null;
	private $inUsed = false;
	private $customData = array ();
	public $error = null;
	public $errorCode = 0;
	private function __construct($timeout = 30000, $headers = array(), $referer = '') {
		$curl = curl_init ();
		curl_setopt ( $curl, CURLOPT_HTTPGET, 1 );
		curl_setopt ( $curl, CURLOPT_TIMEOUT, $timeout );
		curl_setopt ( $curl, CURLOPT_AUTOREFERER, 1 );
		curl_setopt ( $curl, CURLOPT_FOLLOWLOCATION, 1 );
		curl_setopt ( $curl, CURLOPT_MAXREDIRS, 5 );
		if ($referer) {
			curl_setopt ( $curl, CURLOPT_REFERER, $referer );
		}
		curl_setopt ( $curl, CURLOPT_SSL_VERIFYPEER, false );
		curl_setopt ( $curl, CURLOPT_SSL_VERIFYHOST, false );
		curl_setopt ( $curl, CURLOPT_RETURNTRANSFER, true );
		curl_setopt ( $curl, CURLOPT_ENCODING, 'gzip, deflate' );
		curl_setopt ( $curl, CURLOPT_USERAGENT, self::$agent );
		if ($headers) {
			curl_setopt ( $curl, CURLOPT_HTTPHEADER, $headers );
		}
		$this->ch = $curl;
		if (bcfg ( 'curl_proxy_enabled@custom' )) {
			if (cfg ( 'curl_proxy@custom' )) {
				$this->setProxy ( cfg ( 'curl_proxy@custom' ) );
			}
		}
	}
	public function setReferer($referer = '') {
		if ($this->ch && $referer) {
			curl_setopt ( $this->ch, CURLOPT_REFERER, $referer );
		}
	}
	public function setCustomData($cdata) {
		$this->customData = $cdata;
	}
	public function getCustomData() {
		return $this->customData;
	}
	public function setCharset($charset = 'UTF-8') {
		$this->encoding = $charset;
		if (empty ( $this->encoding )) {
			$this->encoding = 'UTF-8';
		}
	}
	public function setProxy($proxy) {
		if ($proxy) {
			$this->proxy = $proxy;
			curl_setopt ( $this->ch, CURLOPT_PROXY, $proxy );
		} else {
			curl_setopt ( $this->ch, CURLOPT_PROXY, null );
		}
	}
	public function onStart($index) {
		if ($this->mcallback instanceof CurlMultiExeCallback) {
			return $this->mcallback->onStart ( $index, $this->ch, $this->customData );
		}
		return true;
	}
	public function onFinish($index, $data) {
		$this->inUsed = false;
		if ($this->mcallback instanceof CurlMultiExeCallback) {
			return $this->mcallback->onFinish ( $index, $data, $this->ch, $this->customData );
		}
		return null;
	}
	public function onError($index) {
		$this->inUsed = false;
		if ($this->mcallback instanceof CurlMultiExeCallback) {
			return $this->mcallback->onError ( $index, $this->ch, $this->customData );
		}
		return null;
	}
	public function getChannel() {
		return $this->ch;
	}
	/**
	 * 准备Client给CurlClientHelper使用.
	 *
	 * @param string $url
	 * @param array $data
	 * @param callback $callback
	 * @return CurlClient;
	 */
	public function preparePost($url, $data, $callback = null, $referer = '') {
		if (! $this->inUsed) {
			$this->inUsed = true;
			$this->mcallback = $callback;
			$options = array (CURLOPT_URL => $url,CURLOPT_POST => true,CURLOPT_AUTOREFERER => 0,CURLOPT_POSTFIELDS => $data,CURLOPT_REFERER => $referer,CURLOPT_HTTPGET => 0 );
			curl_setopt_array ( $this->ch, $options );
			return $this;
		}
		return null;
	}
	public function post($url, $data) {
		$options = array (CURLOPT_URL => $url,CURLOPT_POST => true,CURLOPT_RETURNTRANSFER => true,CURLOPT_POSTFIELDS => $data );
		curl_setopt_array ( $this->ch, $options );
		$rst = curl_exec ( $this->ch );
		if ($rst === false) {
			$this->error = curl_error ( $this->ch );
			$this->errorCode = '500';
			$rst = false;
		} else {
			$code = curl_getinfo ( $this->ch, CURLINFO_HTTP_CODE );
			if ($code != '200') {
				$this->error = '[' . $code . ']' . get_status_header_desc ( $code );
				$this->errorCode = $code;
				$rst = false;
			}
		}
		return $rst;
	}
	/**
	 * 准备Client给CurlClientHelper使用.
	 *
	 * @param string $url
	 * @param callback $callback
	 * @return CurlClient
	 */
	public function prepareGet($url, $callback = null, $referer = '') {
		if (! $this->inUsed) {
			$this->inUsed = true;
			$options = array (CURLOPT_URL => $url,CURLOPT_POST => 0,CURLOPT_AUTOREFERER => 1,CURLOPT_POSTFIELDS => NULL,CURLOPT_REFERER => $referer,CURLOPT_HTTPGET => 1 );
			curl_setopt_array ( $this->ch, $options );
			$this->mcallback = $callback;
			return $this;
		}
		return null;
	}
	public function get($url, $base = null, $re = false, $isImg = false) {
		set_time_limit ( 0 );
		if ($base) {
			$uinfo = CurlClient::getUrlInfo ( $url );
			$lp = false;
			if ($uinfo ['root'] != $this->domain) {
				$ip = rtrim ( $base, DS ) . DS . 'o_tfs' . DS . ($uinfo ['path'] ? $uinfo ['path'] . DS : '');
			} else {
				$ip = rtrim ( $base, DS ) . DS . ($uinfo ['path'] ? $uinfo ['path'] . DS : '');
			}
			
			if (! file_exists ( $ip )) {
				if (! @mkdir ( $ip, 0777, true )) {
					return false;
				}
			}
			
			$tmpName = $ip . $uinfo ['file'];
			if (file_exists ( $tmpName )) {
				if ($re) {
					return @file_get_contents ( $tmpName );
				} else {
					return true;
				}
			}
		}
		$curl = $this->ch;
		curl_setopt ( $curl, CURLOPT_URL, $url );
		$rst = curl_exec ( $curl );
		if ($rst === false) {
			$this->error = curl_error ( $this->ch );
			$this->errorCode = '500';
			$rst = false;
		} else {
			$code = curl_getinfo ( $curl, CURLINFO_HTTP_CODE );
			if ($code != '200') {
				$this->error = '[' . $code . ']' . get_status_header_desc ( $code );
				$this->errorCode = $code;
				$rst = false;
			}
		}
		if ($rst) {
			if (! $isImg && $this->encoding != 'UTF-8') {
				$rst = CurlClient::convertString ( $rst, $this->encoding );
			}
			if (isset ( $tmpName )) {
				if (! @file_put_contents ( $tmpName, $rst )) {
					return false;
				}
			}
		}
		return $rst;
	}
	public function getImage($url, $base = null) {
		return $this->get ( $url, $base, false, true );
	}
	public function setDomain($domain) {
		$this->domain = $domain;
	}
	public function close() {
		if ($this->ch) {
			@curl_close ( $this->ch );
		}
	}
	/**
	 * 并行执行CurlClient请求。
	 *
	 * @param array $clients
	 *        	array(CurlClient,...)。
	 * @return array results for each request. array(0=>success array,1=>failed array,2=>not start).
	 */
	public static function execute($clients) {
		$result = array (0 => array (),1 => array (),2 => array () );
		if ($clients) {
			$mh = curl_multi_init ();
			$handles = array ();
			foreach ( $clients as $i => $client ) {
				if (! ($client instanceof CurlClient)) {
					continue;
				}
				if ($client->onStart ( $i )) {
					$ch = $client->getChannel ();
					$handles [$i] = array ('h' => $ch,'c' => $client );
					curl_multi_add_handle ( $mh, $ch );
				} else {
					$result [2] [$i] = true;
				}
			}
			if (! empty ( $handles )) {
				$active = null;
				do {
					$mrc = curl_multi_exec ( $mh, $active );
					if ($active > 0) {
						usleep ( 50 );
					}
				} while ( $active > 0 );
				foreach ( $handles as $i => $h ) {
					$rtn = curl_multi_getcontent ( $h ['h'] );
					if ($rtn === false) {
						$result [1] [$i] = $h ['c']->onError ( $i );
					} else {
						$result [0] [$i] = $h ['c']->onFinish ( $i, $rtn );
					}
					curl_multi_remove_handle ( $mh, $h ['h'] );
					$h ['c']->close ();
				}
			}
			curl_multi_close ( $mh );
		}
		return $result;
	}
	public static function getClient($timeout = 60, $headers = array(), $referer = false) {
		return new CurlClient ( $timeout, $headers, $referer );
	}
	public static function getUrlInfo($url) {
		$here = explode ( '/', $url );
		array_pop ( $here );
		$here = implode ( '/', $here ) . '/';
		$root = preg_replace ( '#^((htt|ft)ps?://.+?/).*#i', '\1', $here );
		$path = str_replace ( $root, '', $here );
		$file = str_replace ( $here, '', $url );
		$pos = strpos ( $file, '?' );
		if ($pos) {
			$file = substr ( $file, 0, $pos );
		}
		$pos = strpos ( $file, '#' );
		if ($pos) {
			$file = substr ( $file, 0, $pos );
		}
		if (isset ( $_SESSION ['babaurl_mapping'] )) {
			$mapping = $_SESSION ['babaurl_mapping'];
			$k = md5 ( $url );
			if ($mapping && isset ( $mapping [$k] )) {
				$file = $mapping [$k];
			}
		}
		return array ('root' => $root,'here' => $here,'path' => trim ( $path, '/' ),'file' => $file );
	}
	public static function getURL($url, $info) {
		if (preg_match ( '#^(htt|ft)ps?://.+#i', $url )) {
			return $url;
		} else if ($url {0} === '/') {
			$root = $info ['root'];
			return $root . ltrim ( $url, '/' );
		} else {
			return $info ['here'] . $url;
		}
	}
	public static function getScripts($content) {
		if (preg_match_all ( '#<script[^>]+?src\s*=\s*[\'"](.+?)[\'"][^>]*?>#imus', $content, $ms, PREG_PATTERN_ORDER )) {
			return $ms [1];
		}
		return array ();
	}
	public static function getPages($content, $base) {
		$pages = array ();
		if (preg_match_all ( '#<a[^>]+?href\s*=\s*[\'"](.+?)[\'"][^>]*?>#imus', $content, $ms, PREG_PATTERN_ORDER )) {
			$pages = $ms [1];
		}
		if ($pages) {
			$newPages = array ();
			$urlinfo = self::getUrlInfo ( $base );
			foreach ( $pages as $p ) {
				$newPages [] = self::getURL ( $p, $urlinfo );
			}
			$pages = $newPages;
		}
		return $pages;
	}
	public static function getImages($content) {
		$imgs = array ();
		if (preg_match_all ( '#<img[^>]+?src\s*=\s*[\'"](.+?)[\'"][^>]*?>#imus', $content, $ms, PREG_PATTERN_ORDER )) {
			foreach ( $ms [1] as $img ) {
				if (preg_match ( '/.*data:image.+/i', $img )) {
					continue;
				}
				$imgs [] = $img;
			}
		}
		if (preg_match_all ( '#url\s*\((?![\s\'"]*data:)[\'"]?(.+?)[\'"]?\s*\)#ims', $content, $ms1, PREG_PATTERN_ORDER )) {
			$imgs = array_merge ( $imgs, $ms1 [1] );
		}
		return $imgs;
	}
	public static function getLinks($content) {
		if (preg_match_all ( '#<link[^>]+?href\s*=\s*[\'"](.+?)[\'"][^>]*?>#imus', $content, $ms, PREG_PATTERN_ORDER )) {
			return $ms [1];
		}
		return array ();
	}
	public static function getStyles($content) {
		if (preg_match_all ( '#<style[^>]*?>(.+?)</style>#imus', $content, $ms, PREG_PATTERN_ORDER )) {
			return $ms [1];
		}
		return array ();
	}
	public static function getImageFromCss($content) {
		if (preg_match_all ( '#url\s*\((?!\s*data:)[\'"]?(.+?)[\'"]?\s*\)#ims', $content, $ms, PREG_PATTERN_ORDER )) {
			return $ms [1];
		}
		return array ();
	}
	public static function convertString($str, $encoding) {
		if ($encoding != 'UTF-8') {
			$str = mb_convert_encoding ( $str, 'UTF-8', $encoding );
		} else {
			$str = mb_convert_encoding ( $str, 'UTF-8' );
		}
		return $str;
	}
}
