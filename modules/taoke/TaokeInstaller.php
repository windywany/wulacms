<?php

class TaokeInstaller extends AppInstaller {
	public function getVersionLists() {
		$v ['1.0.0'] = '20161210001';
		$v ['1.0.1'] = '20161210002';
		$v ['1.0.2'] = '20161210003';
		$v ['1.0.3'] = '20161210004';
		return $v;
	}

	public function getName() {
		return '淘宝客';
	}

	public function getDscription() {
		return '为系统提供淘宝客功能';
	}

	public function getWebsite() {
		return 'http://www.wulacms.com/plugins/taoke';
	}

	public function getAuthor() {
		return '王伟';
	}

	public function getDependences() {
		return array('cms' => '[3.0.0,)');
	}

	public function onInstall($dialect) {
		$rst = parent::onInstall($dialect);
		if ($rst) {
			$model = new \taoke\classes\TaokeContentModel();
			$rst   = $model->install($dialect);

			return $rst ? true : false;
		}

		return $rst;
	}

	public function uninstall() {
		$model     = new \taoke\classes\TaokeContentModel();
		$models [] = 'taoke';
		if (!$model->uninstall($models)) {
			return false;
		}

		return parent::uninstall();
	}
	public function upgradeTo20161210002($dialect){
		dbupdate('{tbk_goods}')->set(['real_price'=>imv('price - discount')])->where(['use_price <='=>imv('price')])->exec();
		dbupdate('{tbk_goods}')->set(['real_price'=>imv('price')])->where(['use_price >'=>imv('price')])->exec();
		return true;
	}

	public function upgradeTo20161210003($dialect){

		return true;
	}
	public function upgradeTo20161210004($dialect){

		return true;
	}

}