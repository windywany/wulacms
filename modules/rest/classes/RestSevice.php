<?php

/**
 * RESTFul service.
 *
 * @author Guangfeng
 *
 */
class RestSevice {

	/**
	 * 取app info。
	 *
	 * @param array $param
	 *
	 * @return array
	 */
	public function rest_get_get_app($param, $key, $secret) {
		if (isset ($param ['appID']) && $param ['appID']) {
			$appkey = $param ['appID'];
			$rst    = dbselect('name,callback_url,note,appsecret')->from('{rest_apps}')->where(array('appkey' => $appkey))->get(0);
			if (!$rst) {
				$rst = array('error' => '501', 'message' => '应用不存在');
			}
		} else {
			$rst = array('error' => '500', 'message' => '未指定appkey');
		}

		return $rst;
	}

	public function rest_start_session($param, $key, $secret) {
		$sname = cfg('session_name@rest', 'session');
		if ($sname) {
			$sid = isset ($param [ $sname ]) ? $param [ $sname ] : null;
			$sid = Request::getInstance()->startSession($sid);

			return ['error' => 0, 'data' => ['sid' => $sid, 'sname' => $sname]];
		} else {
			return ['error' => 400, 'message' => '无法开启SESSION'];
		}
	}

	/**
	 * 当前可以提供的服务.
	 *
	 * @param array  $param
	 * @param string $key
	 * @param string $secret
	 *
	 * @return array
	 */
	public function rest_get_services($param, $key, $secret) {
		$services = array();
		if (cfg('site_url')) {
			$server                = new RestServer (null);
			$server                = apply_filter('on_init_rest_server', $server);
			$apis                  = $server->getExportServices();
			$services ['services'] = $apis;
			$services ['url']      = tourl('rest');
		} else {
			$services ['error']   = 1;
			$services ['message'] = 'not set site URL.';
		}

		return $services;
	}

	/**
	 * 作为应用中心时为接入的应用提供服务查询。
	 *
	 * @param array  $param (api,version)
	 * @param string $key
	 * @param string $secret
	 *
	 * @return array
	 */
	public function rest_get_lookup($param, $key, $secret) {
		$rtn = array();
		if (!bcfg('connect_server@rest')) {
			$api      = $param ['service'];
			$ver      = $param ['version'];
			$services = RtCache::get('rest_services', false);
			if (!$services) {
				RestServer::syncServices();
				$services = RtCache::get('rest_services', array());
			}
			if (isset ($services [ $api ] [ $ver ])) {
				$rtn ['servers'] = $services [ $api ] [ $ver ];
			} else {
				$rtn ['error']   = 1;
				$rtn ['message'] = "Service: $api(ver:$ver) not found.";
			}
		} else {
			$rtn ['error']   = 1;
			$rtn ['message'] = 'Not support by this server.';
		}

		return $rtn;
	}

	/**
	 * 远程数据源调取.
	 *
	 * @param array  $param
	 * @param string $key
	 * @param string $secret
	 *
	 * @return array
	 */
	public function rest_get_cts($param, $key, $secret) {
		$from = get_condition_value('from', $param);
		if (!$from) {
			return ['error' => 1, 'msg' => '未指定数据源.'];
		}

		unset($param['from'], $param['var']);
		$rtn   = get_data_from_cts_provider($from, $param, []);
		$count = $rtn->getCountTotal();
		$data  = $rtn->toArray();

		return ['error' => 0, 'data' => $data, 'count' => $count];
	}

	public function rest_get_echostr($param, $key, $secret) {
		return ['error' => 0, 'data' => $param['data']];
	}

}