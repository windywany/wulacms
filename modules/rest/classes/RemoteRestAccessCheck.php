<?php
/**
 * 远程取appsecret.
 * @author Guangfeng
 *
 */
class RemoteRestAccessCheck implements IRestAccessCheck {
	private $client;
	public function __construct() {
		$url = cfg ( 'url@rest' );
		$appKey = cfg ( 'appkey@rest' );
		$appSecret = cfg ( 'appsecret@rest' );
		if ($url && $appKey && $appSecret) {
			$this->client = new RestClient ( $url, $appKey, $appSecret );
		}
	}
	public function getAppSecret($appkey) {
		$myAppKey = cfg ( 'appkey@rest' );
		if ($appkey == $myAppKey) {
			return cfg ( 'appsecret@rest' );
		}
		$appSecret = RtCache::get ( 'app_' . $appkey );
		if (! $appSecret) {
			if ($this->client) {
				$rst = $this->client->get ( 'rest.get_app', array ('appID' => $appkey ) );
				if (isset ( $rst ['appsecret'] )) {
					$appSecret = $rst ['appsecret'];
					RtCache::add ( 'app_' . $appkey, $appSecret );
				}
			}
		}
		return $appSecret;
	}
}