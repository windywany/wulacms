<?php
/**
 * 远程文件上传。
 * @author ngf
 *
 */
class RemoteUploader extends FileUploader {
	private $client;
	public function __construct() {
		$url = cfg ( 'url@media' );
		$appKey = cfg ( 'appkey@media' );
		$appSecret = cfg ( 'appsecret@media' );
		if ($url && $appSecret && $appKey) {
			$this->client = new RestClient ( $url, $appKey, $appSecret );
		}
	}
	
	/*
	 * (non-PHPdoc) @see FileUploader::delete()
	 */
	public function delete($file) {
		if ($this->client) {
			$rst = $this->client->post ( 'media.delete', array ('file' => $file ) );
			return empty ( $rst ['error'] ) ? true : false;
		} else {
			return false;
		}
	}
	/*
	 * (non-PHPdoc) @see FileUploader::save()
	 */
	public function save($filepath, $path = null) {
		if ($this->client) {
			$rst = $this->client->post ( 'media.save', array ('file' => '@' . $filepath ) );
			@unlink ( $filepath );
			if (isset ( $rst ['file'] )) {
				return $rst ['file'];
			} else {
				$this->last_error = $rst ['message'];
				return false;
			}
		}
		$this->last_error = 'Can not initialize RESTful Client.';
		return false;
	}
	/*
	 * (non-PHPdoc) @see FileUploader::allowed()
	 */
	public function allowed($ext) {
		if (! $this->client) {
			return false;
		}
		$rst = $this->client->get ( 'media.allowed' );
		if (isset ( $rst ['allowed'] )) {
			return in_array ( $ext, $rst ['allowed'] );
		}
		return false;
	}
	
	/*
	 * (non-PHPdoc) @see FileUploader::getMaxSize()
	 */
	public function getMaxSize() {
		if (! $this->client) {
			return 102400;
		}
		$rst = $this->client->get ( 'media.maxsize' );
		if (isset ( $rst ['maxsize'] )) {
			return $rst ['maxsize'];
		}
		return 102400;
	}
}