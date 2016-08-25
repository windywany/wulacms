<?php
/**
 * 碎片管理.
 * 
 * @author Guangfeng
 */
class ChunkController extends Controller {
	protected $checkUser = true;
	protected $acls = array ('data' => 'r:cms/chunk','index' => 'r:cms/chunk','add' => 'c:cms/chunk','edit' => 'u:cms/chunk','save' => 'id|u:cms/chunk;c:cms/chunk','del' => 'd:cms/chunk','auto_chunk' => 'r:cms/chunk' );
	public function index() {
		$data = array ();
		$data ['canAddChunk'] = icando ( 'c:cms/chunk' );
		$data ['canDelChunk'] = icando ( 'd:cms/chunk' );
		$data ['options'] = array ('0' => '--请选择分类--' );
		dbselect ( '*' )->from ( '{cms_catelog}' )->treeWhere ( array ('type' => 'chunk' ) )->treeOption ( $data ['options'], 'id', 'upid', 'name' );
		return view ( 'chunk/index.tpl', $data );
	}
	public function auto_chunk($q = '', $_cp = 1) {
		$data ['more'] = false;
		$chunks = dbselect ( 'name as text,id' )->from ( '{cms_chunk}' )->limit ( 0, 30 );
		$where ['deleted'] = 0;
		if ($q) {
			$where ['keywords LIKE'] = '%' . $q . '%';
		}
		$chunks->where ( $where );
		$data ['results'] = $chunks->toArray ( array (array ('id' => 0,'text' => '-不绑定-' ) ) );
		return new JsonView ( $data );
	}
	public function add() {
		$data = array ();
		$form = new ChunkForm ();
		$data ['options'] = array ('' => '--请选择分类--' );
		dbselect ( '*' )->from ( '{cms_catelog}' )->treeWhere ( array ('type' => 'chunk' ) )->treeOption ( $data ['options'], 'id', 'upid', 'name' );
		$data ['rules'] = $form->rules ();
		return view ( 'chunk/form.tpl', $data );
	}
	public function edit($id) {
		$id = intval ( $id );
		if (! $id) {
			Response::showErrorMsg ( '非法的编号.', 403 );
		}
		$chunk = dbselect ( '*' )->from ( '{cms_chunk}' )->where ( array ('id' => $id ) );
		if ($chunk [0]) {
			$data = $chunk [0];
			$data ['options'] = array ();
			dbselect ( '*' )->from ( '{cms_catelog}' )->treeWhere ( array ('type' => 'chunk' ) )->treeOption ( $data ['options'], 'id', 'upid', 'name' );
			$form = new ChunkForm ( $data );
			$data ['rules'] = $form->rules ();
			return view ( 'chunk/form.tpl', $data );
		} else {
			Response::showErrorMsg ( '碎片不存在.', 404 );
		}
	}
	public function del($ids) {
		$ids = safe_ids ( $ids, ',', true );
		if (! empty ( $ids )) {
			$data ['deleted'] = 1;
			$data ['update_time'] = time ();
			$data ['update_uid'] = $this->user->getUid ();
			if (dbupdate ( '{cms_chunk}' )->set ( $data )->where ( array ('id IN' => $ids ) )->exec ()) {
				$recycle = new DefaultRecycle ( $ids, 'Chunk', 'cms_chunk', 'ID:{id};碎片名:{name};关键词:{keywords}' );
				RecycleHelper::recycle ( $recycle );
				return NuiAjaxView::ok ( '已删除', 'click', '#refresh' );
			} else {
				return NuiAjaxView::error ( '数据库操作失败.' );
			}
		} else {
			Response::showErrorMsg ( '错误的编号', 500 );
		}
	}
	public function save() {
		$form = new ChunkForm ();
		$chunk = $form->valid ();
		if ($chunk) {
			$time = time ();
			$uid = $this->user->getUid ();
			$chunk ['search_index'] = convert_search_keywords ( $chunk ['keywords'], true );
			if (empty ( $chunk ['id'] )) {
				unset ( $chunk ['id'] );
				$chunk ['create_time'] = $time;
				$chunk ['update_time'] = $time;
				$chunk ['create_uid'] = $uid;
				$chunk ['update_uid'] = $uid;
				$chunk ['deleted'] = 0;
				$rst = dbinsert ( $chunk )->into ( '{cms_chunk}' )->exec ();
			} else {
				$id = $chunk ['id'];
				unset ( $chunk ['id'] );
				$chunk ['update_time'] = $time;
				$chunk ['update_uid'] = $uid;
				$rst = dbupdate ( '{cms_chunk}' )->set ( $chunk )->where ( array ('id' => $id ) )->exec ();
			}
			if ($rst) {
				return NuiAjaxView::ok ( '保存成功', 'click', '#refresh' );
			} else {
				return NuiAjaxView::error ( '保存出错啦:' . DatabaseDialect::$lastErrorMassge );
			}
		} else {
			return NuiAjaxView::validate ( 'ChunkForm', '表单验证出错', $form->getErrors () );
		}
	}
	public function data($_cp = 1, $_lt = 20, $_sf = 'id', $_od = 'd', $_ct = 0) {
		$data = array ();
		$data ['canEditChunk'] = icando ( 'u:cms/chunk' );
		$data ['canDelChunk'] = icando ( 'd:cms/chunk' );
		
		$rows = dbselect ( 'CK.*,CL.name as catelogName' )->from ( '{cms_chunk} AS CK' )->join ( '{cms_catelog} AS CL', 'CK.catelog = CL.id' );
		$rows->sort ( $_sf, $_od );
		$rows->limit ( ($_cp - 1) * $_lt, $_lt );
		
		$keywords = rqst ( 'keywords' );
		$where = array ('CK.deleted' => 0 );
		if ($keywords) {
			$where ['keywords LIKE'] = "%{$keywords}%";
		}
		$catelog = irqst ( 'catelog' );
		if ($catelog) {
			$where ['catelog'] = $catelog;
		}
		$rows->where ( $where );
		$total = '';
		if ($_ct) {
			$total = $rows->count ( 'CK.id' );
		}
		$data ['total'] = $total;
		$data ['rows'] = $rows;
		return view ( 'chunk/data.tpl', $data );
	}
}