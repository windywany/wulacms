<?php

namespace passport\classes;

use redis\Redis4p;

class PassportResetService {
	const DB = 10;
	private $dialect = null;

	/**
	 * 手机号登录.
	 *
	 * @param array  $params
	 *               phone => 手机号码
	 *               password => 密码
	 * @param string $key
	 * @param string $secret
	 *
	 * @return array
	 */
	public function rest_post_login($params, $key, $secret) {
		if (empty($params['phone'])) {
			return ['error' => 401, 'message' => '手机号为空'];
		}
		if (empty($params['password'])) {
			return ['error' => 402, 'message' => '密码为空'];
		}
		$this->getDialect();
		$user = dbselect('*')->from('{member}')->where(['phone' => $params['phone']])->setDialect($this->dialect)->get();

		if (!$user) {
			return ['error' => 404, 'message' => '用户名或密码错误'];
		}

		$passwd = \MemberModelForm::generatePwd($params['password'], $user['salt']);

		if ($passwd != $user['passwd']) {
			return ['error' => 404, 'message' => '用户名或密码错误'];
		}

		return $this->doLogin($user);
	}

	/**
	 * @param array  $params
	 * @param string $key
	 * @param string $secret
	 *
	 * @return array
	 */
	public function rest_post_info($params, $key, $secret) {
		$token = get_condition_value('token', $params);
		if (empty($token)) {
			return ['error' => 400, 'message' => 'token参数错误'];
		}
		Redis4p::select(self::DB);
		$info = Redis4p::getJSON($token);
		if (!$info) {
			return ['error' => 401, 'message' => '用户未登录'];
		}

		return ['error' => 0, 'data' => $info];
	}

	/**
	 * 退出.
	 *
	 * @param array  $params
	 * @param string $key
	 * @param string $secret
	 *
	 * @return array
	 */
	public function rest_post_logout($params, $key, $secret) {

		if (empty($params['token'])) {
			return ['error' => 401, 'message' => 'token为空'];
		}

		try {
			$redis = Redis4p::getRedis(self::DB);
			$redis->del($params['token']);
		} catch (\Exception $e) {
			return ['error' => 500, 'message' => '内部错误'];
		}

		return ['error' => 0, 'data' => ['ok' => 1]];
	}

	/**
	 * 修改密码.
	 *
	 * @param array  $params
	 * @param string $key
	 * @param string $secret
	 *
	 * @return array
	 */
	public function rest_post_reset($params, $key, $secret) {
		if (!defined('REST_SESSION_ID')) {
			return ['error' => 503, 'message' => '请开启会话'];
		}
		$token = get_condition_value('token', $params);
		if (empty($token)) {
			return ['error' => 400, 'message' => 'token参数错误'];
		}

		if (empty($params['password'])) {
			return ['error' => 402, 'message' => '密码为空'];
		}
		if (strlen($params['password']) < 6) {
			return ['error' => 404, 'message' => '密码最少6个字符'];
		}
		if (empty($params['code']) || !ResetPasswdSms::validate($params['code'])) {
			return ['error' => 403, 'message' => '验证码不正确'];
		}

		Redis4p::select(self::DB);
		$info = Redis4p::getJSON($token);
		if (!$info) {
			return ['error' => 401, 'message' => '用户未登录'];
		}
		$mid             = $info['mid'];
		$user ['salt']   = rand_str(64);
		$user ['passwd'] = \MemberModelForm::generatePwd($params['password'], $user['salt']);
		$this->getDialect();
		if (!dbupdate('{member}')->set($user)->setDialect($this->dialect)->where(['mid' => $mid])->exec()) {
			return ['error' => 500, 'message' => '内部错误'];
		}

		return ['error' => 0, 'data' => ['ok' => 1]];
	}

