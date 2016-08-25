<?php
/*
 * the entry of cron
 */
define ( 'WEB_ROOT', dirname(__DIR__) . DIRECTORY_SEPARATOR );
include_once WEB_ROOT . 'bootstrap.php';
define ( 'CRON_MAX_EXECUTE_TIME', 36000 );
function on_cron_timeout() {
	set_cfg ( 'cron_start_time', 0, 'cron' );
	set_cfg ( 'cron_executed_time', time (), 'cron' );
}
set_time_limit ( CRON_MAX_EXECUTE_TIME );
$last_time = cfg ( 'cron_start_time@cron' );
$time = time ();
// 执行完成或执行超过CRON_MAX_EXECUTE_TIME
if (empty ( $last_time ) || ($time - $last_time) > CRON_MAX_EXECUTE_TIME) {
	set_cfg ( 'cron_start_time', $time, 'cron' );
	register_shutdown_function ( 'on_cron_timeout' );
	fire ( 'crontab', icfg ( 'cron_executed_time@cron' ) );
} else {
	echo "cron is running ...";
}
