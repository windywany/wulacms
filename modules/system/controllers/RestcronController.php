<?php
/**
 * 运行定时任务.
 * @author ngf
 *
 */
class RestcronController extends Controller {
	protected $checkUser = true;
	protected $acls = array ('index' => 'cron:system' );
	public function index() {
		set_time_limit ( 0 );
		set_cfg ( 'cron_start_time', time (), 'cron' );
		fire ( 'crontab', icfg ( 'cron_executed_time@cron' ) );
		set_cfg ( 'cron_start_time', 0, 'cron' );
		set_cfg ( 'cron_executed_time', time (), 'cron' );
		return NuiAjaxView::ok ( '定时任务运行完成.' );
	}
}