<?php
defined('KISSGO') or exit ('No direct script access allowed');

bind('do_admin_layout', 'hook_for_do_admin_layout_rest@hooks/do_admin_layout');
bind('get_cms_preference_groups', 'rest_cms_preference_groups@hooks/do_admin_layout');
bind('get_activity_log_type', 'hook_for_activity_types_rest@hooks/do_admin_layout');
bind('get_acl_resource', 'filter_for_rest_acl_resource@hooks/get_acl_resource');
bind('crontab', 'hook_for_rest_crontab@hooks/rest_cron_hook');

register_cts_provider('remote', 'rest_remote_data_provider', '远程数据调取', '通过RestFUL试从远端调取数据.');

function get_condition_for_remote() {
	$fields['app'] = ['label' => 'APP', 'name' => 'app'];

	return $fields;
}

function rest_remote_data_provider($con, $tplvars = [], $dialect = null) {
	static $apps = false;
	if ($apps === false) {
		$appcfg  = trim(cfg('apps@rremote'));
		$appcfgs = explode("\n", $appcfg);
		foreach ($appcfgs as $cfg) {
			$cfg = trim($cfg);
			if ($cfg) {
				$cfgs = explode(',', $cfg);
				if (count($cfgs) == 4) {
					$name          = trim($cfgs[0]);
					$url           = trim($cfgs[1]);
					$id            = trim($cfgs[2]);
					$key           = trim($cfgs[3]);
					$apps[ $name ] = [$url, $id, $key];
				}
			}
		}
	}
	$con['from'] = $con['remote'];
	$app         = trim($con['app']);
	if (!isset($apps[ $app ])) {
		return new CtsData();
	}
	$appcfg = $apps[ $app ];
	unset($con['remote'], $con['app'], $con['nocache']);

	$rest = new RestClient($appcfg[0], $appcfg[1], $appcfg[2], '1', 5);

	$data = $rest->get('rest.cts', $con);
	if (isset($data['data'])) {
		return new CtsData($data['data'], $data['count'] ? $data['count'] : 0);
	}

	return new CtsData();
}
