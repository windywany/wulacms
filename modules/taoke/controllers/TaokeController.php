<?php
/**
 * 相册控制器.
 * @author Leo Ning.
 *
 */
class TaokeController extends Controller {
	protected $acls = array ('*' => 'r:cms/page','upload' => 'u:cms/page','upload_post' => 'u:cms/page','save' => 'u:cms/page','set_hot' => 'u:cms/page','edit' => 'u:cms/page','del' => 'd:cms/page' );
	protected $checkUser = true;
	public function index() {
		$data = array ();
		$data ['canDelPage'] = icando ( 'd:cms/page' );
		$data ['canAddPage'] = icando ( 'c:cms/page' );
		$data ['channels'] = ChannelForm::getChannelTree ( 'taoke', false, true );
		return view ( 'taoke.tpl', $data );
	}
	public function pic($id) {
		$id = intval ( $id );
		if (empty ( $id )) {
			Response::respond ( 404 );
		}
		$album = dbselect ( 'CP.title' )->from ( '{cms_page} AS CP' )->where ( array ('id' => $id ) )->get ( 'title' );
		if (empty ( $album )) {
			Response::respond ( 404 );
		}
		$data = array ('album_name' => $album,'album_id' => $id );
		$data ['canDelPage'] = icando ( 'd:cms/page' );
		$data ['canAddPage'] = icando ( 'c:cms/page' );
		return view ( 'pic.tpl', $data );
	}
	public function upload($id) {
		$id = intval ( $id );
		if (empty ( $id )) {
			Response::respond ( 404 );
		}
		$album = dbselect ( 'CP.title' )->from ( '{cms_page} AS CP' )->where ( array ('id' => $id ) )->get ( 'title' );
		if (empty ( $album )) {
			Response::respond ( 404 );
		}
		$data = array ('album_name' => $album,'album_id' => $id );
		$data ['exts'] = 'jpg,gif,png,jpeg';
		$data ['maxSize'] = cfg ( 'max_upload_size@media', 20 ) . 'M';
		return view ( 'upload.tpl', $data );
	}
	public function upload_post($images = array(), $images_size = array(), $images_width = array(), $images_height = array(), $images_alt = array(), $images_desc = array(), $album_id = 0) {
		$id = intval ( $album_id );
		if (empty ( $album_id ) || ! dbselect ()->from ( '{album}' )->where ( array ('page_id' => $id ) )->exist ( 'page_id' )) {
			Response::respond ( 404 );
		}
		if ($images) {
			$datas = array ();
			$data ['album_id'] = $id;
			$data ['update_time'] = $data ['create_time'] = time ();
			$data ['update_uid'] = $data ['create_uid'] = $this->user->getUid ();
			$data ['deleted'] = 0;
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
			if ($datas) {
				dbinsert ( $datas, true )->into ( '{album_item}' )->exec ();
			}
		}
		
		$rtn_url = tourl ( 'album/pic', false ) . $album_id;
		$html [] = '[<a href="#' . $rtn_url . '" onclick="nUI.closeAjaxDialog()">返回相片列表</a>]';
		$html [] = '[<a href="javascript:void(0);" onclick="nUI.closeAjaxDialog()">继续上传</a>]';
		
		return NuiAjaxView::dialog ( '<p class="text-left">接下来你可以：</p><p class="text-left">' . implode ( '&nbsp;', $html ) . '</p>', '相片上传完成!', array ('model' => true,'height' => 'auto' ) );
	}
	public function edit($id) {
		$id = intval ( $id );
		if (empty ( $id ) || ($pic = dbselect ( '*' )->from ( '{album_item}' )->where ( array ('id' => $id ) )->get ()) === null) {
			Response::respond ( 404 );
		}
		$form = new AlbumPicForm ( $pic );
		$widgets = new DefaultFormRender ( $form->buildWidgets ( $pic ) );
		$rules = $form->rules ();
		$data ['widgets'] = $widgets;
		$data ['rules'] = $rules;
		$data ['pic_url'] = $pic ['url'];
		return view ( 'pic_form.tpl', $data );
	}
	public function save() {
		$form = new AlbumPicForm ();
		$pic = $form->valid ();
		if ($pic) {
			$item ['update_time'] = time ();
			$item ['update_uid'] = $this->user->getUid ();
			$item ['title'] = $pic ['title'];
			$item ['note'] = $pic ['note'];
			$item ['search_index'] = '';
			$item ['is_hot'] = isset ( $pic ['is_hot'] [0] ) ? 1 : 0;
			$keys = $item ['title'] . $item ['note'];
			
			if ($keys) {
				$keys = get_keywords ( null, $keys );
				if ($keys [1]) {
					$item ['search_index'] = $keys [1];
				}
			}
			dbupdate ( '{album_item}' )->set ( $item )->where ( array ('id' => $pic ['id'] ) )->exec ();
			return NuiAjaxView::callback ( 'AlbumPicSaved', '', '保存完成' );
		} else {
			return NuiAjaxView::validate ( 'AlbumPicForm', '表单数据有错', $form->getErrors () );
		}
	}
	public function del($ids) {
		$ids = safe_ids ( $ids, ',', true );
		if (! empty ( $ids )) {
			$data ['deleted'] = 1;
			$data ['update_time'] = time ();
			$data ['update_uid'] = $this->user->getUid ();
			if (dbupdate ( '{album_item}' )->set ( $data )->where ( array ('id IN' => $ids ) )->exec ()) {
				$recycle = new DefaultRecycle ( $ids, 'AlbumPic', 'album_item', 'ID:{id};图片名:{title}' );
				RecycleHelper::recycle ( $recycle );
				return NuiAjaxView::reload ( '#page-table', '图片已删除' );
			} else {
				return NuiAjaxView::error ( '数据库操作失败.' );
			}
		} else {
			Response::showErrorMsg ( '错误的编号', 500 );
		}
	}
	public function sethot($id, $hot) {
		$id = intval ( $id );
		$hot = $hot === '1' ? 1 : 0;
		dbupdate ( '{album_item}' )->set ( array ('is_hot' => $hot,'update_time' => time (),'update_uid' => $this->user->getUid () ) )->where ( array ('id' => $id ) )->exec ();
		return NuiAjaxView::reload ( '#page-table' );
	}
	public function pic_data($id, $_cp = 1, $_lt = 20, $_sf = 'is_hot', $_od = 'd', $_ct = 0) {
		$id = intval ( $id );
		$rows = dbselect ( 'PIC.*,U.nickname AS create_user' )->from ( '{album_item} AS PIC' );
		$rows->join ( '{user} AS U', 'PIC.create_uid = U.user_id' );
		$where = array ('album_id' => $id,'PIC.deleted' => 0 );
		$keywords = rqst ( 'keywords' );
		if ($keywords) {
			$t = '%' . $keywords . '%';
			$keywords = convert_search_keywords ( $keywords );
			$where [] = array ('search_index MATCH' => $keywords,'||title LIKE' => $t );
		}
		if (rqset ( 'flag_hot' )) {
			$where ['is_hot'] = 1;
		}
		$rows->where ( $where );
		$rows->sort ( $_sf, $_od );
		$rows->limit ( ($_cp - 1) * $_lt, $_lt );
		$data = array ();
		$data ['total'] = '';
		if ($_ct) {
			$data ['total'] = $rows->count ( 'id' );
		}
		$data ['rows'] = $rows;
		$data ['canDelPage'] = icando ( 'd:cms/page' );
		$data ['canEditPage'] = icando ( 'u:cms/page' );
		return view ( 'pic_data.tpl', $data );
	}
	public function data($_cp = 1, $_lt = 20, $_sf = 'CP.id', $_od = 'd', $_ct = 0) {
		$rows = dbselect ( 'CP.id,CP.flag_h,CP.flag_c,CP.flag_a,CP.flag_b,CP.flag_j,CP.title,CP.title2,CP.status,CP.update_time,CP.create_time,CP.image,
				CP.publish_time,CP.keywords,CP.url,CH.root,CH.name as channelName,CM.name AS modelName,CU.nickname as cuname' )->from ( '{cms_page} AS CP' );
		$rows->field ( 'UU.nickname AS uuname' );
		$rows->join ( '{cms_channel} AS CH', 'CP.channel = CH.refid' );
		$rows->join ( '{cms_model} AS CM', 'CP.model = CM.refid' );
		$rows->join ( '{user} AS CU', 'CP.create_uid = CU.user_id' );
		$rows->join ( '{user} AS UU', 'CP.update_uid = UU.user_id' );
		$cnt = dbselect ( imv ( 'COUNT(AI.id)' ) )->from ( '{album_item} AS AI' )->where ( array ('AI.album_id' => imv ( 'CP.id' ),'AI.deleted' => 0 ) );
		$rows->field ( $cnt, 'album_cnt' );
		$where ['CP.deleted'] = 0;
		$where ['CP.hidden'] = 0;
		$pid = irqst ( 'pid' );
		if ($pid) {
			$where ['CP.id'] = $pid;
		} else {
			$where ['CP.model'] = 'album';
			$channel = rqst ( 'channel' );
			if ($channel) {
				$where ['CP.channel'] = $channel;
			}
			$keywords = rqst ( 'keywords' );
			if ($keywords) {
				$t = '%' . $keywords . '%';
				$keywords = convert_search_keywords ( $keywords );
				$where [] = array ('search_index MATCH' => $keywords,'||CP.title LIKE' => $t,'||CP.title2 LIKE' => $t );
			}
		}
		$rows->where ( $where );
		$rows->sort ( $_sf, $_od );
		$rows->limit ( ($_cp - 1) * $_lt, $_lt );
		$data = array ();
		$data ['total'] = '';
		if ($_ct) {
			$data ['total'] = $rows->count ( 'CP.id' );
		}
		$data ['rows'] = $rows;
		$data ['canDelPage'] = icando ( 'd:cms/page' );
		$data ['canEditPage'] = icando ( 'u:cms/page' );
		$data ['canEditTag'] = icando ( 'u:cms/tag' );
		$data ['cCache'] = icando ( 'cmc:system' ) && bcfg ( 'enabled@mem' );
		$data ['disable_approving'] = bcfg ( 'disable_approving@cms', false );
		$data ['enableCopy'] = bcfg ( 'enable_copy@cms' );
		$tpl = 'album_data.tpl';
		return view ( $tpl, $data );
	}
}