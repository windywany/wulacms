<?php
class PassportInstaller extends AppInstaller {
	public function getAuthor() {
		return 'Leo Ning';
	}
	public function getDscription() {
		return '提供登录、用户注册、用户激活、找回密码、实名认证，用户管理等功能.';
	}
	public function getName() {
		return '通行证';
	}
	public function getWebsite() {
		return 'http://www.wulacms.com/';
	}
	public function getDependences() {
		$dependences ['media'] = '[0.0.1,)';
		$dependences ['rest'] = '[0.0.2,)';
		return $dependences;
	}
	public function getVersionLists() {
		$versions ['0.0.1'] = '20140730001';
		$versions ['0.0.2'] = '20140820002';
		$versions ['1.0.0'] = '20150119003';
		$versions ['1.5.0'] = '20150205004';
		$versions ['2.0.0'] = '20160126005';
		$versions ['2.1.0'] = '20160128006'; // 会员支持多角色
		return $versions;
	}
	public function upgradeTo20160128006($dialect) {
		$ms = dbselect ( 'mid,role_id' )->from ( '{member}' )->where ( array ('role_id >' => 0 ) )->toArray ();
		if ($ms) {
			dbinsert ( $ms, true )->into ( '{member_has_role}' )->exec ();
		}
		return true;
	}
	public function uninstall() {
		parent::uninstall ();
		dbdelete ()->from ( '{preferences}' )->where ( array ('preference_group' => 'passport' ) )->exec ();
		return true;
	}
}