	/**
	 * 第三方登录(绑定).
	 *
	 * @param array  $params
	 * @param string $key
	 * @param string $secret
	 *
	 * @return array
	 */
	public function rest_post_oauth($params, $key, $secret) {

		$token = get_condition_value('token', $params);

		$app = get_condition_value('app', $params);
		if (empty($app)) {
			return ['error' => 601, 'message' => '第三方应用为空'];
		}
		$oauth['app'] = $app;

		$openid = get_condition_value('openid', $params);
		if (empty($openid)) {
			return ['error' => 602, 'message' => 'OPENID为空'];
		}
		$oauth['openid'] = $openid;
		//推荐码
		$recode = get_condition_value('recode', $params);
		if ($recode && !apply_filter('varify_member_recommend_code', $recode)) {
			return ['error' => 605, 'message' => '邀请码不可用'];
		}
		$oauth['rec_code']    = $recode;
		$oauth['device']      = intval(get_condition_value('device', $params, 0));
		$oauth['deviceId']    = get_condition_value('deviceId', $params);
		$oauth['channel']     = get_condition_value('channel', $params);
		$oauth['nickname']    = get_condition_value('nickname', $params);
		$oauth['ip']          = \Request::getIp();
		$oauth['create_time'] = $oauth['update_time'] = time();
		// 头像
		$avatar = get_condition_value('avatar', $params);
		$this->getDialect();
		$dialect = $this->dialect;
		if ($token) {
			// 绑定操作
			if (dbselect('id')->from('{member_oauth}')->setDialect($dialect)->where(['app' => $app, 'openid' => $openid])->exist('id')) {
				return ['error' => 604, 'message' => '此账户已经绑定过了.'];
			}

			$info = self::loginInfo($token);
			if (!$info) {
				return ['error' => 603, 'message' => '用户未登录'];
			}
			$oauth['mid'] = $info['mid'];
			$rst          = dbinsert($oauth)->setDialect($dialect)->into('{member_oauth}')->exec();
			if (!$rst) {
				return ['error' => 606, 'message' => '绑定失败'];
			}
			$info['oauth'][] = $app;
			$rst             = ['error' => 0, 'data' => ['oauth' => $info['oauth']]];

			if ($avatar && !dbselect()->from('{member_meta}')->setDialect($dialect)->where(['mid' => $oauth['mid'], 'name' => 'uavatar'])->exist('mid')) {
				dbupdate('{member}')->set(['avatar' => $avatar])->setDialect($dialect)->where(['mid' => $oauth['mid']])->exec();
				$rst['data']['avatar'] = $avatar;
			}

			if ($oauth['nickname'] && !dbselect()->from('{member_meta}')->setDialect($dialect)->where(['mid' => $oauth['mid'], 'name' => 'unick'])->exist('mid')) {
				dbupdate('{member}')->set(['nickname' => $oauth['nickname']])->setDialect($dialect)->where(['mid' => $oauth['mid']])->exec();
				$rst['data']['nickname'] = $oauth['nickname'];
				self::updateLoginInfo($token, 'nickname', $oauth['nickname']);
			}

			fire('on_passport_oauth_bind', $oauth, $params, $dialect);

			self::updateLoginInfo($token, 'oauth', $info['oauth']);

			return $rst;
		} else if (!dbselect('id')->from('{member_oauth}')->setDialect($dialect)->where(['app' => $app, 'openid' => $openid])->exist('id')) {
			// 此时需要注册用户.
			$dt = cfg('ds@passport', 'default');
			start_tran($dt);
			// 用户数据
			$user ['group_id']     = 0;
			$user ['group_expire'] = 0;
			$user ['phone']        = '';
			$user ['salt']         = rand_str(64);
			$user ['passwd']       = \MemberModelForm::generatePwd(rand_str(12), $user['salt']);
			$user ['username']     = uniqid();
			$user ['nickname']     = $oauth['nickname'];
			$user ['status']       = 1;
			$user ['registered']   = $user ['update_time'] = time();
			$user ['ip']           = $oauth['ip'];
			$user['avatar']        = $avatar;
			$uid                   = dbinsert($user)->into('{member}')->setDialect($dialect)->exec();
			if (!$uid) {
				rollback_tran($dt);

				return ['error' => 607, 'message' => '登录失败'];
			}
			$oauth['mid'] = $uid[0];
			// 插入第三方登录信息
			$rst = dbinsert($oauth)->setDialect($dialect)->into('{member_oauth}')->exec();
			if (!$rst) {
				rollback_tran($dt);

				return ['error' => 608, 'message' => '登录失败(1)'];
			}
			// 头像已经设置，除非用户手动设置否则不能再变。
			if ($avatar && !dbselect()->from('{member_meta}')->setDialect($dialect)->where(['mid' => $oauth['mid'], 'name' => 'uavatar'])->exist('mid')) {
				dbinsert(['mid' => $oauth['mid'], 'name' => 'uavatar', 'create_time' => $user ['registered'], 'update_time' => $user ['registered'], 'value' => '1'])->into('{member_meta}')->setDialect($dialect)->exec();
			}
			if ($oauth['nickname'] && !dbselect()->from('{member_meta}')->setDialect($dialect)->where(['mid' => $oauth['mid'], 'name' => 'unick'])->exist('mid')) {
				dbinsert(['mid' => $oauth['mid'], 'name' => 'unick', 'create_time' => $user ['registered'], 'update_time' => $user ['registered'], 'value' => '1'])->into('{member_meta}')->setDialect($dialect)->exec();
			}
			fire('on_passport_oauth_bind', $oauth, $params, $dialect);
			commit_tran($dt);
		}

		// 登录操作
		if (!isset($oauth['mid'])) {
			$oauth['mid'] = dbselect('mid')->from('{member_oauth}')->setDialect($dialect)->where(['app' => $app, 'openid' => $openid])->get('mid');
		}
		if (!$oauth['mid']) {
			return ['error' => 608, 'message' => '登录失败(2)'];
		}

		$user = dbselect('*')->from('{member}')->setDialect($dialect)->where(['mid' => $oauth['mid']])->get();
		if (!$user) {
			return ['error' => 608, 'message' => '登录失败(3)'];
		}

		return $this->doLogin($user);
	}

