<?php
/**
 * 用户注册.
 * @author Guangfeng
 *
 */
class JoinController extends AbstractPassportController {
	public function preRun($method) {
		if (! bcfg ( 'allow_join@passport' ) || bcfg ( 'connect_to@passport' )) {
			Response::respond ( 404 );
		}
		$this->setTheme ();
	}
	/**
	 * 注册页面.
	 *
	 * @param string $from
	 *        	推荐码(邀请码)
	 * @return SmartyView
	 */
	public function index($from = '') {
		$form = new PassportJoinForm ();
		$data ['rules'] = $form->rules ();
		$data ['captcha'] = bcfg ( 'enable_captcha@passport', true );
		$data ['agreement'] = cfg ( 'agree@passport' );
		$form_id = rand_str ( 10 );
		$_SESSION ['_join_form_id'] = $form_id;
		$data ['_form_id'] = $form_id;
		$data ['enableOAuth'] = bcfg ( 'enable_oauth@passport' );
		if($data['enableOAuth']){
			$data['oauthVendors'] = PassportPluginImpl::getOauthVendors();
		}
		$data ['enablePhone'] = bcfg ( 'enable_phone@passport' );
		$data ['enableInvation'] = bcfg ( 'enable_invation@passport' );
		$data ['inviteRequired'] = $data ['enableInvation'] && bcfg ( 'invite_required@passport' ) ? 'true' : 'false';
		$data ['user'] = sess_del ( 'reg_user_data', array () );
		if (! isset ( $data ['user'] ['invite_code'] )) {
			if ($from) {
				if (dbselect ()->from ( '{member}' )->where ( array ('recommend_code' => $from ) )->exist ( 'mid' )) {
					$_SESSION ['passport_invite_code'] = $from;
				}
			}
		}
		if (isset ( $_SESSION ['passport_invite_code'] )) {
			$data ['user'] ['invite_code'] = $_SESSION ['passport_invite_code'];
			$data ['bind_invite_code'] = true;
		}
		$data ['error_msg'] = sess_del ( 'reg_error_message' );
		return view ( $this->theme->join (), $data );
	}
	/**
	 * 注册提交处理器.
	 *
	 * @return NuiAjaxView
	 */
	public function index_post($type = 'mail', $_form_id) {
		if (empty ( $_form_id ) || $_form_id != sess_get ( '_join_form_id' )) {
			Response::respond ( 404 );
		}
		$form = new PassportJoinForm ();
		if (! bcfg ( 'enable_captcha@passport', true )) {
			$form->removeRlue ( 'captcha', 'required' );
		}
		if ($type == 'mail') {
			$form->removeRlue ( 'phone', 'required' );
			$form->removeRlue ( 'phone_code', 'required' );
		} else if ($type == 'phone' && bcfg ( 'enable_phone@passport' )) {
			$form->removeRlue ( 'username', 'required' );
			$form->removeRlue ( 'email', 'required' );
		} else {
			Response::respond ( 404 );
		}
		$user = $form->valid ();
		if ($user) {
			$user ['group_id'] = intval ( cfg ( 'default_group@passport', 0 ) );
			$user ['passwd'] = md5 ( $user ['passwd'] );
			unset ( $user ['passwd1'], $user ['user_id'], $user ['captcha'], $user ['phone_code'] );
			$user ['username'] = rand_str ( 12 );
			if (empty ( $user ['nickname'] )) {
				$user ['nickname'] = $type == 'mail' ? substr ( $user ['email'], 0, strpos ( $user ['email'], '@' ) ) : $user ['phone'];
			}
			$user ['status'] = 1;
			$user ['registered'] = $user ['update_time'] = time ();
			$user ['ip'] = $_SERVER ['REMOTE_ADDR'];
			start_tran ();
			$user_id = $this->registerMember ( $user, $type );
			if ($user_id) {
				commit_tran ();
				sess_del ( 'reg_user_data' );
				sess_del ( 'reg_error_message' );
				if (bcfg ( 'enable_active@passport' ) && $type == 'mail') {
					Response::redirect ( tourl ( 'passport/active/' . $user_id ) );
				} else {
					Response::redirect ( tourl ( 'passport/join/done/' . $user_id ) );
				}
			} else {
				rollback_tran ();
				$_SESSION ['reg_user_data'] = $form->toArray ();
				$_SESSION ['reg_error_message'] = '内部错误,无法完成注册.';
				Response::redirect ( tourl ( 'passport/join' ) );
			}
		} else {
			$_SESSION ['reg_user_data'] = $form->toArray ();
			$_SESSION ['reg_error_message'] = '<p>' . implode ( '</p><p>', $form->getErrors () ) . '</p>';
			Response::redirect ( tourl ( 'passport/join' ) );
		}
	}
	/**
	 * 注册完成显示页.
	 *
	 * @param number $uid        	
	 * @return SmartyView
	 */
	public function done($uid = 0) {
		$uid = intval ( $uid );
		if (empty ( $uid )) {
			Response::redirect ( tourl ( 'passport/join' ) );
		}
		$user = dbselect ( '*' )->from ( '{member}' )->where ( array ('mid' => $uid ) )->get ( 0 );
		if (! $user) {
			Response::redirect ( tourl ( 'passport/join' ) );
		}
		$user = apply_filter ( "load_member_data", $user );
		$data ['user'] = $user;
		$data ['join_url'] = cfg ( 'join_url@passport', DETECTED_ABS_URL );
		return view ( $this->theme->done (), $data );
	}
	/**
	 * 检验用户名,邮箱或手机是否存在.
	 *
	 * @param string $type
	 *        	[name|phone|email|code] 中的一个.
	 * @param string $value        	
	 */
	public function validate($type = 'name', $value) {
		$form = new PassportJoinForm ( null, false );
		$message = '';
		switch ($type) {
			case 'name' :
				$rst = $form->checkUsername ( $value, array (), false );
				$message = '用户名已经存在';
				break;
			case 'phone' :
				$rst = $form->checkPhone ( $value, array (), false );
				$message = '手机号已经存在';
				break;
			case 'mail' :
				$rst = $form->checkEmail ( $value, array (), false );
				$message = '邮箱已经存在';
				break;
			case 'code' :
				$rst = $form->checkInviteCode ( $value, array (), false );
				$message = '推荐编号不存在';
				break;
			default :
				$rst = false;
				$message = '未知错误';
				break;
		}
		$data ['success'] = $rst;
		if (! $rst) {
			$data ['msg'] = $message;
		}
		return new JsonView ( $data );
	}
	/**
	 * 注册会员.
	 *
	 * @param unknown $user        	
	 * @return Ambigous <>|boolean
	 */
	private function registerMember($user, $type) {
		$user ['role_id'] = intval ( cfg ( 'default_role@passport', 0 ) );
		if (bcfg ( 'enable_active@passport' ) && $type == 'mail') {
			$user ['status'] = 2;
		}
		if (! empty ( $user ['invite_code'] )) {
			$user ['invite_mid'] = dbselect ()->from ( '{member}' )->where ( array ('recommend_code' => $user ['invite_code'] ) )->get ( 'mid' );
		}
		if (empty ( $user ['invite_mid'] )) {
			unset ( $user ['invite_mid'] );
		}
		// $user ['recommend_code'] = uniqid ( 'r' );
		$rst = dbinsert ( $user )->into ( '{member}' )->exec ();
		if ($rst) {
			$user ['mid'] = $rst [0];
			$user = apply_filter ( 'after_member_created', $user );
			if ($user) {
				return $rst [0];
			}
		}
		return false;
	}
}