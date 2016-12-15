<?php

namespace taoke\controllers;
/**
 * Created by PhpStorm.
 * DEC :
 * User: wangwei
 * Date: 2016/12/15
 * Time: 10:17
 */
class ImportController extends \Controller {

	public function index() {
		try {
			$client = new \GearmanClient();
			$client->addServer();
			$client->addTask('parse-excel', '');
			$client->runTasks();

			return \NuiAjaxView::ok('执行成功，正在后台导入');
		} catch (\GearmanException $e) {
			return \NuiAjaxView::ok($e->getMessage());
		}
	}
}