	/**
	 * 手机号注册.
	 *
	 * @param array  $params
	 * @param string $key
	 * @param string $secret
	 *
	 * @return array
	 */
	public function rest_post_register($params, $key, $secret) {
		if (!defined('REST_SESSION_ID')) {
			return ['error' => 403, 'message' => '请开启会话'];
		}
		$phone    = get_condition_value('phone', $params);
		$password = get_condition_value('password', $params);
		$code     = get_condition_value('code', $params);
		$token    = get_condition_value('token', $params);
		$mid      = 0;
		if ($token) {
			$info = self::loginInfo($token);
			if (!$info) {
				return ['error' => 401, 'message' => '用户未登录'];
			}
			$mid = $info['mid'];
		}
		$recode = get_condition_value('recode', $params);
		if (empty($code)) {
			return ['error' => 600, 'message' => '验证码不正确'];
		}

		if (!preg_match('/^1[34578]\d{9}$/', $phone)) {
			return ['error' => 601, 'message' => '手机号码不正确'];
		}

		if (strlen($password) < 6) {
			return ['error' => 602, 'message' => '密码最少6个字符'];
		}

		if ($recode && !apply_filter('varify_member_recommend_code', $recode)) {
			return ['error' => 605, 'message' => '邀请码不可用'];
		}

		if ($mid) {//绑定手机号
			if (!BindMobileSms::validate($code)) {
				return ['error' => 600, 'message' => '验证码不正确'];
			}
			$dialect = cfg('ds@passport', 'default');
			start_tran($dialect);
			$this->getDialect();
			if (dbselect()->from('{member}')->setDialect($this->dialect)->where(['phone' => $phone])->exist('mid')) {
				return ['error' => 603, 'message' => '手机号码已经存在'];
			}
			$user ['phone']  = $phone;
			$user ['salt']   = rand_str(64);
			$user ['passwd'] = \MemberModelForm::generatePwd($password, $user['salt']);
			if (dbupdate('{member}')->set($user)->setDialect($this->dialect)->where(['mid' => $mid])->exec()) {
				commit_tran($dialect);

				return ['error' => 0, 'data' => ['mid' => $mid]];
			}
			rollback_tran($dialect);

			return ['error' => 606, 'message' => '绑定手机时出错'];
		} else {//注册新用户
			if (!RegCodeTemplate::validate($code)) {
				return ['error' => 600, 'message' => '验证码不正确'];
			}
			$this->getDialect();
			if (dbselect()->from('{member}')->setDialect($this->dialect)->where(['phone' => $phone])->exist('mid')) {
				return ['error' => 603, 'message' => '手机号码已经存在'];
			}
			// 用户数据
			$user ['group_id']     = 0;
			$user ['group_expire'] = 0;
			$user ['phone']        = $phone;
			$user ['salt']         = rand_str(64);
			$user ['passwd']       = \MemberModelForm::generatePwd($password, $user['salt']);
			$user ['username']     = uniqid();
			$user ['nickname']     = $phone;
			$user ['status']       = 1;
			$user ['registered']   = $user ['update_time'] = time();
			$user ['ip']           = \Request::getIp();

			// OAUTH数据
			$oauth['app']         = 'phone';
			$oauth['openid']      = $phone;
			$oauth['rec_code']    = $recode;
			$oauth['device']      = intval(get_condition_value('device', $params, 0));
			$oauth['deviceId']    = get_condition_value('deviceId', $params);
			$oauth['channel']     = get_condition_value('channel', $params);
			$oauth['ip']          = $user ['ip'];
			$oauth['create_time'] = $oauth['update_time'] = time();
			$dialect              = cfg('ds@passport', 'default');
			start_tran($dialect);

			$mid = dbinsert($user)->into('{member}')->setDialect($this->dialect)->exec();
			if ($mid) {
				$mid          = $mid[0];
				$oauth['mid'] = $mid;
				$oid          = dbinsert($oauth)->setDialect($this->dialect)->into('{member_oauth}')->exec();
				if ($oid) {
					commit_tran($dialect);

					return ['error' => 0, 'data' => ['mid' => $mid]];
				} else {
					$error   = 604;
					$message = '无法创建OAUTH数据';
				}
			} else {
				$error   = 500;
				$message = '内部错误';
			}

			rollback_tran($dialect);

			return ['error' => $error, 'message' => $message];
		}
	}

