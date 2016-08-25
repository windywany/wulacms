<?php
class AlbumInstaller extends AppInstaller {
	public function getVersionLists() {
		$v ['1.0.0'] = '20150810001';
		$v ['1.5.0'] = '20151110002';
		$v ['1.5.1'] = '20151127003';
		$v ['1.6.0'] = '20151127004';
		$v ['1.6.5'] = '20160819005';
		return $v;
	}
	public function getName() {
		return '相册';
	}
	public function getDscription() {
		return '为系统提供相册功能';
	}
	public function getWebsite() {
		return 'http://www.wulacms.com/plugins/album';
	}
	public function getAuthor() {
		return 'Leo Ning';
	}
	public function getDependences() {
		return array ('cms' => '[3.0.0,)' );
	}
	public function onInstall($dialect) {
		$model = new AlbumContentModel ();
		$rst = $model->install ( $dialect );
		return true;
	}
	public function uninstall() {
		$model = new AlbumContentModel ();
		$models [] = 'album';
		if (! $model->uninstall ( $models )) {
			return false;
		}
		return parent::uninstall ();
	}
	public function upgradeTo20160819005($dialect){
		$rst = dbupdate('{cms_model}')->set(array('is_delegated'=>0))->where(array('refid'=>'album'))->exec();
		if(!$rst){
			$this->errorMsg = '升级失败，请将内容管理模板升级到4.5.0或更高.';
		}
		return $rst;
	}
}