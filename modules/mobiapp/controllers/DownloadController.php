<?php
class DownloadController extends NonSessionController {
	/**
	 * 下载APP.
	 *
	 * @param string $appkey
	 *        	APP key.
	 * @param string $cid
	 *        	channel.
	 * @param number $uid
	 *        	user.
	 */
	public function index($appkey, $cid = 'guanfang', $uid = 0) {
		if (empty ( $appkey )) {
			Response::showErrorMsg ( 'empty appkey', 403 );
		}
		$rs = dbselect ( 'AV.*' )->from ( '{app_version} AS AV' )->join ( '{rest_apps} AS RA', 'AV.app_id = RA.id' )->where ( array ('AV.deleted' => 0,'RA.appkey' => $appkey ) )->desc ( 'vername' )->get ();
		if (! $rs) {
			Response::showErrorMsg ( 'app not found', 403 );
		}
		$origional_apk_file = WEB_ROOT . $rs ['apk_file'];
		if (! is_file ( $origional_apk_file )) {
			Response::showErrorMsg ( 'origional_apk_file  not found', 403 );
		}
		
		$channel = $cid;
		$userid = $uid;
		
		if (! preg_match ( '/^[\da-z_]{1,15}$/i', $channel )) {
			$channel = 'guanfang';
		}
		
		if (! preg_match ( '/^[\d]{1,10}$/i', $userid )) {
			$userid = '0';
		}
		
		$ext = $rs ['os'] == '2' ? 'ipa' : 'apk';
		$path = $ext . '/' . substr ( md5 ( $channel . '_' . $userid ), 0, 2 );
		$uc = $userid == '0' ? '' : '_' . $userid;
		$url = cfg ( 'apk_home@mobiapp', 'uploads' ) . '/' . $path . '/' . $rs ['prefix'] . '_' . $channel . $uc . '.' . $ext;
		$dest_file = WEB_ROOT . $url;
		
		$host = cfg ( 'host@mobiapp' );
		if (! $host) {
			$host = untrailingslashit ( cfg ( 'site_url' ) );
		}
		
		$downloadUrl = $host . '/' . ltrim ( $url, '/' );
		
		if (is_file ( $dest_file )) {
			Response::redirect ( $downloadUrl );
		}
		
		$channels ['channel'] = $channel;
		$channels ['userid'] = $userid;
		if ($ext == 'ipa') {
			$rst = ApkSignTool::repackIOS ( $origional_apk_file, $dest_file, $channels, $rs ['prefix'] );
		} else {
			$rst = ApkSignTool::repack ( $origional_apk_file, $dest_file, $channels );
		}
		if ($rst) {
			Response::redirect ( $downloadUrl );
		} else {
			Response::respond ( 404 );
		}
	}
}
