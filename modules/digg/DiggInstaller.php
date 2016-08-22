<?php
class DiggInstaller extends AppInstaller {
	public function getVersionLists() {
		$v ['1.0.0'] = '20150129001';
		return $v;
	}
	public function getName() {
		return '顶顶顶';
	}
	public function getDscription() {
		return '顶踩模块(评分,有无帮助等功能)
		本模块支持10级评分,网站可以根据自己的需求定义每一级代表的涵意.如:
		0代表有帮助; 1代表无帮助.
		0代表顶; 1代表踩.
		1代表1星评分,2代表2星评分等.
		ajax接口:' . tourl ( 'digg' ) . '?id=<page_id>&digg=[0-9]';
	}
	public function getWebsite() {
		return 'http://www.kisscms.org/plugins/digg/';
	}
	public function getAuthor() {
		return '宁广丰';
	}
	public function getDependences() {
		$d ['cms'] = '[1.5.1,)';
		return $d;
	}	
}