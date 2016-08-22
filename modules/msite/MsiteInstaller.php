<?php
class MsiteInstaller extends AppInstaller {
	public function getVersionLists() {
		$v ['0.0.1'] = '2014010070001';
		$v ['1.0.0'] = '201512260002';
		return $v;
	}
	public function getName() {
		return '站点管理';
	}
	public function getDscription() {
		return '可以将顶级栏目与专题栏目绑定到指定的二级域名.此模块依赖于CMS模块。';
	}
	public function getWebsite() {
		return 'http://www.crudq.com';
	}
	public function getAuthor() {
		return '宁广丰';
	}
	public function getDependences() {
		$dependences ['cms'] = '[1.0.0,)';
		return $dependences;
	}
	public function uninstall() {
		parent::uninstall();
		dbdelete ()->from ( '{preferences}' )->where ( array ('preference_group' => 'msite_theme' ) )->exec ();
		return true;
	}
}
