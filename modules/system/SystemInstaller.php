<?php
/*
 * KissCms
 */
defined ( 'KISSGO' ) or exit ( 'No direct script access allowed' );
/**
 * 验证程序安装程序.
 *
 * @author Guangfeng
 *
 */
class SystemInstaller extends AppInstaller {
	public function getAuthor() {
		return 'Guangfeng Ning';
	}
	public function getDscription() {
		return '运行时环境.';
	}
	public function getName() {
		return '核心应用';
	}
	public function getWebsite() {
		return 'http://www.kissgo.org/';
	}
	public function getVersionLists() {
		$versions ['0.0.1'] = '2014081300000';
		$versions ['0.0.2'] = '2014081300001';
		$versions ['0.0.3'] = '2014081300002';
		$versions ['0.0.4'] = '2014081300003';
		$versions ['0.0.5'] = '2014081800004';
		$versions ['0.0.6'] = '2014090500005';
		$versions ['0.0.7'] = '2014090500007';
		$versions ['0.0.8'] = '2014091600008';
		$versions ['1.0.0'] = '2014101100009';
		$versions ['1.0.1'] = '2014101300010';
		$versions ['1.0.2'] = '2014111200011';
		$versions ['1.0.3'] = '2014111300012';
		$versions ['1.1.0'] = '2014121400013';
		$versions ['1.1.1'] = '2014121500014';
		$versions ['1.2.0'] = '2015010700015';
		$versions ['1.5.0'] = '2015011300016';
		$versions ['1.5.1'] = '2015011300017';
		$versions ['1.5.2'] = '2015020500018';
		$versions ['2.0.0'] = '2015020500018';
		$versions ['2.1.0'] = '2015032000019';
		$versions ['2.5.0'] = '2015070400020';
		$versions ['2.5.1'] = '2015102200021';
		$versions ['2.5.2'] = '2015102300022';
		$versions ['3.0.0'] = '2015102300022'; // support namespace.
		$versions ['3.1.0'] = '2016011300023'; // 修改catalog表的ALIAS索引
		$versions ['3.1.1'] = '2016021600024'; // 修改preferences表 name 字段长度
		$versions ['3.1.2'] = '2016030900025'; // ajax controller 更改
		$versions ['3.1.3'] = '2016031400026'; // catalog 表 增加sub，parents字段
		$versions ['3.1.4'] = '2016032300027'; // 更新catalog 表中的sub，parents字段
		return $versions;
	}
	public function upgradeTo2014081300003($dialect) {
		dbdelete ()->setDialect ( $dialect )->from ( '{user_role_acl}' )->exec ();
		return true;
	}
	public function upgradeTo2014101100009($dialect) {
		// 取所有栏目数据
		$groups = dbselect ( 'upid,group_id' )->setDialect ( $dialect )->from ( '{user_group}' )->toArray ();
		// 遍历树形数据
		$iterator = new TreeIterator ( $groups, 0, 'group_id', 'upid' );
		$nodes = $iterator->getNodes ();
		unset ( $nodes [0] );
		foreach ( $nodes as $id => $node ) {
			$parents = $node->getParentsIdList ( 'group_id' );
			if ($parents) {
				$parents = implode ( ',', $parents );
			} else {
				$parents = '';
			}
			dbupdate ( '{user_group}' )->setDialect ( $dialect )->set ( array ('parents' => $parents ) )->where ( array ('group_id' => $id ) )->exec ();
		}
		return true;
	}
	public function upgradeTo2016032300027($dialect) {
		TreeIterator::updateTreeNode ( '{catalog}', array ('deleted' => 0 ), 'id', 'upid', 'parents', 'sub', $dialect );
		return true;
	}
}