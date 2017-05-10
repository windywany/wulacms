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

	public function index($file = '') {
		$rst = true;
		if ($file) {
			$file = WEB_ROOT . $file;
			if (is_file($file)) {
				$rst = @rename($file, WEB_ROOT . 'excel' . DS . date('Y-m-d') . '.xls');
			}
		}
		if (!$rst) {
			return \NuiAjaxView::error('移动优惠券文件失败.');
		}
		$client = null;
		try {
			$client = new \GearmanClient();
			$client->addServer("127.0.0.1");
			if (isset($_SESSION['p_excel_jid'])) {
				$status = $client->jobStatus($_SESSION['p_excel_jid']);
				if ($status && $status[0] && $status[1]) {
					return \NuiAjaxView::click('#disbtn', '系统正在导入数据,请稍等.');
				}
			}
			$jobH = $client->doBackground('parse-excel', 'gearman task');
			if ($jobH) {
				$_SESSION['p_excel_jid'] = $jobH;

				return \NuiAjaxView::callback('startImport', [], '系统已经开始导入商品，请稍等.');
			} else {
				return \NuiAjaxView::error('导入失败，无法创建导入任务.');
			}
		} catch (\GearmanException $e) {
			return \NuiAjaxView::error($e->getMessage());
		}
	}

	public function checkstatus() {
		if (isset($_SESSION['p_excel_jid'])) {
			$jh = $_SESSION['p_excel_jid'];
			try {
				$client = new \GearmanClient();
				$client->addServer("127.0.0.1");
				$status = $client->jobStatus($jh);
				if (!$status || !$status[0] || !$status[1]) {
					$_SESSION['p_excel_jid'] = null;

					return ['done' => true, 'msg' => '商品导入完成.'];
				} else {
					return ['done' => false];
				}
			} catch (\GearmanException $e) {
				log_error($e->getMessage(), 'gearman');
			}
		}

		return ['done' => true];
	}
}
