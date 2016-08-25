<?php
class NoticeController extends Controller {
	protected $checkUser = true;
	protected $acls = array ('data' => 'r:system/notice','index' => 'r:system/notice','add' => 'c:system/notice','edit' => 'u:system/notice','save' => 'id|u:system/notice;c:system/notice','del' => 'd:system/notice' );
	/**
	 * 首页.
	 *
	 * @return SmartyView
	 */
	public function index() {
		$data = array ();
		$data ['canAddNotice'] = icando ( 'c:system/notice' );
		$data ['canDelNotice'] = icando ( 'd:system/notice' );
		return view ( 'notice/index.tpl', $data );
	}
	/**
	 * 新增用户.
	 *
	 * @return SmartyView
	 */
	public function add() {
		$data = array ();
		$form = new SystemNoticeForm ( array ('id' => 0 ) );
		$data ['rules'] = $form->rules ();
		$data ['roles'] = array ();
		$data ['expire_time'] = date ( 'Y-m-d', strtotime ( date ( 'Y-m-d H:00:00' ) . ' +1 month' ) );
		return view ( 'notice/form.tpl', $data );
	}
	/**
	 * 编辑用户.
	 *
	 * @param int $id        	
	 * @return NuiAjaxView SmartyView
	 */
	public function edit($id) {
		$id = intval ( $id );
		$notice = dbselect ( '*' )->from ( '{notification}' )->where ( array ('id' => $id ) )->get ( 0 );
		if (empty ( $notice )) {
			return NuiAjaxView::error ( '通知不存在.' );
		} else {
			$form = new SystemNoticeForm ( $notice );
			if ($notice ['expire_time']) {
				$notice ['expire_time'] = date ( 'Y-m-d', $notice ['expire_time'] );
			} else {
				$notice ['expire_time'] = date ( 'Y-m-d', strtotime ( date ( 'Y-m-d H:00:00' ) . ' +1 month' ) );
			}
			$notice ['rules'] = $form->rules ( true );
			return view ( 'notice/form.tpl', $notice );
		}
	}
	public function del($uids) {
		$uids = safe_ids ( $uids, ',', true );
		if (! empty ( $uids )) {
			$data ['deleted'] = 1;
			$data ['update_time'] = time ();
			$data ['update_uid'] = $this->user->getUid ();
			if (dbupdate ( '{notification}' )->set ( $data )->where ( array ('id IN' => $uids ) )->exec ()) {
				$recycle = new DefaultRecycle ( $uids, 'Notice', 'notification', '{title}' );
				RecycleHelper::recycle ( $recycle );
				fire ( 'on_delete_notice', $uids );
				ActivityLog::warn ( __ ( 'delete notices:%s', implode ( ',', $uids ) ), 'Notice' );
				return NuiAjaxView::ok ( '已删除', 'click', '#refresh' );
			} else {
				return NuiAjaxView::error ( '数据库操作失败.' );
			}
		} else {
			Response::showErrorMsg ( '错误的编号', 404 );
		}
	}
	/**
	 * 保存.
	 */
	public function save() {
		$form = new SystemNoticeForm ();
		$notice = $form->valid ();
		if ($notice) {
			$id = $notice ['id'];
			unset ( $notice ['id'] );
			
			if (empty ( $notice ['expire_time'] )) {
				$notice ['expire_time'] = strtotime ( date ( 'Y-m-d H:i:s' ) . ' +1 month' );
			} else {
				$notice ['expire_time'] = strtotime ( $notice ['expire_time'] . ' 23:59:59' );
			}
			$time = time ();
			$uid = $this->user->getUid ();
			if (empty ( $id )) {
				$notice ['create_time'] = $time;
				$notice ['update_time'] = $time;
				$notice ['create_uid'] = $uid;
				$notice ['update_uid'] = $uid;
				$rst = dbinsert ( $notice )->into ( '{notification}' )->exec ();
			} else {
				$notice ['update_time'] = $time;
				$notice ['update_uid'] = $uid;
				$rst = dbupdate ( '{notification}' )->set ( $notice )->where ( array ('id' => $id ) )->exec ();
			}
			if ($rst) {
				return NuiAjaxView::ok ( '成功保存通知', 'click', '#refresh' );
			} else {
				return NuiAjaxView::error ( '保存通知出错啦:' . DatabaseDialect::$lastErrorMassge );
			}
		} else {
			return NuiAjaxView::validate ( 'SystemNoticeForm', '保存通知出错啦,数据校验失败!', $form->getErrors () );
		}
	}
	/**
	 * 角色数据.
	 *
	 * @param int $_cp        	
	 * @param int $_lt        	
	 * @param string $_sf        	
	 * @param string $_od        	
	 * @param int $_ct        	
	 * @return SmartyView
	 */
	public function data($_cp = 1, $_lt = 20, $_sf = 'id', $_od = 'd', $_ct = 0) {
		$rows = dbselect ( 'U.nickname,NT.*' )->from ( '{notification} AS NT' )->limit ( ($_cp - 1) * $_lt, $_lt );
		$rows->join ( '{user} AS U', 'U.user_id = NT.create_uid' );
		$rows->sort ( $_sf, $_od );
		$where ['NT.deleted'] = 0;
		$ktype = rqst ( 'ktype', 'noticename' );
		$keyword = rqst ( 'keyword' );
		if ($keyword) {
			if ($ktype == 'id') {
				$where [$ktype] = intval ( $keyword );
			} else {
				$where [$ktype . ' LIKE'] = "%{$keyword}%";
			}
		}
		$rows->where ( $where );
		$total = '';
		if ($_ct) {
			$total = $rows->count ( 'NT.id' );
		}
		$data = array ('total' => $total,'rows' => $rows );
		$data ['canEditNotice'] = icando ( 'u:system/notice' );
		return view ( 'notice/data.tpl', $data );
	}
}
