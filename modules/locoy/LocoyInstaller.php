<?php
class LocoyInstaller extends AppInstaller {
	public function getVersionLists() {
		$v ['1.0.0'] = '20150205001';
		return $v;
	}
	public function getName() {
		return '火车头';
	}
	public function getDscription() {
		return '火车头采集器WEB发布接口.将火车采集器发布上来的数据入库.';
	}
	public function getWebsite() {
		return 'http://www.kisscms.org/plugins/locoy/';
	}
	public function getAuthor() {
		return '宁广丰';
	}
	public function getDependences() {
		$d ['system'] = '[1.5.2,0';
		$d ['cms'] = '[1.5.1,0)';
		return $d;
	}
}