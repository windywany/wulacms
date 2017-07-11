<?php
/**
 * 图片工具.
 * @author LeoNing
 *
 */
include_once INCLUDES . '/vendors/image.class.php';

class ImageUtil {
	public static  $MIMES     = array('image/bmp' => 'bmp', 'image/cis-cod' => 'cod', 'image/png' => 'png', 'image/gif' => 'gif', 'image/ief' => 'ief', 'image/jpeg' => 'jpg', 'image/pipeg' => 'jfif', 'image/svg+xml' => 'svg', 'image/tiff' => 'tiff', 'image/x-cmu-raster' => 'ras', 'image/x-cmx' => 'cmx', 'image/x-icon' => 'ico', 'image/x-portable-anymap' => 'pnm', 'image/x-portable-bitmap' => 'pbm', 'image/x-portable-graymap' => 'pgm', 'image/x-portable-pixmap' => 'ppm', 'image/x-rgb' => 'rgb', 'image/x-xbitmap' => 'xbm', 'image/x-xpixmap' => 'xpm');
	private        $file;
	private static $POSITIONS = array('tl', 'tm', 'tr', 'ml', 'mm', 'mr', 'bl', 'bm', 'br');

	public function __construct($file) {
		if (file_exists($file) && self::isImage($file)) {
			$this->file = $file;
		} else {
			$this->file = false;
		}
	}

	/**
	 * 生成缩略图
	 *
	 * @param array  $size 尺寸集.
	 * @param string $sep
	 * @param string $replace
	 *
	 * @return array
	 */
	public function thumbnail($size = array(array(80, 60)), $sep = '-', $replace = '') {
		$files = array();
		if ($this->file && !empty ($size)) {
			foreach ($size as $i => $s) {
				if (is_array($s) && isset ($s [0]) && isset ($s [1])) {
					$width  = intval($s [0]);
					$height = intval($s [1]);
					if (isset($s[2])) {
						$file  = $s[2];
						$tfile = get_thumbnail_filename($this->file, $file, 0, $sep);
					} else {
						$tfile = get_thumbnail_filename($this->file, $width, $height, $sep);
					}
					if ($replace) {
						$tfile = str_replace($replace, '', $tfile);
					}
					if (is_file($tfile)) {
						$files [ $i ] = $tfile;
						continue;
					}
					$image = new image ($this->file);
					$image->attach(new image_fx_resize ($width, $height));
					$rst = $image->save($tfile);
					if (!$rst) {
						log_error('生成缩略图失败:' . $tfile);
					} else {
						$files [ $i ] = $tfile;
					}
					$image->destroyImage();
				}
			}
		}

		return $files;
	}

	public function crop($x, $y, $w, $h, $destFile = null) {
		$file = $this->file;
		if ($this->file) {
			$image = new image ($this->file);
			$ow    = $image->imagesx();
			$oh    = $image->imagesy();
			if (empty ($w)) {
				$w = $ow - $x;
			} else if ($w < 0) {
				$w = $ow + $w - $x;
			}
			if ($w <= 0) {
				return $this->file;
			}
			if (empty ($h)) {
				$h = $oh - $y;
			} else if ($h < 0) {
				$h = $oh + $h - $y;
			}
			if ($h <= 0) {
				return $this->file;
			}

			$tx = $x;
			$ty = $y;
			$nw = $w;
			$nh = $h;

			$fx = new image_fx_crop ($tx, $ty, $nw, $nh);
			$image->attach($fx);
			if (!$destFile) {
				$destFile = $this->file;
			}
			if ($image->save($destFile)) {
				$file = $destFile;
			} else {
				$file = null;
			}
			$image->destroyImage();
		}

		return $file;
	}

	public function mosaic($pos, $size) {
		if ($this->file) {
			$image = new image ($this->file);
			$image->attach(new image_fx_mosaic ($pos, $size));
			$image->save($this->file);
			$image->destroyImage();
		}
	}

	/**
	 * 添加水印
	 *
	 * @param string $mark
	 *            水印图片
	 * @param string $pos
	 *            位置
	 * @param string $minSize
	 *            最小值
	 */
	public function watermark($mark, $pos, $minSize = false) {
		if ($this->file && file_exists($mark)) {
			$image     = new image ($this->file);
			$iw        = intval($image->imagesx());
			$ih        = intval($image->imagesy());
			$watermark = new image ($mark);
			$w         = 3 * intval($watermark->imagesx());
			$h         = 3 * intval($watermark->imagesy());
			if ($minSize) {
				$minSize = explode('x', $minSize);
				$w       = intval($minSize [0]);
				if (isset ($minSize [1])) {
					$h = intval($minSize [1]);
				} else {
					$h = $w;
				}
			}
			if ($iw > $w && $ih > $h) {
				if ($pos == 'rd') {
					$pos = array_rand(self::$POSITIONS);
					$pos = self::$POSITIONS [ $pos ];
					if ($pos == 'mm') {
						$pos = 'br';
					}
				}
				$trans = cfg('transxy@media');
				if (!preg_match('/^[1-9]\d*x[1-9]\d*$/', $trans)) {
					$trans = '0x0';
				}
				$fx  = $image->attach(new image_draw_watermark ($watermark, $pos, $trans));
				$rst = $image->save($this->file);
				if (!$rst) {
					log_error('添加水印失败:' . $this->file);

					return false;
				}
			}
			$image->destroyImage();
		}

		return true;
	}

