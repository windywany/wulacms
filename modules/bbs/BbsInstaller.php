<?php
class BbsInstaller extends AppInstaller {
	public function getWebsite() {
		return 'http://www.wulacms.com/plugins/bbs';
	}
	public function getName() {
		return '轻论坛';
	}
	public function getAuthor() {
		return 'Leo Ning';
	}
	public function getVersionLists() {
		$v ['1.0.0'] = '20160819001';
		return $v;
	}
	public function getDscription() {
		return '轻论坛';
	}
	/**
	 *
	 * {@inheritdoc}
	 *
	 * @see AppInstaller::getDependences()
	 */
	public function getDependences() {
		$d ['cms'] = '[4.1.1,)';
		$d ['passport'] = '[2.1.0,)';
		return $d;
	}
}

