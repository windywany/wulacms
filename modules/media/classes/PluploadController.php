<?php
/**
 *
 * User: Leo Ning.
 * Date: 08/10/2016 13:57
 */

namespace media\classes;

abstract class PluploadController extends \Controller implements IUploaderController {
	/**
	 * 上传
	 */
	public final function upload_post() {
		$canUpload = $this->canUpload();
		$chunk     = irqst('chunk');
		$chunks    = irqst('chunks');
		$name      = rqst('name');
		if (!$canUpload) {
			status_header(403);
			die ('{"jsonrpc" : "2.0", "error" : {"code": 200, "message": "无权上传文件."}, "id" : "id"}');
		}
		$targetDir = TMP_PATH . "plupload";
		if (!is_dir($targetDir)) {
			mkdir($targetDir, 0755, true);
		}
		if (!is_dir($targetDir)) {
			status_header(422);
			die ('{"jsonrpc" : "2.0", "error" : {"code": 200, "message": "临时目录不存在，无法上传."}, "id" : "id"}');
		}
		$cleanupTargetDir = true;
		$maxFileAge       = 1080000;
		@set_time_limit(0);
		// Clean the fileName for security reasons
		if (empty ($name)) {
			$name = isset ($_FILES ['file'] ['name']) ? $_FILES ['file'] ['name'] : false;
		}
		if (empty ($name)) {
			status_header(422);
			die ('{"jsonrpc" : "2.0", "error" : {"code": 200, "message": "无法完成上传."}, "id" : "id"}');
		}
		$name     = thefilename($name);
		$fileName = preg_replace('/[^\w\._]+/', rand_str(5, 'a-z'), $name);
		$filext   = strtolower(strrchr($fileName, '.'));

		$uploader = $this->createUploader();
		if (!$uploader->allowed($filext)) {
			status_header(422);
			die ('{"jsonrpc" : "2.0", "error" : {"code": 200, "message": "文件扩展名错误。"}, "id" : "id"}');
		}
		// Make sure the fileName is unique but only if chunking is disabled
		if ($chunks < 2 && file_exists($targetDir . DS . $fileName)) {
			$fileName = unique_filename($targetDir, $fileName);
		}
		$filePath = $targetDir . DS . $fileName;
		// Create target dir
		if (!file_exists($targetDir)) {
			@mkdir($targetDir, 0755, true);
		}
		// Remove old temp files
		if ($cleanupTargetDir && is_dir($targetDir) && ($dir = opendir($targetDir))) {
			while (($file = readdir($dir)) !== false) {
				$tmpfilePath = $targetDir . DS . $file;
				// Remove temp file if it is older than the max age and is not the current file
				if (preg_match('/\.part$/', $file) && (filemtime($tmpfilePath) < time() - $maxFileAge) && ($tmpfilePath != "{$filePath}.part")) {
					@unlink($tmpfilePath);
				}
			}
			@closedir($dir);
		} else {
			status_header(422);
			die ('{"jsonrpc" : "2.0", "error" : {"code": 100, "message": "无法打开临时目录。"}, "id" : "id"}');
		}
		$contentType = '';
		// Look for the content type header
		if (isset ($_SERVER ["HTTP_CONTENT_TYPE"])) {
			$contentType = $_SERVER ["HTTP_CONTENT_TYPE"];
		}
		if (isset ($_SERVER ["CONTENT_TYPE"])) {
			$contentType = $_SERVER ["CONTENT_TYPE"];
		}
		// Handle non multipart uploads older WebKit versions didn't support multipart in HTML5
		if (strpos($contentType, "multipart") !== false) {
			if (!empty ($_FILES ['file'] ['error'])) {
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
				status_header(422);
				die ('{"jsonrpc" : "2.0", "error" : {"code": 100, "message": "' . $error . '"}, "id" : "id"}');
			}
			if (isset ($_FILES ['file'] ['tmp_name']) && is_uploaded_file($_FILES ['file'] ['tmp_name'])) {
				$out = fopen("{$filePath}.part", $chunk == 0 ? "wb" : "ab");
				if ($out) {
					$in = fopen($_FILES ['file'] ['tmp_name'], "rb");
					if ($in) {
						do {
							$buff = fread($in, 4096);
							if ($buff) {
								fwrite($out, $buff);
							}
						} while ($buff);
					} else {
						status_header(422);
						die ('{"jsonrpc" : "2.0", "error" : {"code": 101, "message": "系统错误，无法打开输入流。"}, "id" : "id"}');
					}
					@fclose($in);
					@fclose($out);
					@unlink($_FILES ['file'] ['tmp_name']);
				} else {
					status_header(422);
					die ('{"jsonrpc" : "2.0", "error" : {"code": 102, "message": "系统错误，无法保存临时文件。"}, "id" : "id"}');
				}
			} else {
				status_header(422);
				die ('{"jsonrpc" : "2.0", "error" : {"code": 103, "message": "系统错误，尝试打开系统文件。"}, "id" : "id"}');
			}
		} else {
			$out = fopen("{$filePath}.part", $chunk == 0 ? "wb" : "ab");
			if ($out) {
				// Read binary input stream and append it to temp file
				$in = fopen("php://input", "rb");
				if ($in) {
					do {
						$buff = fread($in, 4096);
						if ($buff) fwrite($out, $buff);
					} while ($buff);
				} else {
					status_header(422);
					die ('{"jsonrpc" : "2.0", "error" : {"code": 101, "message": "系统错误，无法打开输入流。"}, "id" : "id"}');
				}
				@fclose($in);
				@fclose($out);
			} else {
				status_header(422);
				die ('{"jsonrpc" : "2.0", "error" : {"code": 102, "message": "系统错误，无法保存临时文件。"}, "id" : "id"}');
			}
		}
		// Check if file has been uploaded
		if (!$chunks || $chunk == $chunks - 1) {
			if (rename("{$filePath}.part", $filePath)) {
				if (filesize($filePath) > $uploader->getMaxSize()) {
					@unlink($filePath);
					status_header(422);
					die ('{"jsonrpc" : "2.0", "error" : {"code": 102, "message": "文件太大啦，已经超出系统允许的最大值."}, "id" : "id"}');
				}
				$water = $this->watermark();
				if ($water) {
					$img = new \ImageUtil ($filePath);
					list($watermark, $pos, $msize) = $water;
					$img->watermark(WEB_ROOT . $watermark, $pos, $msize);
				}
				$rst = $uploader->save($filePath);
				$uploader->close();
				if ($rst) {
					$type = get_media_type($filext);
					die ('{"jsonrpc" : "2.0","type":"' . $type . '", "result" : true,"size":"' . $rst [3] . '","width":"' . $rst [4] . '","height":"' . $rst [5] . '","url1":"' . $rst [0] . '","url":"' . the_media_src($rst [0]) . '","file":"' . $rst [2] . '"}');
				} else {
					@unlink($filePath);
					$message = $uploader->get_last_error();
					status_header(422);
					die ('{"jsonrpc" : "2.0", "error" : {"code": 102, "message": "' . $message . '"}, "id" : "id"}');
				}
			} else {
				@unlink("{$filePath}.part");
				status_header(422);
				die ('{"jsonrpc" : "2.0", "error" : {"code": 102, "message": "系统错误，无法保存文件"}, "id" : "id"}');
			}
		}
		die ('{"jsonrpc" : "2.0", "error" : {"code": 102, "message": "数据不完整."}, "id" : "id"}');
	}

	public function createUploader() {
		return \MediaUploadHelper::getUploader();
	}

	public function watermark() {
		return null;
	}

}