	/**
	 * delete thumbnail
	 *
	 * @param unknown_type $filename
	 *
	 * @return boolean
	 */
	public static function deleteThumbnail($filename) {
		$pos = strrpos($filename, '.');
		if ($pos === false) {
			return false;
		}
		$shortname = substr($filename, 0, $pos);
		$ext       = substr($filename, $pos);
		$filep     = $shortname . '-*' . $ext;
		$files     = glob($filep);
		if ($files) {
			foreach ($files as $f) {
				// TODO: 使用uploader删除.
				@unlink($f);
			}
		}
	}

	public static function isImage($file) {
		$ext = strrchr($file, '.');

		return in_array(strtolower($ext), array('.jpeg', '.jpg', '.gif', '.png'));
	}

	/**
	 * 下载远程图片到本地.
	 *
	 * @param string|array    $imgUrls
	 *            要下载的图片地址数组或地址.
	 * @param IUploader $uploader
	 *            图片上传器.
	 * @param int       $timeout
	 *            超时时间.
	 * @param array     $watermark
	 *            水印设置.
	 * @param array     $resize
	 *            重置大小.
	 * @param string    $referer
	 *            引用.
	 *
	 * @return array (url,name,path,ext)
	 */
	public static function downloadRemotePic($imgUrls, $uploader, $timeout = 30, $watermark = array(), $resize = array(), $referer = '') {
		// 忽略抓取时间限制
		set_time_limit(0);
		$tmpNames = array();
		$savePath = TMP_PATH . 'img' . DS;
		if (!file_exists($savePath) && !mkdir($savePath, 0777, true)) {
			return false;
		}
		if (is_string($imgUrls)) {
			$imgUrls = array($imgUrls);
		}
		$callback = new ImageDownloadCallback ($savePath, $uploader, $watermark, $resize);
		$clients  = array();
		foreach ($imgUrls as $imgUrl) {
			if (is_array($imgUrl)) {
				list ($imgUrl, $mosaic) = $imgUrl;
			} else {
				$mosaic = null;
			}
			if (strpos($imgUrl, "http") !== 0) {
				continue;
			}
			$client = CurlClient::getClient($timeout);
			if (!$referer) {
				$referer = $imgUrl;
			}
			$client                        = $client->prepareGet($imgUrl, $callback, $referer);
			$callback->mosaics [ $imgUrl ] = $mosaic;
			if ($client) {
				$clients [ $imgUrl ] = $client;
			}
		}
		if ($clients) {
			$rsts = CurlClient::execute($clients);
			if ($rsts [0]) {
				foreach ($rsts [0] as $url => $rst) {
					if ($rst) {
						$tmpNames [ $url ] = $rst;
					}
				}
			}
		}

		return $tmpNames;
	}
}

class ImageDownloadCallback implements CurlMultiExeCallback {
	private $savePath;
	private $uploader;
	private $watermark;
	private $config;
	private $resize;
	public  $mosaics = array();

	public function __construct($savedPath, $uploader, $watermark, $resize) {
		$this->savePath  = $savedPath;
		$this->uploader  = $uploader;
		$this->watermark = $watermark;
		$this->resize    = $resize;
		$this->config    = array("fileType" => array(".gif", ".png", ".jpg", ".jpeg", ".bmp"), "fileSize" => 50000); // 文件大小限制，单位KB
	}

	public function onStart($index, $curl, $cdata) {
		return true;
	}

	public function onError($imgUrl, $curl, $cdata) {
		log_error("cannot download img:" . $imgUrl . ' [' . $cdata . ']');

		return null;
	}

	public function onFinish($imgUrl, $data, $curl, $cdata) {
		// 获取请求头
		$contentType = strtolower(curl_getinfo($curl, CURLINFO_CONTENT_TYPE));
		if (!strstr($contentType, 'image')) {
			return null;
		}
		$maxSize = 1024 * $this->config ['fileSize'];
		// 格式验证(扩展名验证和Content-Type验证)
		$oriPath  = explode("/", $imgUrl);
		$fileType = strtolower(strrchr($oriPath [ count($oriPath) - 1 ], '.'));
		if (empty ($fileType)) {
			$fileType = '.' . ImageUtil::$MIMES [ $contentType ];
		}
		if (!in_array($fileType, $this->config ['fileType'])) {
			return null;
		}
		// 生成随机文件名.
		$tmpName = $this->savePath . unique_filename($this->savePath, rand_str(6, 'a-z') . $fileType);
		$size    = @file_put_contents($tmpName, $data);
		if ($size !== false && $size > 0) {
			if (isset ($this->mosaics [ $imgUrl ]) && $this->mosaics [ $imgUrl ]) {
				list ($pos, $size) = $this->mosaics [ $imgUrl ];
				$img = new ImageUtil ($tmpName);
				$img->mosaic($pos, $size);
			}
			if ($this->resize) {
				$img = new ImageUtil ($tmpName);
				$cnt = count($this->resize);
				if ($cnt == 2) {
					$img->thumbnail(array($this->resize));
				} else if ($cnt == 4) {
					$img->crop($this->resize [0], $this->resize [1], $this->resize [2], $this->resize [3]);
				}
			}
			if ($this->watermark) {
				list ($wimg, $pos, $size) = $this->watermark;
				$img = new ImageUtil ($tmpName);
				$img->watermark($wimg, $pos, $size);
			}
			$rst = $this->uploader->save($tmpName);
			if ($rst) {
				FileUploader::save2db($rst, $fileType);
				$rst [] = $fileType;

				return $rst;
			} else {
				log_error($this->uploader->get_last_error(), 'remote_down_pic');
			}
		}

		return null;
	}
}