	/**
	 * 更新用户信息.
	 *
	 * @param array  $params
	 * @param string $key
	 * @param string $secret
	 *
	 * @return array
	 */
	public function rest_post_meta($params, $key, $secret) {
		$token = get_condition_value('token', $params);
		if (empty($token)) {
			return ['error' => 400, 'message' => 'token参数错误'];
		}
		$info = self::loginInfo($token);
		if (!$info) {
			return ['error' => 401, 'message' => '用户未登录'];
		}
		unset($params['ver'], $params['api'], $params['appkey'], $params['debug'], $params['token']);

		$user = [];
		$this->getDialect();
		$dialect = $this->dialect;
		$mid     = $info['mid'];
		foreach ($params as $key => $param) {
			if ($key == 'gender') {
				$user['gender'] = intval($param);
				$info['gender'] = intval($param);
				continue;
			}
			if ($key == 'nickname' && $param) {
				$user['nickname'] = $param;
				$info['nickname'] = $param;
				continue;
			}
			$this->updateUserMeta($mid, $key, $param, $dialect);
			$info['meta'][ $key ] = $param;
		}
		if ($user) {
			$time = time();
			dbupdate('{member}')->set($user)->where(['mid' => $mid])->setDialect($dialect)->exec();
			if (!dbselect()->from('{member_meta}')->setDialect($dialect)->where(['mid' => $mid, 'name' => 'unick'])->exist('mid')) {
				dbinsert(['mid' => $mid, 'name' => 'unick', 'create_time' => $time, 'update_time' => $time, 'value' => '1'])->into('{member_meta}')->setDialect($dialect)->exec();
			}
		}

		Redis4p::select(self::DB);
		if (!Redis4p::set($token, $info)) {
			return ['error' => 500, 'message' => '更新错误'];
		}

		return ['error' => 0, 'data' => ['ok' => 1]];
	}

	/**
	 * 上传用户头像.
	 *
	 * @param array  $params
	 * @param string $key
	 * @param string $secret
	 *
	 * @return array
	 */
	public function rest_post_avatar($params, $key, $secret) {
		$token = get_condition_value('token', $params);
		if (empty($token)) {
			return ['error' => 400, 'message' => 'token参数错误'];
		}
		$info = self::loginInfo($token);
		if (!$info) {
			return ['error' => 401, 'message' => '用户未登录'];
		}
		$mid = $info['mid'];
		if (isset ($_FILES ['file'] ['error']) && $_FILES ['file'] ['error']) {
			switch ($_FILES ['file'] ['error']) {
				case '1' :
					$error = '超过php.ini允许的大小。';
					break;
				case '2' :
					$error = '超过表单允许的大小。';
					break;
				case '3' :
					$error = '图片只有部分被上传。';
					break;
				case '4' :
					$error = '请选择图片。';
					break;
				case '6' :
					$error = '找不到临时目录。';
					break;
				case '7' :
					$error = '写文件到硬盘出错。';
					break;
				case '8' :
					$error = 'File upload stopped by extension。';
					break;
				case '999' :
				default :
					$error = '未知错误。';
			}

			return array('error' => 500 + $_FILES ['file'] ['error'], 'message' => $error);
		} else if (isset ($_FILES ['file'] ['tmp_name']) && is_uploaded_file($_FILES ['file'] ['tmp_name'])) {
			$file     = $_FILES ['file'];
			$name     = $file ['name'];
			$size     = $file ['size'];
			$tmp_file = $file ['tmp_name'];
			$destfile = TMP_PATH . $name;
			if ($size > \FileUploader::getMaxUploadSize()) {
				return array('error' => 403, 'message' => '文件太大啦，已经超出系统允许的最大值.');
			}
			if (move_uploaded_file($tmp_file, $destfile)) {
				$uploader = apply_filter('get_uploader', new \FileUploader ()); // 得到文件上传器
				$rst      = $uploader->save($destfile);
				@unlink($destfile);
				if ($rst) {
					$this->getDialect();
					$dialect = $this->dialect;
					$avatar  = the_media_src($rst[0]);

					dbupdate('{member}')->set(['avatar' => $rst[0]])->setDialect($dialect)->where(['mid' => $mid])->exec();

					if (!dbselect()->from('{member_meta}')->setDialect($dialect)->where(['mid' => $mid, 'name' => 'uavatar'])->exist('mid')) {
						$time = time();
						dbinsert(['mid' => $mid, 'name' => 'uavatar', 'create_time' => $time, 'update_time' => $time, 'value' => '1'])->into('{member_meta}')->setDialect($dialect)->exec();
					}

					return array('error' => 0, 'data' => ['avatar' => $avatar]);
				} else {
					return array('error' => 500, 'message' => $uploader->get_last_error());
				}
			} else {
				return array('error' => 402, 'message' => '无法保存文件.');
			}
		}

		return array('error' => 404, 'message' => '未指定要上传的头像');
	}

