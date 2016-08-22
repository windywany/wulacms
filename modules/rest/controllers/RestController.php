<?php
/**
 * RESTFul 服务器.
 *
 * @author Guangfeng
 *
 */
class RestController extends NonSessionController {
	private $server;
	private $debuging = false;
	public function preRun($method) {
		if (! bcfg ( 'allow_remote@rest' )) {
			Response::respond ( 403 );
		}
		$offline = bcfg ( 'isOffline', false );
		if ($offline) {
			$allow = false;
			$ips = cfg ( 'allowIps' );
			if ($ips) {
				$ips = explode ( "\n", $ips );
				$ip = Request::getIp ();
				if ($ip) {
					$allow = in_array ( $ip, $ips );
				}
			}
			if (! $allow) {
				$return ['error'] = '1024';
				$return ['message'] = cfg ( 'offlineMsg', '系统正在维护，请耐心等待...' );
				$view = new JsonView ( $return );
				echo $view->render ();
				exit ();
			}
		}
		if (bcfg ( 'connect_server@rest' )) {
			$restAccessCheck = new RemoteRestAccessCheck ();
		} else {
			$restAccessCheck = new DefaultRestAccessCheck ();
		}
		$this->debuging = bcfg ( 'develop_mode' ) && rqset ( 'debug' );
		$this->server = new RestServer ( $restAccessCheck, $this->debuging );
		$this->server->registerClass ( new RestSevice (), '1', 'rest' );
		$this->server = apply_filter ( 'on_init_rest_server', $this->server );
		if (cfg ( 'session_name@rest' )) {
			bind ( 'get_session_name', array (this,'get_session_name' ) );
		}
	}
	public function index_get($ver = '1.0', $api = '') {
		if ($api) {
			return $this->handle ( $_GET, 'get', $ver, $api );
		} else {
			return $this->handle ( $_GET, 'get', $ver );
		}
	}
	public function index_post($ver = '1.0', $api = '') {
		if ($api) {
			return $this->handle ( $_POST, 'post', $ver, $api );
		} else {
			return $this->handle ( $_POST, 'post', $ver );
		}
	}
	public function index_put($ver = '1.0', $api = '') {
		if ($api) {
			return $this->handle ( $_POST, 'put', $ver, $api );
		} else {
			return $this->handle ( $_POST, 'put', $ver );
		}
	}
	public function get_session_name() {
		return cfg ( 'session_name@rest' );
	}
	private function handle($args, $method, $ver, $api = false) {
		unset ( $args ['_url'] );
		$return = $this->server->handle ( $args, $method, $ver, $api );
		return new JsonView ( $return );
	}
}