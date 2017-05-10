<?php
defined('KISSGO') or exit ('No direct script access allowed');
bind('do_admin_layout', '&PassportPluginImpl');
bind('get_acl_resource', '&PassportPluginImpl');
bind('get_default_apps', '&PassportPluginImpl');
bind('get_user_group_types', '&PassportPluginImpl');
bind('get_rbac_driver', '&PassportPluginImpl', 100, 2);
bind('on_save_user_passport_vip', '&PassportPluginImpl', 1, 2);
bind('get_sms_templates', '&\passport\classes\BindMobileSms');
bind('get_sms_templates', '&\passport\classes\ResetPasswdSms');
bind('get_sms_templates', '&passport\classes\RegCodeTemplate');
bind('on_init_rest_server', function (RestServer $server) {
	$server->registerClass(new \passport\classes\PassportResetService(), '1.0', 'user');

	return $server;
});
bind('get_columns_of_member-table', function ($cols) {

	$cols['group_id'] = ['name' => '会员组', 'show' => true, 'width' => 110, 'sort' => 'M.group_id,d', 'order' => 10, 'render' => function ($v, $data, $groups) {
		return $groups[ $v ];
	}];

	$cols['contact'] = ['name' => '联系方式', 'show' => true, 'width' => 120, 'order' => 11, 'render' => function ($v, $data, $extras) {
		$html = '';
		if ($data['email']) {
			$html .= '<p><a href="mailto:' . $data['email'] . '">' . $data['email'] . '</a></p>';
		}
		if ($data['phone']) {
			$html .= '<p><i class="fa fa-mobile-phone"></i>' . $data['phone'] . '</p>';
		}

		return $html;
	}];

	$cols['group_expire'] = ['name' => '过期日期', 'show' => false, 'sort' => 'M.group_expire,d', 'order' => 13, 'width' => 100, 'render' => function ($v, $data, $extra) {
		if (!$v) {
			return '久不过期';
		}

		return date('Y-m-d', $v);
	}];

	$cols['registered'] = ['name' => '注册日期', 'show' => false, 'sort' => 'M.registered,d', 'order' => 12, 'width' => 100, 'render' => function ($v, $data, $extra) {
		return date('Y-m-d', $v);
	}];

	$cols['lastlogin'] = ['name' => '最后登录', 'show' => true, 'sort' => 'M.lastlogin,d', 'order' => 99, 'width' => 130, 'render' => function ($v, $data, $extra) {
		return date('Y-m-d H:i', $v);
	}];

	return $cols;
}, 1);
/**
 * 取会员通行证.
 *
 * @param int $uid
 *            默认为0表示当前登录用户.
 *
 * @return Passport Passport 实例.
 */
function passport($uid = 0) {
	return whoami('vip', $uid);
}
