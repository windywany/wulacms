<?php
class UCHomeTheme implements IPassportTheme {
	public function getStyles() {
		return array ('default' => '默认','blue' => '蓝色','green' => '绿色' );
	}
	/*
	 * (non-PHPdoc) @see IPassportTheme::active()
	 */
	public function active() {
		return 't1/active.tpl';
	}
	
	/*
	 * (non-PHPdoc) @see IPassportTheme::actived()
	 */
	public function actived() {
		return 't1/actived.tpl';
	}
	
	/*
	 * (non-PHPdoc) @see IPassportTheme::done()
	 */
	public function done() {
		return 't1/done.tpl';
	}
	
	/*
	 * (non-PHPdoc) @see IPassportTheme::forgetpassword()
	 */
	public function forgetpassword() {
		return 't1/reset.tpl';
	}
	
	/*
	 * (non-PHPdoc) @see IPassportTheme::join()
	 */
	public function join() {
		return 't1/join.tpl';
	}
	
	/*
	 * (non-PHPdoc) @see IPassportTheme::login()
	 */
	public function login() {
		return 't1/login.tpl';
	}
	/*
	 * (non-PHPdoc) @see IPassportTheme::active_email()
	 */
	public function active_mail($user) {
		$mail ['subject'] = '请激活您在' . cfg ( 'site_name' ) . '的账户';
		$mail ['type'] = 'html';
		$view = view ( 'passport/views/t1/active_mail.tpl', $user );
		$mail ['content'] = $view->render ();
		return $mail;
	}
	public function forgetpassword_mail($user) {
		$mail ['subject'] = '找回密码 - ' . cfg ( 'site_name' );
		$mail ['type'] = 'html';
		$view = view ( 'passport/views/t1/reset_mail.tpl', $user );
		$mail ['content'] = $view->render ();
		return $mail;
	}
}
