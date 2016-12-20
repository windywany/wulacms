<?php

class PointsInstaller extends AppInstaller {
	public function getWebsite() {
		return 'http://www.kisscms.cn/plugins/points';
	}

	public function getName() {
		return '积分系统';
	}

	public function getAuthor() {
		return 'FLY';
	}

	public function getVersionLists() {
		$v ['1.0.0'] = '20160617001';

		return $v;
	}

	public function getDependences() {
		$d['passport'] = '[2.0.0,)';
		$d['finance']  = '[2.0.0,)';

		return $d;
	}

	public function getDscription() {
		return '积分系统，真诚多,没套路';
	}
}