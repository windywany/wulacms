<?php
class PageviewController extends Controller {
	protected $checkUser = true;
	protected $acls = array ('*' => 'pv:mobi' );
	public function index() {
		$data = array ();
		$data ['canDelPv'] = $data ['canAddPv'] = icando ( 'ch:mobi' );
		return view ( 'pageview/index.tpl', $data );
	}
	public function add() {
		$data = array ();
		$form = new MobiPageViewForm ();
		$data ['rules'] = $form->rules ();
		$data ['formName'] = get_class ( $form );
		$data ['widgets'] = new DefaultFormRender ( $form->buildWidgets ( $data ) );
		return view ( 'pageview/form.tpl', $data );
	}
	public function edit($id) {
		$id = intval ( $id );
		if (empty ( $id )) {
			Response::showErrorMsg ( '未指定要编辑的模板编号。' );
		}
		$channel = dbselect ( '*' )->from ( '{mobi_page_view}' )->where ( array ('id' => $id ) )->get ( 0 );
		if (empty ( $channel )) {
			Response::showErrorMsg ( '未找到要编辑的模板编号。' );
		}
		$data = $channel;
		if ($data ['models']) {
			$data ['models'] = explode ( ',', trim ( $data ['models'], ',' ) );
		}
		$form = new MobiPageViewForm ( $data );
		$data ['rules'] = $form->rules ();
		
		$data ['formName'] = get_class ( $form );
		$data ['widgets'] = new DefaultFormRender ( $form->buildWidgets ( $data ) );
		
		return view ( 'pageview/form.tpl', $data );
	}
	public function del($ids) {
		$ids = safe_ids2 ( $ids );
		if (empty ( $ids )) {
			return NuiAjaxView::error ( '请选择模板.' );
		}
		if (dbupdate ( '{mobi_page_view}' )->set ( array ('deleted' => 1 ) )->where ( array ('id IN' => $ids ) )->exec ()) {
			$recycle = new DefaultRecycle ( $ids, 'MobiPageView', 'mobi_page_view', '({id}){name}' );
			RecycleHelper::recycle ( $recycle );
			return NuiAjaxView::reload ( '#mobi-pv-table', '所选模板已放入回收站.' );
		} else {
			return NuiAjaxView::error ( '数据库操作失败.' );
		}
	}
	public function save() {
		$form = new MobiPageViewForm ();
		$pageview = $form->valid ();
		if ($pageview) {
			$time = time ();
			$uid = $this->user->getUid ();
			$pageview ['update_time'] = $time;
			$pageview ['update_uid'] = $uid;
			$id = $pageview ['id'];
			$models = $pageview ['models'];
			if ($models) {
				$pageview ['models'] = ',' . implode ( ',', $models ) . ',';
			} else {
				$pageview ['models'] = '';
			}
			unset ( $pageview ['id'] );
			if (empty ( $id )) {
				$pageview ['create_time'] = $time;
				$pageview ['create_uid'] = $uid;
				$rst = dbinsert ( $pageview )->into ( '{mobi_page_view}' )->exec ();
			} else {
				$rst = dbupdate ( '{mobi_page_view}' )->set ( $pageview )->where ( array ('id' => $id ) )->exec ();
			}
			if ($rst) {
				if (empty ( $id )) {
					$id = $rst [0];
				}
				return NuiAjaxView::click ( '#rtn2ads', '模板已经保存.' );
			} else {
				return NuiAjaxView::error ( '保存模板出错啦:' . DatabaseDialect::$lastErrorMassge );
			}
		} else {
			return NuiAjaxView::validate ( get_class ( $form ), '表单数据格式有误', $form->getErrors () );
		}
	}
	public function data($_cp = 1, $_lt = 20, $_sf = 'Pv.id', $_od = 'd', $_ct = 0) {
		$rows = dbselect ( 'Pv.*' )->from ( '{mobi_page_view} AS Pv' );
		// 排序
		$rows->sort ( $_sf, $_od );
		// 分页
		$rows->limit ( ($_cp - 1) * $_lt, $_lt );
		$where = Condition::where ( array ('Pv.name','LIKE','keywords' ), array ('Pv.refid','LIEKE','refid' ) );
		$where ['Pv.deleted'] = 0;
		$rows->where ( $where );
		// 总数
		$total = '';
		if ($_ct) {
			$total = $rows->count ( 'Pv.id' );
		}
		$data ['total'] = $total;
		foreach ( $rows as $row ) {
			if ($row ['models']) {
				$models = explode ( ',', trim ( $row ['models'], ',' ) );
				$models = dbselect ( 'name' )->from ( '{cms_model}' )->where ( array ('refid IN' => $models ) )->toArray ( 'name' );
				$row ['models'] = implode ( ',', $models );
			}
			$data ['rows'] [] = $row;
		}
		
		$data ['canEditPV'] = icando ( 'ch:mobi' );
		$data ['canDelPV'] = icando ( 'ch:mobi' );
		return view ( 'pageview/data.tpl', $data );
	}
}
