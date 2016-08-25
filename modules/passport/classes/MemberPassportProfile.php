<?php
class MemberPassportProfile {
	private $error;
	private $connected = false;
	private $url;
	private $sync = false;
	private $appkey;
	private $appsecret;
	public function __construct() {
		$this->connected = bcfg ( 'connect_to@passport' );
		$this->url = cfg ( 'passport_rest_url@passport' );
		$this->sync = bcfg ( 'sync_member@passport' ) || ! $this->connected;
		$this->appkey = cfg ( 'appkey@rest' );
		$this->appsecret = cfg ( 'appsecret@rest' );
	}
	/**
	 * update member profile.
	 *
	 * @param array $data        	
	 * @param int $mid        	
	 * @return boolean
	 */
	public function update($data, $mid) {
		$rst = true;
		$data ['mid'] = $mid;
		if ($this->connected) {
			$client = new RestClient ( $this->url, $this->appkey, $this->appsecret, '1' );
			$rtn = $client->post ( 'passport.member.update', $data );
			if ('0' != $rtn ['error']) {
				$rst = false;
				$this->error = $rtn ['message'];
			}
		}
		if ($rst && $this->sync) {
			$service = new MemberRestService ();
			$rtn = $service->rest_post_update ( $data, $this->appkey, $this->appsecret );
			if ('0' != $rtn ['error']) {
				$rst = false;
				$this->error = $rtn ['message'];
			}
		}
		return $rst;
	}
	public function update_passwd($data, $mid) {
		$rst = true;
		$data ['mid'] = $mid;
		if ($this->connected) {
			$client = new RestClient ( $this->url, $this->appkey, $this->appsecret, '1' );
			$rtn = $client->post ( 'passport.member.change_password', $data );
			if ('0' != $rtn ['error']) {
				$rst = false;
				$this->error = $rtn ['message'];
			}
		}
		if ($rst && $this->sync) {
			$service = new MemberRestService ();
			$rtn = $service->rest_post_change_password ( $data, $this->appkey, $this->appsecret );
			if ('0' != $rtn ['error']) {
				$rst = false;
				$this->error = $rtn ['message'];
			}
		}
		return $rst;
	}
	public function update_avatar($data, $mid) {
		$rst = true;
		$avatar = array ();
		$data ['mid'] = $mid;
		if ($this->connected) {
			$client = new RestClient ( $this->url, $this->appkey, $this->appsecret, '1' );
			$rtn = $client->post ( 'passport.member.update_avatar', $data );
			if ('0' != $rtn ['error']) {
				$rst = false;
				$this->error = $rtn ['message'];
			} else {
				$avatar = $rst;
			}
		}
		if ($rst && $this->sync) {
			if (! $avatar) {
				$service = new MemberRestService ();
				$rtn = $service->rest_post_update_avatar ( $data, $this->appkey, $this->appsecret );
				if ('0' != $rtn ['error']) {
					$this->error = $rtn ['message'];
				} else {
					$avatar = $rtn;
				}
			} else {
				dbupdate ( '{member}' )->set ( $avatar )->where ( array ('mid' => $mid ) )->exec ();
			}
		}
		return $avatar;
	}
	public function getError() {
		return $this->error;
	}
}