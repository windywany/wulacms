<?php
/**
 * 分类管理.
 * 
 * @author Guangfeng
 */
class CatelogController extends Controller {
	protected $checkUser = true;
	protected $acls = array ('data' => 'r:cms/catalog','index' => 'r:cms/catalog','add' => 'c:cms/catalog','edit' => 'u:cms/catalog','save' => 'id|u:cms/catalog;c:cms/catalog','del' => 'd:cms/catalog' );
	public function index($type = 'chunk') {
		$catelogTypes = apply_filter ( 'get_cms_catalog_types', array () );
		if (isset ( $catelogTypes [$type] )) {
			$data ['catelogType'] = $type;
			$data ['catelogTitle'] = $catelogTypes [$type] ['name'];
			$data ['canDeleteCatelog'] = icando ( 'd:cms/catalog' );
			$data ['canAdd'] = icando ( 'c:cms/catalog' );
			$data ['items'] = dbselect ( '*' )->from ( '{cms_catelog}' )->where ( array ('type' => $type,'deleted' => 0 ) )->sort ( 'id', 'a' );
			return view ( 'catelog/index.tpl', $data );
		} else {
			Response::showErrorMsg ( '未知分类类型', 403 );
		}
	}
	public function add($type = 'chunk', $upid = 0) {
		$catelogTypes = apply_filter ( 'get_cms_catalog_types', array () );
		if (isset ( $catelogTypes [$type] )) {
			$data ['catelogType'] = $type;
			$data ['catelogTitle'] = $catelogTypes [$type] ['name'];
			$data ['options'] = array ('0' => '无' );
			$data ['upid'] = $upid;
			dbselect ( '*' )->from ( '{cms_catelog}' )->treeWhere ( array ('type' => $type ) )->treeOption ( $data ['options'], 'id', 'upid', 'name' );
			$form = new CatelogForm ( array ('id' => 0,'type' => $type ) );
			$data ['rules'] = $form->rules ();
			return view ( 'catelog/form.tpl', $data );
		} else {
			Response::showErrorMsg ( '未知分类类型', 403 );
		}
	}
	public function edit($id) {
		$id = intval ( $id );
		if (! $id) {
			Response::showErrorMsg ( '非法的分类编号.', 403 );
		}
		$catelog = dbselect ( '*' )->from ( '{cms_catelog}' )->where ( array ('id' => $id ) );
		if ($catelog [0]) {
			$data = $catelog [0];
			$catelogTypes = apply_filter ( 'get_cms_catalog_types', array () );
			$data ['catelogType'] = $data ['type'];
			$data ['catelogTitle'] = $catelogTypes [$data ['type']] ['name'];
			$data ['options'] = array ('0' => '无' );
			dbselect ( '*' )->from ( '{cms_catelog}' )->treeWhere ( array ('type' => $data ['type'] ) )->treeOption ( $data ['options'], 'id', 'upid', 'name', $id );
			$form = new CatelogForm ( $data );
			$data ['rules'] = $form->rules ();
			return view ( 'catelog/form.tpl', $data );
		} else {
			Response::showErrorMsg ( '分类不存在.', 404 );
		}
	}
	public function del($id) {
		$id = intval ( $id );
		if ($id) {
			if (dbselect ( 'id' )->from ( '{cms_catelog}' )->where ( array ('upid' => $id ) )->count ( 'id' ) == 0) {
				$data ['deleted'] = 1;
				$data ['update_time'] = time ();
				$data ['update_uid'] = $this->user->getUid ();
				dbupdate ( '{cms_catelog}' )->set ( $data )->where ( array ('id' => $id ) )->exec ();
				$recycle = new DefaultRecycle ( $id, 'Catalog', 'cms_catelog', 'ID:{id};分类:{name},类型:{type}' );
				RecycleHelper::recycle ( $recycle );
				return NuiAjaxView::ok ( '分类已经放入回收站.', 'click', '#refresh' );
			} else {
				Response::showErrorMsg ( '请先删除此分类的所有子类，然后再删除本该分类.', 403 );
			}
		} else {
			Response::showErrorMsg ( '分类不存在.', 404 );
		}
	}
	public function save($type = 'chunk') {
		$form = new CatelogForm ();
		$catelog = $form->valid ();
		if ($catelog) {
			if (empty ( $catelog ['upid'] )) {
				$catelog ['upid'] = 0;
			}
			$time = time ();
			$uid = $this->user->getUid ();
			if (empty ( $catelog ['id'] )) {
				unset ( $catelog ['id'] );
				$catelog ['create_time'] = $time;
				$catelog ['update_time'] = $time;
				$catelog ['create_uid'] = $uid;
				$catelog ['update_uid'] = $uid;
				$rst = dbinsert ( $catelog )->into ( '{cms_catelog}' )->exec ();
			} else {
				$id = $catelog ['id'];
				unset ( $catelog ['id'] );
				$catelog ['update_time'] = $time;
				$catelog ['update_uid'] = $uid;
				$rst = dbupdate ( '{cms_catelog}' )->set ( $catelog )->where ( array ('id' => $id ) )->exec ();
			}
			if ($rst) {
				return NuiAjaxView::ok ( '成功保存分类', 'click', '#refresh' );
			} else {
				return NuiAjaxView::error ( '保存分类出错啦:' . DatabaseDialect::$lastErrorMassge );
			}
		} else {
			return NuiAjaxView::validate ( 'CatelogForm', '表单验证出错', $form->getErrors () );
		}
	}
}