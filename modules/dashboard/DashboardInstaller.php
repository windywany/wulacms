<?php
/**
 * 管理员模板安装器.
 *
 * @author Guangfeng Ning <windywany@gmail.com>
 *        
 */
class DashboardInstaller extends AppInstaller {
	public function getAuthor() {
		return 'Guangfeng Ning';
	}
	public function getDscription() {
		return '其它应用或插件都基于此应用.';
	}
	public function getName() {
		return '控制面板';
	}
	public function getWebsite() {
		return 'http://www.kissgo.org/';
	}
	public function getVersionLists() {
		$lists ['0.0.1'] = '2014060600001';
		$lists ['0.0.2'] = '2014111800002';
		$lists ['1.0.0'] = '2014121500003';
		$lists ['1.1.0'] = '2014123000004';
		$lists ['1.2.0'] = '2015020500005';
		$lists ['2.0.0'] = '2015032000006';
		$lists ['2.1.0'] = '2015070700007';
		$lists ['2.2.0'] = '2015070900008';
		return $lists;
	}
}