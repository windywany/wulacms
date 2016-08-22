<?php
class BaidutoolkitInstaller extends AppInstaller {
	public function getVersionLists() {
		$v ['1.0.0'] = '20150603001';
		return $v;
	}
	public function getName() {
		return '百度站长工具箱';
	}
	public function getDscription() {
		return '1.主动推荐文章给百度，使用百度尽快收录; 2.移动适配';
	}
	public function getWebsite() {
		return 'http://www.kisscms.org/plugin/baidutoolkit';
	}
	public function getAuthor() {
		return '宁广丰';
	}
	/**
	 *
	 * @param DatabaseDialect $dialect        	
	 */
	public function onInstall($dialect) {
		$table = $dialect->getTableName ( '{cms_page}' );
		dbexec ( "ALTER TABLE `" . $table . "` ADD `baidu_sync` TINYINT(1) DEFAULT 0 COMMENT '是否已经同步给百度'", $dialect );
		return true;
	}
}