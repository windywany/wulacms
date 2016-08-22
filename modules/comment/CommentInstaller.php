<?php
class CommentInstaller extends AppInstaller {
	public function getVersionLists() {
		$v ['1.0.0'] = '20150107001';
		$v ['1.0.1'] = '20150107001';
		return $v;
	}
	public function getName() {
		return '评论&留言系统';
	}
	public function getDscription() {
		return '为页面提供评论功能.当不指定页面在时即为系统留言.';
	}
	public function getWebsite() {
		return 'http://www.kissgo.org/plugins/comments';
	}
	public function getAuthor() {
		return '宁广丰';
	}
	public function getDependences() {
		$d ['system'] = '[2.1.0,)';
		$d ['dashboard'] = '[2.0.0,)';
		$d ['rest'] = '[2.0.0,)';
		$d ['cms'] = '[1.0.0,)';
		return $d;
	}
}