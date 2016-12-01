<?php
class WeixinInstaller extends AppInstaller {
	public function getWebsite() {
		return 'http://www.wulacms.com/plugins/weixin';
	}
	public function getName() {
		return '微信接口';
	}
	public function getAuthor() {
		return 'Leo Ning';
	}
	public function getVersionLists() {
		$v ['1.0.2'] = '20151211001';
		$v ['1.0.3'] = '20151211002';
		$v ['1.0.4'] = '20160119003';
		$v ['1.0.5'] = '20160128004';
		return $v;
	}
	public function getDscription() {
		return '为第三方模块提供与微信平台对接的基础功能，将微信的回调分发给第三方模块，并提供简单的微信管理功能（会员，素材，菜单）。';
	}
}