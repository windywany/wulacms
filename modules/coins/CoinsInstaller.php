<?php

class CoinsInstaller extends AppInstaller {
	public function getWebsite() {
		return 'http://www.kisscms.cn/plugins/coins';
	}

	public function getName() {
		return '虚拟币系统';
	}

	public function getAuthor() {
		return 'FLY';
	}

	public function getVersionLists() {
		$v ['1.0.0'] = '20160905001';
		$v ['1.0.1'] = '20161220002';// 修正字段
		$v['1.1.0']  = '20161227003';// 添加默认金币类型

		return $v;
	}

	public function getDependences() {
		$d['passport'] = '[2.0.0,)';
		$d['finance']  = '[2.0.0,)';

		return $d;
	}

	public function getDscription() {
		return '金币虚拟,点亮生活';
	}
}