<?php
class AlbumContentModel extends DefaultContentModel {
	public function __construct() {
		$model ['name'] = '相册';
		$model ['refid'] = 'album';
		$model ['status'] = 1;
		$model ['is_topic_model'] = 0;
		$model ['creatable'] = 1;
		$model ['addon_table'] = 'album';
		$model ['search_page_prefix'] = '';
		$model ['search_page_tpl'] = '';
		$model ['search_page_limit'] = '';
		$model ['template'] = '@album/views/album_form.tpl';
		$model ['note'] = '相册模型';
		$model ['role'] = '';
		parent::__construct ( $model );
	}
	public function getForm() {
		return new AlbumForm ();
	}
	/*
	 * (non-PHPdoc) @see DefaultContentModel::save()
	 */
	public function save($page, $form) {
		$user = whoami ();
		$album ['page_id'] = $page ['id'];
		if (! dbselect ()->from ( '{album}' )->where ( $album )->exist ( 'page_id' )) {
			$album ['user_id'] = 0;
			dbinsert ( $album )->into ( '{album}' )->exec ();
		}
		
		$datas = array ();
		$data ['album_id'] = $page ['id'];
		$data ['update_time'] = $data ['create_time'] = time ();
		$data ['update_time'] = $data ['create_uid'] = $user->getUid ();
		$data ['deleted'] = 0;
		
		$images = rqst ( 'album_pics', array () );
		$images_alt = rqst ( 'album_pics_alt' );
		$images_desc = rqst ( 'album_pics_desc' );
		$images_size = rqst ( 'album_pics_size' );
		$images_width = rqst ( 'album_pics_width' );
		$images_height = rqst ( 'album_pics_height' );
		foreach ( $images as $key => $img ) {
			$data ['url'] = $img;
			$data ['url1'] = $img;
			$keys = array ();
			$data ['search_index'] = '';
			if (isset ( $images_alt [$key] )) {
				$data ['title'] = $images_alt [$key];
			} else {
				continue;
			}
			if (isset ( $images_size [$key] )) {
				$data ['size'] = intval ( $images_size [$key] );
			} else {
				$data ['size'] = 0;
			}
			if (isset ( $images_width [$key] )) {
				$data ['width'] = intval ( $images_width [$key] );
			} else {
				$data ['width'] = 0;
			}
			if (isset ( $images_height [$key] )) {
				$data ['height'] = intval ( $images_height [$key] );
			} else {
				$data ['height'] = 0;
			}
			if (isset ( $images_desc [$key] )) {
				$data ['note'] = $images_desc [$key];
			} else {
				$data ['note'] = '';
			}
			$keys = $data ['title'] . $data ['note'];
			if ($keys) {
				$keys = get_keywords ( null, $keys );
				if ($keys [1]) {
					$data ['search_index'] = $keys [1];
				}
			}
			$datas [] = $data;
		}
		$this->download_remote_pics ( $datas, $page ['id'], $user->getUid (), $page ['title'] );
		if ($datas) {
			dbinsert ( $datas, true )->into ( '{album_item}' )->exec ();
		}
	}
	/*
	 * (non-PHPdoc) @see DefaultContentModel::load()
	 */
	public function load(&$data, $id) {
		$pics = dbselect ( 'title,url,url1 AS thumb_url' )->from ( '{album_item}' )->where ( array ('album_id' => $id,'deleted' => 0 ) )->asc ( 'id' )->toArray ();
		
		$data ['album_items'] = $pics;
		$data ['album_items_count'] = count ( $pics );
	}
	public function download_remote_pics(&$datas, $pid, $uid, $title) {
		$remote_pics = rqst ( 'remote_pics' );
		if (! $remote_pics) {
			return;
		}
		$remote_pics = explode ( "\n", $remote_pics );
		$images = array ();
		$titles = array ();
		foreach ( $remote_pics as $pic ) {
			$img = false;
			$ppp = explode ( '||', $pic );
			$ppp [0] = trim ( $ppp [0] );
			if (! $ppp [0]) {
				continue;
			}
			if (preg_match ( '#^//.+$#', $ppp [0] )) {
				$img = 'http:' . $ppp [0];
			} else if (preg_match ( '#^(f|ht)tps?://.+#i', $pic )) {
				$img = $ppp [0];
			}
			if ($img) {
				$images [] = $img;
				if (isset ( $ppp [1] )) {
					$titles [$img] = $ppp [1];
				} else {
					$titles [$img] = $title;
				}
			}
		}
		if (! $images) {
			return;
		}
		$uploader = bcfg ( 'store_type@media' ) ? new RemoteUploader () : apply_filter ( 'get_uploader', new FileUploader () );
		$watermark = cfg ( 'watermark@media' );
		if ($watermark && file_exists ( WEB_ROOT . $watermark )) {
			$watermarkcfg = array (WEB_ROOT . $watermark,cfg ( 'watermark_pos@media', 'br' ),cfg ( 'watermark_min_size@media' ) );
		} else {
			$watermarkcfg = false;
		}
		$resize_h = irqst ( 'resize_h', 0 );
		if ($resize_h > 0) {
			$resize = array (0,0,0,- $resize_h );
		} else {
			$resize = array ();
		}
		$rst = ImageUtil::downloadRemotePic ( $images, $uploader, cfg ( 'timeout@media', 30 ), $watermarkcfg, $resize, 'http://www.baidu.com' );
		if ($rst) {
			$data ['album_id'] = $pid;
			$data ['update_time'] = $data ['create_time'] = time ();
			$data ['update_time'] = $data ['create_uid'] = $uid;
			$data ['deleted'] = 0;
			foreach ( $rst as $key => $img ) {
				$data ['url'] = $img [0];
				$data ['url1'] = $img [0];
				$data ['search_index'] = '';
				$data ['title'] = $titles [$key];
				$data ['size'] = intval ( $img [3] );
				$data ['width'] = intval ( $img [4] );
				$data ['height'] = intval ( $img [5] );
				$data ['note'] = $titles [$key];
				$datas [] = $data;
			}
		}
	}
}