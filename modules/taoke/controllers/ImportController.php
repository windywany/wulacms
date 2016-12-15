<?php

namespace taoke\controllers;
/**
 * Created by PhpStorm.
 * User: wangwei
 * Date: 2016/12/15
 * Time: 10:17
 */
class ImportController extends \Controller {

	public function index() {
<<<<<<< HEAD
		if (isset($_SESSION['p_excel_jid'])) {
			return NuiAjaxView::click('#disbtn', '数据导入正在执行');
		}
		try {
			$client = new GearmanClient();
			$client->addServer();
			$client->jobStatus();
			$job                     = $client->addTaskBackground('parse-excel', 'gearman task');
			$_SESSION['p_excel_jid'] = $job->jobHandle();
			if ($client->returnCode() != GEARMAN_SUCCESS) {
				return NuiAjaxView::error('后台正在导入中');
			} else {
				$today = date('Y-m-d', time());
				$res   = dbselect('*')->from('{preferences}')->where(['preference_group' => 'taoke', 'name' => 'endtime'])->get();
				if (!$res) {
					$data['user_id']          = 1;
					$data['update_time']      = time();
					$data['preference_group'] = 'taoke';
					$data['name']             = 'endtime';
					$data['value']            = $today;
					$up_res                   = dbinsert($data)->into('{preferences}')->exec();
				} else {
					$up_res = dbupdate('{preferences}')->set(['value' => $today])->where(['preference_group' => 'taoke', 'name' => 'endtime'])->exec();
				}
				if ($up_res) {
					unset($_SESSION['p_excel_jid']);

					return NuiAjaxView::click('#disbtn', '数据导入成功');
				} else {
					return NuiAjaxView::error('过期时间设置失败');
				}
			}
		} catch (GearmanException $e) {
			return NuiAjaxView::error($e->getMessage());
=======
		try {
			$client = new \GearmanClient();
			$client->addServer();
			$client->addTask('parse-excel', '');
			$client->runTasks();

			return \NuiAjaxView::ok('执行成功，正在后台导入');
		} catch (\GearmanException $e) {
			return \NuiAjaxView::ok($e->getMessage());
>>>>>>> d0922d61b56c45fe4e60727ac8870ab2ca43efdf
		}
	}

}
