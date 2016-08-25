<?php
/**
 * 内容模型管理.
 *
 * @author Guangfeng
 */
class ModelController extends Controller {
	protected $checkUser = true;
	protected $acls = array ('data' => 'r:cms/model','index' => 'r:cms/model','add' => 'c:cms/model','edit' => 'u:cms/model','save' => 'id|u:cms/model;c:cms/model','del' => 'd:cms/model' );
	/**
	 * 内容模型列表页.
	 */
	public function index() {
		return view ( 'model/index.tpl', $data );
	}
	
	/**
	 * 新增.
	 *
	 * @param int $upid
	 *        	上级用户组.
	 * @return string
	 */
	public function add($upid = 0) {
		$data ['models'] = array ('0' => '顶级模型' );
		dbselect ()->from ( '{cms_model}' )->treeWhere ( array ('deleted' => 0,'hidden' => 0 ) )->treeOption ( $data ['models'], 'id', 'upid', 'name' );
		$form = new ModelForm ( array ('id' => 0 ) );
		$data ['rules'] = $form->rules ();
		$data ['status'] = 1;
		$data ['groups'] = apply_filter ( 'get_model_link_groups', array ('' => '不分组','page' => '文章','topic' => '专题' ) );
		return view ( 'model/form.tpl', $data );
	}
	/**
	 * 编辑.
	 *
	 * @param int $id        	
	 */
	public function edit($id) {
		$id = intval ( $id );
		$model = dbselect ( '*' )->from ( '{cms_model}' )->where ( array ('id' => $id ) )->get ( 0 );
		if (empty ( $model )) {
			return NuiAjaxView::error ( '内容模型不存在.' );
		} else {
			$model ['models'] = array ('0' => '顶级模型' );
			dbselect ()->from ( '{cms_model}' )->treeWhere ( array ('deleted' => 0,'hidden' => 0 ) )->treeOption ( $model ['models'], 'id', 'upid', 'name', $model ['id'] );
			$form = new ModelForm ( $model );
			$model ['rules'] = $form->rules ();
			$model ['groups'] = apply_filter ( 'get_model_link_groups', array ('' => '不分组','page' => '文章','topic' => '专题' ) );
			return view ( 'model/form.tpl', $model );
		}
	}
	public function del($id) {
		$id = intval ( $id );
		if (! empty ( $id )) {
			$data ['deleted'] = 1;
			$data ['update_time'] = time ();
			$data ['update_uid'] = $this->user->getUid ();
			if (dbupdate ( '{cms_model}' )->set ( $data )->where ( array ('id' => $id ) )->exec ()) {
				$recycle = new DefaultRecycle ( $id, 'Model', 'cms_model', 'ID:{id};模型:{name}' );
				RecycleHelper::recycle ( $recycle );
				return NuiAjaxView::ok ( '#refresh', '内容模型已删除' );
			} else {
				return NuiAjaxView::error ( '数据库操作失败.' );
			}
		} else {
			Response::showErrorMsg ( '错误的编号', 500 );
		}
	}
	/**
	 * 保存内容模型信息.
	 */
	public function save() {
		$form = new ModelForm ();
		$model = $form->valid ();
		if ($model) {
			$time = time ();
			$uid = $this->user->getUid ();
			$model ['update_time'] = $time;
			$model ['update_uid'] = $uid;
			if ($model ['status'] == 'on') {
				$model ['status'] = 1;
			} else {
				$model ['status'] = 0;
			}
			if (empty ( $model ['upid'] )) {
				$model ['upid'] = 0;
			}
			$id = $model ['id'];
			unset ( $model ['id'] );
			if ($model ['is_topic_model'] && empty ( $model ['template'] )) {
				$model ['template'] = 'topic_form.tpl';
			} else if (empty ( $model ['template'] )) {
				$model ['template'] = 'default_form.tpl';
			}
			if (empty ( $model ['search_page_limit'] )) {
				unset ( $model ['search_page_limit'] );
			}
			if (empty ( $id )) {
				$model ['create_time'] = $time;
				$model ['create_uid'] = $uid;
				$rst = dbinsert ( $model )->into ( '{cms_model}' )->exec ();
			} else {
				$rst = dbupdate ( '{cms_model}' )->set ( $model )->where ( array ('id' => $id ) )->exec ();
			}
			if ($rst) {
				return NuiAjaxView::click ( '#btn2model', '保存完成' );
			} else {
				return NuiAjaxView::error ( '保存内容模型出错啦:' . DatabaseDialect::$lastErrorMassge );
			}
		} else {
			return NuiAjaxView::validate ( 'ModelForm', '保存内容模型出错啦,数据校验失败!', $form->getErrors () );
		}
	}
	public function data($_tid = '', $_cp = 1, $_lt = 10, $_sf = 'id', $_od = 'a', $_ct = 0) {
		$data ['canDeleteModel'] = icando ( 'd:cms/model' );
		$data ['groups'] = apply_filter ( 'get_model_link_groups', array ('' => '不分组','page' => '文章','topic' => '专题' ) );
		
		$where = array ('deleted' => 0,'hidden' => 0 );
		$type = irqst ( 'type', - 1 );
		if ($type >= 0) {
			$where ['is_topic_model'] = $type;
		}
		$keywords = rqst ( 'keywords' );
		if ($keywords) {
			$v = "%{$keywords}%";
			$where [] = array ('name LIKE' => $v,'||refid LIKE' => $v );
			$data ['search'] = 'true';
		} else {
			$where ['upid'] = $_tid;
			$data ['search'] = false;
		}
		$items = dbselect ( '*' )->from ( '{cms_model} AS CM' )->where ( $where );
		
		if (! $data ['search']) {
			$cnt = dbselect ( imv ( 'COUNT(CM1.id)' ) )->from ( '{cms_model} AS CM1' )->where ( array ('CM1.upid' => imv ( 'CM.id' ) ) );
			$items->field ( $cnt, 'child_cnt' );
		}
		$items->where ( $where )->sort ( $_sf, $_od )->limit ( ($_cp - 1) * $_lt, $_lt );
		;
		$data ['models'] = $items;
		$total = '';
		if ($_ct) {
			$total = $items->count ( 'CM.id' );
		}
		$data ['total'] = $total;
		
		return view ( 'model/data.tpl', $data );
	}
}