<?php

/**
 * RESTFul 服务器.
 *
 * @author Guangfeng
 *
 */
class RestController extends NonSessionController {
	/**
	 * @var RestServer
	 */
	private $server;

	private $debuging = false;

	public function preRun($method) {
		if (!bcfg('allow_remote@rest')) {
			Response::respond(403);
		}
		$offline = bcfg('isOffline', false);
		if ($offline) {
			$allow = false;
			$ips   = cfg('allowIps');
			if ($ips) {
				$ips = explode("\n", $ips);
				$ip  = Request::getIp();
				if ($ip) {
					$allow = in_array($ip, $ips);
				}
			}
			if (!$allow) {
				$return ['error']   = '1024';
				$return ['message'] = cfg('offlineMsg', '系统正在维护，请耐心等待...');
				$view               = new JsonView ($return);
				echo $view->render();
				exit ();
			}
		}
		if (bcfg('connect_server@rest')) {
			$restAccessCheck = new RemoteRestAccessCheck ();
		} else {
			$restAccessCheck = new DefaultRestAccessCheck ();
		}
		$this->debuging = bcfg('develop_mode') && rqset('debug');
		$this->server   = new RestServer ($restAccessCheck, $this->debuging);
		$this->server->registerClass(new RestSevice (), '1', 'rest');
		$this->server = apply_filter('on_init_rest_server', $this->server);
		if (cfg('session_name@rest')) {
			bind('get_session_name', array($this, 'get_session_name'), 100000);
		}
	}

	public function index_get($ver = '1.0', $api = '') {
		if ($api) {
			return $this->handle($_GET, 'get', $ver, $api);
		} else {
			return $this->handle($_GET, 'get', $ver);
		}
	}

	public function index_post($ver = '1.0', $api = '') {
		if ($api) {
			return $this->handle($_POST, 'post', $ver, $api);
		} else {
			return $this->handle($_POST, 'post', $ver);
		}
	}

	public function index_put($ver = '1.0', $api = '') {
		if ($api) {
			return $this->handle($_POST, 'put', $ver, $api);
		} else {
			return $this->handle($_POST, 'put', $ver);
		}
	}

	/**
	 * 输出验证码.
	 *
	 * @param string $sid
	 * @param string $type
	 * @param string $size
	 * @param int    $font
	 */
	public function captcha($sid, $type = 'gif', $size = '60x20', $font = 15) {
		if (empty ($sid)) {
			Response::respond(404);
		}
		Request::getInstance()->startSession($sid);
		Response::nocache();
		$size = explode('x', $size);
		if (count($size) == 1) {
			$width  = intval($size [0]);
			$height = $width * 3 / 4;
		} else if (count($size) >= 2) {
			$width  = intval($size [0]);
			$height = intval($size [1]);
		} else {
			$width  = 60;
			$height = 20;
		}
		$font          = intval($font);
		$font          = max(array(18, $font));
		$type          = in_array($type, array('gif', 'png')) ? $type : 'png';
		$auth_code_obj = new CaptchaCode ();
		// 定义验证码信息
		$arr ['code'] = array('characters' => 'A-H,J-K,M-N,P-Z,3-9', 'length' => 4, 'deflect' => true, 'multicolor' => true);
		$auth_code_obj->setCode($arr ['code']);
		// 定义干扰信息
		$arr ['molestation'] = array('type' => 'both', 'density' => 'normal');
		$auth_code_obj->setMolestation($arr ['molestation']);
		// 定义图像信息. 设置图象类型请确认您的服务器是否支持您需要的类型
		$arr ['image'] = array('type' => $type, 'width' => $width, 'height' => $height);
		$auth_code_obj->setImage($arr ['image']);
		// 定义字体信息
		$arr ['font'] = array('space' => 5, 'size' => $font, 'left' => 5);
		$auth_code_obj->setFont($arr ['font']);
		// 定义背景色
		$arr ['bg'] = array('r' => 255, 'g' => 255, 'b' => 255);
		$auth_code_obj->setBgColor($arr ['bg']);
		$auth_code_obj->paint();
		Response::getInstance()->close(true);
	}

	private function handle($args, $method, $ver, $api = false) {
		unset ($args ['_url']);
		$sname = cfg('session_name@rest');
		if ($sname && isset ($args [ $sname ]) && $args [ $sname ]) {
			Request::getInstance()->startSession($args [ $sname ]);
		}
		$return = $this->server->handle($args, $method, $ver, $api);

		return new JsonView ($return);
	}
}