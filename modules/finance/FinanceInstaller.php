<?php

class FinanceInstaller extends AppInstaller {
	public function getWebsite() {
		return 'http://www.kisscms.cn/plugins/finance';
	}

	public function getName() {
		return '财务系统';
	}

	public function getAuthor() {
		return 'FLY';
	}

	public function getVersionLists() {
		$v ['2.0.0'] = '20160917001';
		$v ['2.0.1'] = '2016122317001';
		$v ['2.1.0'] = '20161228003'; // 添加支出记录表，添加订单记录表
		$v ['2.2.0'] = '20170105004'; // 添加设备与推广渠道
		return $v;
	}

	public function getDscription() {
		return '财务系统，财务管的好,对象才好找!';
	}

	public function getDependences() {
		$d['passport'] = '[2.0.0,)';

		return $d;
	}
}