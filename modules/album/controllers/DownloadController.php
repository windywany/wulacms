<?php
/**
 * 下载远程图片.
 * @author leo
 *
 */
class DownloadController extends Controller {
	protected $acls = array ('*' => 'u:cms/page' );
	protected $checkUser = true;
	public function index($id) {
		$id = intval ( $id );
		if (empty ( $id ) || ($pic = dbselect ( 'id,title' )->from ( '{cms_page}' )->where ( array ('id' => $id ) )->get ()) === null) {
			Response::respond ( 404 );
		}
		$form = new DownloadPicForm ();
		$data = $pic;
		$widgets = new DefaultFormRender ( $form->buildWidgets () );
		$rules = $form->rules ();
		$data ['widgets'] = $widgets;
		$data ['rules'] = $rules;
		return view ( 'download.tpl', $data );
	}
	public function index_post($id, $title) {
		$id = intval ( $id );
		if (empty ( $id ) || ($pic = dbselect ( 'id,title' )->from ( '{cms_page}' )->where ( array ('id' => $id ) )->get ()) === null) {
			Response::respond ( 404 );
		}
		$uid = $this->user->getUid ();
		$model = new AlbumContentModel ();
		$datas = array ();
		$model->download_remote_pics ( $datas, $id, $uid, $title );
		if ($datas) {
			dbinsert ( $datas, true )->into ( '{album_item}' )->exec ();
		}
		return NuiAjaxView::callback ( 'AlbumPicDownloaded', '', '图片下载完成' );
	}
}

