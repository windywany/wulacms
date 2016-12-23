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