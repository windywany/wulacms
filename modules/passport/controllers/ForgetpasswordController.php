<?php
/**
 * 找回密码.
 * @author Guangfeng
 *
 */
class ForgetpasswordController extends AbstractPassportController {
	public function index() {
		$data ['captcha'] = true;
		$form_id = rand_str ( 10 );
		$_SESSION ['_join_form_id'] = $form_id;
		$data ['_form_id'] = $form_id;
		$data ['enableOAuth'] = bcfg ( 'enable_oauth@passport' );
		if($data['enableOAuth']){
			$data['oauthVendors'] = PassportPluginImpl::getOauthVendors();
		}
		$data ['step'] = 1;
		$data ['join_url'] = cfg ( 'join_url@passport', DETECTED_ABS_URL );
		return view ( $this->theme->forgetpassword (), $data );
	}
	public function index_post($_form_id) {
		$form_id = sess_del ( '_join_form_id' );
		if ($form_id != $_form_id) {
			$message = '表单提交超时,请重新提交.';
		} else {
			$email = trim ( rqst ( 'username' ) );
			
			$auth_code_obj = new CaptchaCode ();
			if (! $auth_code_obj->validate ( rqst ( 'captcha' ), false )) {
				$message = '验证码错误,请重新输入.';
			} else if (empty ( $email )) {
				$message = '请输入邮件地址.';
			} else {
				$user = dbselect ( '*' )->from ( '{member}' )->where ( array ('email' => $email,'deleted' => 0 ) )->get ( 0 );
			}
			if (empty ( $user )) {
				$message = '用户不存在.';
			} else {
				$time = time ();
				$data ['mid'] = $user ['mid'];
				$data ['new_password'] = rand_str ( 12 );
				$data ['user'] = $user;
				$data ['join_url'] = cfg ( 'join_url@passport', DETECTED_ABS_URL );
				$mail = $this->theme->forgetpassword_mail ( $data );
				
				if (! $mail) {
					Response::respond ( 500, 'System Error - Can not send email.' );
				}
				$mail = array_merge ( array ('type' => 'html','subject' => __ ( 'Reset your password.' ),'content' => '' ), $mail );
				if (sendmail ( $user ['email'], $mail ['subject'], $mail ['content'], array (), $mail ['type'] )) {
					$u ['passwd'] = md5 ( $data ['new_password'] );
					dbupdate ( '{member}' )->set ( $u )->where ( array ('mid' => $user ['mid'] ) )->exec ();
				} else {
					Response::respond ( 500, 'System Error -Can not send email.' );
				}
			}
		}
		if ($message) {
			Response::redirect ( tourl ( 'passport/forgetpassword' ) );
		} else {
			$data ['join_url'] = cfg ( 'join_url@passport', DETECTED_ABS_URL );
			$data ['enableOAuth'] = bcfg ( 'enable_oauth@passport' );
			if($data['enableOAuth']){
				$data['oauthVendors'] = PassportPluginImpl::getOauthVendors();
			}
			$data ['step'] = 2;
			return view ( $this->theme->forgetpassword (), $data );
		}
	}
}
