<?php
class PrettyhtmlInstaller extends AppInstaller {
	public function getVersionLists() {
		$v ['1.0.0'] = '20150707001';
		return $v;
	}
	public function getName() {
		return '一键排版';
	}
	public function getDscription() {
		return '为编辑器提供格式化HTML美化插件.';
	}
	public function getWebsite() {
		return 'http://www.kisscms.cn/plugins/prettyhtml';
	}
	public function getAuthor() {
		return '宁广丰';
	}
	public function getDependences() {
		$d ['dashboard'] = '[2.1.0,)';
		$d ['cms'] = '[2.7.0,)';
		return $d;
	}
}