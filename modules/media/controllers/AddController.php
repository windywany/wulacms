<?php
class AddController extends Controller {
	protected $checkUser = true;
	protected $acls = array ('index' => 'm:media','index_post' => 'upload:media' );
	public function index() {
		$data ['exts'] = cfg ( 'allow_exts@media', 'jpg,gif,png,bmp,jpeg,zip,rar,7z,tar,gz,bz2,doc,docx,txt,ppt,pptx,xls,xlsx,pdf,mp3,avi,mp4,flv,swf,apk' );
		$data ['maxSize'] = cfg ( 'max_upload_size@media', 20 ) . 'M';
		return view ( 'add.tpl', $data );
	}
	public function index_post($images = array(), $images_alt = array(), $images_desc = array()) {
		if ($images) {
			foreach ( $images as $key => $img ) {
				$data = array ();
				if (isset ( $images_alt [$key] )) {
					$data ['alt'] = $images_alt [$key];
					$data ['filename'] = $images_alt [$key];
				}
				if (isset ( $images_desc [$key] )) {
					$data ['note'] = $images_desc [$key];
				}
				if ($data) {
					dbupdate ( '{media}' )->set ( $data )->where ( array ('url' => $img ) )->exec ();
				}
			}
		}		
		return NuiAjaxView::refresh ( '上传完成' );
	}
}