	/**
	 * 登录用户信息.
	 *
	 * @param string $token
	 *
	 * @return array
	 */
	public static function loginInfo($token) {
		Redis4p::select(self::DB);
		$info = Redis4p::getJSON($token);

		return $info;
	}

	/**
	 * 更新用户登录信息.
	 *
	 * @param string $token
	 * @param string $key
	 * @param mixed  $data
	 *
	 * @return bool
	 */
	public static function updateLoginInfo($token, $key, $data) {
		$info = self::loginInfo($token);
		if ($info) {
			$info[ $key ] = $data;

			return Redis4p::set($token, $info);
		}

		return false;
	}

	/**
	 * 获取dialect.
	 */
	private function getDialect() {
		$this->dialect = \DatabaseDialect::getDialect(cfg('ds@passport', 'default'));
	}

	/**
	 * 处理登录信息.
	 *
	 * @param array $user
	 *
	 * @return array
	 */
	private function doLogin($user) {
		if (empty($user['status'])) {
			return ['error' => 403, 'message' => '用户已经禁用'];
		}

		unset($user['salt'], $user['passwd'], $user['ip'], $user['deleted'], $user['update_time'], $user['update_uid'], $user['group_expire'], $user['email']);

		$user['login'] = time();

		dbupdate('{member}')->set(['lastip' => \Request::getIp(), 'lastlogin' => time()])->where(['mid' => $user['mid']])->setDialect($this->dialect)->exec();

		$token = uniqid();
		$user  = apply_filter('on_passport_login', $user, $this->dialect);
		// oauth
		$oauth = dbselect('app')->from('{member_oauth}')->where(['mid' => $user['mid']])->toArray('app');
		if ($oauth) {
			$user['oauth'] = array_diff($oauth, ['phone']);
		} else {
			$user['oauth'] = [];
		}
		// meta
		$meta         = dbselect('name,value')->from('{member_meta}')->where(['mid' => $user['mid'], 'app' => 1])->toArray('value', 'name');
		$user['meta'] = $meta ? $meta : [];
		Redis4p::select(self::DB);
		if (!Redis4p::set($token, $user)) {
			return ['error' => 500, 'message' => '内部错误'];
		}
		$user['token'] = $token;

		return ['error' => 0, 'data' => $user];
	}

	/**
	 * 更新用户meta信息.
	 *
	 * @param int              $mid
	 * @param string           $name
	 * @param string           $value
	 * @param \DatabaseDialect $dialect
	 */
	private function updateUserMeta($mid, $name, $value, $dialect) {
		$time = time();
		if (!dbselect()->from('{member_meta}')->setDialect($dialect)->where(['mid' => $mid, 'name' => $value])->exist('mid')) {
			dbinsert(['mid' => $mid, 'name' => $name, 'create_time' => $time, 'update_time' => $time, 'value' => $value, 'app' => 1])->into('{member_meta}')->setDialect($dialect)->exec();
		} else {
			dbupdate('{member_meta}')->set(['update_time' => $time, 'value' => $value])->where(['mid' => $mid, 'name' => $name])->setDialect($dialect)->exec();
		}
	}
}