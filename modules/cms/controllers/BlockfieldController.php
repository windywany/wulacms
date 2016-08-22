<?php
/**
 * 区块自定义字段.
 *
 * @author Guangfeng
 */
class BlockfieldController extends Controller {
	protected $checkUser = true;
	protected $acls = array ('data' => 'r:cms/block','index' => 'r:cms/block','add' => 'c:cms/block','edit' => 'u:cms/block','save' => 'id|u:cms/block;c:cms/block','del' => 'd:cms/block' );
	/**
	 *
	 * @param string $model        	
	 */
	public function index($model) {
		$model = dbselect ( 'refid,name,id' )->from ( '{cms_block}' )->where ( array ('deleted' => 0,'refid' => $model ) )->get ( 0 );
		if ($model) {
			$widgets = new CustomeFieldWidgetRegister ();
			$data = array ();
			$data ['canDelModel'] = icando ( 'd:cms/block' );
			$data ['canAddModel'] = icando ( 'c:cms/block' );
			$data ['block'] = $model ['refid'];
			$data ['blockId'] = $model ['id'];
			$data ['blockName'] = $model ['name'];
			$data ['widgets'] = $widgets;
			
			$data ['items'] = dbselect ( '*' )->from ( '{cms_block_field}' )->where ( array ('deleted' => 0,'block' => $model ['refid'] ) )->asc ( 'sort' );
			return view ( 'block/field_index.tpl', $data );
		}
		
		Response::showErrorMsg ( '未知的内容模型.', 404 );
	}
	public function add($model) {
		$model = dbselect ( 'refid,name,id' )->from ( '{cms_block}' )->where ( array ('deleted' => 0,'refid' => $model ) )->get ( 0 );
		if ($model) {
			$widgets = new CustomeFieldWidgetRegister ();
			$data = array ();
			$data ['canAddModel'] = icando ( 'c:cms/block' );
			$data ['block'] = $model ['refid'];
			$data ['blockId'] = $model ['id'];
			$data ['blockName'] = $model ['name'];
			$data ['widgets'] = $widgets;
			$data ['type'] = 'text';
			$widget = $widgets->getWidget ( 'text' );
			if ($widget) {
				$providor = $widget->getDataProvidor ( '' );
				$data ['defaultFormat'] = $providor->getOptionsFormat ();
			}
			$form = new BlockFieldForm ( array ('id' => 0,'block' => $data ['block'] ) );
			$data ['rules'] = $form->rules ();
			return view ( 'block/field_form.tpl', $data );
		}
		Response::showErrorMsg ( '未知的内容模型.', 404 );
	}
	public function edit($id) {
		$id = intval ( $id );
		if (! empty ( $id ) && ($data = dbselect ( '*' )->from ( '{cms_block_field}' )->where ( array ('id' => $id ) )->get ( 0 )) != null) {
			$model = dbselect ( 'name' )->from ( '{cms_block}' )->where ( array ('deleted' => 0,'refid' => $data ['block'] ) )->get ( 0 );
			$widgets = new CustomeFieldWidgetRegister ();
			$data ['canAddModel'] = icando ( 'c:cms/block' );
			$data ['blockName'] = $model ['name'];
			$data ['blockId'] = $id;
			$data ['widgets'] = $widgets;
			$widget = $widgets->getWidget ( $data ['type'] );
			if ($widget) {
				$providor = $widget->getDataProvidor ( $data ['defaults'] );
				$data ['defaultFormat'] = $providor->getOptionsFormat ();
			}
			$form = new BlockFieldForm ( $data );
			$data ['rules'] = $form->rules ();
			return view ( 'block/field_form.tpl', $data );
		} else {
			Response::showErrorMsg ( '未知的自定义字段.', 404 );
		}
	}
	public function del($ids) {
		$ids = safe_ids ( $ids, ',', true );
		if (! empty ( $ids )) {
			$data ['deleted'] = 1;
			$data ['update_time'] = time ();
			$data ['update_uid'] = $this->user->getUid ();
			if (dbupdate ( '{cms_block_field}' )->set ( $data )->where ( array ('id IN' => $ids ) )->exec ()) {
				$recycle = new DefaultRecycle ( $ids, 'Block Field', 'cms_block_field', 'ID:{id};字段名:{label};字段:{name};区块:{block}' );
				RecycleHelper::recycle ( $recycle );
				return NuiAjaxView::click ( '#refresh', '字段已经删除.' );
			} else {
				return NuiAjaxView::error ( '数据库操作失败.' );
			}
		} else {
			Response::showErrorMsg ( '错误的编号', 500 );
		}
	}
	public function save() {
		$form = new BlockFieldForm ();
		$field = $form->valid ();
		if ($field) {
			if (empty ( $field ['block'] )) {
				return NuiAjaxView::error ( '未指定区块.' );
			}
			$time = time ();
			$uid = $this->user->getUid ();
			$field ['update_time'] = $time;
			$field ['update_uid'] = $uid;
			$field ['deleted'] = 0;
			$field ['group'] = intval ( $field ['group'] );
			$field ['col'] = intval ( $field ['col'] );
			$id = $field ['id'];
			unset ( $field ['id'] );
			if (empty ( $id )) {
				$field ['create_time'] = $time;
				$field ['create_uid'] = $uid;
				$rst = dbinsert ( $field )->into ( '{cms_block_field}' )->exec ();
			} else {
				$rst = dbupdate ( '{cms_block_field}' )->set ( $field )->where ( array ('id' => $id ) )->exec ();
			}
			if ($rst) {
				return NuiAjaxView::click ( '#gobackmodel', '保存成功' );
			} else {
				return NuiAjaxView::error ( '保存出错啦:' . DatabaseDialect::$lastErrorMassge );
			}
		} else {
			return NuiAjaxView::validate ( 'BlockFieldForm', '数据格式不正确，请重新填写.', $form->getErrors () );
		}
	}
}