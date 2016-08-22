<?php
/**
 * 图片服务器。
 * @author Guangfeng
 *
 */
class MediaRestService {
	public function rest_post_save($param, $key, $secret) {
		if (isset ( $_FILES ['file'] ['error'] ) && $_FILES ['file'] ['error']) {
			switch ($_FILES ['file'] ['error']) {
				case '1' :
					$error = '超过php.ini允许的大小。';
					break;
				case '2' :
					$error = '超过表单允许的大小。';
					break;
				case '3' :
					$error = '图片只有部分被上传。';
					break;
				case '4' :
					$error = '请选择图片。';
					break;
				case '6' :
					$error = '找不到临时目录。';
					break;
				case '7' :
					$error = '写文件到硬盘出错。';
					break;
				case '8' :
					$error = 'File upload stopped by extension。';
					break;
				case '999' :
				default :
					$error = '未知错误。';
			}
			return array ('error' => '201','message' => $error );
		} else if (isset ( $_FILES ['file'] ['tmp_name'] ) && is_uploaded_file ( $_FILES ['file'] ['tmp_name'] )) {
			$file = $_FILES ['file'];
			$name = $file ['name'];
			$size = $file ['size'];
			$tmp_file = $file ['tmp_name'];
			$destfile = TMP_PATH . $name;
			if ($size > FileUploader::getMaxUploadSize ()) {
				return array ('error' => 203,'message' => '文件太大啦，已经超出系统允许的最大值.' );
			}
			if (move_uploaded_file ( $tmp_file, $destfile )) {
				$uploader = apply_filter ( 'get_uploader', new FileUploader () ); // 得到文件上传器
				$rst = $uploader->save ( $destfile );
				@unlink ( $destfile );
				if ($rst) {
					return array ('file' => $rst );
				} else {
					return array ('error' => 204,'message' => $uploader->get_last_error () );
				}
			} else {
				return array ('error' => 202,'message' => '无法保存文件.' );
			}
		}
		return array ('error' => 200,'message' => '未指定要上传的文件' );
	}
	public function rest_get_allowed($param, $key, $secret) {
		return array ('allowed' => FileUploader::getAllowedExetensions () );
	}
	public function rest_get_maxsize($param, $key, $secret) {
		return array ('maxsize' => FileUploader::getMaxUploadSize () );
	}
	public function rest_get_delete($param, $key, $secret) {
		if (isset ( $param ['file'] )) {
			$uploader = apply_filter ( 'get_uploader', new FileUploader () ); // 得到文件上传器
			$rst = $uploader->delete ( $param ['file'] );
			$uploader->close ();
		} else {
			$rst = false;
		}
		return array ('error' => $rst ? 0 : '1' );
	}
}
