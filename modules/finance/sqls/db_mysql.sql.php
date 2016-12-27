<?php
defined('KISSGO') or exit ('No direct script access allowed');
$tables ['2.0.0'] [] = "CREATE TABLE `{prefix}member_withdraw_record` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `create_time` int(10) unsigned NOT NULL COMMENT '提现时间',
  `mid` int(10) unsigned NOT NULL COMMENT '提现会员编号',
  `amount` decimal(13,5) unsigned NOT NULL COMMENT '提现金额',
  `tax_rate` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '税率,万分之X',
  `tax_amount` decimal(13,5) unsigned NOT NULL DEFAULT '0.00000' COMMENT '税费',
  `discount_rate` int(10) unsigned NOT NULL DEFAULT '0' COMMENT ' 手续费,万分之X',
  `discount` decimal(13,5) unsigned NOT NULL DEFAULT '0.00000' COMMENT '手续费',
  `payment` decimal(13,5) unsigned NOT NULL COMMENT '实际付款',
  `platform` varchar(16) NOT NULL COMMENT '提现平台',
  `account` varchar(64) NOT NULL COMMENT '账户',
  `username` varchar(45) DEFAULT NULL COMMENT '用户真实姓名',
  `phone` varchar(13) DEFAULT '0' COMMENT '手机号',
  `status` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT '提现状态 0：申请中,1：审核通过，2:审核失败，3：已经付款',
  `approve_uid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '审核人',
  `approve_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '审核日期',
  `paid_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '付款日期',
  `paid_uid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '付款人',
  `transid` varchar(64) DEFAULT NULL COMMENT '付款回执编号',
  `approve_message` varchar(256) DEFAULT NULL COMMENT '审核备注',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='会员提现记录'";

$tables ['2.0.0'] [] = "CREATE TABLE `{prefix}member_deposit_record` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `create_time` int(10) unsigned NOT NULL COMMENT '充值时间',
  `mid` int(10) unsigned NOT NULL COMMENT '会员编号',
   `order_type` varchar(20) DEFAULT NULL COMMENT '订单类型', 
  `orderid` bigint(20) unsigned NOT NULL COMMENT '系统订单ID',
  `amount` decimal(13,3) unsigned NOT NULL COMMENT '充值金额',
  `platform` varchar(16) NOT NULL COMMENT '第三方平台',
  `transid` varchar(64) NOT NULL COMMENT '第三方交易ID',
  `account` varchar(128) NOT NULL COMMENT '充值账户',
  `confirmed` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '入账时间',
  `subject` varchar(128) DEFAULT NULL COMMENT '项目',
  `note` varchar(1024) DEFAULT NULL COMMENT '备注说明',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='会员充值记录'";

$tables ['2.0.0'] [] = "CREATE TABLE `{prefix}member_finance_account` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `create_time` int(10) unsigned NOT NULL COMMENT '账户创建时间',
  `mid` int(10) unsigned NOT NULL COMMENT '会员ID',
  `amount` decimal(13,3) unsigned NOT NULL DEFAULT '0.000' COMMENT '账户总额',
  `balance` decimal(13,3) unsigned NOT NULL DEFAULT '0.000' COMMENT '账户余额',
  `frozen_amount` decimal(13,3) unsigned NOT NULL DEFAULT '0.000' COMMENT '冻结金额',
  `mname` varchar(255) DEFAULT NULL COMMENT '会员名称',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4";
$tables['2.0.1'][]   = "ALTER TABLE `{prefix}member_deposit_record` ADD COLUMN  `deleted` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除,0正1删' AFTER `create_time`";
$tables['2.0.1'][]   = "ALTER TABLE `{prefix}member_deposit_record` ADD COLUMN  `order_confirmed` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '订单处理完成时间' AFTER `orderid`";
$tables['2.0.1'][]   = "ALTER TABLE `{prefix}member_finance_account` MODIFY COLUMN `mname`  varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '会员名称' AFTER `mid`";
$tables['2.0.1'][]   = "ALTER TABLE `{prefix}member_finance_account` ADD COLUMN `spend`  decimal(13,3) NULL DEFAULT 0.000 COMMENT '已经花费' AFTER `balance`";
$tables['2.0.1'][]   = "ALTER TABLE `{prefix}member_finance_account` ADD COLUMN `update_time`  decimal(10,0) NULL DEFAULT 0 COMMENT '更新时间' AFTER `spend`";

