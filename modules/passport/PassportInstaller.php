<?php

class PassportInstaller extends AppInstaller {
	public function getAuthor() {
		return '宁广丰';
	}

	public function getDscription() {
		return '提供登录、用户注册、用户激活、找回密码、实名认证，用户管理等功能.';
	}

	public function getName() {
		return '通行证';
	}

	public function getWebsite() {
		return 'http://www.kissgo.org/';
	}

	public function getDependences() {
		$dependences ['media'] = '[0.0.1,)';
		$dependences ['rest']  = '[0.0.2,)';
		$dependences ['sms']   = '[1.0.0,)';

		return $dependences;
	}

	public function getVersionLists() {
		$versions ['0.0.1'] = '20140730001';
		$versions ['0.0.2'] = '20140820002';
		$versions ['1.0.0'] = '20150119003';
		$versions ['1.5.0'] = '20150205004';
		$versions ['2.0.0'] = '20160126005';
		$versions ['2.1.0'] = '20160128006'; // 会员支持多角色
		$versions ['2.1.1'] = '20160412007'; // 接口新增第三方登录，手机注册
		$versions ['2.1.2'] = '20160509008'; // nickname 限制过滤
		$versions ['2.1.3'] = '20160902001'; // 关注用户
		return $versions;
	}

	public function uninstall() {
		parent::uninstall();
		dbdelete()->from('{preferences}')->where(array('preference_group' => 'passport'))->exec();

		return true;
	}
}
