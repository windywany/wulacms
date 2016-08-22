<?php
defined ( 'KISSGO' ) or exit ( 'No direct script access allowed' );

$tables = array ();

$tables ['0.0.1'] [] = "CREATE TABLE `{prefix}cms_msite` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `create_time` int(10) unsigned NOT NULL,
  `create_uid` int(10) unsigned NOT NULL,
  `update_time` int(10) unsigned NOT NULL,
  `update_uid` int(10) unsigned NOT NULL,
  `domain` varchar(32) NOT NULL COMMENT '二级域名',
  `channel` varchar(32) NOT NULL COMMENT '栏目',
  `topics` text NULL COMMENT '专题栏目',		
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='站点域名'";

$tables ['1.0.0'] [] = "ALTER TABLE `{prefix}cms_msite` ADD `mdomain` varchar(32) NOT NULL DEFAULT '' COMMENT '移动二级域名' AFTER `domain`";