<?php
/**
 * 区块.
 * 
 * @author Guangfeng
 */
class BlockController extends Controller {
	protected $acls = array ('data' => 'r:cms/block','index' => 'r:cms/block','add' => 'c:cms/block','edit' => 'u:cms/block','save' => 'id|u:cms/block;c:cms/block','del' => 'd:cms/block' );
	protected $checkUser = true;
	public function index() {
		$data = array ();
		$data ['canAddBlock'] = icando ( 'c:cms/block' );
		$data ['canDelBlock'] = icando ( 'd:cms/block' );
		$data ['options'] = array ('0' => '--请选择分类--' );
		
		dbselect ( '*' )->from ( '{cms_catelog}' )->treeWhere ( array ('type' => 'block' ) )->treeOption ( $data ['options'], 'id', 'upid', 'name' );
		
		return view ( 'block/index.tpl', $data );
	}
	public function add() {
		$data = array ();
		$form = new BlockForm ( array ('id' => 0 ) );
		$data ['options'] = array ('' => '--请选择分类--' );
		
		dbselect ( '*' )->from ( '{cms_catelog}' )->treeWhere ( array ('type' => 'block' ) )->treeOption ( $data ['options'], 'id', 'upid', 'name' );
		
		$data ['rules'] = $form->rules ();
		return view ( 'block/form.tpl', $data );
	}
	public function edit($id) {
		$id = intval ( $id );
		if (! $id) {
			Response::showErrorMsg ( '非法的编号.', 403 );
		}
		$block = dbselect ( '*' )->from ( '{cms_block}' )->where ( array ('id' => $id ) );
		if ($block [0]) {
			$data = $block [0];
			$data ['options'] = array ();
			dbselect ( '*' )->from ( '{cms_catelog}' )->treeWhere ( array ('type' => 'block' ) )->treeOption ( $data ['options'], 'id', 'upid', 'name' );
			$form = new BlockForm ( $data );
			$data ['rules'] = $form->rules ();
			return view ( 'block/form.tpl', $data );
		} else {
			Response::showErrorMsg ( '区块不存在.', 404 );
		}
	}
	public function del($ids) {
		$ids = safe_ids ( $ids, ',', true );
		if (! empty ( $ids )) {
			$data ['deleted'] = 1;
			$data ['update_time'] = time ();
			$data ['update_uid'] = $this->user->getUid ();
			if (dbupdate ( '{cms_block}' )->set ( $data )->where ( array ('id IN' => $ids ) )->exec ()) {
				$recycle = new DefaultRecycle ( $ids, 'Block', 'cms_block', 'ID:{id};区块名:{name};引用ID:{refid}' );
				RecycleHelper::recycle ( $recycle );
				return NuiAjaxView::reload ( '#block-table', '已删除' );
			} else {
				return NuiAjaxView::error ( '数据库操作失败.' );
			}
		} else {
			Response::showErrorMsg ( '错误的编号', 500 );
		}
	}
	public function save() {
		$form = new BlockForm ();
		$block = $form->valid ();
		if ($block) {
			$time = time ();
			$uid = $this->user->getUid ();
			$block ['update_time'] = $time;
			$block ['update_uid'] = $uid;
			$id = $block ['id'];
			unset ( $block ['id'] );
			if (empty ( $id )) {
				$block ['create_time'] = $time;
				$block ['create_uid'] = $uid;
				$rst = dbinsert ( $block )->into ( '{cms_block}' )->exec ();
			} else {
				$rst = dbupdate ( '{cms_block}' )->set ( $block )->where ( array ('id' => $id ) )->exec ();
			}
			if ($rst) {
				return NuiAjaxView::ok ( '保存成功', 'click', '#btn-rtn' );
			} else {
				return NuiAjaxView::error ( '保存出错啦:' . DatabaseDialect::$lastErrorMassge );
			}
		} else {
			return NuiAjaxView::validate ( 'BlockForm', '表单验证出错', $form->getErrors () );
		}
	}
	public function select_data($q = '', $_cp = 1, $p = 0) {
		$data ['more'] = false;
		$topics = dbselect ( 'CB.refid as id,CB.name as text' )->from ( '{cms_block} AS CB' );
		$topics->join ( '{cms_catelog} AS CC', 'CB.catelog = CC.id' );
		$where ['CB.deleted'] = 0;
		if ($p) {
			$where ['CC.id'] = $p;
		}
		$topics->where ( $where );
		
		$data ['results'] = $topics->toArray ( array (array ('id' => '','text' => '-请选择-' ) ) );
		return new JsonView ( $data );
	}
	public function data($_cp = 1, $_lt = 20, $_sf = 'id', $_od = 'd', $_ct = 0) {
		$data = array ();
		$data ['canEditBlock'] = icando ( 'u:cms/block' );
		$data ['canDelBlock'] = icando ( 'd:cms/block' );
		$data ['canAddBlock'] = icando ( 'c:cms/block' );
		$rows = dbselect ( 'CB.*,CL.name as catelogName' )->from ( '{cms_block} AS CB' )->join ( '{cms_catelog} AS CL', 'CB.catelog = CL.id' );
		$cnt = dbselect ( imv ( 'COUNT(CBI.id)' ) )->from ( '{cms_block_items} AS CBI' )->where ( array ('CBI.block' => imv ( 'CB.id' ),'CBI.deleted' => 0 ) );
		$rows->field ( $cnt, 'mcnt' );
		$rows->sort ( $_sf, $_od );
		$rows->limit ( ($_cp - 1) * $_lt, $_lt );
		
		$name = rqst ( 'name' );
		$where = array ('CB.deleted' => 0 );
		if ($name) {
			$where ['CB.name LIKE'] = "%{$name}%";
		}
		$refid = rqst ( 'refid' );
		if ($refid) {
			$where ['CB.refid LIKE'] = "%{$refid}%";
		}
		$catelog = irqst ( 'catelog' );
		if ($catelog) {
			$where ['catelog'] = $catelog;
		}
		$rows->where ( $where );
		$total = '';
		if ($_ct) {
			$total = $rows->count ( 'CB.id' );
		}
		$data ['total'] = $total;
		$data ['rows'] = $rows;
		return view ( 'block/data.tpl', $data );
	}
}