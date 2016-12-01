<?php

namespace media\classes;
/**
 *
 * Interface IUploaderController
 * @package media\classes
 */
interface IUploaderController {
	/**
	 * 当前用户是否有权限上传文件.
	 *
	 * @return bool 有权限返回true,反之返回false.
	 */
	public function canUpload();

	/**
	 * 创建上传器.
	 * @return \IUploader
	 */
	public function createUploader();

	/**
	 * 完成文件上传.
	 * @return mixed .
	 */
	public function upload_post();

	/**
	 * 水印信息.
	 * @return array array('file'=>'','pos'=>'tl,tc,tr,br,bc,bl,rc,lc,cc','msize'=>'');
	 */
	public function watermark();
}
