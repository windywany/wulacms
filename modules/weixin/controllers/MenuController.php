<?php
/**
 * 栏目.
 *
 * @author Guangfeng
 */
class MenuController extends Controller {
	protected $checkUser = true;
	protected $acls = array ('index' => 'r:weixin/channel','add' => 'c:weixin/channel','edit' => 'u:weixin/channel','save' => 'id|u:weixin/channel;c:weixin/channel','updateurl' => 'cu:weixin/channel','updateurl_post' => 'cu:weixin/channel' );
	protected $typeList = array ('click' => '点击推事件','view' => '跳转URL','scancode_push' => '扫码推事件','scancode_waitmsg' => '扫码推事件且弹出“消息接收中”提示框','pic_sysphoto' => '弹出系统拍照发图','pic_photo_or_album' => '弹出拍照或者相册发图','pic_weixin' => '弹出微信相册发图器','location_select' => '弹出地理位置选择器','media_id' => '下发消息（除文本消息）','view_limited' => '跳转图文消息URL' );
	public function index($type = 0) {
		$data = array ();
		$data ['canAddChannel'] = icando ( 'c:weixin/channel' );
		$data ['canDeleteChannel'] = icando ( 'd:weixin/channel' );
		$data ['canEditChannel'] = icando ( 'u:weixin/channel' );
		$data ['canUpdateURL'] = icando ( 'cu:weixin/channel' );
		return view ( 'menu/index.tpl', $data );
	}
	public function add($type = 0, $upid = 0) {
		$upid = intval ( $upid );
		$data = array ();
		$form = new WeixinMenuForm ();
		
		$data ['upid'] = $upid ? $upid : 0;
		$data ['oupid'] = 0;
		if ($upid) {
			$updata = dbselect ( 'page_name,default_model,default_template,default_url_pattern,list_page as list_page_id' )->from ( '{weixin_menu}' )->where ( array ('id' => $upid ) )->get ( 0 );
		}
		
		$data ['rules'] = $form->rules ();
		
		$data ['typeList'] = $this->typeList;
		
		return view ( 'menu/form.tpl', $data );
	}
	public function edit($id) {
		$id = intval ( $id );
		$ch = dbselect ( '*' )->from ( '{weixin_menu}' )->where ( array ('id' => $id ) )->get ( 0 );
		if ($ch) {
			$form = new WeixinMenuForm ( $ch );
			$ch ['rules'] = $form->rules ();
			$ch ['oupid'] = $ch ['upid'];
			$ch ['typeList'] = $this->typeList;
			return view ( 'menu/form.tpl', $ch );
		} else {
			Response::showErrorMsg ( '内容不存在', 404 );
		}
	}
	
	/**
	 * 还有一些子栏目删除的问题
	 *
	 * @param number $id        	
	 */
	public function del($id = 0) {
		if (empty ( $id )) {
			Response::showErrorMsg ( '菜单不存在', 404 );
		}
		$subs = dbselect ( '*' )->from ( '{weixin_menu}' )->where ( array ('id' => $id ) )->get ( 'id' );
		if (empty ( $subs )) {
			Response::showErrorMsg ( '菜单不存在', 404 );
		}
		$data ['deleted'] = 1;
		$data ['update_time'] = time ();
		$data ['update_uid'] = $this->user->getUid ();
		if (dbupdate ( '{weixin_menu}' )->set ( $data )->where ( array ('id' => $id ) )->exec ()) {
			$recycle = new DefaultRecycle ( $subs, 'weixin_menu', 'weixin_menu', 'ID:{id};菜单名:{name}' );
			RecycleHelper::recycle ( $recycle );
			return NuiAjaxView::ok ( '菜单已经删除', 'click', '#refresh' );
		} else {
			return NuiAjaxView::error ( '数据库操作失败.' );
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
			dbupdate ( '{weixin_menu}' )->set ( array ('sort' => $sort ) )->where ( array ('id' => $id ) )->exec ();
		}
		return NuiAjaxView::reload ( '#channel-table' );
	}
	public function save() {
		$form = new WeixinMenuForm ();
		$ch = $form->valid ();
		if ($ch) {
			if ($this->_tranformClickToVal ( $ch ['menu_type'] ) == 'url') {
				if (! preg_match ( '#^https?://.+$#', $ch ['key'] )) {
					return NuiAjaxView::error ( 'KEY 值为URL' );
				}
			}
			
			$ch ['upid'] = ( int ) $ch ['upid'];
			$time = time ();
			$uid = $this->user->getUid ();
			$ch ['update_uid'] = $uid;
			$ch ['update_time'] = $time;
			if (empty ( $ch ['sort'] )) {
				$ch ['sort'] = 999;
			}
			$id = $ch ['id'];
			unset ( $ch ['id'] );
			
			if (empty ( $id )) { // 新增
				$ch ['create_uid'] = $uid;
				$ch ['create_time'] = $time;
				$db = dbinsert ( $ch );
				$rst = $db->into ( '{weixin_menu}' )->exec ();
			} else { // 修改
				$rst = dbupdate ( '{weixin_menu}' )->set ( $ch )->where ( array ('id' => $id ) )->exec ();
			}
			if ($rst) {
				return NuiAjaxView::ok ( '保存成功', 'click', '#rtnbtn' );
			} else {
				return NuiAjaxView::error ( '保存出错啦:' . DatabaseDialect::$lastErrorMassge );
			}
		} else {
			return NuiAjaxView::validate ( 'ChannelForm', '表单不正确，请重新填写.', $form->getErrors () );
		}
	}
	public function data($type = 0, $_tid = 0, $_cp = 1, $_lt = 20, $_sf = 'CH.sort', $_od = 'a', $_ct = 0) {
		$data = array ();
		$data ['canAddChannel'] = icando ( 'c:weixin/channel' );
		$data ['canDeleteChannel'] = icando ( 'd:weixin/channel' );
		$data ['canEditChannel'] = icando ( 'u:weixin/channel' );
		$data ['canUpdateURL'] = icando ( 'cu:weixin/channel' );
		$items = dbselect ( 'CH.id,CH.upid,CH.sort,CH.name,CH.menu_type,CH.key' )->from ( '{weixin_menu} AS CH' );
		$where = array ('CH.deleted' => 0 );
		
		$keywords = rqst ( 'keywords' );
		if ($keywords) {
			$v = "%{$keywords}%";
			$where [] = array ('CH.name LIKE' => $v,'||CH.refid LIKE' => $v );
			$data ['search'] = 'true';
		} else {
			$where ['CH.upid'] = $_tid;
			$data ['search'] = false;
		}
		if (! $data ['search']) {
			$cnt = dbselect ( imv ( 'COUNT(CH1.id)' ) )->from ( '{weixin_menu} AS CH1' )->where ( array ('CH1.upid' => imv ( 'CH.id' ) ) );
			$items->field ( $cnt, 'child_cnt' );
		}
		$items->where ( $where );
		$total = '';
		if ($_ct) {
			$total = $items->count ( 'CH.id' );
		}
		$rst = $items->asc ( 'sort' )->asc ( 'id' )->limit ( ($_cp - 1) * $_lt, $_lt )->toArray ();
		foreach ( $rst as $key => $val ) {
			$rst [$key] ['typeName'] = $this->typeList [$val ['menu_type']];
		}
		
		$data ['total'] = $total;
		$data ['items'] = $rst;
		$data ['_tid'] = $_tid;
		
		return view ( 'menu/data.tpl', $data );
	}
	
