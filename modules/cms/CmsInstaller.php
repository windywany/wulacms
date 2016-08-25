<?php
/*
 * KissCms
 */
defined ( 'KISSGO' ) or exit ( 'No direct script access allowed' );
/**
 * 管理员模板安装器.
 *
 * @author Leo Ning <windywany@gmail.com>
 *        
 */
class CmsInstaller extends AppInstaller {
	public function getAuthor() {
		return 'Leo Ning';
	}
	public function getDscription() {
		return '内容管理系统，提供内容管理功能.';
	}
	public function getName() {
		return '内容管理系统';
	}
	public function getDependences() {
		$dependences ['media'] = '[0.0.1,)';
		return $dependences;
	}
	public function getWebsite() {
		return 'http://www.wulacms.com/';
	}
	public function getVersionLists() {
		$lists ['0.0.1'] = '2014060600001';
		$lists ['0.0.2'] = '2014081300002';
		$lists ['0.0.3'] = '2014081900003';
		$lists ['0.0.4'] = '2014090400004';
		$lists ['0.0.5'] = '2014090500005';
		$lists ['0.0.6'] = '2014090500006';
		$lists ['0.0.7'] = '2014091100007';
		$lists ['0.0.8'] = '2014091100008';
		$lists ['0.0.9'] = '2014091100009';
		$lists ['0.0.10'] = '2014091400010';
		$lists ['0.0.11'] = '2014091500011';
		$lists ['1.0.0'] = '2014100800012';
		$lists ['1.0.1'] = '2014101100013';
		$lists ['1.0.2'] = '2014111000014';
		$lists ['1.0.3'] = '2014121900015';
		$lists ['1.1.0'] = '2014122900016';
		$lists ['1.2.0'] = '2014123000017';
		$lists ['1.5.0'] = '2015011500018';
		$lists ['1.5.1'] = '2015012900019';
		$lists ['2.0.0'] = '2015020800020';
		$lists ['2.0.1'] = '2015031200021';
		$lists ['2.5.0'] = '2015062800022';
		$lists ['2.5.1'] = '2015070300023';
		$lists ['2.6.0'] = '2015070400024';
		$lists ['2.7.0'] = '2015070700025';
		$lists ['3.0.0'] = '2015081000026';
		$lists ['3.0.1'] = '2015101100027';
		$lists ['3.5.0'] = '2015112000028';
		$lists ['4.0.0'] = '2016051300029';
		$lists ['4.0.2'] = '201605310030';
		$lists ['4.1.0'] = '201605310031';
		$lists ['4.1.1'] = '201607180032';
		$lists ['4.5.0'] = '201608190033';
		return $lists;
	}
	/**
	 * 重新生成页面的url_key当页面的url_key为空时。
	 *
	 * @param DatabaseDialect $dialect        	
	 */
	public function upgradeTo2014081300002($dialect) {
		dbupdate ( '{cms_page}' )->setDialect ( $dialect )->set ( array ('url_key' => imv ( 'MD5(url)' ) ) )->where ( array ('url_key $' => null ) )->exec ();
		dbupdate ( '{cms_page}' )->setDialect ( $dialect )->set ( array ('image' => '' ) )->where ( array ('image $' => null ) )->exec ();
		return true;
	}
	/**
	 * 升级到1.0.0 ,计算栏目的breadcrumb和每个栏目的最顶级栏目.
	 *
	 * @param unknown $dialect        	
	 */
	public function upgradeTo2014100800012($dialect) {
		// 取所有栏目数据
		$channels = dbselect ( 'upid,id,refid' )->setDialect ( $dialect )->from ( '{cms_channel}' )->toArray ();
		// 遍历树形数据
		$iterator = new TreeIterator ( $channels, 0, 'id', 'upid' );
		$nodes = $iterator->getNodes ();
		unset ( $nodes [0] );
		foreach ( $nodes as $id => $node ) {
			$parents = $node->getParentsIdList ( 'refid' );
			if ($parents) {
				$len = count ( $parents ) - 1;
				$root = $parents [$len];
				$parents = implode ( ',', $parents );
			} else {
				$data = $node->getData ();
				$root = $data ['refid'];
				$parents = '';
			}
			dbupdate ( '{cms_channel}' )->setDialect ( $dialect )->set ( array ('parents' => $parents,'root' => $root ) )->where ( array ('id' => $id ) )->exec ();
		}
		return true;
	}
	public function upgradeTo2014101100013($dialect) {
		$data ['status'] = 2;
		dbupdate ( '{cms_page}' )->setDialect ( $dialect )->set ( $data )->exec ();
		return true;
	}
}