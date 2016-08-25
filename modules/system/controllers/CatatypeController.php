<?php
class CatatypeController extends \Controller {
	protected $checkUser = true;
	protected $acls = array ('index' => 'm:system/catalog','data' => 'm:system/catalog','add' => 'ct:system/catalog','edit' => 'ut:system/catalog','save' => 'id|ut:system/catalog;ct:system/catalog','del' => 'dt:system/catalog' );
	public function index() {
		$data ['canAdd'] = icando ( 'ct:system/catalog' );
		$data ['canEdit'] = icando ( 'ut:system/catalog' );
		$data ['canDel'] = icando ( 'dt:system/catalog' );
		return view ( 'catalog/type.tpl', $data );
	}
	public function add() {
		$form = new CatatypeForm ();
		$data ['widgets'] = new DefaultFormRender ( $form->buildWidgets () );
		$data ['rules'] = $form->rules ();
		return view ( 'catalog/typeform.tpl', $data );
	}
	public function edit($id) {
		$id = intval ( $id );
		if (! $id) {
			Response::showErrorMsg ( '非法的数据编号.', 403 );
		}
		$catalog = dbselect ( '*' )->from ( '{catalog_type}' )->where ( array ('id' => $id ) )->get ( 0 );
		if ($catalog) {
			$form = new CatatypeForm ( $catalog );
			$data ['rules'] = $form->rules ();
			$data ['widgets'] = new DefaultFormRender ( $form->buildWidgets ( $catalog ) );
			return view ( 'catalog/typeform.tpl', $data );
		} else {
			Response::showErrorMsg ( '数据不存在.', 404 );
		}
	}
	public function del($id) {
		$id = intval ( $id );
		if ($id) {
			$data ['deleted'] = 1;
			$data ['update_time'] = time ();
			$data ['update_uid'] = $this->user->getUid ();
			dbupdate ( '{catalog_type}' )->set ( $data )->where ( array ('id' => $id ) )->exec ();
			$recycle = new DefaultRecycle ( $id, 'System Catalog', 'catalog_type', 'ID:{id};数据:{name},类型:{type}' );
			RecycleHelper::recycle ( $recycle );
			return NuiAjaxView::ok ( '数据已经放入回收站.', 'click', '#refresh' );
		} else {
			Response::showErrorMsg ( '数据不存在.', 404 );
		}
	}
	public function save() {
		$form = new CatatypeForm ();
		$catalog = $form->valid ();
		if ($catalog) {
			$time = time ();
			$uid = $this->user->getUid ();
			if ($catalog ['is_enum']) {
				$catalog ['is_enum'] = 1;
			} else {
				$catalog ['is_enum'] = 0;
			}
			if (empty ( $catalog ['id'] )) {
				unset ( $catalog ['id'] );
				$catalog ['create_time'] = $time;
				$catalog ['update_time'] = $time;
				$catalog ['create_uid'] = $uid;
				$catalog ['update_uid'] = $uid;
				$rst = dbinsert ( $catalog )->into ( '{catalog_type}' )->exec ();
			} else {
				$id = $catalog ['id'];
				unset ( $catalog ['id'] );
				$catalog ['update_time'] = $time;
				$catalog ['update_uid'] = $uid;
				$rst = dbupdate ( '{catalog_type}' )->set ( $catalog )->where ( array ('id' => $id ) )->exec ();
			}
			if ($rst) {
				return NuiAjaxView::click ( '#rtn2catalog', '成功定义数据' );
			} else {
				return NuiAjaxView::error ( '定义数据出错啦:' . DatabaseDialect::$lastErrorMassge );
			}
		} else {
			return NuiAjaxView::validate ( 'CatatypeForm', '数据验证失败。', $form->getErrors () );
		}
	}
	public function data($keywords = '', $_cp = 1, $_lt = 20, $_sf = 'type', $_od = 'a', $_ct = 0) {
		$where ['deleted'] = 0;
		$keyword = rqst ( 'keyword' );
		if ($keywords) {
			$v = "%{$keywords}%";
			$where [] = array ('name LIKE' => $v,'||type LIKE' => $v );
			$data ['search'] = true;
		}
		$types = dbselect ( '*' )->from ( '{catalog_type}' )->where ( $where );
		$types->sort ( $_sf, $_od );
		$types->limit ( ($_cp - 1) * $_lt, $_lt );
		$total = '';
		if ($_ct) {
			$total = $types->count ( 'id' );
		}
		$data ['items'] = $types;
		$data ['total'] = $total;
		$data ['canAdd'] = icando ( 'ct:system/catalog' );
		$data ['canEdit'] = icando ( 'ut:system/catalog' );
		$data ['canDel'] = icando ( 'dt:system/catalog' );
		return view ( 'catalog/typedata.tpl', $data );
	}
}