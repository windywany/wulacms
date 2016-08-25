<?php
defined ( 'KISSGO' ) or exit ( 'No direct script access allowed' );
function hook_for_rest_crontab($last_executed_time) {
	// 未接入应用中心时（自己有可能成为应用中心)
	if (! bcfg ( 'connect_server@rest' )) {
		RestServer::syncServices();
	}
}