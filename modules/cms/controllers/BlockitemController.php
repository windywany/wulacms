<?php
/**
 * 区块内容.
 * 
 * @author Guangfeng
 */
class BlockitemController extends Controller {
	protected $checkUser = true;
	protected $acls = array ('data' => 'r:cms/block','index' => 'r:cms/block','add' => 'c:cms/block','edit' => 'u:cms/block','save' => 'id|u:cms/block;c:cms/block','del' => 'd:cms/block' );
	public function index($id) {
		$data = array ();
		$data ['canDelBlock'] = icando ( 'd:cms/block' );
		$data ['canAddBlock'] = icando ( 'c:cms/block' );
		$data ['block'] = $id;
		$block = dbselect ( 'name' )->from ( '{cms_block}' )->where ( array ('id' => $id ) )->get ( 0 );
		if ($block) {
			$data ['blockName'] = $block ['name'];
			return view ( 'block/items_index.tpl', $data );
		} else {
			Response::showErrorMsg ( '区块不存在', 404 );
		}
	}
	public function add($id) {
		$data = array ();
		$data ['block'] = $id;
		$block = dbselect ( 'name,refid' )->from ( '{cms_block}' )->where ( array ('id' => $id ) )->get ( 0 );
		if ($block) {
			$data ['blockName'] = $block ['name'];
			$form = new BlockItemForm ( array ('id' => 0 ) );
			$widgets = BlockFieldForm::loadCustomerFields ( $form, $block ['refid'] );
			if ($widgets) {
				$data ['widgets'] = new DefaultFormRender ( AbstractForm::prepareWidgets ( CustomeFieldWidgetRegister::initWidgets ( $widgets ) ) );
			}
			$data ['refid'] = $block ['refid'];
			$data ['rules'] = $form->rules ();
			$data ['sort'] = 999;
			return view ( 'block/items_form.tpl', $data );
		} else {
			Response::showErrorMsg ( '区块不存在', 404 );
		}
	}
	public function edit($id) {
		$id = intval ( $id );
		if (! $id) {
			Response::showErrorMsg ( '非法的编号.', 403 );
		}
		$block = dbselect ( '*' )->from ( '{cms_block_items}' )->where ( array ('id' => $id ) );
		if ($block [0]) {
			$data = $block [0];
			$block = dbselect ( 'name,refid' )->from ( '{cms_block}' )->where ( array ('id' => $data ['block'] ) )->get ( 0 );
			$data ['blockName'] = $block ['name'];
			$data ['refid'] = $block ['refid'];
			
			$form = new BlockItemForm ( $data );
			$widgets = BlockFieldForm::loadCustomerFields ( $form, $block ['refid'] );
			
			if ($widgets) {
				$cvalues = @json_decode ( $data ['cvalue'], true );
				if (! $cvalues) {
					$cvalues = array ();
				}
				foreach ( $cvalues as $cf => $cv ) {
					if (isset ( $widgets [$cf] )) {
						$cvalues [$cf] = apply_filter ( 'parse_' . $widgets [$cf] ['type'] . '_field_value', $cv );
					} else {
						$cvalues [$cf] = $cv;
					}
				}
				$data ['widgets'] = new DefaultFormRender ( AbstractForm::prepareWidgets ( CustomeFieldWidgetRegister::initWidgets ( $widgets, $cvalues ) ) );
			}
			$data ['rules'] = $form->rules ();
			return view ( 'block/items_form.tpl', $data );
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
			if (dbupdate ( '{cms_block_items}' )->set ( $data )->where ( array ('id IN' => $ids ) )->exec ()) {
				$recycle = new DefaultRecycle ( $ids, 'Block Item', 'cms_block_items', 'ID:{id};内容:{title}' );
				RecycleHelper::recycle ( $recycle );
				return NuiAjaxView::reload ( '#blockitem-table', '已删除.' );
			} else {
				return NuiAjaxView::error ( '数据库操作失败.' );
			}
		} else {
			Response::showErrorMsg ( '错误的编号', 500 );
		}
	}
	/**
	 * 排序.
	 *
	 * @param int $id        	
	 * @param int $sort        	
	 * @return NuiAjaxView
	 */
	public function csort($id, $sort) {
		$id = intval ( $id );
		$sort = intval ( $sort );
		if (! empty ( $id )) {
			dbupdate ( '{cms_block_items}' )->set ( array ('sort' => $sort ) )->where ( array ('id' => $id ) )->exec ();
		}
		return NuiAjaxView::reload ( '#blockitem-table' );
	}
	public function save($refid) {
		$form = new BlockItemForm ();
		$widgets = BlockFieldForm::loadCustomerFields ( $form, $refid );
		$block = $form->valid ();
		if ($block) {
			$time = time ();
			$uid = $this->user->getUid ();
			$form = new BlockItemForm ();
			$block = $form->valid ();
			$block ['update_time'] = $time;
			$block ['update_uid'] = $uid;
			if (empty ( $block ['page_id'] )) {
				$block ['page_id'] = 0;
			}
			if (empty ( $block ['sort'] )) {
				$block ['sort'] = 999;
			}
			if (empty ( $block ['id'] )) {
				unset ( $block ['id'] );
				$block ['create_time'] = $time;
				$block ['create_uid'] = $uid;
				$rst = dbinsert ( $block )->into ( '{cms_block_items}' )->exec ();
				if ($rst && $rst [0]) {
					$rst = $rst [0];
				}
			} else {
				$id = $block ['id'];
				unset ( $block ['id'] );
				$rst = dbupdate ( '{cms_block_items}' )->set ( $block )->where ( array ('id' => $id ) )->exec ();
			}
			if ($rst) {
				if (isset ( $id )) {
					$block ['id'] = $id;
				} else {
					$block ['id'] = $rst;
				}
				$this->saveCustomField ( $block ['id'], $refid );
				fire ( 'after_block_item_saved', $block );
				return NuiAjaxView::click ( '#goback', '保存成功' );
			} else {
				return NuiAjaxView::error ( '保存出错啦:' . DatabaseDialect::$lastErrorMassge );
			}
		} else {
			return NuiAjaxView::validate ( 'BlockItemForm', '表单验证出错,请重新填写表单.', $form->getErrors () );
		}
	}
	public function data($block, $_cp = 1, $_lt = 20, $_sf = 'sort', $_od = 'a', $_ct = 0) {
		$data = array ();
		$data ['canEditBlock'] = icando ( 'u:cms/block' );
		$data ['canDelBlock'] = icando ( 'd:cms/block' );
		
		$rows = dbselect ( '*' )->from ( '{cms_block_items}' );
		$rows->sort ( 'sort', $_od );
		$rows->limit ( ($_cp - 1) * $_lt, $_lt );
		
		$where = array ('deleted' => 0,'block' => $block );
		
		$rows->where ( $where );
		$total = '';
		if ($_ct) {
			$total = $rows->count ( 'id' );
		}
		$data ['total'] = $total;
		$data ['rows'] = $rows;
		$data ['block'] = $block;
		return view ( 'block/items_data.tpl', $data );
	}
	private function saveCustomField($id, $block) {
		if (empty ( $id ) || empty ( $block )) {
			return;
		}
		$datas = array ();
		$id = intval ( $id );
		$fields = dbselect ( 'name,type,default_value AS `default`' )->from ( '{cms_block_field}' )->where ( array ('deleted' => 0,'block' => $block ) );
		
		foreach ( $fields as $field ) {
			$val = apply_filter ( 'alter_' . $field ['type'] . '_field_value', rqst ( $field ['name'], $field ['default'] ), $field ['name'] );
			$datas [$field ['name']] = is_array ( $val ) ? implode ( ',', $val ) : $val;
		}
		$cvalue = json_encode ( $datas );
		dbupdate ( '{cms_block_items}' )->set ( array ('cvalue' => $cvalue ) )->where ( array ('id' => $id ) )->exec ();
	}
}