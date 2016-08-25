<?php
/**
 * 可接入程序管理.
 * 
 * @author Guangfeng
 */
class AppController extends Controller {
	protected $checkUser = true;
	protected $acls = array ('data' => 'rest:system','index' => 'rest:system','add' => 'rest:system','edit' => 'rest:system','save' => 'rest:system','del' => 'rest:system' );
	public function preRun($method) {
		parent::preRun ( $method );
		if (bcfg ( 'connect_server@rest' )) {
			Response::respond ( 403 );
		}
	}
	public function index() {
		$data ['canAddApp'] = icando ( 'rest:system' );
		$data ['canDelApp'] = icando ( 'rest:system' );
		return view ( 'index.tpl', $data );
	}
	public function add() {
		$form = new RestAppForm ( array ('id' => 0 ) );
		$data ['rules'] = $form->rules ();
		$data ['appkey'] = uniqid ();
		$data ['appsecret'] = uniqid ( '', true );
		return view ( 'form.tpl', $data );
	}
	public function edit($id) {
		$id = intval ( $id );
		$app = dbselect ( '*' )->from ( '{rest_apps}' )->where ( array ('id' => $id ) )->get ( 0 );
		if (empty ( $app )) {
			return NuiAjaxView::error ( '接入应用不存在.' );
		} else {
			$form = new RestAppForm ( $app );
			$app ['rules'] = $form->rules ();
			return view ( 'form.tpl', $app );
		}
	}
	public function del($id) {
		$id = intval ( $id );
		if (! empty ( $id )) {
			$where = array ('id' => $id );
			$app = dbselect ( 'appkey' )->from ( '{rest_apps}' )->where ( $where )->get ( 'name' );
			if ($app) {
				if (dbdelete ()->from ( 'rest_apps' )->where ( $where )->exec ()) {
					ActivityLog::info ( __ ( 'Delete Rest App "%s" successfully.', $app ), 'Rest App' );
					return NuiAjaxView::ok ( '应用程序已删除', 'click', '#refresh' );
				} else {
					ActivityLog::info ( __ ( 'Delete Rest App "%s" failed.', $app ), 'Rest App' );
					return NuiAjaxView::error ( '数据库操作失败.' );
				}
			} else {
				return NuiAjaxView::error ( '应用程序不存在,无法删除.' );
			}
		} else {
			Response::showErrorMsg ( '错误的编号', 500 );
		}
	}
	public function save() {
		$form = new RestAppForm ();
		$app = $form->valid ();
		if ($app) {
			$time = time ();
			$uid = $this->user->getUid ();
			$app ['update_time'] = $time;
			$app ['update_uid'] = $uid;
			$id = $app ['id'];
			unset ( $app ['id'] );
			if (empty ( $id )) {
				$app ['create_time'] = $time;
				$app ['create_uid'] = $uid;
				$rst = dbinsert ( $app )->into ( '{rest_apps}' )->exec ();
				ActivityLog::info ( __ ( 'Add new Rest App "%s"', $app ['appkey'] ), 'Rest App' );
			} else {
				$rst = dbupdate ( '{rest_apps}' )->set ( $app )->where ( array ('id' => $id ) )->exec ();
				ActivityLog::info ( __ ( 'Update Rest App "%s"', $app ['appkey'] ), 'Rest App' );
			}
			if ($rst) {
				return NuiAjaxView::ok ( '成功保存应用程序', 'click', '#refresh' );
			} else {
				return NuiAjaxView::error ( '保存应用程序出错啦:' . DatabaseDialect::$lastErrorMassge );
			}
		} else {
			return NuiAjaxView::validate ( 'RestAppForm', '保存应用程序出错,数据校验失败!',$form->getErrors() );
		}
	}
	public function data($_cp = 1, $_lt = 20, $_sf = 'id', $_od = 'd', $_ct = 0) {
		$apps = dbselect ( '*' )->from ( '{rest_apps}' );
		// 排序
		$apps->sort ( $_sf, $_od );
		// 分页
		$apps->limit ( ($_cp - 1) * $_lt, $_lt );
		// 条件
		$where = Condition::where ( array ('name','LIKE' ), array ('appkey','LIKE' ), array ('appsecret','LIKE' ) );
		$apps->where ( $where );
		// 总数
		$total = '';
		if ($_ct) {
			$total = $apps->count ( 'id' );
		}
		$data ['total'] = $total;
		$data ['rows'] = $apps;
		$data ['canEditApp'] = icando ( 'rest:system' );
		$data ['canDelApp'] = icando ( 'rest:system' );
		return view ( 'data.tpl', $data );
	}
}