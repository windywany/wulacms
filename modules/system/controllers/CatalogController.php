<?php
/**
 * 数据项管理.
 *
 * @author Guangfeng
 */
class CatalogController extends Controller {
	protected $checkUser = true;
	public function index($type = 'core') {
		if (! icando ( 'r:system/catalog/' . $type )) {
			Response::showErrorMsg ( '你无权进行此操作，请与管理员联系.', 403 );
		}
		$catalogTypes = apply_filter ( 'get_catalog_types', array () );
		if (isset ( $catalogTypes [$type] )) {
			$data ['catalogType'] = $type;
			$data ['catalogTitle'] = $catalogTypes [$type] ['name'];
			$data ['catalogName'] = $catalogTypes [$type] ['name'];
			$data ['hiddenID'] = isset ( $catalogTypes [$type] ['hiddenID'] ) ? $catalogTypes [$type] ['hiddenID'] : false;
			$data ['is_enum'] = isset ( $catalogTypes [$type] ['is_enum'] ) ? $catalogTypes [$type] ['is_enum'] : false;
			$data ['canDeleteCatalog'] = icando ( 'd:system/catalog/' . $type );
			$data ['canAddCatalog'] = icando ( 'c:system/catalog/' . $type );
			$data ['canEditCatalog'] = icando ( 'u:system/catalog/' . $type );
			$data ['head_col_tpl'] = apply_filter ( 'get_catalog_' . $type . '_head_tpl', '' );
			return view ( 'catalog/index.tpl', $data );
		} else {
			Response::showErrorMsg ( '数据项未定义', 403 );
		}
	}
	public function ms($type, $ss = '') {
		$data ['ss'] = $ss;
		$data ['type'] = $type;
		$catalogTypes = apply_filter ( 'get_catalog_types', array () );
		$data ['catalogTitle'] = $catalogTypes [$type] ['name'];
		return view ( 'catalog/browser.tpl', $data );
	}
	public function msdata($type, $_cp = 1, $_lt = 20, $_sf = 'CT.id', $_od = 'd', $_ct = 0) {
		$rows = dbselect ( 'CT.*' )->from ( '{catalog} AS CT' );
		$where ['CT.deleted'] = 0;
		$where ['CT.type'] = $type;
		$keywords = rqst ( 'keywords' );
		if ($keywords) {
			$t = '%' . $keywords . '%';
			$where [] = array ('CT.name LIKE' => $t,'||CT.alias LIKE' => $t );
		}
		$rows->where ( $where );
		$rows->sort ( $_sf, $_od );
		$rows->limit ( ($_cp - 1) * $_lt, $_lt );
		$data = array ();
		$data ['total'] = '';
		if ($_ct) {
			$data ['total'] = $rows->count ( 'CT.id' );
		}
		$data ['ss'] = rqst ( 'ss' );
		$data ['rows'] = $rows;
		return view ( 'catalog/browserdata.tpl', $data );
	}
	public function add($type = 'chunk', $upid = 0) {
		if (! icando ( 'c:system/catalog/' . $type )) {
			Response::showErrorMsg ( '你无权进行此操作，请与管理员联系.', 403 );
		}
		$catalogTypes = apply_filter ( 'get_catalog_types', array () );
		if (isset ( $catalogTypes [$type] )) {
			$data ['catalogType'] = $type;
			$data ['catalogTitle'] = $catalogTypes [$type] ['name'];
			$data ['is_enum'] = isset ( $catalogTypes [$type] ['is_enum'] ) ? $catalogTypes [$type] ['is_enum'] : false;
			$data ['upid'] = $upid;
			$data ['uptext'] = TreeViewWidget::getTreeValueText ( 'catalog', $data ['upid'] );
			$form = new CatalogForm ( array ('id' => 0 ) );
			$cform = $this->getCustomForm ( $type, $data );
			$data ['rules'] = $form->rules ( $cform );
			if ($cform) {
				$cdata = $cform->getInitData ();
				$data ['widgets'] = new DefaultFormRender ( $cform->buildWidgets ( $cdata ) );
			}
			return view ( 'catalog/form.tpl', $data );
		} else {
			Response::showErrorMsg ( '数据项未定义', 403 );
		}
	}
	public function edit($id) {
		$id = intval ( $id );
		if (! $id) {
			Response::showErrorMsg ( '数据项未定义.', 403 );
		}
		$catalog = dbselect ( '*' )->from ( '{catalog}' )->where ( array ('id' => $id ) );
		if ($catalog [0]) {
			$data = $catalog [0];
			if (! icando ( 'r:system/catalog/' . $data ['type'] )) {
				Response::showErrorMsg ( '你无权进行此操作，请与管理员联系.', 403 );
			}
			$catalogTypes = apply_filter ( 'get_catalog_types', array () );
			$data ['catalogType'] = $data ['type'];
			$data ['catalogTitle'] = $catalogTypes [$data ['type']] ['name'];
			$data ['is_enum'] = isset ( $catalogTypes [$data ['type']] ['is_enum'] ) ? $catalogTypes [$data ['type']] ['is_enum'] : false;
			$data ['uptext'] = TreeViewWidget::getTreeValueText ( 'catalog', $data ['upid'] );
			$form = new CatalogForm ( $data );
			$cform = $this->getCustomForm ( $data ['type'], $data );
			$data ['rules'] = $form->rules ( $cform );
			if ($cform) {
				$cdata = $cform->getInitData ();
				$data ['widgets'] = new DefaultFormRender ( $cform->buildWidgets ( $cdata ) );
			}
			return view ( 'catalog/form.tpl', $data );
		} else {
			Response::showErrorMsg ( '数据项未定义.', 404 );
		}
	}
	public function del($id) {
		$id = intval ( $id );
		if ($id) {
			$type = dbselect ()->from ( '{catalog}' )->where ( array ('id' => $id ) )->get ( 'type' );
			if (! $type || ! icando ( 'd:system/catalog/' . $type )) {
				Response::showErrorMsg ( '你无权进行此操作，请与管理员联系.', 403 );
			}
			if (dbselect ( 'id' )->from ( '{catalog}' )->where ( array ('upid' => $id ) )->count ( 'id' ) == 0) {
				$data ['deleted'] = 1;
				$data ['update_time'] = time ();
				$data ['update_uid'] = $this->user->getUid ();
				dbupdate ( '{catalog}' )->set ( $data )->where ( array ('id' => $id ) )->exec ();
				
				\TreeIterator::updateTreeNode ( '{catalog}', array ('deleted' => 0 ), 'id', 'upid', 'parents', 'sub' );
				
				$recycle = new DefaultRecycle ( $id, 'System Catalog', 'catalog', 'ID:{id};数据项:{name},类型:{type}' );
				RecycleHelper::recycle ( $recycle );
				return NuiAjaxView::ok ( '数据项已经放入回收站.', 'click', '#refresh' );
			} else {
				Response::showErrorMsg ( '请先删除此数据项值的所有子值，然后再删除本该数据项值.', 403 );
			}
		} else {
			Response::showErrorMsg ( '数据项值不存在.', 404 );
		}
	}
	public function save() {
		$form = new CatalogForm ();
		$catalog = $form->valid ();
		if ($catalog) {
			$type = $catalog ['type'];
			$id = $catalog ['id'];
			unset ( $catalog ['id'] );
			$cform = $this->getCustomForm ( $type, $catalog );
			if ($cform instanceof AbstractForm) {
				$cdata = $cform->valid ();
				if ($cdata === false) {
					return NuiAjaxView::validate ( 'CatalogForm', '数据验证失败。', $cform->getErrors () );
				}
			}
			if (empty ( $catalog ['upid'] )) {
				$catalog ['upid'] = 0;
			}
			$time = time ();
			$uid = $this->user->getUid ();
			if (empty ( $id )) {
				if (! icando ( 'c:system/catalog/' . $type )) {
					Response::showErrorMsg ( '你无权进行此操作，请与管理员联系.', 403 );
				}
				$catalog ['create_time'] = $time;
				$catalog ['update_time'] = $time;
				$catalog ['create_uid'] = $uid;
				$catalog ['update_uid'] = $uid;
				$rst = dbinsert ( $catalog )->into ( '{catalog}' )->exec ();
				if ($rst) {
					$id = $rst [0];
				}
			} else {
				if (! icando ( 'u:system/catalog/' . $type )) {
					Response::showErrorMsg ( '你无权进行此操作，请与管理员联系.', 403 );
				}
				$catalog ['update_time'] = $time;
				$catalog ['update_uid'] = $uid;
				$rst = dbupdate ( '{catalog}' )->set ( $catalog )->where ( array ('id' => $id ) )->exec ();
			}
			if ($rst) {
				\TreeIterator::updateTreeNode ( '{catalog}', array ('deleted' => 0,'type' => $type ), 'id', 'upid', 'parents', 'sub' );
				
				$provider = $providor = apply_filter ( 'get_catalog_' . $type . '_provider', null );
				if ($provider instanceof ICatalogProvider) {
					$provider->save ( $cdata, $id );
				} else {
					apply_filter ( 'after_catalog_' . $type . '_saved', $cdata, $id );
				}
				return NuiAjaxView::callback ( 'cataload_saved', array ('id' => $id ) );
			} else {
				return NuiAjaxView::error ( '定义数据项值出错啦:' . DatabaseDialect::$lastErrorMassge );
			}
		} else {
			return NuiAjaxView::validate ( 'CatalogForm', '数据验证失败。', $form->getErrors () );
		}
	}
	public function auto($type, $q = '', $_cp = 1) {
		if (! icando ( 'r:system/catalog/' . $type )) {
			Response::showErrorMsg ( '你无权进行此操作，请与管理员联系.', 403 );
		}
		$data ['more'] = false;
		$chunks = dbselect ( 'name as text,id' )->from ( '{catalog}' )->limit ( 0, 30 );
		$where ['deleted'] = 0;
		$where ['upid'] = 0;
		$where ['type'] = $type;
		if ($q) {
			$where ['keywords LIKE'] = '%' . $q . '%';
		}
		$chunks->where ( $where );
		$data ['results'] = $chunks->toArray ( array (array ('id' => 0,'text' => '-请选择-' ) ) );
		return new JsonView ( $data );
	}
	public function data($type, $_tid = '', $_cp = 1, $_lt = 20, $_sf = 'CT.id', $_od = 'd', $_ct = 0) {
		if (! icando ( 'r:system/catalog/' . $type )) {
			Response::showErrorMsg ( '你无权进行此操作，请与管理员联系.', 403 );
		}
		$catalogTypes = apply_filter ( 'get_catalog_types', array () );
		$data ['catalogTitle'] = $catalogTypes [$type] ['name'];
		$data ['is_enum'] = isset ( $catalogTypes [$type] ['is_enum'] ) ? $catalogTypes [$type] ['is_enum'] : false;
		$data ['catalogType'] = $type;
		$data ['hiddenID'] = isset ( $catalogTypes [$type] ['hiddenID'] ) ? $catalogTypes [$type] ['hiddenID'] : false;
		$data ['canDeleteCatalog'] = icando ( 'd:system/catalog/' . $type );
		$data ['canAddCatalog'] = icando ( 'c:system/catalog/' . $type );
		$data ['canEditCatalog'] = icando ( 'u:system/catalog/' . $type );
		$data ['total'] = '';
		
		$where = array ('CT.type' => $type,'CT.deleted' => 0,'CT.upid' => $_tid );
		
		$items = dbselect ( 'CT.*' )->from ( '{catalog} AS CT' );
		$items = apply_filter ( 'filter_catalog_' . $type . '_query', $items );
		$searched = false;
		
		$keywords = rqst ( 'keywords' );
		if ($keywords) {
			$t = '%' . $keywords . '%';
			$where ['CT.name LIKE'] = $t;
			$searched = true;
		}
		$alias = rqst ( 'alias' );
		if ($alias) {
			$t = '%' . $alias . '%';
			$where ['CT.alias LIKE'] = $t;
			$searched = true;
		}
		if (empty ( $_tid ) || $searched) {
			$items->limit ( ($_cp - 1) * $_lt, $_lt );
		}
		if ($searched) {
			unset ( $where ['CT.upid'] );
		}
		$items->sort ( $_sf, $_od );
		$items->where ( $where );
		
		if ($_ct && (empty ( $_tid ) || $searched)) {
			$data ['total'] = $items->count ( 'CT.id' );
		}
		$data ['disable_tree'] = $searched ? 'true' : '';
		
		if (empty ( $_tid )) {
			$data ['addingtip'] = true;
		}
		$list = $items->toArray ();
		foreach ( $list as $key => $val ) {
			$val = apply_filter ( 'filter_catalog_' . $type . '_foreach', $val );
			$list [$key] = $val;
		}
		$data ['items'] = $list;
		
		$data ['data_col_tpl'] = apply_filter ( 'get_catalog_' . $type . '_data_tpl', '' );
		return view ( 'catalog/data.tpl', $data );
	}
	private function getCustomForm($type, $data = array()) {
		$providor = apply_filter ( 'get_catalog_' . $type . '_provider', null );
		if ($providor instanceof ICatalogProvider) {
			$cform = $providor->getCustomForm ( null, $data );
		} else {
			$cform = apply_filter ( 'get_catalog_' . $type . '_form', null, $data );
		}
		return $cform;
	}
}