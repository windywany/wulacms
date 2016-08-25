<?php
/**
 * RESTFul service.
 * @author Guangfeng
 *
 */
class RestSevice {
	/**
	 * 取app info。
	 *
	 * @param array $param        	
	 * @return multitype:string
	 */
	public function rest_get_get_app($param, $key, $secret) {
		if (isset ( $param ['appID'] ) && $param ['appID']) {
			$appkey = $param ['appID'];
			$rst = dbselect ( 'name,callback_url,note,appsecret' )->from ( '{rest_apps}' )->where ( array ('appkey' => $appkey ) )->get ( 0 );
			if (! $rst) {
				$rst = array ('error' => '501','message' => '应用不存在' );
			}
		} else {
			$rst = array ('error' => '500','message' => '未指定appkey' );
		}
		return $rst;
	}
	public function rest_get_provider_data($param, $key, $secret) {
		if (isset ( $param ['datasource'] ) && ! empty ( $param ['datasource'] )) {
			$p = $param ['datasource'];
			unset ( $param ['datasource'] );
			$data = get_data_from_cts_provider ( $p, $param, array () );
			$rst = $data->toArray ();
			if ($rst && isset ( $param ['safe_url'] ) && $param ['safe_url'] == 'true' && isset ( $rst [0] ['url'] )) {
				foreach ( $rst as $i => $r ) {
					$rst [$i] ['url'] = safe_url ( $r );
				}
			}
			$rtn = array ('error' => 0,'data' => $rst,'countTotal' => $data->getCountTotal () );
		} else {
			$rtn = array ('error' => 1,'message' => '未定义数据源' );
		}
		return $rtn;
	}
	
	/**
	 * 当前可以提供的服务.
	 *
	 * @param array $param        	
	 * @param unknown $key        	
	 * @param unknown $secret        	
	 * @return multitype:number string unknown
	 */
	public function rest_get_services($param, $key, $secret) {
		$services = array ();
		if (cfg ( 'site_url' )) {
			$server = new RestServer ( null );
			$server = apply_filter ( 'on_init_rest_server', $server );
			$apis = $server->getExportServices ();
			$services ['services'] = $apis;
			$services ['url'] = tourl ( 'rest' );
		} else {
			$services ['error'] = 1;
			$services ['message'] = 'not set site URL.';
		}
		return $services;
	}
	/**
	 * 作为应用中心时为接入的应用提供服务查询。
	 *
	 * @param array $param
	 *        	(api,version)
	 * @param unknown $secret        	
	 */
	public function rest_get_lookup($param, $key, $secret) {
		$rtn = array ();
		if (! bcfg ( 'connect_server@rest' )) {
			$api = $param ['service'];
			$ver = $param ['version'];
			$services = RtCache::get ( 'rest_services', false );
			if (! $services) {
				RestServer::syncServices ();
				$services = RtCache::get ( 'rest_services', array () );
			}
			if (isset ( $services [$api] [$ver] )) {
				$rtn ['servers'] = $services [$api] [$ver];
			} else {
				$rtn ['error'] = 1;
				$rtn ['message'] = "Service: $api(ver:$ver) not found.";
			}
		} else {
			$rtn ['error'] = 1;
			$rtn ['message'] = 'Not support by this server.';
		}
		return $rtn;
	}
}