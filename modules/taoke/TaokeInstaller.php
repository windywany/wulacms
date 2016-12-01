<?php

class TaokeInstaller extends AppInstaller {
	public function getVersionLists() {
		$v ['1.0.0'] = '20161210001';


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
			$model = new TaokeContentModel ();
			$rst   = $model->install($dialect);

			return $rst ? true : false;
		}

		return $rst;
	}

	public function uninstall() {
		$model     = new TaokeContentModel ();
		$models [] = 'taoke';
		if (!$model->uninstall($models)) {
			return false;
		}

		return parent::uninstall();
	}


}