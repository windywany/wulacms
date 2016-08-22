<?php
/**
 * 系统日志.
 * 
 * @author Guangfeng
 */
class SyslogController extends Controller {
	protected $checkUser = true;
	protected $acls = array ('data' => 'r:system/log','index' => 'r:system/log','del' => 'd:system/log' );
	/**
	 * 首页.
	 *
	 * @return SmartyView
	 */
	public function index() {
		$data ['canDelLog'] = icando ( 'd:system/log' );
		$data ['types'] = apply_filter ( 'get_activity_log_type', array ('' => __ ( 'All' ) ) );
		return view ( 'syslog/index.tpl', $data );
	}
	/**
	 * 删除系统日志.
	 *
	 * @param string $id        	
	 * @return NuiAjaxView
	 */
	public function del($id) {
		$id = safe_ids ( $id, ',', true );
		$con = array ('id IN' => $id );
		$rst = dbdelete ()->from ( '{activity_log}' )->where ( $con )->exec ();
		if ($rst) {
			ActivityLog::warn ( __ ( 'Delete system activity.' ), 'Delete Activity' );
			return NuiAjaxView::ok ( '所选活动日志已经删除.', 'click', '#refresh' );
		} else {
			return NuiAjaxView::error ( '删除活动日志出错啦,数据库操作失败.' );
		}
	}
	/**
	 * 表格数据.
	 *
	 * @param number $_cp        	
	 * @param number $_lt        	
	 * @param string $_sf        	
	 * @param string $_od        	
	 * @param number $_ct        	
	 * @return SmartyView
	 */
	public function data($_cp = 1, $_lt = 20, $_sf = 'id', $_od = 'd', $_ct = 0, $bd = '', $sd = '') {
		$logs = dbselect ( 'L.*,U.nickname' )->from ( '{activity_log} AS L' );
		$logs->join ( '{user} AS U', 'L.user_id = U.user_id' );
		
		// 排序
		$logs->sort ( $_sf, $_od );
		// 分页
		$logs->limit ( ($_cp - 1) * $_lt, $_lt );
		// 条件
		$where = Condition::where ( array ('U.nickname','LIKE','user' ), 'activity', 'level' );
		if (! empty ( $bd )) {
			$where ['L.create_time >='] = strtotime ( $bd . ' 00:00:00' );
		}
		if (! empty ( $sd )) {
			$where ['L.create_time <='] = strtotime ( $sd . ' 23:59:59' );
		}
		$logs->where ( $where );
		// 总数
		$total = '';
		if ($_ct) {
			$total = $logs->count ( 'id' );
		}
		$data ['types'] = apply_filter ( 'get_activity_log_type', array ('' => __ ( 'All' ) ) );
		$data ['total'] = $total;
		$data ['rows'] = $logs;
		return view ( 'syslog/data.tpl', $data );
	}
}