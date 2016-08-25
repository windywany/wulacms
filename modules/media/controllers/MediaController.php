<?php
/**
 * 多媒体控制器.
 *
 * @author Guangfeng
 */
class MediaController extends Controller {
	protected $checkUser = array ('dashboard','admin','upload' );
	protected $acls = array ('data' => 'm:media','index' => 'm:media','preference' => 'media:system/preference','preference_post' => 'media:system/preference' );
	public function index() {
		$data ['canDelMedia'] = icando ( 'm:media' );
		$data ['canUploadMedia'] = icando ( 'upload:media' );
		$data ['types'] = get_media_type_list ();
		return view ( 'index.tpl', $data );
	}
	public function preference($_g = 'base') {
		if ($_g == 'client') {
			$form = new MediaClientPreferenceForm ();
		} else if ($_g == 'remote') {
			$form = new MediaRemotePreferenceForm ();
		} else {
			$_g = 'base';
			$form = new MediaPreferenceForm ();
		}
		$data ['_g'] = $_g;
		$data ['rules'] = $form->rules ();
		$data ['form'] = $form;
		$data ['groups'] = array ('base' => '基本设置','client' => '接入设置','remote' => '下载设置' );
		$data ['formName'] = get_class ( $form );
		$values = dbselect ( 'name,value' )->from ( '{preferences}' )->where ( array ('preference_group' => 'media' ) )->toArray ( 'value', 'name' );
		
		$data ['widgets'] = new DefaultFormRender ( $form->buildWidgets ( $values ) );
		return view ( 'preference.tpl', $data );
	}
	public function preference_post($_g = 'base') {
		if ($_g == 'client') {
			$form = new MediaClientPreferenceForm ();
		} else if ($_g == 'remote') {
			$form = new MediaRemotePreferenceForm ();
		} else {
			$_g = 'base';
			$form = new MediaPreferenceForm ();
		}
		$cfgs = $form->valid ();
		if ($cfgs) {
			$time = time ();
			$uid = $this->user->getUid ();
			$datas = array ();
			foreach ( $cfgs as $name => $value ) {
				$data = array ();
				$data ['preference_group'] = 'media';
				$data ['name'] = $name;
				$cfg = dbselect ( 'preference_id,value' )->from ( '{preferences}' )->where ( $data )->get ();
				if ($cfg && $cfg ['value'] != $value) {
					$data ['value'] = $value;
					$data ['update_time'] = $time;
					$data ['user_id'] = $uid;
					unset ( $cfg ['value'] );
					dbupdate ( '{preferences}' )->set ( $data )->where ( $cfg )->exec ();
				} else if (! $cfg) {
					$data ['value'] = $value;
					$data ['update_time'] = $time;
					$data ['user_id'] = $uid;
					$datas [] = $data;
				}
			}
			if ($datas) {
				dbinsert ( $datas, true )->into ( '{preferences}' )->exec ();
			}
			RtCache::delete ( 'system_preferences' );
			return NuiAjaxView::ok ( "保存完成." );
		} else {
			return NuiAjaxView::validate ( get_class ( $form ), '数据格式不正确，请重新填写.', $form->getErrors () );
		}
	}
	public function data($_cp = 1, $_lt = 20, $_sf = 'M.id', $_od = 'd', $_ct = 0, $bd = '', $sd = '', $file = '') {
		$medias = dbselect ( 'M.*,U.nickname' )->from ( '{media} AS M' );
		$medias->join ( '{user} AS U', 'M.uid = U.user_id' );
		// 排序
		$medias->sort ( $_sf, $_od );
		// 分页
		$medias->limit ( ($_cp - 1) * $_lt, $_lt );
		// 条件
		$where = Condition::where ( array ('U.nickname','LIKE','user' ), 'type', array ('filename','LIKE' ) );
		if (! empty ( $bd )) {
			$where ['M.create_time >='] = strtotime ( $bd . ' 00:00:00' );
		}
		if (! empty ( $sd )) {
			$where ['M.create_time <='] = strtotime ( $sd . ' 23:59:59' );
		}
		$medias->where ( $where );
		// 总数
		$total = '';
		if ($_ct) {
			$total = $medias->count ( 'M.id' );
		}
		
		$data ['total'] = $total;
		$data ['rows'] = $medias;
		$data ['types'] = get_media_type_list ();
		return view ( 'data.tpl', $data );
	}
	/**
	 * plupload方式的文件上传.
	 */
	public function upload($chunk = 0, $chunks = 0, $name = '', $dir = '', $water = 1, $locale = 0) {
		$userType = rqst ( 'userType', 'admin' );
		$locale = is_numeric ( $locale ) ? intval ( $locale ) : $locale;
		$this->user = whoami ( $userType );
		if (! icando ( 'upload:media', $this->user )) {
			status_header ( 403 );
			die ( '{"jsonrpc" : "2.0", "error" : {"code": 200, "message": "无权上传文件."}, "id" : "id"}' );
		}
		$targetDir = TMP_PATH . "plupload";
		if (! file_exists ( $targetDir )) {
			mkdir ( $targetDir, 0755 );
		}
		$cleanupTargetDir = true;
		$maxFileAge = 1080000;
		@set_time_limit ( 0 );
		// Clean the fileName for security reasons
		if (empty ( $name )) {
			$name = isset ( $_FILES ['file'] ['name'] ) ? $_FILES ['file'] ['name'] : false;
		}
		if (empty ( $name )) {
			status_header ( 422 );
			die ( '{"jsonrpc" : "2.0", "error" : {"code": 200, "message": "无法完成上传."}, "id" : "id"}' );
		}
		$name = thefilename ( $name );
		$fileName = preg_replace ( '/[^\w\._]+/', rand_str ( 5, 'a-z' ), $name );
		$filext = strtolower ( strrchr ( $fileName, '.' ) );
		
		$pathinfo = pathinfo ( $name );
		$name = $pathinfo ['filename'];
		
		if ($locale === 2) {
			$uploader = new FileUploader ();
		} else {
			$uploader = MediaUploadHelper::getUploader ( $locale );
		}
		if (! $uploader->allowed ( $filext )) {
			status_header ( 422 );
			die ( '{"jsonrpc" : "2.0", "error" : {"code": 200, "message": "文件扩展名错误。"}, "id" : "id"}' );
		}
		// Make sure the fileName is unique but only if chunking is disabled
		if ($chunks < 2 && file_exists ( $targetDir . DS . $fileName )) {
			$fileName = unique_filename ( $targetDir, $fileName );
		}
		$filePath = $targetDir . DS . $fileName;
		// Create target dir
		if (! file_exists ( $targetDir )) {
			@mkdir ( $targetDir, 0755, true );
		}
		// Remove old temp files
		if ($cleanupTargetDir && is_dir ( $targetDir ) && ($dir = opendir ( $targetDir ))) {
			while ( ($file = readdir ( $dir )) !== false ) {
				$tmpfilePath = $targetDir . DS . $file;
				// Remove temp file if it is older than the max age and is not the current file
				if (preg_match ( '/\.part$/', $file ) && (filemtime ( $tmpfilePath ) < time () - $maxFileAge) && ($tmpfilePath != "{$filePath}.part")) {
					@unlink ( $tmpfilePath );
				}
			}
			@closedir ( $dir );
		} else {
			status_header ( 422 );
			die ( '{"jsonrpc" : "2.0", "error" : {"code": 100, "message": "无法打开临时目录。"}, "id" : "id"}' );
		}
		
		// Look for the content type header
		if (isset ( $_SERVER ["HTTP_CONTENT_TYPE"] )) {
			$contentType = $_SERVER ["HTTP_CONTENT_TYPE"];
		}
		if (isset ( $_SERVER ["CONTENT_TYPE"] )) {
			$contentType = $_SERVER ["CONTENT_TYPE"];
		}
		// Handle non multipart uploads older WebKit versions didn't support multipart in HTML5
		if (strpos ( $contentType, "multipart" ) !== false) {
			if (! empty ( $_FILES ['file'] ['error'] )) {
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
				status_header ( 422 );
				die ( '{"jsonrpc" : "2.0", "error" : {"code": 100, "message": "' . $error . '"}, "id" : "id"}' );
			}
			if (isset ( $_FILES ['file'] ['tmp_name'] ) && is_uploaded_file ( $_FILES ['file'] ['tmp_name'] )) {
				$out = fopen ( "{$filePath}.part", $chunk == 0 ? "wb" : "ab" );
				if ($out) {
					$in = fopen ( $_FILES ['file'] ['tmp_name'], "rb" );
					if ($in) {
						do {
							$buff = fread ( $in, 4096 );
							if ($buff) {
								fwrite ( $out, $buff );
							}
						} while ( $buff );
					} else {
						status_header ( 422 );
						die ( '{"jsonrpc" : "2.0", "error" : {"code": 101, "message": "系统错误，无法打开输入流。"}, "id" : "id"}' );
					}
					@fclose ( $in );
					@fclose ( $out );
					@unlink ( $_FILES ['file'] ['tmp_name'] );
				} else {
					status_header ( 422 );
					die ( '{"jsonrpc" : "2.0", "error" : {"code": 102, "message": "系统错误，无法保存临时文件。"}, "id" : "id"}' );
				}
			} else {
				status_header ( 422 );
				die ( '{"jsonrpc" : "2.0", "error" : {"code": 103, "message": "系统错误，尝试打开系统文件。"}, "id" : "id"}' );
			}
		} else {
			$out = fopen ( "{$filePath}.part", $chunk == 0 ? "wb" : "ab" );
			if ($out) {
				// Read binary input stream and append it to temp file
				$in = fopen ( "php://input", "rb" );
				if ($in) {
					do {
						$buff = fread ( $in, 4096 );
						if ($buff)
							fwrite ( $out, $buff );
					} while ( $buff );
				} else {
					status_header ( 422 );
					die ( '{"jsonrpc" : "2.0", "error" : {"code": 101, "message": "系统错误，无法打开输入流。"}, "id" : "id"}' );
				}
				@fclose ( $in );
				@fclose ( $out );
			} else {
				status_header ( 422 );
				die ( '{"jsonrpc" : "2.0", "error" : {"code": 102, "message": "系统错误，无法保存临时文件。"}, "id" : "id"}' );
			}
		}
		// Check if file has been uploaded
		if (! $chunks || $chunk == $chunks - 1) {
			if (rename ( "{$filePath}.part", $filePath )) {
				if (filesize ( $filePath ) > $uploader->getMaxSize ()) {
					@unlink ( $filePath );
					status_header ( 422 );
					die ( '{"jsonrpc" : "2.0", "error" : {"code": 102, "message": "文件太大啦，已经超出系统允许的最大值."}, "id" : "id"}' );
				}
				$water = $water && bcfg ( 'enable_watermark@media', false );
				if ($water) {
					$watermark = cfg ( 'watermark@media' );
					if ($watermark && file_exists ( WEB_ROOT . $watermark )) {
						$img = new ImageUtil ( $filePath );
						$img->watermark ( WEB_ROOT . $watermark, cfg ( 'watermark_pos@media', 'br' ), cfg ( 'watermark_min_size@media' ) );
					}
				}
				$rst = $uploader->save ( $filePath, $locale === 2 ? '/tmp/' : null );
				$uploader->close ();
				if ($rst) {
					if ($userType == 'admin') {
						ActivityLog::info ( __ ( 'Upload file "%s" successfully.', $rst [0] ), 'Upload' );
						FileUploader::save2db ( $rst, $filext );
					}
					$type = get_media_type ( $filext );
					die ( '{"jsonrpc" : "2.0","type":"' . $type . '", "result" : true, "id" : "id","size":"' . $rst [3] . '","width":"' . $rst [4] . '","height":"' . $rst [5] . '","url1":"' . $rst [0] . '","url":"' . the_media_src ( $rst [0] ) . '","file":"' . $rst [2] . '"}' );
				} else {
					@unlink ( $filePath );
					$message = $uploader->get_last_error ();
					status_header ( 422 );
					die ( '{"jsonrpc" : "2.0", "error" : {"code": 102, "message": "' . $message . '"}, "id" : "id"}' );
				}
			} else {
				@unlink ( "{$filePath}.part" );
				status_header ( 422 );
				die ( '{"jsonrpc" : "2.0", "error" : {"code": 102, "message": "系统错误，无法保存文件"}, "id" : "id"}' );
			}
		}
		die ( '{"jsonrpc" : "2.0", "error" : {"code": 102, "message": "数据不完整."}, "id" : "id"}' );
	}
	private function uploadError($msg) {
		return new JsonView ( array ('error' => 1,'message' => $msg ) );
	}
}
