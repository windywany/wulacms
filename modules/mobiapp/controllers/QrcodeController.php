<?php
class QrcodeController extends NonSessionController {
	public function index($appkey, $cid = 'guanfang', $uid = 0) {
		if (empty ( $appkey )) {
			Response::respond ( 404 );
		}
		$key = md5 ( $appkey . '-' . $cid . '-' . $uid ) . '.png';
		$ext = substr ( $key, 0, 2 );
		$file = cfg ( 'apk_home@mobiapp', 'uploads' ) . DS . $ext . DS;
		$path = WEB_ROOT . $file;
		$dest_file = $file . $key;
		$full_dest_file = WEB_ROOT . $dest_file;
		if (! is_dir ( $path )) {
			if (! mkdir ( $path, 0755, true )) {
				Response::respond ( 404 );
			}
		}
		
		if (is_file ( $full_dest_file )) {
			Response::redirect ( BASE_URL . $dest_file );
		}
		
		$url = tourl ( 'mobiapp/download', true, false ) . '?appkey=' . $appkey . '&cid=' . urlencode ( $cid ) . '&uid=' . urlencode ( $uid );
		
		QRcode::png ( $url, $full_dest_file, 1, 4, 4 );
		if (is_file ( $full_dest_file )) {
			Response::redirect ( BASE_URL . $dest_file );
		}
		Response::respond ( 404 );
	}
	public function url($url){
		if (!preg_match('#^(f|ht)tps?://.+#i', $url)) {
			Response::respond ( 404 );
		}
		$key = md5 ($url ) . '.png';
		$ext = substr ( $key, 0, 2 );
		$file = cfg ( 'apk_home@mobiapp', 'uploads' ) . DS . $ext . DS;
		$path = WEB_ROOT . $file;
		$dest_file = $file . $key;
		$full_dest_file = WEB_ROOT . $dest_file;
		if (! is_dir ( $path )) {
			if (! mkdir ( $path, 0755, true )) {
				Response::respond ( 404 );
			}
		}
		if (is_file ( $full_dest_file )) {
			Response::redirect ( BASE_URL . $dest_file );
		}
		QRcode::png ( $url, $full_dest_file, 1, 4, 4 );
		if (is_file ( $full_dest_file )) {
			Response::redirect ( BASE_URL . $dest_file );
		}
		Response::respond ( 404 );
	}
}
