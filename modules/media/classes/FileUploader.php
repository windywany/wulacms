<?php
/**
 * 本地文件上传器.
 * @author Leo
 *
 */
class FileUploader implements IUploader {
	protected $last_error = '';
	protected $upload_root_path = '';
	public function __construct($path = null) {
		if (empty ( $path )) {
			$this->upload_root_path = WEB_ROOT;
		} else {
			$this->upload_root_path = $path;
		}
	}
	/**
	 * 默认文件上传器.
	 *
	 * @param string $filepath        	
	 * @return array
	 */
	public function save($filepath, $path = null) {
		$path = $this->getDestDir ( $path );
		
		$destdir = $this->upload_root_path . $path;
		$tmp_file = $filepath;
		
		$fileinfo = stat ( $tmp_file );
		$maxSize = $this->getMaxSize ();
		if ($fileinfo) {
			$size = $fileinfo [7];
		} else {
			$size = - 1;
		}
		if ($size > $maxSize) {
			$this->last_error = '文件体积超出允许值[' . $maxSize . ']';
			@unlink ( $tmp_file );
			return false;
		}
		
		if (! is_dir ( $destdir ) && ! @mkdir ( $destdir, 0777, true )) { // 目的目录不存在，且创建也失败
			$this->last_error = '无法创建目录[' . $destdir . ']';
			@unlink ( $tmp_file );
			return false;
		}
		$pathinfo = pathinfo ( $tmp_file );
		$fext = '.' . strtolower ( $pathinfo ['extension'] );
		if (! $this->allowed ( $fext )) {
			@unlink ( $tmp_file );
			$this->last_error = '不允许上传此类型文件：'.$tmp_file;
			return false;
		}
		$name = $pathinfo ['filename'] . $fext;
		$name = unique_filename ( $destdir, $name );
		$fileName = $path . $name;
		$destfile = $destdir . $name;
		$result = rename ( $tmp_file, $destfile );
		if ($result == false) {
			@unlink ( $tmp_file );
			$this->last_error = '无法将文件[' . $tmp_file . ']重命名为[' . $destfile . ']';
			return false;
		}
		if (ImageUtil::isImage ( $destfile )) {
			$img = new image ( $destfile );
			$width = $img->imagesx ();
			$height = $img->imagesy ();
			$img->destroyImage ();
		} else {
			$width = $height = 0;
		}
		$fileName = str_replace ( DS, '/', $fileName );
		return array ($fileName,$pathinfo ['basename'],$fileName,$size,intval ( $width ),intval ( $height ) );
	}
	public function get_last_error() {
		return $this->last_error;
	}
	public function getMaxSize() {
		return FileUploader::getMaxUploadSize ();
	}
	public function allowed($ext) {
		static $types = false;
		if (! $types) {
			$types = FileUploader::getAllowedExetensions ();
		}
		return in_array ( $ext, $types );
	}
	public function delete($file) {
		$file = $this->upload_root_path . $file;
		if (file_exists ( $file )) {
			if (@unlink ( $file )) {
				ImageUtil::deleteThumbnail ( $file );
			}
		}
		return true;
	}
	public function close() {
		// nothing to do.
	}
	public function getDestDir($path = null) {
		if (! $path) {
			$path = date ( cfg ( 'store_dir@media', '/Y/n/' ) );
			$rand_cnt = icfg ( 'rand_cnt@media', 0 );
			if ($rand_cnt > 1) {
				$cnt = rand ( 0, $rand_cnt - 1 );
				$path .= $cnt . '/';
			}
		}
		return cfg ( 'upload_dir@media', 'uploads' ) . $path;
	}
	public static function getAllowedExetensions() {
		$types = ',' . cfg ( 'allow_exts@media', 'jpg,gif,png,bmp,jpeg,zip,rar,7z,tar,gz,bz2,doc,docx,txt,ppt,pptx,xls,xlsx,pdf,mp3,avi,mp4,flv,swf' );
		$types = str_replace ( ',', ',.', $types );
		$types = explode ( ',', $types );
		return $types;
	}
	public static function getMaxUploadSize() {
		return cfg ( 'max_upload_size@media', 20 ) * 10485760;
	}
	public static function save2db($media, $filext = '') {
		$user = whoami ();
		$data ['uid'] = $user->getUid ();
		$data ['create_time'] = time ();
		$data ['filename'] = $media [1];
		
		if ($filext) {
			$data ['ext'] = trim ( $filext, '.' );
		} else if (isset ( $media [3] )) {
			$data ['ext'] = trim ( $media [3], '.' );
		} else {
			$data ['ext'] = trim ( strtolower ( strrchr ( $media [1], '.' ) ), '.' );
		}
		$data ['type'] = get_media_type ( $data ['ext'] );
		$data ['alt'] = '';
		$data ['url'] = $media [0];
		$data ['filepath'] = $media [2];
		$data ['note'] = '';
		$m = dbinsert ( $data )->into ( '{media}' )->exec ();
		if (! $m) {
			ActivityLog::error ( __ ( 'Can not save new media to database.' ), 'Upload' );
		} else {
			$data ['id'] = $m [0];
			fire ( 'on_media_uploaded', $data );
		}
	}
}