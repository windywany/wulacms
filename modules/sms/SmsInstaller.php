<?php
class SmsInstaller extends AppInstaller {
	public function getWebsite() {
		return 'http://www.wulacms.com/plugins/sms';
	}
	public function getName() {
		return 'sms';
	}
	public function getAuthor() {
		return 'Leo Ning';
	}
	public function getVersionLists() {
		$v ['1.0.0'] = '20151009001';
		return $v;
	}
	public function getDscription() {
		return '短信网关，为提供第三方模块sendsms方法来发送短信,并记录下短信发送记录以供查询。';
	}
	/*
	 * (non-PHPdoc)
	 * @see AppInstaller::getDependences()
	 */
	public function getDependences() {
		$d ['system'] = '[1.0.0,)';
		return $d;
	}
}
