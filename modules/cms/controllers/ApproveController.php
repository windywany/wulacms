<?php
class ApproveController extends Controller {
	protected $checkUser = true;
	protected $acls = array ('data' => 'approve:cms','index' => 'approve:cms','submit' => 'submit:cms','approving' => 'u:cms/cpage','save' => 'id|u:cms/cpage;c:cms/cpage' );
	public function index() {
		if (! bcfg ( 'disable_approving@cms', false )) {
			Response::respond ( 404 );
		}
		$data ['channels'] = ChannelForm::getChannelTree ( null, null );
		$data ['channels'] ['_'] = '自定义页面';
		$data ['models'] = array ('' => '请选择内容模型' );
		dbselect ()->from ( '{cms_model}' )->treeWhere ( array ('deleted' => 0,'hidden' => 0,'is_topic_model' => $data ['type'] == 'topic' ? 1 : 0 ) )->treeKey ( 'refid' )->treeOption ( $data ['models'] );
		
		$data ['canApprove'] = icando ( 'approve:cms' ) && bcfg ( 'allow_bentch_approve@cms', false );
		return view ( 'approve/index.tpl', $data );
	}
	public function approving($id = 0) {
		if (! icando ( 'approve:cms' )) {
			Response::showErrorMsg ( '你无权审核这个页面.', 403 );
		}
		$rows = dbselect ( 'PG.*,CH.root,CH.gid AS cgid,UU.nickname AS uuname' )->from ( '{cms_page} AS PG' );
		$rows->join ( '{cms_channel} AS CH', 'PG.channel = CH.refid' );
		$rows->join ( '{user} AS UU', 'PG.update_uid = UU.user_id' );
		$where = sess_get ( 'approving_condition' );
		if (! empty ( $where )) {
			$id = intval ( $id );
			if (! empty ( $id )) {
				$where ['PG.id'] = $id;
			}
			$rows->where ( $where );
			$page = $rows->get ( 0 );
			if ($page) {
				if (bcfg ( 'enable_group_bind@cms' )) {
					$ch ['gid'] = $page ['cgid'];
					if (! CmsPage::checkGroupPrevilige ( $ch )) {
						Response::showErrorMsg ( '你无权审核这个页面.', 403 );
					}
				}
				$page ['url'] = safe_url ( $page );
			}
		} else {
			$page ['id'] = 0;
		}
		return view ( 'approve/approving.tpl', $page );
	}
	public function submit($table, $ids, $pubdate = '', $pubtime = '') {
		if (icando ( 'submit:cms' )) {
			$ids = safe_ids ( $ids, ',', true );
			if ($ids) {
				$uid = $this->user->getUid ();
				$time = time ();
				$data ['update_uid'] = $uid;
				$data ['update_time'] = $time;
				$data ['status'] = 1;
				if ($pubdate && $pubtime) {
					$pubdate = strtotime ( $pubdate . ' ' . $pubtime . ':00' );
					if ($pubdate && $pubdate > $time) {
						$data ['publish_time'] = $pubdate;						
					}
				}
				$where ['id IN'] = $ids;
				// $where ['status !='] = 2; 允许已经发布文章可以再次送审.
				
				dbupdate ( '{cms_page}' )->set ( $data )->where ( $where )->exec ();
			}
			return NuiAjaxView::reload ( '#' . $table, '已经提交审核请耐心等待.' );
		} else {
			return NuiAjaxView::error ( '你没权限这么做.' );
		}
	}
	public function approve($result, $ids, $pubdate = '', $pubtime = '') {
		if (icando ( 'approve:cms' )) {
			$ids = safe_ids ( $ids, ',', true );
			if ($ids) {
				$time = time ();
				$result = intval ( $result );
				if (! bcfg ( 'allow_bentch_approve@cms', false )) {
					$ids = array ($ids [0] );
				}
				$msg = '审核了以下页面:' . implode ( ',', $ids ) . '.结果为:';
				if ($result == 1) {
					$data ['status'] = 2;
					$msg .= '通过';
				} else if ($result == 2) {
					$data ['status'] = 4;
					$msg .= '定时发布';
				} else {
					$data ['status'] = 0;
					$msg .= '拒绝';
				}
				$where ['id IN'] = $ids;
				$where ['status IN'] = array (1,4 );
				if ($result == 1) {
					$data ['publish_time'] = $time;
					$where ['publish_time'] = 0;
					dbupdate ( '{cms_page}' )->set ( $data )->where ( $where )->exec ();
					unset ( $data ['publish_time'], $where ['publish_time'] );
					$where ['publish_time >'] = 0;
				} else if ($result == 2) {
					$where ['publish_time >'] = $time;
					dbupdate ( '{cms_page}' )->set ( $data )->where ( $where )->exec ();
					if ($pubdate && $pubtime) {
						$pubdate = strtotime ( $pubdate . ' ' . $pubtime . ':00' );
						if ($pubdate && $pubdate > $time) {
							unset ( $where ['publish_time >'] );
							$where ['publish_time <='] = $time;
							$data ['publish_time'] = $pubdate;
						}
					}
				}
				dbupdate ( '{cms_page}' )->set ( $data )->where ( $where )->exec ();
				ActivityLog::info ( $msg, 'Approve' );
			}
			if (Request::isAjaxRequest ()) {
				return NuiAjaxView::reload ( '#page-table', '审核完成.' );
			} else {
				Response::redirect ( tourl ( 'cms/approve/approving' ) );
			}
		} else {
			if (Request::isAjaxRequest ()) {
				return NuiAjaxView::error ( '你没权限这么做.' );
			} else {
				Response::showErrorMsg ( '你没权限这么做.', 403 );
			}
		}
	}
	public function data($_cp = 1, $_lt = 20, $_sf = 'PG.id', $_od = 'd', $_ct = 0) {
		$rows = dbselect ( 'PG.*,CH.root,CH.name as channelName,CM.name AS modelName,CU.nickname as cuname' )->from ( '{cms_page} AS PG' );
		$rows->field ( 'UU.nickname AS uuname' );
		$rows->join ( '{cms_channel} AS CH', 'PG.channel = CH.refid' );
		$rows->join ( '{cms_model} AS CM', 'PG.model = CM.refid' );
		$rows->join ( '{user} AS CU', 'PG.create_uid = CU.user_id' );
		$rows->join ( '{user} AS UU', 'PG.update_uid = UU.user_id' );
		$where ['PG.deleted'] = 0;
		$where ['PG.hidden'] = 0;
		$willpub = rqst ( 'willpub' );
		if ($willpub == 'on') {
			$where ['PG.status'] = 4;
		} else {
			$where ['PG.status'] = 1;
		}
		$uid = $this->user->getUid ();
		if (bcfg ( 'enable_group_bind@cms' ) && $uid != 1) {
			$where ['CH.gid IN'] = $this->user->getAttr ( 'subgroups', array () );
		}
		$channel = rqst ( 'channel' );
		if ($channel) {
			$where ['PG.channel'] = $channel;
		}
		$model = rqst ( 'model' );
		if ($model) {
			$where ['PG.model'] = $model;
		}
		$uuname = irqst ( 'uuname' );
		if ($uuname) {
			$where ['PG.update_uid'] = $uuname;
		}
		$keywords = rqst ( 'keywords' );
		if ($keywords) {
			$t = '%' . $keywords . '%';
			$keywords = convert_search_keywords ( $keywords );
			$where [] = array ('search_index MATCH' => $keywords,'||PG.title LIKE' => $t,'||PG.title2 LIKE' => $t );
		}
		$rows->where ( $where );
		$_SESSION ['approving_condition'] = $where;
		$rows->sort ( $_sf, $_od );
		$rows->limit ( ($_cp - 1) * $_lt, $_lt );
		$data = array ();
		$data ['total'] = '';
		if ($_ct) {
			$data ['total'] = $rows->count ( 'PG.id' );
		}
		$data ['rows'] = $rows;
		$tpl = 'approve/page_data.tpl';
		return view ( $tpl, $data );
	}
}
