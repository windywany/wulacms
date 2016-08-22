<?php
/**
 * 媒体上传帮助类.
 * @author Guangfeng
 *
 */
class MediaUploadHelper {
	/**
	 * 上传文件，如果成功返回被上传的文件的具体信息。
	 *
	 * @param array $params        	
	 * @param string $field        	
	 * @return array 成功：error = 0, file = array(url,filepath,filename)
	 */
	public static function moveRestUploadedFile(&$params, $field) {
		if (isset ( $_FILES [$field] )) {
			$file = self::moveFile ( $field );
			if ($file ['error']) {
				return $file;
			}
			$file = $file ['file'];
		} else if (isset ( $params [$field] )) {
			$file = substr ( $params [$field], 1 );
			unset ( $params [$field] );
		}
		if (isset ( $file ) && $file && file_exists ( $file )) {
			$uploader = self::getUploader (); // 得到文件上传器
			$rst = $uploader->save ( $file );
			@unlink ( $file );
			if ($rst) {
				return array ('error' => 0,'file' => $rst );
			} else {
				return array ('error' => 3,'message' => $uploader->get_last_error () );
			}
		} else {
			return array ('error' => 4,'message' => '文件不存在.' );
		}
	}
	private static function moveFile($filename = 'avatar') {
		if (isset ( $_FILES [$filename] ['error'] ) && $_FILES [$filename] ['error']) {
			switch ($_FILES [$filename] ['error']) {
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
			return array ('error' => 1,'message' => $error );
		} else if (isset ( $_FILES [$filename] ['tmp_name'] ) && is_uploaded_file ( $_FILES [$filename] ['tmp_name'] )) {
			$file = $_FILES [$filename];
			$name = $file ['name'];
			$size = $file ['size'];
			$tmp_file = $file ['tmp_name'];
			$uploader = self::getUploader ();
			if ($size > $uploader->getMaxSize ()) {
				return array ('error' => 1,'message' => '文件太大啦，已经超出系统允许的最大值.' );
			}
			$destfile = TMP_PATH . $name;
			if (@move_uploaded_file ( $tmp_file, $destfile )) {
				return array ('error' => 0,'file' => $destfile );
			} else {
				return array ('error' => 1,'message' => '无法移动文件.' );
			}
		}
		return array ('error' => 1,'message' => '未找到上传的文件.' );
	}
	/**
	 * 取文件上传器.
	 *
	 * @param boolean $is_local        	
	 * @return IUploader
	 */
	public static function getUploader($is_local = 0) {
		if (! is_numeric ( $is_local )) {
			$uploader = apply_filter ( 'get_custom_uploader', null, $is_local );
			if ($uploader) {
				return $uploader;
			}
		}
		if ($is_local === 1) {
			$uploader = new AppdataFileUploader ();
		} else if (bcfg ( 'store_type@media' )) {
			$uploader = new RemoteUploader ();
		} else {
			$uploader = apply_filter ( 'get_uploader', new FileUploader () ); // 得到文件上传器
		}
		return $uploader;
	}
}