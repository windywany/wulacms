<?php
defined ( 'KISSGO' ) or exit ( 'No direct script access allowed' );
bind ( 'do_admin_layout', '&PassportPluginImpl' );
bind ( 'on_init_rest_server', '&PassportPluginImpl' );
bind ( 'get_acl_resource', '&PassportPluginImpl' );
bind ( 'get_default_apps', '&PassportPluginImpl' );
bind ( 'get_user_group_types', '&PassportPluginImpl' );
bind ( 'get_rbac_driver', '&PassportPluginImpl', 100, 2 );
bind ( 'on_init_autocomplete_condition_up_passport', '&PassportPluginImpl' );
bind ( 'on_save_user_passport_vip', '&PassportPluginImpl', 1, 2 );
/**
 * 取会员通行证.
 *
 * @param number $uid
 *        	默认为0表示当前登录用户.
 * @return Passport Passport 实例.
 */
function passport($uid = 0) {
	return whoami ( 'vip', $uid );
}