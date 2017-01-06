<?php
class MemcachedInstaller extends AppInstaller {
	public function getAuthor() {
		return 'Leo Ning';
	}
	public function getDscription() {
		return '基于memcached的缓存系统，提供系统缓存功能.需要安装memcache(d)扩展。';
	}
	public function getName() {
		return '缓存';
	}
	public function getWebsite() {
		return 'http://www.wulacms.com/plugins/memcache';
	}
	public function getVersionLists() {
		$v = array ('0.0.1' => '20140730001' );
		$v ['1.0.0'] = '201507030002';
		return $v;
	}
	public function uninstall() {
		return true;
	}
}
?>