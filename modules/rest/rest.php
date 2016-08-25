<?php
defined ( 'KISSGO' ) or exit ( 'No direct script access allowed' );

bind ( 'do_admin_layout', 'hook_for_do_admin_layout_rest@hooks/do_admin_layout' );
bind ( 'get_activity_log_type', 'hook_for_activity_types_rest@hooks/do_admin_layout' );
bind ( 'get_acl_resource', 'filter_for_rest_acl_resource@hooks/get_acl_resource' );
bind ( 'crontab', 'hook_for_rest_crontab@hooks/rest_cron_hook' );

/**
 * 用于调用软件数据.
 *
 * @param array $conditions        	
 * @return CtsData
 */
function rest_remote_provider($con, $tplvars) {
	$group = get_condition_value ( 'group', $con, 'default' );
	return new RestCtsData ( $con, $group );
}

register_cts_provider ( 'remote', array ('rest_remote_provider',ksg_include ( 'rest', 'provider.php', true ) ), '远程调用标签', '用于从远程调用数据.' );
