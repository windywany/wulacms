<?php
/**
 * 内容模型自定义字段.
 *
 * @author Guangfeng
 */
class ModelfieldController extends Controller {
	protected $checkUser = true;
	protected $acls = array ('data' => 'r:cms/model','index' => 'r:cms/model','add' => 'c:cms/model','edit' => 'u:cms/model','save' => 'id|u:cms/model;c:cms/model','del' => 'd:cms/model' );
	/**
	 *
	 * @param string $model        	
	 */
	public function index($model) {
		$model = dbselect ( 'refid,name' )->from ( '{cms_model}' )->where ( array ('deleted' => 0,'refid' => $model ) )->get ( 0 );
		if ($model) {
			$widgets = new CustomeFieldWidgetRegister ();
			$data = array ();
			$data ['canDelModel'] = icando ( 'd:cms/model' );
			$data ['canAddModel'] = icando ( 'c:cms/model' );
			$data ['model'] = $model ['refid'];
			$data ['modelName'] = $model ['name'];
			$data ['widgets'] = $widgets;
			
			$data ['items'] = dbselect ( '*' )->from ( '{cms_model_field}' )->where ( array ('deleted' => 0,'model' => $model ['refid'] ) )->asc ( 'sort' );
			return view ( 'model/field_index.tpl', $data );
		}
		
		Response::showErrorMsg ( '未知的内容模型.', 404 );
	}
	public function add($model) {
		$model = dbselect ( 'refid,name' )->from ( '{cms_model}' )->where ( array ('deleted' => 0,'refid' => $model ) )->get ( 0 );
		if ($model) {
			$widgets = new CustomeFieldWidgetRegister ();
			$data = array ();
			$data ['canAddModel'] = icando ( 'c:cms/model' );
			$data ['model'] = $model ['refid'];
			$data ['modelName'] = $model ['name'];
			$data ['widgets'] = $widgets;
			$data ['type'] = 'text';
			$data ['data_types'] = array('text'=>'文本','int'=>'数字');
			$widget = $widgets->getWidget ( 'text' );
			if ($widget) {
				$providor = $widget->getDataProvidor ( '' );
				$data ['defaultFormat'] = $providor->getOptionsFormat ();
			}
			$form = new ModelFieldForm ( array ('id' => 0,'model' => $data ['model'] ) );
			$data ['rules'] = $form->rules ();
			return view ( 'model/field_form.tpl', $data );
		}
		Response::showErrorMsg ( '未知的内容模型.', 404 );
	}
	public function edit($id) {
		$id = intval ( $id );
		if (! empty ( $id ) && ($data = dbselect ( '*' )->from ( '{cms_model_field}' )->where ( array ('id' => $id ) )->get ( 0 )) != null) {
			$model = dbselect ( 'name' )->from ( '{cms_model}' )->where ( array ('deleted' => 0,'refid' => $data ['model'] ) )->get ( 0 );
			$widgets = new CustomeFieldWidgetRegister ();
			$data ['canAddModel'] = icando ( 'c:cms/block' );
			$data ['modelName'] = $model ['name'];
			$data ['widgets'] = $widgets;
			$widget = $widgets->getWidget ( $data ['type'] );
			if ($widget) {
				$providor = $widget->getDataProvidor ( $data ['defaults'] );
				$data ['defaultFormat'] = $providor->getOptionsFormat ();
			}
			$data ['data_types'] = array('text'=>'文本','int'=>'数字');
			$form = new ModelFieldForm ( $data );
			$data ['rules'] = $form->rules ();
			return view ( 'model/field_form.tpl', $data );
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
			if (dbupdate ( '{cms_model_field}' )->set ( $data )->where ( array ('id IN' => $ids ) )->exec ()) {
				$recycle = new DefaultRecycle ( $ids, 'Model Field', 'cms_model_field', 'ID:{id};字段名:{label};字段:{name};模型:{model}' );
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
		$form = new ModelFieldForm ();
		$field = $form->valid ();
		if ($field) {
			if (empty ( $field ['model'] )) {
				return NuiAjaxView::error ( '未指定内容模型.' );
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
			if ($field ['searchable']) {
				$field ['searchable'] = 1;
			} else {
				$field ['searchable'] = 0;
			}
			if ($field ['cstore']) {
				$field ['cstore'] = 1;
			} else {
				$field ['cstore'] = 0;
			}
			if (empty ( $id )) {
				$field ['create_time'] = $time;
				$field ['create_uid'] = $uid;
				$rst = dbinsert ( $field )->into ( '{cms_model_field}' )->exec ();
			} else {
				$rst = dbupdate ( '{cms_model_field}' )->set ( $field )->where ( array ('id' => $id ) )->exec ();
			}
			
			if ($rst) {
				return NuiAjaxView::click ( '#gobackmodel', '保存成功' );
			} else {
				return NuiAjaxView::error ( '保存出错啦:' . DatabaseDialect::$lastErrorMassge );
			}
		} else {
			return NuiAjaxView::validate ( 'ModelFieldForm', '数据格式不正确，请重新填写.', $form->getErrors () );
		}
	}
}