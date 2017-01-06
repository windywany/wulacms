<?php
defined('KISSGO') or exit ('No direct script access allowed');
$tables ['1.0.0'] [] = "CREATE TABLE `{prefix}member_points_account` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `create_time` int(10) unsigned NOT NULL COMMENT '账户创建时间',
  `mid` int(10) unsigned NOT NULL COMMENT '会员编号',
  `type` varchar(16) NOT NULL COMMENT '积分类型, summary 表示汇总账户',
  `amount` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '总积分',
  `balance` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '可用积分',
  `outlay` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '已经使用积分',
  `mname` varchar(255) DEFAULT NULL COMMENT '会员名称',
  `use_priority` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT '值越大越优先使用',
  PRIMARY KEY (`id`),
  KEY `IDX_TYPE_MID` (`type`,`mid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='会员积分账户表'";

$tables ['1.0.0'] [] = "CREATE TABLE `{prefix}member_points_record` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `create_time` int(10) unsigned NOT NULL COMMENT '积分产生时间',
  `mid` int(10) unsigned NOT NULL COMMENT '会员编号',
  `type` varchar(16) NOT NULL COMMENT '积分类型',
  `is_outlay` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否是支出，1是',
  `amount` int(11) NOT NULL COMMENT '数量',
  `balance` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '可用数量,只有is_outlay=0时才有意义',
  `expire_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '过期时间，只有is_outlay等于0的积分才有过期时间',
  `expired` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否已经过期',
  `subject` varchar(128) DEFAULT NULL COMMENT '积分项目,记录积分具体用途',
  `note` varchar(512) DEFAULT NULL COMMENT '备注说明',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='积分记录表'";

$tables ['1.0.0'] [] = "CREATE TABLE `{prefix}member_points_type` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(64) NOT NULL COMMENT '积分类型名称',
  `type` varchar(16) NOT NULL COMMENT '积分类型',
  `reserved` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否是系统预留的',
  `note` varchar(256) DEFAULT NULL,
  `deleted` tinyint(2) DEFAULT '0' COMMENT '0=>正常,1=>删除',
  `create_time` int(10) DEFAULT NULL,
  `update_time` int(10) DEFAULT NULL,
  `update_uid` int(11) DEFAULT '0',
  `create_uid` int(11) DEFAULT '0',
  `use_priority` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT '使用优先级越大越先使用',
  PRIMARY KEY (`id`),
  UNIQUE KEY `UDX_TYPE` (`type`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8 COMMENT='积分类型表'";

$tables['1.1.0'][] = "INSERT INTO `{prefix}member_points_type` (`name`,`type`,`reserved`,`note`,`deleted`,`create_time`,`update_time`,`update_uid`,`create_uid`,`use_priority`) VALUES ('汇总','summary',1,'积分汇总',0,0,0,0,0,0)";

$tables['1.1.0'][] = "INSERT INTO `{prefix}member_points_type` (`name`,`type`,`reserved`,`note`,`deleted`,`create_time`,`update_time`,`update_uid`,`create_uid`,`use_priority`) VALUES ('默认','default',1,'默认积分',0,0,0,0,0,0)";