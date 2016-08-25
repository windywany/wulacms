<?php
/**
 * 回收站.
 * 
 * @author ngf
 */
class RecycleController extends Controller {
	protected $checkUser = true;
	protected $acls = array ('index' => 'r:recycle','data' => 'r:recycle','del' => 'd:recycle','restore' => 'u:recycle' );
	public function index() {
		$data ['canEmptyRecycle'] = icando ( 'd:recycle' );
		$data ['canRestoreRecycle'] = icando ( 'u:recycle' );
		$data ['types'] = apply_filter ( 'get_recycle_content_type', array ('' => __ ( 'All' ),'Unkown' => __ ( 'Unkown' ) ) );
		return view ( 'recycle/index.tpl', $data );
	}
	public function del($id) {
		$ids = safe_ids ( $id, ',', true );
		if ($ids) {
			$where = array ('id IN' => $ids );
			$logs = dbselect ( 'recycle_type,restore_clz,restore_value' )->from ( '{recycle} AS L' )->where ( $where );
			foreach ( $logs as $log ) {
				$clz = $log ['restore_clz'];
				if (class_exists ( $clz ) && is_subclass_of2 ( $clz, 'IRecycle' )) {
					$clz = new $clz ();
					$clz->delete ( $log ['restore_value'] );
				}
			}
			dbdelete ()->from ( '{recycle}' )->where ( $where )->exec ();
			return NuiAjaxView::click ( '#refresh', '内容已经彻底删除！' );
		} else {
			return NuiAjaxView::error ( '错误的编号!' );
		}
	}
	public function restore($id) {
		$ids = safe_ids ( $id, ',', true );
		if ($ids) {
			$rst = RecycleHelper::restore ( $ids );
			if ($rst) {
				return NuiAjaxView::reload ( '#recycle-log-table', '所选内容已经还原' );
			} else {
				return NuiAjaxView::error ( '无法还原所内容.' );
			}
		} else {
			return NuiAjaxView::error ( '错误的编号!' );
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
		$logs = dbselect ( 'L.*,U.nickname' )->from ( '{recycle} AS L' );
		$logs->join ( '{user} AS U', 'L.user_id = U.user_id' );
		
		// 排序
		$logs->sort ( $_sf, $_od );
		// 分页
		$logs->limit ( ($_cp - 1) * $_lt, $_lt );
		// 条件
		$where = Condition::where ( array ('U.nickname','LIKE','user' ), 'recycle_type' );
		if (! empty ( $bd )) {
			$where ['L.recycle_time >='] = strtotime ( $bd . ' 00:00:00' );
		}
		if (! empty ( $sd )) {
			$where ['L.recycle_time <='] = strtotime ( $sd . ' 23:59:59' );
		}
		$logs->where ( $where );
		// 总数
		$total = '';
		if ($_ct) {
			$total = $logs->count ( 'id' );
		}
		
		$data ['total'] = $total;
		$data ['rows'] = $logs;
		$data ['canEmptyRecycle'] = icando ( 'd:recycle' );
		$data ['canRestoreRecycle'] = icando ( 'u:recycle' );
		$data ['types'] = apply_filter ( 'get_recycle_content_type', array ('' => __ ( 'All' ),'Unkown' => __ ( 'Unkown' ) ) );
		return view ( 'recycle/data.tpl', $data );
	}
}
