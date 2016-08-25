<?php
/**
 * 用户数据访问对象。
 * @author ngf
 *
 */
class UserDao {
	private $client;
	private $appSecret;
	public function __construct() {
		if (bcfg ( 'enable_client@corepst' )) {
			$url = cfg ( 'url@corepst' );
			$appKey = cfg ( 'appkey@corepst' );
			$this->appSecret = $appSecret = cfg ( 'appsecret@corepst' );
			$this->client = new RestClient ( $url, $appKey, $appSecret );
		}
	}
	public function getUserInfo($user_id) {
		if ($this->client) {
			$user ['user_id'] = $user_id;
			$rst = $this->client->post ( 'passport.admin.getUser', $user );
			if (isset ( $rst ['user'] )) {
				return $rst ['user'];
			} else {
				return false;
			}
		} else {
			return dbselect ( '*' )->from ( '{user}' )->where ( array ('user_id' => $user_id ) )->get ( 0 );
		}
	}
	public function updateUser($user, $user_id) {
		if ($this->client) {
			if (isset ( $user ['passwd'] )) {
				$user ['passwd'] = authcode ( $user ['passwd'], 'ENCODE', $this->appSecret );
			}
			$user ['user_id'] = $user_id;
			$rst = $this->client->post ( 'passport.admin.updateUser', $user );
			if ($rst ['result']) {
				return true;
			} else {
				return false;
			}
		} else {
			return dbupdate ( '{user}' )->set ( $user )->where ( array ('user_id' => $user_id ) )->exec ();
		}
	}
	public function insertUser($user) {
		if ($this->client) {
			if (isset ( $user ['passwd'] )) {
				$user ['passwd'] = authcode ( $user ['passwd'], 'ENCODE', $this->appSecret );
			}
			$user_id = 0;
			$rst = $this->client->post ( 'passport.admin.insertUser', $user );
			if (isset ( $rst ['user_id'] )) {
				$user_id = $rst ['user_id'];
			}
		} else {
			$rst = dbinsert ( $user )->into ( '{user}' )->exec ();
			$user_id = $rst [0];
		}
		return $user_id;
	}
	public function checkDuplicate($field, $value, $user_id) {
		if ($this->client) {
			$rst = $this->client->get ( 'passport.admin.validate', array ('field' => $field,'value' => $value,'user_id' => $user_id ) );
			if (isset ( $rst ['valid'] )) {
				return $rst ['valid'];
			}
			return false;
		} else {
			$rst = dbselect ( 'user_id' )->from ( '{user}' );
			$where [$field] = $value;
			if (! empty ( $user_id )) {
				$where ['user_id !='] = $user_id;
			}
			$rst->where ( $where );
			if ($rst->count ( 'user_id' ) > 0) {
				return false;
			}
			return true;
		}
	}
}
?>