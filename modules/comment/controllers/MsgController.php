<?php
class MsgController extends Controller {
	private $tipText = array ('0' => '驳回','1' => '标记为已处理','2' => '标记为垃圾','3' => '标记为处理中' );
	protected $checkUser = array ('dashboard','admin','post_post' );
	protected $acls = array ('index' => 'm:comment','data' => 'm:comment','status' => 'a:comment','reply_post' => 'reply:comment','edit' => 'u:comment','save' => 'u:comment','del' => 'd:comment' );
	public function index($status = -1) {
		if ($status == - 1) {
			$data ['status'] = rqst ( 'status', '0' );
		} else {
			$data ['status'] = $status;
		}
		$data ['status_text'] = CommentHooksImpl::$MSG_STATUS;
		$data ['canDelComment'] = icando ( 'd:comment' );
		$data ['canApproveComment'] = icando ( 'a:comment' );
		return view ( 'msg_index.tpl', $data );
	}
	/**
	 * 改变留言的状态.需要a:comment权限.
	 *
	 * @param unknown $status        	
	 * @param unknown $ids        	
	 * @return NuiAjaxView
	 */
	public function status($status, $ids) {
		$ids = safe_ids2 ( $ids );
		$status = intval ( $status );
		if (! empty ( $ids ) && isset ( $this->tipText [$status] )) {
			$data ['update_time'] = time ();
			$data ['update_uid'] = $this->user->getUid ();
			$data ['status'] = $status;
			dbupdate ( '{comments_msg}' )->set ( $data )->where ( array ('id IN' => $ids ) )->exec ();
			return NuiAjaxView::reload ( '#commentmsg-table', $this->tipText [$status] . '留言完成.' );
		} else if (isset ( $this->tipText [$status] )) {
			return NuiAjaxView::error ( '要' . $this->tipText [$status] . '的留言为空.' );
		} else {
			return NuiAjaxView::error ( '未知状态.' );
		}
	}
	/**
	 * 删除.
	 *
	 * @param unknown $ids        	
	 * @return NuiAjaxView
	 */
	public function del($ids) {
		$ids = safe_ids2 ( $ids );
		if ($ids) {
			$subs = dbselect ( 'id' )->from ( '{comments_msg}' )->where ( array ('parent IN' => $ids ) )->toArray ( 'id' );
			$where = array ('id IN' => $ids );
			if (dbdelete ()->from ( '{comments_msg}' )->where ( $where )->exec () && dbdelete ()->from ( '{comments_msg}' )->where ( array ('replyto IN' => $ids ) )->exec () && $subs) {
				$where ['id IN'] = $subs;
				dbupdate ( '{comments_msg}' )->set ( array ('parent' => 0 ) )->where ( $where )->exec ();
			}
			return NuiAjaxView::reload ( '#commentmsg-table', '留言已删除.' );
		}
		return NuiAjaxView::error ( '未指定要删除的留言编号.' );
	}
	/**
	 * 编辑.
	 *
	 * @param unknown $id        	
	 * @return SmartyView
	 */
	public function edit($id) {
		$data = array ();
		$id = intval ( $id );
		if ($id) {
			$comment = dbselect ( '*' )->from ( '{comments_msg}' )->where ( array ('id' => $id,'deleted' => 0 ) )->get ( 0 );
			if ($comment) {
				$msgs = array ();
				
				if ($comment ['parent']) {
					$msgs [] = dbselect ( '*' )->from ( '{comments_msg}' )->where ( array ('id' => $comment ['parent'] ) )->get ( 0 );
					$pid = $comment ['parent'];
				} else {
					$msgs [] = $comment;
				}
				
				if ($pid) {
					$msgs = dbselect ( '*' )->from ( '{comments_msg}' )->where ( array ('parent' => $pid,'replyto' => 0 ) )->asc ( 'create_time' )->toArray ( null, null, $msgs );
				}
				
				foreach ( $msgs as $mid => $msg ) {
					$msgs [$mid] ['replies'] = dbselect ( '*' )->from ( '{comments_msg}' )->where ( array ('replyto' => $msg ['id'] ) )->toArray ();
				}
				
				$data ['msgs'] = $msgs;
				$form = new CommentMsgEditForm ();
				$data ['rules'] = $form->rules ();
				$comment ['create_time1'] = date ( 'H:i', $comment ['create_time'] );
				$comment ['create_time'] = date ( 'Y-m-d', $comment ['create_time'] );
				$data ['widgets'] = new DefaultFormRender ( $form->buildWidgets ( $comment ) );
				$data ['comment'] = $comment;
				$data ['canReplyComment'] = icando ( 'reply:comment' ) && empty ( $comment ['replyto'] );
				$data ['cannotApproveComment'] = ! icando ( 'a:comment' ) || ! empty ( $comment ['replyto'] );
				return view ( 'msg_form.tpl', $data );
			}
		}
		Response::showErrorMsg ( '未找到要编辑的留言.', 404 );
	}
	public function save() {
		$form = new CommentMsgEditForm ();
		$data = $form->valid ();
		if ($data) {
			$data ['content'] = $data ['comment_content'];
			$id = $data ['id'];
			unset ( $data ['comment_content'], $data ['id'] );
			$data ['update_time'] = time ();
			$data ['update_uid'] = $this->user->getUid ();
			if (! icando ( 'a:comment' ) || $data ['status'] === '') {
				unset ( $data ['status'] );
			}
			if (dbupdate ( '{comments_msg}' )->set ( $data )->where ( array ('id' => $id ) )->exec ()) {
				$replycontent = rqst ( 'replycontent', '', true );
				$rsubject = rqst ( 'rsubject' );
				if ($replycontent || $rsubject) {
					$this->reply_post ( $id, $replycontent, $rsubject );
				}
				$dialog [] = '<p class="text-left">';
				$dialog [] = '[<a href="#' . tourl ( 'comment/msg', false ) . '?status=' . $data ['status'] . '" onclick="nUI.closeAjaxDialog()">返回列表</a>]';
				$dialog [] = ' [<a href="javascript:;" onclick="nUI.closeAjaxDialog()">继续编辑</a>]';
				$dialog [] = '</p>';
				return NuiAjaxView::dialog ( implode ( '', $dialog ), '更新完成!', array ('model' => true,'height' => 'auto','func' => 'commentSaved' ) );
			}
			return NuiAjaxView::error ( '无法更新留言,数据库出错.' );
		}
		return NuiAjaxView::validate ( 'CommentMsgEditForm', '表单数据校验失败.', $form->getErrors () );
	}
	/**
	 * 管理员回复.
	 *
	 * @param unknown $id        	
	 * @param unknown $content        	
	 * @return NuiAjaxView
	 */
	public function reply_post($id, $content, $subject = '') {
		$id = intval ( $id );
		$content = trim ( $content );
		if ($id && ($content || $subject)) {
			$comment = dbselect ( '*' )->from ( '{comments_msg}' )->where ( array ('id' => $id,'deleted' => 0 ) )->get ( 0 );
			if ($comment) {
				$data ['create_uid'] = $this->user->getUid ();
				$date ['update_uid'] = $this->user->getUid ();
				$data ['create_time'] = time ();
				$data ['update_time'] = $data ['create_time'];
				$data ['parent'] = $comment ['parent'] ? $comment ['parent'] : $id;
				$data ['content'] = $content;
				$data ['author_ip'] = $_SERVER ['REMOTE_ADDR'];
				$data ['author'] = $this->user->getDisplayName ();
				$data ['author_email'] = $this->user->getEmail ();
				$data ['status'] = 3;
				$data ['replyto'] = $id;
				$data ['page_id'] = $comment ['page_id'];
				$data ['subject'] = $subject;
				$rst = dbinsert ( $data )->into ( '{comments_msg}' )->exec ();
				if ($rst) {
					if (icando ( 'a:comment' )) {
						dbupdate ( '{comments_msg}' )->set ( array ('status' => 3 ) )->where ( array ('id' => $id ) )->exec ();
					}
					return NuiAjaxView::reload ( '#commentmsg-table', '成功回复留言' );
				} else {
					return NuiAjaxView::error ( '不能将数据写入数据库.' );
				}
			}
		}
		return NuiAjaxView::error ( '留言不存在.' );
	}
	/**
	 * 会员留言.
	 */
	public function post_post() {
		$allow_anonymouse = bcfg ( 'allow_anonymouse1@comment' );
		$enable_captcha = bcfg ( 'enable_captcha@comment' );
		$interval = icfg ( 'interval1@comment', 60 );
		$last_time = sess_get ( 'last_post_msg', 0 );
		if ($last_time > 0 && (time () - $last_time) < $interval) {
			return NuiAjaxView::error ( '你留言地太快了,请等等.' );
		}
		if ($enable_captcha) {
			$captcha = rqst ( 'captcha' );
			$auth_code_obj = new CaptchaCode ();
			if (! $auth_code_obj->validate ( $captcha, false )) {
				return NuiAjaxView::error ( '验证码不正确.' );
			}
		}
		$this->user = whoami ( 'vip' );
		if ($this->user->isLogin ()) {
			$uid = $this->user->getUid ();
		} elseif ($allow_anonymouse) {
			$uid = 0;
		} else {
			$uid = - 1;
		}
		
		if ($uid < 0) {
			return NuiAjaxView::auth ( '请登录.' );
		}
		$form = new CommentMsgVipPostForm ();
		$data = $form->valid ();		
		if ($data) {
			$data ['create_uid'] = $date ['update_uid'] = $uid;
			$data ['create_time'] = $data ['update_time'] = time ();
			$data ['status'] = 0;
			$data ['user_id'] = $uid;
			$data ['author_ip'] = $_SERVER ['REMOTE_ADDR'];
			$rst = dbinsert ( $data )->into ( '{comments_msg}' )->exec ();
			if ($rst) {
				$_SESSION ['last_post_msg'] = time ();
				$data ['id'] = $rst [0];
				return NuiAjaxView::ok ( '留言成功', false, $data );
			} else {
				return NuiAjaxView::error ( '不能将数据写入数据库.' );
			}
		}
		
		return NuiAjaxView::validate ( 'CommentMsgVipPostForm', '表单校验失败.', $form->getErrors () );
	}
	/**
	 * 列表数据.
	 *
	 * @param number $_cp        	
	 * @param number $_lt        	
	 * @param string $_sf        	
	 * @param string $_od        	
	 * @param number $_ct        	
	 * @return SmartyView
	 */
	public function data($_cp = 1, $_lt = 20, $_sf = 'C.id', $_od = 'd', $_ct = 0) {
		$rows = dbselect ( 'C.*,CC.author AS pauthor,CC.subject AS psubject' )->from ( '{comments_msg} AS C' )->limit ( ($_cp - 1) * $_lt, $_lt );
		
		$rows->join ( '{comments_msg} AS CC', 'C.parent = CC.id' );
		
		$rows->sort ( $_sf, $_od );
		
		$where ['C.deleted'] = 0;
		
		$where ['C.replyto'] = 0;
		
		$status = rqst ( 'status' );
		
		if (is_numeric ( $status )) {
			$where ['C.status'] = intval ( $status );
		}
		$uuname = irqst ( 'uuname' );
		
		if ($uuname) {
			$where ['RC.update_uid'] = $uuname;
			$rows->join ( '{comments_msg} AS RC', 'C.id = RC.replyto' );
		}
		
		$rows->field ( 'CP.url,CH.root,CP.title,CP.title2' );
		
		$ccnt = dbselect ( imv ( 'COUNT(CCNT.id)' ) )->from ( '{comments_msg} AS CCNT' )->where ( array ('CCNT.page_id' => imv ( 'C.page_id' ) ) );
		
		$rows->field ( $ccnt, 'comment_count' );
		
		$rows->join ( '{cms_page} AS CP', 'C.page_id = CP.id' );
		
		$rows->join ( '{cms_channel} AS CH', 'CP.channel = CH.refid' );
		
		$author = rqst ( 'author' );
		
		if ($author) {
			$v = '%' . $author . '%';
			$where [] = array ('C.author LIKE' => $v,'||C.author_url LIKE' => $v,'||C.author_ip LIKE' => $v );
		}
		
		$contact = rqst ( 'contact' );
		if ($contact) {
			$v = '%' . $contact . '%';
			$where ['C.author_email LIKE'] = $v;
			$where ['C.author_contacts LIKE'] = $v;
		}
		$keywords = rqst ( 'keywords' );
		if ($keywords) {
			$where ['CC.subject LIKE'] = $where ['C.subject LIKE'] = $where ['C.content LIKE'] = '%' . $keywords . '%';
		}
		
		$page_id = trim ( rqst ( 'page_id' ) );
		if ($page_id) {
			if (is_numeric ( $page_id )) {
				$where ['C.page_id'] = intval ( $page_id );
			} else {
				$where ['CP.keywords LIKE'] = '%' . $page_id . '%';
			}
		}
		$rows->where ( $where );
		$data ['total'] = '';
		if ($_ct) {
			$data ['total'] = $rows->count ( 'C.id' );
		}
		$msgs = array ();
		foreach ( $rows as $row ) {
			$row ['replies'] = dbselect ( '*' )->from ( '{comments_msg}' )->where ( array ('replyto' => $row ['id'] ) )->desc ( 'id' )->toArray ();
			$msgs [] = $row;
		}
		$data ['rows'] = $msgs;
		$data ['canDelComment'] = icando ( 'd:comment' );
		$data ['canApproveComment'] = icando ( 'a:comment' );
		$data ['canEditComment'] = icando ( 'u:comment' );
		$data ['canReplyComment'] = icando ( 'reply:comment' );
		$data ['status_cls'] = array ('0' => 'warn-td','1' => 'succ-td','2' => '','3' => 'proc-td' );
		return view ( 'msg_data.tpl', $data );
	}
}
