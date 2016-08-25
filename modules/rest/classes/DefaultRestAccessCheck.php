<?php
/**
 * 默认的Secret获取器.
 * @author ngf
 *
 */
class DefaultRestAccessCheck implements IRestAccessCheck {
	public function getAppSecret($appkey) {
		$sec = RtCache::get ( 'secret_' . $appkey );
		if ($sec) {
			return $sec;
		}
		$app = dbselect ()->from ( '{rest_apps}' )->where ( array (
				'appkey' => $appkey 
		) )->get ( 'appsecret' );
		if ($app) {
			RtCache::add ( 'secret_' . $appkey, $app );
		}
		return $app;
	}
}
?>