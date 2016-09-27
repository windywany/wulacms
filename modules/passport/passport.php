<?php
defined('KISSGO') or exit ('No direct script access allowed');
bind('do_admin_layout', '&PassportPluginImpl');
bind('get_acl_resource', '&PassportPluginImpl');
bind('get_default_apps', '&PassportPluginImpl');
bind('get_user_group_types', '&PassportPluginImpl');
bind('get_rbac_driver', '&PassportPluginImpl', 100, 2);
bind('on_init_autocomplete_condition_up_passport', '&PassportPluginImpl');
bind('on_save_user_passport_vip', '&PassportPluginImpl', 1, 2);
bind('get_sms_templates', '&\passport\classes\BindMobileSms');
bind('get_sms_templates', '&\passport\classes\ResetPasswdSms');
define('PASSPORT_MEM_KEY', 'passport_member_');

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

// 会员可用设备类型
function memDevices() {
	// 0:PC;1:Android;2:iOS;3:H5
	return array(1, 2, 3, 4);
}
