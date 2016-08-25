<?php
/**
 * RESTful Server.
 * @author ngf
 *
 */
class RestServer {
	private $accessCheck = null;
	private $apis = array ();
	private $exports = array ();
	private $debug;
	/**
	 * 使用$restAccessCheck创建一个RESTful Server实例。
	 *
	 * @param IRestAccessCheck $restAccessCheck        	
	 */
	public function __construct($restAccessCheck, $debug = false) {
		$this->accessCheck = $restAccessCheck;
		$this->debug = $debug;
	}
	/**
	 * 处理RESTful请求.
	 */
	public function handle($args, $method = 'get', $ver = false, $api = false) {
		if (! isset ( $this->apis [$method] ) && ! $this->debug) {
			return array ('error' => '100','message' => __ ( 'Not support method: %s', $method ) );
		}
		if (! $this->debug) {
			if (! isset ( $args ['crc'] ) || ! isset ( $args ['appkey'] ) || (! isset ( $args ['api'] ) && ! $api) || (! isset ( $args ['ver'] ) && ! $ver)) {
				return array ('error' => '101','message' => __ ( 'Bad request parameters.' ) );
			}
			
			$appSecret = $this->accessCheck->getAppSecret ( $args ['appkey'] );
			if (empty ( $appSecret )) {
				return array ('error' => '102','message' => __ ( 'Unkown application key.' ) );
			}
			$crc = $args ['crc'];
			unset ( $args ['crc'] );
			$cc = RestClient::chucksum ( $args, $appSecret );
			if ($cc != $crc) {
				return array ('error' => '103','message' => __ ( 'crc is invalid.' ) );
			}
		}
		$api = $api ? $api : $args ['api'];
		$ver = isset ( $args ['ver'] ) ? $args ['ver'] : $ver;
		
		if (! isset ( $this->apis [$method] [$api] ) || ! isset ( $this->apis [$method] [$api] [$ver] )) {
			return array ('error' => '104','message' => __ ( 'Unkown API: %s, (version: %s)', $api, $ver ) );
		}
		$handler = $this->apis [$method] [$api] [$ver];
		if (! is_callable ( $handler )) {
			return array ('error' => '105','message' => __ ( 'Not implimented.' ) );
		}
		$appkey = $args ['appkey'];
		unset ( $args ['api'], $args ['crc'], $args ['ver'], $args ['appkey'] );
		$rst = call_user_func_array ( $handler, array ($args,$appkey,$appSecret ) );
		if (! is_array ( $rst )) {
			return array ('error' => '106','message' => __ ( 'Internal error.' ) );
		}
		return $rst;
	}
	/**
	 * 取一个api对应的处理器.
	 *
	 * @param string $api        	
	 * @param string $ver        	
	 * @param string $method        	
	 * @return callable
	 */
	public function getHandler($api, $ver, $method) {
		if (isset ( $this->apis [$method] [$api] [$ver] )) {
			return $this->apis [$method] [$api] [$ver];
		}
		return null;
	}
	/**
	 * 注册一个api的处理器.
	 *
	 * @param string $api        	
	 * @param string $ver        	
	 * @param callable $handler        	
	 * @param string $method        	
	 * @return RestServer
	 */
	public function register($api, $ver, $handler, $method = 'get') {
		$this->apis [$method] [$api] [$ver] = $handler;
		$this->exports [$api] = $ver;
		return $this;
	}
	/**
	 * 将一个类的实例注册为接口实现者.
	 *
	 * @param Object $clz        	
	 * @param string $ver        	
	 * @return RestServer
	 */
	public function registerClass($clz, $ver, $alias = null) {
		$ref = new ReflectionObject ( $clz );
		$name = empty ( $alias ) ? $ref->getName () : $alias;
		$apis = $ref->getMethods ( ReflectionMethod::IS_PUBLIC );
		foreach ( $apis as $api ) {
			$apiName = $api->name;
			if (! $api->isStatic ()) {
				if (preg_match ( '#^rest_((post|get)_)?(.+)#', $apiName, $m )) {
					if ($m [2]) {
						$method = $m [2];
					} else {
						$method = 'get';
					}
					$api = $m [3];
					$this->register ( $name . '.' . $api, $ver, array ($clz,$apiName ), $method );
				}
			}
		}
		return $this;
	}
	public function getExportServices() {
		return $this->exports;
	}
	/**
	 * 同步服务列表.
	 */
	public static function syncServices() {
		if (! bcfg ( 'allow_remote@rest' )) {
			return;
		}
		$apps = dbselect ( '*' )->from ( '{rest_apps}' );
		$clients = array ();
		// 需要使用curl的异步请求。
		foreach ( $apps as $app ) {
			$url = $app ['callback_url'];
			if ($url) {
				$appKey = $app ['appkey'];
				$appSecret = $app ['appsecret'];
				$client = new RestClient ( $url, $appKey, $appSecret );
				$client->get ( 'rest.services', array ('ver' => 1 ), 10, false );
				$clients [] = $client;
			}
		}
		if ($clients) {
			$rsts = RestClient::execute ( $clients );
			if ($rsts) {
				// 将本身的rest服务提供给被管理的应用使用.
				$server = new RestServer ( null );
				$server->registerClass ( new RestSevice (), '1', 'rest' );
				$rsts [] = array ('url' => tourl ( 'rest' ),'services' => $server->getExportServices () );
				
				$services = array ();
				foreach ( $rsts as $server ) {
					if (isset ( $server ['services'] ) && ! empty ( $server ['services'] )) {
						foreach ( $server ['services'] as $api => $ver ) {
							if (isset ( $services [$api] [$ver] )) {
								$services [$api] [$ver] [] = $server ['url'];
							} else {
								$services [$api] [$ver] = array ($server ['url'] );
							}
						}
					}
				}
				RtCache::add ( 'rest_services', $services );
			}
		}
	}
}