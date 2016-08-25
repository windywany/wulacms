<?php
/**
 * 将文件上传到appdata目录。
 * @author ngf
 *
 */
class AppdataFileUploader extends FileUploader {
	public function getDestDir($path = null) {
		$path = date ( cfg ( 'store_dir@media', '/Y/n/' ) );
		if ($path) {
			return APPDATA_DIR . DS . 'misc' . DS . ltrim ( $path, '/' );
		}
		return APPDATA_DIR . DS . 'misc' . DS;
	}
}