<?php
/**
 * 粉丝管理
 * 
 * @author dingqiang
 *
 */
class FansController extends Controller {
	protected $checkUser = true;
	protected $acls = array ('index' => 'r:weixin/fans','add' => 'c:weixin/fans','edit' => 'u:weixin/fans','save' => 'id|u:weixin/fans;c:weixin/fans');
	
	public function index($type = 0) {
		$data = array ();
		$data ['canAddFans'] = icando ( 'c:weixin/fans' );
		$data ['canDeleteFans'] = icando ( 'd:weixin/fans' );
		$data ['canEditFans'] = icando ( 'u:weixin/fans' );
		
		$data ['cronStatus'] = cfg('sync_status@weixin',0);
		
		return view ( 'fans/index.tpl', $data );
	}
	
	/**
	 * 删除
	 *
	 * @param number $id        	
	 */
	public function del($id = 0) {
		if (empty ( $id )) {
			Response::showErrorMsg ( '用户不存在', 404 );
		}
		$subs = dbselect ( '*' )->from ( '{weixin_subscriber}' )->where ( array ('id' => $id ) )->get ( 'id' );
		if (empty ( $subs )) {
			Response::showErrorMsg ( '用户不存在', 404 );
		}
		$data ['deleted'] = 1;
		$data ['update_time'] = time ();
		$data ['update_uid'] = $this->user->getUid ();
		if (dbupdate ( '{weixin_subscriber}' )->set ( $data )->where ( array ('id' => $id ) )->exec ()) {
			$recycle = new DefaultRecycle ( $subs, 'weixin_subscriber', 'weixin_subscriber', 'ID:{id};昵称:{nickname}' );
			RecycleHelper::recycle ( $recycle );
			return NuiAjaxView::ok ( '用户已经删除', 'click', '#refresh' );
		} else {
			return NuiAjaxView::error ( '数据库操作失败.' );
		}
	}
	
	public function data( $_tid = 0, $_cp = 1, $_lt = 20, $_sf = 'CH.sort', $_od = 'a', $_ct = 0) {
		$data = array ();
		$data ['canDeleteFans'] = icando ( 'd:weixin/fans' );
		$items = dbselect ( '*' )->from ( '{weixin_subscriber} AS CH' );
		$where = array ('CH.deleted' => 0 );
		
		$keywords = rqst ( 'keywords' );
		if ($keywords) {
			$where [] = array ('CH.nickname LIKE' => "%{$keywords}%");
		}
		
		$items->where ( $where );
		$total = '';
		if ($_ct) {
			$total = $items->count ( 'CH.id' );
		}
		
		$rst = $items->desc ( 'id' )->limit ( ($_cp - 1) * $_lt, $_lt )->toArray ();
		
		foreach ( $rst as $key => $val ) {
		    if($val['sex'] == 1){
		        $rst[$key]['sexName'] = '男';
		    }else{
		        $rst[$key]['sexName'] = '女';
		    }
		    if($val['subscribe'] == 1){
		        $rst[$key]['subscribeName'] = '订阅';
		    }else{
		        $rst[$key]['subscribeName'] = '取消';
		    }
		    if($val['subscribe_time']){
		        $rst[$key]['subscribeTime'] = date('Y-m-d H:i',$val['subscribe_time']);
		    }
		    if($val['update_time']){
		        $rst[$key]['updateTime'] = date('Y-m-d H:i',$val['update_time']);
		    }
		}
		
		$data ['total'] = $total;
		$data ['items'] = $rst;
		return view ( 'fans/data.tpl', $data );
	}
	
	/**
	 * 同步菜单配置到微信服务器端
	 */
	public function sync() {
	    $status = cfg('sync_status@weixin',0);
	    if($status == 0){
	        set_cfg('sync_status', 1 ,'weixin');
	    }
        return NuiAjaxView::ok ( '该任务已经正在运行，请耐心等待！' );
	}
	
}