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
