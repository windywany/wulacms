<?php
class MobiappInstaller extends AppInstaller {
	public function getVersionLists() {
		$v ['1.0.0'] = '20150703001';
		$v ['1.1.0'] = '20150723002';
		$v ['1.2.0'] = '20151130003';
		$v ['1.2.1'] = '20151211001';
		return $v;
	}
	public function getName() {
		return 'APP数据源';
	}
	public function getDscription() {
		return '基于内容管理模块为移动端提供数据.可以对APP包进行分渠道管理，同时提供版本升级检测功能，请在多媒体配置中允许上传apk和ipa文件.';
	}
	public function getWebsite() {
		return 'http://www.kisscms.cn/plugins/mobiappdata/';
	}
	public function getAuthor() {
		return '宁广丰';
	}
	public function getDependences() {
		$d ['dashboard'] = '[2.0.0,)';
		$d ['system'] = '[2.5.0,)';
		$d ['rest'] = '[2.1.0,)';
		$d ['cms'] = '[2.0.0,)';
		return $d;
	}
	public function upgradeTo20150723002($dialect) {
		$sql = dbupdate ( '{mobi_page}' )->set ( array ('publish_day' => imv ( "DATE_FORMAT(DATE_ADD(DATE_ADD('1970-01-01',INTERVAL publish_time SECOND),INTERVAL 8 HOUR),'%Y-%m-%d')" ) ) );
		$sql->where ( array ('publish_time >' => 0 ) )->setDialect ( $dialect )->exec ();
		return true;
	}
}
