<?php

namespace taoke\controllers;
/**
 * Created by PhpStorm.
 * User: wangwei
 * Date: 2016/12/15
 * Time: 10:17
 */
class ImportController extends \Controller {
	protected $checkUser = true;

	public function index() {
		$client = new \GearmanClient();
		$client->addServer("127.0.0.1");
		if (isset($_SESSION['p_excel_jid'])) {
			$job_res = $client->jobStatus($_SESSION['p_excel_jid']);
			if ($job_res[0]) {
				return \NuiAjaxView::click('#disbtn', '数据导入正在执行');
			} else {
				$add_res = $this->add();
				if ($add_res['status'] == 0) {
					return \NuiAjaxView::click('#disbtn', '数据导入成功');
				} else {
					return \NuiAjaxView::error('过期时间设置失败');
				}
			}

		} else {

			$job                     = $client->addTaskBackground('parse-excel', 'gearman task');
			$_SESSION['p_excel_jid'] = $job->jobHandle();
			$client->runTasks();
			//return \NuiAjaxView::error('后台正在导入中');
			if ($client->returnCode() != GEARMAN_SUCCESS) {
				return \NuiAjaxView::error('后台正在导入中');
			} else {
				$add_res = $this->add();
				if ($add_res['status'] == 0) {
					return \NuiAjaxView::click('#disbtn', '数据导入成功');
				} else {
					return \NuiAjaxView::error('过期时间设置失败');
				}
			}

		}
	}

	private function add() {
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

			return ['status' => 0];
		} else {

			return ['status' => 1];
		}
	}

}
