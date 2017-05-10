<?php
class ChannelController extends Controller {
	protected $checkUser = true;
	protected $acls = array ('*' => 'ch:mobi' );
	public function index() {
		$data = array ();
		$data ['canDelCh'] = $data ['canAddCh'] = icando ( 'ch:mobi' );
		return view ( 'channel/index.tpl', $data );
	}
	public function add() {
		$data = array ();
		$form = new MobiChannelForm ();
		$data ['rules'] = $form->rules ();
		$data ['formName'] = get_class ( $form );
		$data ['widgets'] = new DefaultFormRender ( $form->buildWidgets ( $data ) );
		return view ( 'channel/form.tpl', $data );
	}
	public function edit($id) {
		$id = intval ( $id );
		if (empty ( $id )) {
			Response::showErrorMsg ( '未指定要编辑的栏目编号。' );
		}
		$channel = dbselect ( '*' )->from ( '{mobi_channel}' )->where ( array ('id' => $id ) )->get ( 0 );
		if (empty ( $channel )) {
			Response::showErrorMsg ( '未找到要编辑的栏目编号。' );
		}
		$data = $channel;
		$binds = dbselect ( 'cms_refid' )->from ( '{mobi_channel_binds}' )->where ( array ('mobi_refid' => $channel ['refid'] ) );
		$data ['channels'] = $binds->toArray ( 'cms_refid' );
		$form = new MobiChannelForm ( $data );
		$data ['orefid'] = $data ['refid'];
		$data ['rules'] = $form->rules ();
		$data ['formName'] = get_class ( $form );
		$data ['widgets'] = new DefaultFormRender ( $form->buildWidgets ( $data ) );
		
		return view ( 'channel/form.tpl', $data );
	}
	public function del($ids) {
		$ids = safe_ids2 ( $ids );
		if (empty ( $ids )) {
			return NuiAjaxView::error ( '请选择栏目.' );
		}
		if (dbupdate ( '{mobi_channel}' )->set ( array ('deleted' => 1 ) )->where ( array ('id IN' => $ids ) )->exec ()) {
			$recycle = new DefaultRecycle ( $ids, 'MobiChannel', 'mobi_channel', '({id}){name}' );
			RecycleHelper::recycle ( $recycle );
			return NuiAjaxView::reload ( '#mobi-ch-table', '所选栏目已放入回收站.' );
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
			dbupdate ( '{mobi_channel}' )->set ( array ('sort' => $sort ) )->where ( array ('id' => $id ) )->exec ();
		}
		return NuiAjaxView::reload ( '#mobi-ch-table' );
	}
	public function save() {
		$form = new MobiChannelForm ();
		$channel = $form->valid ();
		if ($channel) {
			$time = time ();
			$uid = $this->user->getUid ();
			$channel ['update_time'] = $time;
			$channel ['update_uid'] = $uid;
			$id = $channel ['id'];
			$channels = $channel ['channels'];
			$orefid = $channel ['orefid'];
			unset ( $channel ['id'], $channel ['channels'], $channel ['orefid'] );
			if (empty ( $id )) {
				$channel ['create_time'] = $time;
				$channel ['create_uid'] = $uid;
				$rst = dbinsert ( $channel )->into ( '{mobi_channel}' )->exec ();
			} else {
				$rst = dbupdate ( '{mobi_channel}' )->set ( $channel )->where ( array ('id' => $id ) )->exec ();
			}
			if ($rst) {
				if (empty ( $id )) {
					$id = $rst [0];
				}
				if ($orefid && $orefid != $channel ['refid']) {
					// 如果修改了refid,删除已经绑定的CMS栏目.
					dbdelete ()->from ( '{mobi_channel_binds}' )->where ( array ('mobi_refid' => $orefid ) )->exec ();
				}
				$this->saveBindChannels ( $channel ['refid'], $channels );
				return NuiAjaxView::click ( '#rtn2ads', '栏目已经保存.' );
			} else {
				return NuiAjaxView::error ( '保存栏目出错啦:' . DatabaseDialect::$lastErrorMassge );
			}
		} else {
			return NuiAjaxView::validate ( get_class ( $form ), '表单数据格式有误', $form->getErrors () );
		}
	}
	public function data($_cp = 1, $_lt = 20, $_sf = 'CH.sort', $_od = 'a', $_ct = 0) {
		$rows = dbselect ( 'CH.*' )->from ( '{mobi_channel} AS CH' );
		// 排序
		$rows->sort ( 'CH.hidden', 'a' );
		$rows->sort ( $_sf, $_od );
		// 分页
		$rows->limit ( ($_cp - 1) * $_lt, $_lt );
		$where = Condition::where ( array ('CH.name','LIKE','keywords' ), array ('CH.refid','LIEKE','refid' ) );
		$where ['CH.deleted'] = 0;
		$rows->where ( $where );
		// 总数
		$total = '';
		if ($_ct) {
			$total = $rows->count ( 'CH.id' );
		}
		$data ['total'] = $total;
		foreach ( $rows as $row ) {
			$row ['binds'] = MobiChannelForm::getBindChannels ( $row ['refid'] );
			$data ['rows'] [] = $row;
		}
		
		$data ['canEditCH'] = icando ( 'ch:mobi' );
		$data ['canDelCH'] = icando ( 'ch:mobi' );
		return view ( 'channel/data.tpl', $data );
	}
	private function saveBindChannels($refid, $channels) {
		dbdelete ()->from ( '{mobi_channel_binds}' )->where ( array ('mobi_refid' => $refid ) )->exec ();
		if ($channels) {
			$datas = array ();
			
			foreach ( $channels as $ch ) {
				$datas [] = array ('mobi_refid' => $refid,'cms_refid' => $ch );
			}
			if ($datas) {
				dbinsert ( $datas, true )->into ( '{mobi_channel_binds}' )->exec ();
			}
		}
	}
}