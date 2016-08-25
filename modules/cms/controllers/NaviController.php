<?php
class NaviController extends Controller {
	protected $acls = array ('data' => 'r:cms/navi','index' => 'r:cms/navi','add' => 'c:cms/navi','edit' => 'u:cms/navi','save' => 'id|u:cms/navi;c:cms/navi','del' => 'd:cms/navi' );
	protected $checkUser = true;
	private $target = array ('_self' => '原窗口','_blank' => '新窗口' );
	public function index($type = 'default') {
		$data = array ();
		$naviName = $this->prepareData ( $data, $type );
		$data ['canAddNavi'] = icando ( 'c:cms/navi' );
		$data ['canDeleteNavi'] = icando ( 'd:cms/navi' );
		$data ['canEditNavi'] = icando ( 'u:cms/navi' );
		$items = dbselect ( '*' )->from ( '{cms_navi_menu} AS CH' )->where ( array ('CH.deleted' => 0,'CH.navi' => $type ) );
		$data ['items'] = $items->asc('sort');
		return view ( 'navi/index.tpl', $data );
	}
	public function add($type = 'default', $upid = 0) {
		$upid = intval ( $upid );
		$data = array ();
		$naviType = $this->prepareData ( $data, $type );
		
		$data ['navis'] = array ('0' => '顶级菜单项' );
		dbselect ( '*' )->from ( '{cms_navi_menu}' )->treeWhere ( array ('navi' => $type ) )->treeOption ( $data ['navis'] );
		$data ['targets'] = $this->target;
		$form = new NaviMenuForm ();
		$data ['upid'] = $upid;
		
		$data ['rules'] = $form->rules ();
		return view ( 'navi/form.tpl', $data );
	}
	public function edit($id) {
		$id = intval ( $id );
		$ch = dbselect ( '*' )->from ( '{cms_navi_menu}' )->where ( array ('id' => $id ) )->get ( 0 );
		if ($ch) {
			$type = $ch ['navi'];
			$naviType = $this->prepareData ( $ch, $type );
			$ch ['navis'] = array ('0' => '顶级菜单项' );
			$tree = dbselect ( '*' )->from ( '{cms_navi_menu}' )->treeWhere ( array ('navi' => $type ) );
			$tree->treeOption ( $ch ['navis'], 'id', 'upid', 'name', $ch ['id'] );
			$ch ['targets'] = $this->target;
			$form = new NaviMenuForm ( $ch );
			$ch ['rules'] = $form->rules ();
			return view ( 'navi/form.tpl', $ch );
		} else {
			Response::showErrorMsg ( '内容不存在', 404 );
		}
	}
	public function csort($id, $sort) {
		$id = intval ( $id );
		$sort = intval ( $sort );
		if (! empty ( $id )) {
			dbupdate ( '{cms_navi_menu}' )->set ( array ('sort' => $sort ) )->where ( array ('id' => $id ) )->exec ();
		}
		return NuiAjaxView::ok ( '排序修改完成.' );
	}
	public function del($id) {
		$id = intval ( $id );
		if ($id) {
			if (dbselect ( 'id' )->from ( '{cms_navi_menu}' )->where ( array ('upid' => $id ) )->count ( 'id' ) == 0) {
				$data ['deleted'] = 1;
				$data ['update_time'] = time ();
				$data ['update_uid'] = $this->user->getUid ();
				dbupdate ( '{cms_navi_menu}' )->set ( $data )->where ( array ('id' => $id ) )->exec ();
				$recycle = new DefaultRecycle ( $id, 'NaviMenu', 'cms_navi_menu', 'ID:{id};菜单项:{name},菜单:{navi}' );
				RecycleHelper::recycle ( $recycle );
				return NuiAjaxView::ok ( '菜单项已经放入回收站.', 'click', '#refresh' );
			} else {
				Response::showErrorMsg ( '请先删除此菜单的所有子菜单，然后再删除本菜单.', 403 );
			}
		} else {
			Response::showErrorMsg ( '菜单项不存在.', 404 );
		}
	}
	public function save() {
		$form = new NaviMenuForm ();
		$catelog = $form->valid ();
		if ($catelog) {
			if (empty ( $catelog ['upid'] )) {
				$catelog ['upid'] = 0;
			}
			if ($catelog ['hidden'] == 'on') {
				$catelog ['hidden'] = 1;
			} else {
				$catelog ['hidden'] = 0;
			}
			if (empty ( $catelog ['sort'] )) {
				$catelog ['sort'] = 999;
			}
			if ($catelog ['page_id']) {
				$url = dbselect ( 'url' )->from ( '{cms_page}' )->where ( array ('id' => $catelog ['page_id'] ) )->get ( 'url' );
				$catelog ['url'] = $url;
			}
			$time = time ();
			$uid = $this->user->getUid ();
			if (empty ( $catelog ['id'] )) {
				unset ( $catelog ['id'] );
				$catelog ['create_time'] = $time;
				$catelog ['update_time'] = $time;
				$catelog ['create_uid'] = $uid;
				$catelog ['update_uid'] = $uid;
				$rst = dbinsert ( $catelog )->into ( '{cms_navi_menu}' )->exec ();
			} else {
				$id = $catelog ['id'];
				unset ( $catelog ['id'] );
				$catelog ['update_time'] = $time;
				$catelog ['update_uid'] = $uid;
				$rst = dbupdate ( '{cms_navi_menu}' )->set ( $catelog )->where ( array ('id' => $id ) )->exec ();
			}
			if ($rst) {
				return NuiAjaxView::ok ( '成功保存菜单项', 'click', '#refresh' );
			} else {
				return NuiAjaxView::error ( '保存菜单项出错啦:' . DatabaseDialect::$lastErrorMassge );
			}
		} else {
			return NuiAjaxView::validate ( 'NaviMenuForm', '数据校验出错啦.', $form->getErrors () );
		}
	}
	private function prepareData(&$data, $type) {
		$naviType = dbselect ( 'name' )->from ( '{cms_catelog}' )->where ( array ('type' => 'navi','alias' => $type ) )->get ( 'name' );
		$data ['naviType'] = $naviType;
		$data ['type'] = $type;
		return $naviType;
	}
}
