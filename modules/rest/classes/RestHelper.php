<?php
class RestHelper {
	private $api;
	private $timeout = null;
	private $ver = '1';
	public function __construct($timeout = null, $ver = '1', $api = null) {
		$this->api = $api;
		$this->timeout = null;
		$this->ver = $ver;
	}
	public function __get($name) {
		if ($this->api) {
			$name = $this->api . '.' . strtolower ( $name );
		}
		return new RestHelper ( $this->timeout, $this->ver, $name );
	}
	public function __call($name, $args) {
		if ($this->api) {
			$api = $this->api . '.' . strtolower ( $name );
			$method = 'get';
			$len = count ( $args );
			if ($len == 2) {
				$method = $args [1];
				if ($method != 'get') {
					$method = 'post';
				}
			} else if ($len == 0) {
				$args [0] = array ();
			}
			if (! bcfg ( 'connect_server@rest', false )) {
				$server = new RestServer ( null );
				$server->registerClass ( new RestSevice (), '1', 'rest' );
				$server = apply_filter ( 'on_init_rest_server', $server );
				if ($server instanceof RestServer) {
					$handler = $server->getHandler ( $api, $this->ver, $method );
					if (is_callable ( $handler )) {
						$appKey = cfg ( 'appkey@rest' );
						$appSecret = cfg ( 'appsecret@rest' );
						$rst = call_user_func_array ( $handler, array ($args [0],$appKey,$appSecret ) );
						if (! is_array ( $rst )) {
							return array ('error' => '106','message' => __ ( 'Internal error.' ) );
						} else {
							return $rst;
						}
					}
				}
			} else if ($method == 'get') {
				return RestHelper::get ( $api, $args [0], $this->ver, $this->timeout );
			} else if ($method == 'post') {
				return RestHelper::post ( $api, $args [0], $this->ver, $this->timeout );
			}
		}
		return array ('error' => '404','message' => __ ( 'Internal error.' ) );
	}
	/**
	 * 使用
	 *
	 * @param unknown $api        	
	 * @param unknown $param        	
	 * @param string $ver        	
	 * @param string $timeout        	
	 */
	public static function get($api, $params = array(), $ver = '1', $timeout = null) {
		$server = RestHelper::getServer ( $api, $ver );
		if ($server) {
			list ( $servers, $appKey, $appSecret ) = $server;
			$server = RestHelper::getRandomServer ( $servers );
			$client = new RestClient ( $server, $appKey, $appSecret, $ver );
			$rtn = $client->get ( $api, $params, $timeout );
			return $rtn;
		}
		return array ('error' => 1,'message' => 'Not found service server.' );
	}
	/**
	 *
	 * @param unknown $api        	
	 * @param unknown $params        	
	 * @param string $ver        	
	 * @param string $timeout        	
	 * @return Ambigous <multitype:, mixed, multitype:number string , multitype:number Ambigous <string, string, unknown> >|multitype:number string
	 */
	public static function post($api, $params = array(), $ver = '1', $timeout = null) {
		$server = RestHelper::getServer ( $api, $ver );
		if ($server) {
			list ( $servers, $appKey, $appSecret ) = $server;
			$server = RestHelper::getRandomServer ( $servers );
			$client = new RestClient ( $server, $appKey, $appSecret, $ver );
			$rtn = $client->post ( $api, $params, $timeout );
			return $rtn;
		}
		return array ('error' => 1,'message' => 'Not found service server.' );
	}	
	private static function getServer($api, $ver) {
		static $url = false, $appKey = false, $appSecret = false;
		if ($url === false) {
			$url = cfg ( 'url@rest' );
			$appKey = cfg ( 'appkey@rest' );
			$appSecret = cfg ( 'appsecret@rest' );
		}
		
		$services = RtCache::get ( 'rest_helper_services', array () );
		
		if (! isset ( $services [$api] [$ver] )) {
			if ($url && $appKey && $appSecret) {
				$client = new RestClient ( $url, $appKey, $appSecret );
				$servers = $client->get ( 'rest.lookup', array ('service' => $api,'version' => $ver ), 10 );
				if (isset ( $servers ['servers'] ) && ! empty ( $servers ['servers'] )) {
					$services [$api] [$ver] = $servers ['servers'];
					RtCache::add ( 'rest_helper_services', $services );
				}
			}
		}
		
		if (isset ( $services [$api] [$ver] )) {
			return array ($services [$api] [$ver],$appKey,$appSecret );
		} else {
			return false;
		}
	}
	private static function getRandomServer($servers) {
		$len = count ( $servers );
		if ($len == 1) {
			return $servers [0];
		} else {
			$rand = rand ( 1, 10000 ) % $len;
			return $servers [$rand];
		}
	}
}