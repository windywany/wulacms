<?php
/**
 * FTP文件上传器,将文件上传到ftp服务器。
 * @author leo
 *
 */
class FtpUploader extends FileUploader {
	private $host;
	private $port;
	private $user;
	private $pwd;
	private $path;
	private $timeout = 60;
	private $passive = true;
	private $error = null;
	private $ftp = null;
	/**
	 * 使用host,port等信息构建连接。
	 *
	 * @param string $host
	 *        	FTP 主机.
	 * @param string $port
	 *        	端口.
	 * @param string $user
	 *        	用户.
	 * @param string $password
	 *        	密码.
	 * @param string $path
	 *        	路径.
	 * @param boolean $passive
	 *        	是否是被动模式.
	 */
	public function __construct($host = 'localhost', $port = '21', $user = '', $password = '', $timeout = 60, $path = '', $passive = true) {
		$this->host = $host;
		$this->port = $port;
		$this->user = $user;
		$this->pwd = $password;
		$this->path = $path ? untrailingslashit ( $path ) . '/' : '';
		$this->timeout = $timeout;
		$this->passive = $passive;
	}
	public function save($filepath, $path = null) {
		if (! $this->ftp) {
			$this->initFtpConnection ();
		}
		if (! $this->ftp) {
			@unlink ( $filepath );
			return false;
		}
		$path = $this->getDestDir ( $path );
		
		$destdir = $path;
		if (! $this->checkDir ( $destdir )) {
			$this->last_error = '无法创建目录' . $destdir;
		}
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
		
		$pathinfo = pathinfo ( $tmp_file );
		$name = $pathinfo ['filename'] . '.' . $pathinfo ['extension'];
		if (! $this->allowed ( '.' . $pathinfo ['extension'] )) {
			$this->last_error = '不允许上传此类型文件';
			@unlink ( $tmp_file );
			return false;
		}
		$name = $this->unique_filename ( $destdir, $name );
		$fileName = $path . $name;
		$destfile = $destdir . $name;
		if (ImageUtil::isImage ( $tmp_file )) {
			$img = new image ( $destfile );
			$width = $img->imagesx ();
			$height = $img->imagesy ();
			$img->destroyImage ();
		} else {
			$width = $height = 0;
		}
		$result = @ftp_put ( $this->ftp, $destfile, $tmp_file, FTP_BINARY );
		
		@unlink ( $tmp_file );
		if ($result == false) {
			$this->last_error = '无法将文件[' . $tmp_file . ']上传到FTP服务器[' . $destfile . ']';
			return false;
		}
		$fileName = str_replace ( DS, '/', $destfile );
		return array ($fileName,$pathinfo ['basename'],$fileName,$size,$width,$height );
	}
	public function delete($file) {
		if (! $this->ftp) {
			$this->initFtpConnection ();
		}
		
		if (! $this->ftp) {
			return false;
		}
		
		return @ftp_delete ( $this->ftp, '/' . untrailingslashit ( $file ) );
	}
	public function close() {
		if ($this->ftp) {
			@ftp_close ( $this->ftp );
		}
		$this->ftp = null;
	}
	private function checkDir($path) {
		$paths = explode ( '/', trim ( $path, '/' ) );
		foreach ( $paths as $path ) {
			if (! @ftp_chdir ( $this->ftp, $path )) {
				if (! @ftp_mkdir ( $this->ftp, $path )) {
					@ftp_chdir ( $this->ftp, '/' );
					return false;
				}
				
				if (! @ftp_chdir ( $this->ftp, $path )) {
					@ftp_chdir ( $this->ftp, '/' );
					return false;
				}
			}
		}
		@ftp_chdir ( $this->ftp, '/' );
		return true;
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
		return $this->path . untrailingslashit ( ltrim ( $path, '/' ) ) . '/';
	}
	private function unique_filename($dir, $name) {
		$dir = untrailingslashit ( $dir );
		@ftp_chdir ( $this->ftp, '/' . $dir );
		$list = @ftp_nlist ( $this->ftp, '.' );
		if ($list) {
			if (! in_array ( $name, $list ) && ! in_array ( './' . $name, $list )) {
				@ftp_chdir ( $this->ftp, '/' );
				return $name;
			}
			$filename = $name;
			$info = pathinfo ( $filename );
			$ext = ! empty ( $info ['extension'] ) ? '.' . $info ['extension'] : '';
			$name = basename ( $filename, $ext );
			$i = 1;
			while ( true ) {
				$name1 = $name . '_' . $i . $ext;
				if (! in_array ( $name1, $list ) && ! in_array ( './' . $name1, $list )) {
					$filename = $name1;
					break;
				}
				$i ++;
			}
		} else {
			$filename = $name;
		}
		@ftp_chdir ( $this->ftp, '/' );
		return $filename;
	}
	public function initFtpConnection() {
		if (! function_exists ( 'ftp_connect' )) {
			$this->last_error = 'the ftp extension is not installed!';
			return null;
		}
		$this->ftp = @ftp_connect ( $this->host, $this->port, $this->timeout );
		if ($this->ftp && $this->user) {
			if (! @ftp_login ( $this->ftp, $this->user, $this->pwd )) {
				@ftp_close ( $this->ftp );
				$this->last_error = 'login fail!';
				$this->ftp = null;
			}
		} else if (! $this->ftp) {
			$this->last_error = 'cannot connect to the ftp server';
		}
		if ($this->ftp) {
			@ftp_pasv ( $this->ftp, $this->passive );
			@ftp_chdir ( $this->ftp, '/' );
		}
		return $this->ftp;
	}
}