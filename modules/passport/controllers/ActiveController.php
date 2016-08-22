<?php
/**
 * 激活用户.
 * @author Guangfeng
 *
 */
class ActiveController extends AbstractPassportController {
	/**
	 * 激活账户.
	 *
	 * @param number $uid        	
	 * @param number $resend        	
	 * @param string $code        	
	 * @return Ambigous <View, SmartyView>|SmartyView
	 */
	public function index($uid = 0, $resend = 0, $code = false) {
		if ($code) {
			return $this->active ( $code );
		} else {
			if (empty ( $uid )) {
				Response::respond ( 404 );
			}
			$user = dbselect ( '*' )->from ( '{member}' )->where ( array ('mid' => $uid ) )->get ( 0 );
			if (! $user) {
				Response::respond ( 404 );
			}
			$user = apply_filter ( "load_member_data", $user );
			$data = $user;
			$data ['uid'] = $uid;
			$data ['join_url'] = cfg ( 'join_url@passport', DETECTED_ABS_URL );
			if ($user ['status'] != '2' && $user ['status'] != '3') {
				return view ( $this->theme->actived (), $data );
			}
			if (empty ( $user ['email'] )) {
				Response::respond ( 404 );
			}
			if ($user ['status'] == '2' || $resend) {
				if (empty ( $user ['email'] )) {
					Response::respond ( 404 );
				}
				$time = time ();
				$active ['mid'] = $uid;
				$active ['update_time'] = $time;
				$active ['mail_active_code'] = md5 ( rand_str ( 10 ) . $user ['email'] );
				$active ['mail_active_code_expire'] = $time + intval ( cfg ( 'code_expire@passport', 24 ) ) * 3600;
				$mail = $this->theme->active_mail ( array_merge ( $data, $active ) );
				if (! $mail) {
					Response::respond ( 500, 'System Error.' );
				}
				$mail = array_merge ( array ('type' => 'html','subject' => __ ( 'Please active your account.' ),'content' => '' ), $mail );
				
				if (sendmail ( $user ['email'], $mail ['subject'], $mail ['content'], array (), $mail ['type'] )) {
					if (dbselect ()->from ( 'member_activation' )->where ( array ('mid' => $uid ) )->exist ( 'mid' )) {
						dbupdate ( '{member_activation}' )->set ( $active )->where ( array ('mid' => $uid ) )->exec ();
					} else {
						$active ['create_time'] = $time;
						dbinsert ( $active )->into ( '{member_activation}' )->exec ();
					}
					if ($user ['status'] == '2') {
						dbupdate ( '{member}' )->set ( array ('status' => 3 ) )->where ( array ('mid' => $uid ) )->exec ();
					}
				} else {
					Response::respond ( 500, 'System Error - Can not send activation mail.' );
				}
			}
			return view ( $this->theme->active (), $data );
		}
	}
	/**
	 * 激活.
	 *
	 * @param string $code
	 *        	激活码.
	 * @return View
	 */
	private function active($code = '') {
		if (empty ( $code )) {
			Response::respond ( 404 );
		}
		$activation = dbselect ( '*' )->from ( '{member_activation}' )->where ( array ('mail_active_code' => $code ) )->get ( 0 );
		if (! $activation) {
			Response::respond ( 404 );
		}
		$uid = $activation ['mid'];
		$time = time ();
		$expire = $activation ['mail_active_code_expire'];
		
		$user = dbselect ( '*' )->from ( '{member}' )->where ( array ('mid' => $uid ) )->get ( 0 );
		
		if (! $user) {
			Response::respond ( 404 );
		}
		if ($user ['status'] == '3') {
			if ($time > $expire) {
				Response::redirect ( tourl ( 'passport/active' ) ) . $uid . '/1';
			}
			dbupdate ( '{member}' )->set ( array ('status' => 1 ) )->where ( array ('mid' => $uid ) )->exec ();
			$user ['status'] = 1;
			$user = apply_filter ( "load_memeber_data", $user );
			$user ['join_url'] = cfg ( 'join_url@passport', DETECTED_ABS_URL );
			return view ( $this->theme->actived (), $user );
		} else {
			Response::redirect ( tourl ( 'passport/join/done/' ) . $uid );
		}
	}
}