<?php
/**
 * 评论&留言.
 * @author ngf
 *
 */
class CommentController extends Controller {
	private $tipText = array ('0' => '驳回','1' => '批准','2' => '标记为垃圾' );
	protected $checkUser = array ('dashboard','admin','post_post' );
	protected $acls = array ('index' => 'm:comment','data' => 'm:comment','status' => 'a:comment','reply_post' => 'reply:comment','edit' => 'u:comment','save' => 'u:comment','del' => 'd:comment' );
	public function index() {
		$data ['status'] = rqst ( 'status', '0' );
		$data ['status_text'] = CommentHooksImpl::$STATUS;
		$data ['canDelComment'] = icando ( 'd:comment' );
		$data ['canApproveComment'] = icando ( 'a:comment' );
		return view ( 'index.tpl', $data );
	}
	/**
	 * 改变评论的状态.需要a:comment权限.
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
			dbupdate ( '{comments}' )->set ( $data )->where ( array ('id IN' => $ids ) )->exec ();
			return NuiAjaxView::reload ( '#comment-table', $this->tipText [$status] . '评论完成.' );
		} else if (isset ( $this->tipText [$status] )) {
			return NuiAjaxView::error ( '要' . $this->tipText [$status] . '的评论为空.' );
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
			$subs = dbselect ( 'id' )->from ( '{comments}' )->where ( array ('parent IN' => $ids ) )->toArray ( 'id' );
			$where = array ('id IN' => $ids );
			if (dbdelete ()->from ( '{comments}' )->where ( $where )->exec () && $subs) {
				$where ['id IN'] = $subs;
				dbupdate ( '{comments}' )->set ( array ('parent' => 0 ) )->where ( $where )->exec ();
			}
			return NuiAjaxView::reload ( '#comment-table', '评论已删除.' );
		}
		return NuiAjaxView::error ( '未指定要删除的评论编号.' );
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
			$comment = dbselect ( '*' )->from ( '{comments}' )->where ( array ('id' => $id,'deleted' => 0 ) )->get ( 0 );
			if ($comment) {
				$form = new CommentPostForm ();
				$data ['rules'] = $form->rules ();
				$comment ['create_time1'] = date ( 'H:i', $comment ['create_time'] );
				$comment ['create_time'] = date ( 'Y-m-d', $comment ['create_time'] );
				$data ['widgets'] = new DefaultFormRender ( $form->buildWidgets ( $comment ) );
				$data ['comment'] = $comment;
				$data ['canReplyComment'] = icando ( 'reply:comment' );
				$data ['cannotApproveComment'] = ! icando ( 'a:comment' );
				return view ( 'form.tpl', $data );
			}
		}
		Response::showErrorMsg ( '未找到要编辑的评论.', 404 );
	}
	public function save() {
		$form = new CommentPostForm ();
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
			if (dbupdate ( '{comments}' )->set ( $data )->where ( array ('id' => $id ) )->exec ()) {
				$replycontent = rqst ( 'replycontent', '', true );
				if ($replycontent) {
					$this->reply_post ( $id, $replycontent );
				}
				$dialog [] = '<p class="text-left">';
				$dialog [] = '[<a href="#' . tourl ( 'comment', false ) . '?status=' . $data ['status'] . '" onclick="nUI.closeAjaxDialog()">返回列表</a>]';
				$dialog [] = ' [<a href="javascript:;" onclick="nUI.closeAjaxDialog()">继续编辑</a>]';
				$dialog [] = '</p>';
				return NuiAjaxView::dialog ( implode ( '', $dialog ), '更新完成!', array ('model' => true,'height' => 'auto','func' => 'commentSaved' ) );
			}
			return NuiAjaxView::error ( '无法更新评论,数据库出错.' );
		}
		return NuiAjaxView::validate ( 'CommentEditForm', '表单数据校验失败.', $form->getErrors () );
	}
	/**
	 * 管理员回复.
	 *
	 * @param unknown $id        	
	 * @param unknown $content        	
	 * @return NuiAjaxView
	 */
	public function reply_post($id, $content) {
		$id = intval ( $id );
		$content = trim ( $content );
		if ($id && $content) {
			$comment = dbselect ( '*' )->from ( '{comments}' )->where ( array ('id' => $id,'deleted' => 0 ) )->get ( 0 );
			if ($comment) {
				$data ['create_uid'] = $date ['update_uid'] = $this->user->getUid ();
				$data ['create_time'] = $data ['update_time'] = time ();
				$data ['parent'] = $id;
				$data ['content'] = $content;
				$data ['author_ip'] = $_SERVER ['REMOTE_ADDR'];
				$data ['author'] = $this->user->getDisplayName ();
				$data ['author_email'] = $this->user->getEmail ();
				$data ['status'] = 1;
				$data ['page_id'] = $comment ['page_id'];
				$rst = dbinsert ( $data )->into ( '{comments}' )->exec ();
				if ($rst) {
					if (icando ( 'a:comment' )) {
						dbupdate ( '{comments}' )->set ( array ('status' => 1 ) )->where ( array ('id' => $id ) )->exec ();
					}
					return NuiAjaxView::reload ( '#comment-table', '成功回复评论' );
				} else {
					return NuiAjaxView::error ( '不能将数据写入数据库.' );
				}
			}
		}
		return NuiAjaxView::error ( '评论不存在.' );
	}
	/**
	 * 会员评论.
	 */
	public function post_post() {
		$allow_anonymouse = bcfg ( 'allow_anonymouse@comment' );
		$enable_captcha = bcfg ( 'enable_captcha@comment' );
		$interval = icfg ( 'interval@comment', 60 );
		$last_time = sess_get ( 'last_post_comment', 0 );
		if ($last_time > 0 && (time () - $last_time) < $interval) {
			return NuiAjaxView::error ( '你评论地太快了,请等等.' );
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
		
		$form = new CommentVipPostForm ();
		$data = $form->valid ();
		if ($data) {
			$data ['create_uid'] = $date ['update_uid'] = $uid;
			$data ['create_time'] = $data ['update_time'] = time ();
			$data ['status'] = 0;
			$data ['author_ip'] = $_SERVER ['REMOTE_ADDR'];
			$rst = dbinsert ( $data )->into ( '{comments}' )->exec ();
			if ($rst) {
				$data ['id'] = $rst [0];
				$_SESSION ['last_post_comment'] = time ();
				return NuiAjaxView::ok ( '评论成功', false, $data );
			} else {
				return NuiAjaxView::error ( '不能将数据写入数据库.' );
			}
		}
		return NuiAjaxView::validate ( 'CommentVipPostForm', '表单校验失败.', $form->getErrors () );
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
		$rows = dbselect ( 'C.*,CC.author AS pauthor' )->from ( '{comments} AS C' )->limit ( ($_cp - 1) * $_lt, $_lt );
		
		$rows->join ( '{comments} AS CC', 'C.parent = CC.id' );
		
		$rows->sort ( $_sf, $_od );
		
		$where ['C.deleted'] = 0;
		
		$status = rqst ( 'status' );
		
		if (is_numeric ( $status )) {
			$where ['C.status'] = intval ( $status );
		}
		
		$rows->field ( 'CP.url,CH.root,CP.title,CP.title2' );
		$ccnt = dbselect ( imv ( 'COUNT(CCNT.id)' ) )->from ( '{comments} AS CCNT' )->where ( array ('CCNT.page_id' => imv ( 'C.page_id' ) ) );
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
		}
		$keywords = rqst ( 'keywords' );
		if ($keywords) {
			$where ['C.content LIKE'] = '%' . $keywords . '%';
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
		$data ['rows'] = $rows;
		$data ['canDelComment'] = icando ( 'd:comment' );
		$data ['canApproveComment'] = icando ( 'a:comment' );
		$data ['canEditComment'] = icando ( 'u:comment' );
		$data ['canReplyComment'] = icando ( 'reply:comment' );
		
		return view ( 'data.tpl', $data );
	}
}
