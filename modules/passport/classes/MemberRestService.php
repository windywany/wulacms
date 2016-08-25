<?php
/**
 * 通行证会员服务.
 * @author ngf
 *
 */
class MemberRestService {
	/**
	 * 登录成功后应该立即调用此方法以获取用户信息.
	 *
	 * @param array $params
	 *        	<ul><li><b>token</b> - 登录token.</li></ul>
	 * @param string $key        	
	 * @param string $secret        	
	 * @return array
	 */
	public function rest_userinfo($params, $key, $secret) {
		$token = isset ( $params ['token'] ) ? $params ['token'] : false;
		if ($token) {
			$user_id = dbselect ( 'user_id' )->from ( '{passport_session}' )->where ( array ('session_id' => $token,'expire_time >' => time () ) )->get ( 'user_id' );
			if ($user_id) {
				$user = $this->rest_getMemberInfo ( array ('mid' => $user_id ), $key, $secret );
				if ($user ['member']) {
					dbdelete ()->from ( '{passport_session}' )->where ( array ('session_id' => $token ) )->exec ();
					dbdelete ()->from ( '{passport_session}' )->where ( array ('expire_time <' => time () ) )->exec ();
					return $user;
				} else {
					return array ('error' => '503','message' => 'Member not found' );
				}
			} else {
				return array ('error' => '502','message' => 'session expire' );
			}
		} else {
			return array ('error' => '501','message' => 'bad token' );
		}
	}
	/**
	 * 取用户信息.
	 *
	 * @param array $params
	 *        	<ul><li><b>mid</b> - 会员编号.</li></ul>
	 * @param string $key        	
	 * @param string $secret        	
	 * @return multitype:Ambigous <Ambigous, NULL, unknown, multitype:> |multitype:string
	 */
	public function rest_getMemberInfo($params, $key, $secret) {
		$user_id = $params ['mid'];
		if ($user_id) {
			$user = dbselect ( 'M.*,UR.role_name,UG.group_name,UG.group_refid AS group_alias,IM.username AS inviter' )->from ( '{member} AS M' );
			$user->join ( '{user_role} AS UR', 'M.role_id = UR.role_id' );
			$user->join ( '{user_group} AS UG', 'M.group_id = UG.group_id' );
			$user->join ( '{member} AS IM', 'M.invite_mid = IM.mid' );
			$user = $user->where ( array ('M.mid' => $user_id,'M.deleted' => 0,'M.status !=' => 0 ) )->get ( 0 );
			if ($user) {
				unset ( $user ['passwd'] );
				unset ( $user ['deleted'] );
				if ($user ['avatar']) {
					$user ['avatar'] = the_media_src ( $user ['avatar'] );
				}
				if ($user ['avatar_big']) {
					$user ['avatar_big'] = the_media_src ( $user ['avatar_big'] );
				}
				if ($user ['avatar_small']) {
					$user ['avatar_small'] = the_media_src ( $user ['avatar_small'] );
				}
				return array ('member' => $user );
			} else {
				return array ('error' => '503','message' => 'Member not found' );
			}
		} else {
			return array ('error' => '501','message' => 'no member id specified.' );
		}
	}
	public function rest_member($params, $key, $secret) {
		return $this->rest_getMemberInfo ( $params, $key, $secret );
	}
	/**
	 * ACL 权限.
	 *
	 * @param array $params
	 *        	<ul><li><b>mid</b> - 会员编号.</li></ul>
	 * @param string $key        	
	 * @param string $secret        	
	 * @return array
	 */
	public function rest_getAcls($params, $key, $secret) {
		$operations = array ();
		$user_id = $params ['mid'];
		if ($user_id) {
			$acls = dbselect ( 'resource,allowed' )->from ( '{user_role_acl} AS URC' );
			$acls->join ( '{member} AS UR', 'UR.role_id = URC.role_id' );
			$acls->where ( array ('UR.mid' => $user_id ) );
			$acls->sort ( 'priority', 'a' );
			foreach ( $acls as $acl ) {
				$res = $acl ['resource'];
				$operations [$res] = $acl ['allowed'];
			}
		}
		if ($operations) {
			return $operations;
		} else {
			return array ('error' => '500','message' => 'no acl for the user.' );
		}
	}
	/**
	 * 注册一个会员,注册时,会员名/手机/邮件三选一.
	 *
	 * @param array $params
	 *        	<ul>
	 *        	<li>type - 类型:P:个人,C:公司(团体),U:未知[可选]</li>
	 *        	<li>group_id - 组[可选]</li>
	 *        	<li>role_id　-　角色[可选]</li>
	 *        	<li>invite_code　-　推荐会员编号[可选]</li>
	 *        	<li>username　-　会员名</li>
	 *        	<li>email　-　邮件</li>
	 *        	<li>phone　-　手机</li>
	 *        	<li>passwd　-　密码</li>
	 *        	<li>registered　-　注册时间,时间戳.[可选]</li>
	 *        	<li>ip　-　注册ＩＰ</li>
	 *        	<li>nickname　-　昵称</li>
	 *        	<li>extra_info - 额外信息,借第三方插件使用[可选]</li>
	 *        	</ul>
	 * @param string $key        	
	 * @param string $secret        	
	 * @return array 包括会员编号的会员信息.
	 *         <ul>
	 *         <li>error - 0 success;greater than 0 failed.</li>
	 *         <li>member - 会员数据.</li>
	 *         </ul>
	 */
	public function rest_post_register($params, $key, $secret) {
		$form = new MemberModelForm ( $params );
		$username = false;
		$rst = true;
		if (! empty ( $params ['username'] )) {
			$username = $params ['username'];
			$rst = $form->checkUsername ( $username, array (), '用户名已经存在.' );
		}
		if (! $username && ! empty ( $params ['email'] )) {
			$params ['username'] = $username = $params ['email'];
			$rst = $form->validateField ( 'email', $params );
		}
		if (! $username && ! empty ( $params ['phone'] )) {
			$params ['username'] = $username = $params ['phone'];
			$rst = $form->validateField ( 'phone', $params );
		}
		if ($rst !== true) {
			return array ('error' => 1,'message' => $rst );
		}
		if (! $username) {
			return array ('error' => 2,'message' => '会员名,邮件,手机至少提供一个.' );
		}
		if (empty ( $params ['passwd'] ) || strlen ( $params ['passwd'] ) < 6) {
			return array ('error' => 3,'message' => '密码至少由6个字符组成.' );
		} else {
			$params ['passwd'] = md5 ( $params ['passwd'] );
		}
		
		if (empty ( $params ['ip'] )) {
			return array ('error' => 4,'message' => '注册IP不能为空.' );
		}
		if (! in_array ( $params ['type'], array ('P','U','C' ) )) {
			$params ['type'] = 'U';
		}
		if (empty ( $params ['registered'] )) {
			$params ['registered'] = time ();
		} else {
			$params ['registered'] = intval ( $params ['registered'] );
		}
		if (empty ( $params ['group_id'] )) {
			$params ['group_id'] = icfg ( 'default_group@passport', 0 );
		}
		if (empty ( $params ['role_id'] )) {
			$params ['role_id'] = icfg ( 'default_role@passport', 0 );
		}
		
		if (empty ( $params ['invite_code'] ) || ! $form->checkInviteCode ( $params ['invite_code'], array (), false )) {
			$params ['invite_mid'] = 0;
		} else {
			$params ['invite_mid'] = dbselect ()->from ( '{member}' )->where ( array ('recommend_code' => $params ['invite_code'] ) )->get ( 'mid' );
		}
		if (empty ( $params ['invite_mid'] )) {
			$params ['invite_mid'] = 0;
		}
		
		if (bcfg ( 'enable_active@passport' )) {
			$params ['status'] = 2;
		} else {
			$params ['status'] = 1;
		}
		$params ['update_time'] = time ();
		$params ['update_uid'] = 0;
		// $user ['recommend_code'] = uniqid ( 'r' );
		start_tran ();
		$extraInfo = $params ['extra_info'];
		unset ( $params ['extra_info'] );
		$rst = dbinsert ( $params )->into ( '{member}' )->exec ();
		if ($rst) {
			$params ['mid'] = $rst [0];
			$params ['extra_info'] = $extraInfo;
			$params = apply_filter ( 'after_vip_created', $params );
			if (is_array ( $params ) && ! empty ( $params )) {
				commit_tran ();
				if ($params ['status'] == '2') {
					$params ['need_activating'] = true;
					$params ['activating_url'] = tourl ( 'passport/active' ) . $params ['mid'];
				} else {
					$params ['need_activating'] = false;
				}
				return array ('error' => 0,'member' => $params );
			} else {
				return array ('error' => 6,'message' => $params );
			}
		}
		rollback_tran ();
		return array ('error' => 5,'message' => '无法创建会员.' );
	}
	/**
	 * 修改密码.
	 *
	 * @param array $params
	 *        	<ul>
	 *        	<li>mid - 会员编号.</li>
	 *        	<li>oldpasswd - 老密码.</li>
	 *        	<li>passwd - 新密码.</li>
	 *        	</ul>
	 * @param string $key        	
	 * @param string $secret        	
	 * @return array error equals 0 means update success.
	 */
	public function rest_post_change_password($params, $key, $secret) {
		$mid = intval ( $params ['mid'] );
		if (! $mid) {
			return array ('error' => 1,'message' => '未指定会员编号.' );
		}
		$where = array ('mid' => $mid );
		$passwsd = dbselect ()->from ( '{member}' )->where ( $where )->get ( 'passwd' );
		if (! $passwsd) {
			return array ('error' => 2,'会员不存在.' );
		}
		$oldpasswd = md5 ( $params ['oldpasswd'] );
		if ($passwsd != $oldpasswd) {
			return array ('error' => 3,'message' => '旧密码错误.' );
		}
		$npasswd = trim ( $params ['passwd'] );
		if (empty ( $npasswd ) || strlen ( $npasswd ) < 6) {
			return array ('error' => 4,'message' => '新密码至少由6个字符组成.' );
		}
		$npasswd = md5 ( $npasswd );
		if ($npasswd == $passwsd) {
			return array ('error' => 5,'message' => '新旧密码不能相同.' );
		}
		$rst = dbupdate ( '{member}' )->set ( array ('passwd' => $npasswd ) )->where ( $where )->exec ();
		return array ('error' => 0 );
	}
	/**
	 * 更新会员资料.
	 *
	 * @param array $params
	 *        	<ul>
	 *        	<li>type - 类型:P:个人,C:公司(团体),U:未知[可选]</li>
	 *        	<li>group_id - 组[可选]</li>
	 *        	<li>role_id　-　角色[可选]</li>
	 *        	<li>invite_mid　-　推荐会员编号[可选]</li>
	 *        	<li>username　-　会员名</li>
	 *        	<li>email　-　邮件</li>
	 *        	<li>phone　-　手机</li>
	 *        	<li>passwd　-　密码</li>
	 *        	<li>nickname　-　昵称</li>
	 *        	<li>auth_status - 认证状态：0:未认证,1:认证中,2:已经认证,3:认证失败</li>
	 *        	<li>auth_error - 认证失败原因.</li>
	 *        	</ul>
	 * @param string $key        	
	 * @param string $secret        	
	 */
	public function rest_post_update($params, $key, $secret) {
		$form = new MemberModelForm ( $params );
		$mid = intval ( $params ['mid'] );
		if (! $mid) {
			return array ('error' => 1,'message' => '未指定会员编号.' );
		}
		$member = array ();
		$where = array ('mid' => $mid );
		$rst = true;
		if (! empty ( $params ['username'] )) {
			$member ['username'] = $params ['username'];
			$rst = $form->checkUsername ( $params ['username'], $params, '用户名已经存在.' );
		}
		if ($rst === true && ! empty ( $params ['email'] )) {
			$member ['email'] = $params ['email'];
			$rst = $form->validateField ( 'email', $params );
		}
		if ($rst === true && ! empty ( $params ['phone'] )) {
			$member ['phone'] = $params ['phone'];
			$rst = $form->validateField ( 'phone', $params );
		}
		if ($rst !== true) {
			return array ('error' => 1,'message' => $rst );
		}
		if (! empty ( $params ['passwd'] ) && strlen ( $params ['passwd'] ) > 5) {
			$member ['passwd'] = md5 ( $params ['passwd'] );
		}
		$types = MemberModelForm::getMemberTypes ();
		if (! empty ( $params ['type'] ) && array_key_exists ( $params ['type'], $types )) {
			$member ['type'] = $params ['type'];
		}
		if (isset ( $params ['group_id'] )) {
			$member ['group_id'] = intval ( $params ['group_id'] );
		}
		if (isset ( $params ['role_id'] )) {
			$member ['role_id'] = intval ( $params ['role_id'] );
		}
		if (isset ( $params ['invite_mid'] ) && $form->checkInviteCode ( $params ['invite_mid'], $params, false )) {
			$member ['invite_mid'] = intval ( $params ['invite_mid'] );
		}
		if (! empty ( $params ['nickname'] )) {
			$member ['nickname'] = $params ['nickname'];
		}
		if (isset ( $params ['auth_status'] )) {
			$member ['auth_status'] = intval ( $params ['auth_status'] );
		}
		if (isset ( $params ['auth_error'] )) {
			$member ['auth_error'] = intval ( $params ['auth_error'] );
		}
		$member ['update_time'] = time ();
		
		$rst = dbupdate ( '{member}' )->set ( $member )->where ( $where )->exec ();
		if ($rst) {
			return array ('error' => 0,'member' => $member );
		}
		return array ('error' => 2,'message' => '修改会员信息出错.' );
	}
	/**
	 * 修改头像.
	 *
	 * @param array $params
	 *        	<ul>
	 *        	<li>mid - 会员编号.</li>
	 *        	<li>@avatar - 头像文件.</li>
	 *        	</ul>
	 * @param string $key        	
	 * @param string $secret        	
	 * @return array error equals 0 means update success.
	 */
	public function rest_post_update_avatar($params, $key, $secret) {
		$mid = intval ( $params ['mid'] );
		if (! $mid) {
			return array ('error' => 2,'message' => '未指定会员编号.' );
		}
		$avatar = MediaUploadHelper::moveRestUploadedFile ( $params, 'avatar' );
		$avatar_big = MediaUploadHelper::moveRestUploadedFile ( $params, 'avatar_big' );
		$avatar_small = MediaUploadHelper::moveRestUploadedFile ( $params, 'avatar_small' );
		$data = array ();
		if (! $avatar ['error']) {
			$data ['avatar'] = the_media_src ( $avatar ['file'] [0] );
		}
		if (! $avatar_big ['error']) {
			$data ['avatar_big'] = the_media_src ( $avatar_big ['file'] [0] );
		}
		if (! $avatar_small ['error']) {
			$data ['avatar_small'] = the_media_src ( $avatar_small ['file'] [0] );
		}
		if (empty ( $data )) {
			return array ('error' => 1,'message' => '头像修改失败.' );
		} else {
			$rst = dbupdate ( '{member}' )->set ( $data )->where ( array ('mid' => $mid ) )->exec ();
			$data ['error'] = 0;
			return $data;
		}
	}
	