	/**
	 * 同步菜单配置到微信服务器端
	 */
	public function sync() {
		$url = 'https://api.weixin.qq.com/cgi-bin/menu/create?access_token=#TOKEN#';
		$rstParent = dbselect ( '*' )->from ( '{weixin_menu}' )->where ( array ('upid' => 0,'deleted' => 0 ) )->asc ( 'sort' )->asc ( 'id' )->toArray ();
		$data = array ();
		foreach ( $rstParent as $key => $val ) {
			$rs = dbselect ( '*' )->from ( '{weixin_menu}' )->where ( array ('upid' => $val ['id'],'deleted' => 0 ) )->asc ( 'sort' )->toArray ();
			$tmpData = array ();
			if ($rs) {
				$tmpData ['name'] = urlencode ( $val ['name'] );
				foreach ( $rs as $keyRs => $keyVal ) {
					$subKey = $this->_tranformClickToVal ( $keyVal ['menu_type'] );
					$tmpData ['sub_button'] [] = array ("type" => $keyVal ['menu_type'],"name" => urlencode ( $keyVal ['name'] ),$subKey => $keyVal ['key'] );
				}
			} else {
				$subKey = $this->_tranformClickToVal ( $val ['menu_type'] );
				$tmpData = array ("type" => $val ['menu_type'],"name" => urlencode ( $val ['name'] ),$subKey => $val ['key'] );
			}
			$data [] = $tmpData;
		}
		if (! $data) {
			return NuiAjaxView::error ( '请先添加菜单！:' );
		}
		// 验证数据合法性，最多3个一级菜单，二级菜单最多5个
		
		if (count ( $data ) > 3) {
			return NuiAjaxView::error ( '一级菜单最多3个！:' );
		}
		foreach ( $data as $val ) {
			if (count ( $val ['sub_button'] ) > 5) {
				return NuiAjaxView::error ( $val ['name'] . '下二级菜单最多5个！:' );
			}
		}
		$input = array ('button' => $data );
		$return = WeixinUtil::apiPost ( $url, $input );
		if ($return && $return ['errmsg'] == "ok") {
			return NuiAjaxView::ok ( '同步成功' );
		} else {
			return NuiAjaxView::error ( '同步失败！微信返回信息，errcode:' . $return ['errcode'] . '。errmsg:' . $return ['errmsg'] );
		}
	}
	
	/**
	 * 通过点击事件的值，转换传入参数的值
	 *
	 * @author DQ
	 *         @date 2015年12月15日 下午4:38:52
	 * @param        	
	 *
	 * @return
	 *
	 */
	public function _tranformClickToVal($clickType = '') {
		$string = "";
		switch ($clickType) {
			case "click" :
			case "pic_sysphoto" :
			case "pic_photo_or_album" :
			case "pic_weixin" :
			case "location_select" :
				$string = 'key';
				break;
			case "view" :
				$string = 'url';
				break;
			case "media_id" :
			case "view_limited" :
				$string = 'media_id';
				break;
			default :
				$string = 'key';
		}
		return $string;
	}
}