	/**
	 * 激活邮件.
	 *
	 * @param array $params
	 *        	<ul>
	 *        	<li>mid - 会员编号.</li>
	 *        	<li>email - 邮件地址</li>
	 *        	</ul>
	 * @param string $key        	
	 * @param string $secret        	
	 */
	public function rest_post_active_mail($params, $key, $secret) {
		$form = new MemberModelForm ( $params );
		$mid = intval ( $params ['mid'] );
		$where = array ('mid' => $mid );
		if (! $mid) {
			return array ('error' => 1,'message' => '未指定会员编号.' );
		}
		$status = dbselect ()->from ( '{member}' )->where ( $where )->get ( 'status' );
		if ($status === false) {
			return array ('error' => 5,'message' => '会员不存在.' );
		}
		$time = time ();
		$member ['status'] = 1;
		$member ['update_time'] = $time;
		$member ['update_uid'] = $mid;
		$rst = false;
		if (! empty ( $params ['email'] )) {
			$member ['email'] = $params ['email'];
			$rst = $form->validateField ( 'email', $params );
		} else {
			return array ('error' => 4,'message' => '请指定邮箱地址.' );
		}
		if ($rst !== true) {
			return array ('error' => 2,'message' => $rst );
		} else {
			start_tran ();
			$rst = dbupdate ( '{member}' )->set ( $member )->where ( $where )->exec ();
			if ($rst) {
				$active ['update_time'] = $time;
				$active ['update_uid'] = $mid;
				$active ['mail_actived_time'] = $time;
				$active ['mail_active_code'] = '';
				$active ['mail_active_code_expire'] = 0;
				if (dbselect ()->from ( 'member_activation' )->where ( array ('mid' => $mid ) )->exist ( 'mid' )) {
					$rst = dbupdate ( '{member_activation}' )->set ( $active )->where ( array ('mid' => $mid ) )->exec ();
				} else {
					$active ['create_time'] = $time;
					$active ['mid'] = $mid;
					$rst = dbinsert ( $active )->into ( '{member_activation}' )->exec ();
				}
			}
			if ($rst) {
				commit_tran ();
				return array ('error' => 0 );
			} else {
				rollback_tran ();
				return array ('error' => 3,'message' => '无法激活你的邮箱地址.' );
			}
		}
	}
	/**
	 * 激活手机.
	 *
	 * @param array $params
	 *        	<ul>
	 *        	<li>mid - 会员编号.</li>
	 *        	<li>phone - 手机号</li>
	 *        	</ul>
	 * @param string $key        	
	 * @param string $secret        	
	 */
	public function rest_post_active_phone($params, $key, $secret) {
		$form = new MemberModelForm ( $params );
		$mid = intval ( $params ['mid'] );
		$where = array ('mid' => $mid );
		if (! $mid) {
			return array ('error' => 1,'message' => '未指定会员编号.' );
		}
		$status = dbselect ()->from ( '{member}' )->where ( $where )->get ( 'status' );
		if ($status === false) {
			return array ('error' => 5,'message' => '会员不存在.' );
		}
		$time = time ();
		$member ['status'] = 1;
		$member ['update_time'] = $time;
		$member ['update_uid'] = $mid;
		$rst = false;
		if (! empty ( $params ['phone'] )) {
			$member ['phone'] = $params ['phone'];
			$rst = $form->validateField ( 'phone', $params );
		} else {
			return array ('error' => 4,'message' => '请指定手机号.' );
		}
		if ($rst !== true) {
			return array ('error' => 2,'message' => $rst );
		} else {
			start_tran ();
			$rst = dbupdate ( '{member}' )->set ( $member )->where ( $where )->exec ();
			if ($rst) {
				$active ['update_time'] = $time;
				$active ['update_uid'] = $mid;
				$active ['phone_actived_time'] = $time;
				$active ['phone_active_code'] = '';
				if (dbselect ()->from ( 'member_activation' )->where ( array ('mid' => $mid ) )->exist ( 'mid' )) {
					$rst = dbupdate ( '{member_activation}' )->set ( $active )->where ( array ('mid' => $mid ) )->exec ();
				} else {
					$active ['create_time'] = $time;
					$active ['mid'] = $mid;
					$rst = dbinsert ( $active )->into ( '{member_activation}' )->exec ();
				}
			}
			if ($rst) {
				commit_tran ();
				return array ('error' => 0 );
			} else {
				rollback_tran ();
				return array ('error' => 3,'message' => '无法激活你的手机号.' );
			}
		}
	}
	
	/**
	 * 验证会员信息.Sample:
	 * <code>$rest = new RestHelper();
	 * $rest->passport->member->validate ( array ('value' => '13764558765','field' => 'phone' ) );
	 * </code>
	 *
	 * @param array $params
	 *        	<ul>
	 *        	<li><b>mid</b> - 会员编号[可选].</li>
	 *        	<li><b>field</b> - 要检查的字段.</li>
	 *        	<li><b>value</b> - 要检查的值.</li>
	 *        	</ul>
	 * @param string $key        	
	 * @param string $secret        	
	 */
	public function rest_validate($params, $key, $secret) {
		$rst = false;
		if (isset ( $params ['mid'] )) {
			$user_id = $params ['mid'];
		} else {
			$user_id = 0;
		}
		$rst = dbselect ()->from ( '{member}' );
		if (isset ( $params ['field'] )) {
			$field = $params ['field'];
			$where [$field] = $params ['value'];
			if (! empty ( $user_id )) {
				$where ['mid !='] = $user_id;
			}
			$rst->where ( $where );
			if (! $rst->exist ( 'mid' )) {
				$rst = true;
			}
		}
		return array ('error' => 0,'valid' => $rst );
	}
}