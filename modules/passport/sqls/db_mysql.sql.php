<?php
defined('KISSGO') or exit ('No direct script access allowed');

$tables ['0.0.1'] [] = "CREATE TABLE `{prefix}passport_session` (
    `session_id` VARCHAR(32) NOT NULL COMMENT '会话ID',
    `user_id` INT UNSIGNED NOT NULL COMMENT '用户ID',
    `create_time` INT UNSIGNED NOT NULL COMMENT '创建时间',
    `expire_time` INT UNSIGNED NOT NULL COMMENT '过期时间',
    PRIMARY KEY (`session_id`)
) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8 COMMENT='通行证会话表'";

$tables ['0.0.2'] [] = "CREATE TABLE `{prefix}member` (
    `mid` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
	`registered` INT(10) UNSIGNED NOT NULL COMMENT '注册时间',
    `update_time` INT(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '最后修改时间',
    `update_uid` INT(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '最后修改用户',
	`deleted` TINYINT(1) NOT NULL DEFAULT 0,
    `group_id` SMALLINT UNSIGNED NOT NULL DEFAULT 0 COMMENT '用户组',
    `gender` TINYINT(2) NOT NULL DEFAULT 0 COMMENT '性别,0保密,1男,2女',
    `username` VARCHAR(32) NOT NULL COMMENT '用户名',
    `email` VARCHAR(64) DEFAULT NULL COMMENT '邮件',
	`phone` VARCHAR(32) DEFAULT NULL COMMENT '手机号',
    `passwd` VARCHAR(64) NOT NULL COMMENT '密码',
    `status` SMALLINT(4) UNSIGNED NOT NULL COMMENT '状态1:正常,0:禁用,2:未激活',
    `nickname` VARCHAR(64) NULL DEFAULT '' COMMENT '昵称',
	`avatar` VARCHAR(1024) NULL COMMENT '头像',
	`avatar_big` VARCHAR(1024) NULL COMMENT '大头像',
	`avatar_small` VARCHAR(1024) NULL COMMENT '小头像',
	`salt` VARCHAR(64) NULL COMMENT 'salt',
	`ip` VARCHAR(64) NULL COMMENT 'IP',
    PRIMARY KEY (`mid`),
	INDEX `IDX_USERNAME` (`username` ASC),
	INDEX `IDX_EMAIL` (`email` ASC),
	INDEX IDX_MOBILE_PHONE (`phone` ASC),
    INDEX `IDX_STATUS` (`status` ASC),
	INDEX `IDX_DELETED` (`deleted` ASC),
    INDEX `IDX_GROUPID` (`group_id` ASC)
)  ENGINE=InnoDB DEFAULT CHARACTER SET=utf8mb4 COMMENT='会员表'";

$tables ['0.0.2'] [] = "CREATE TABLE `{prefix}member_meta` (
    `mid` INT(10) UNSIGNED NOT NULL,
    `create_time` INT UNSIGNED NOT NULL DEFAULT 0,
    `update_time` INT UNSIGNED NOT NULL DEFAULT 0,
    `update_uid` INT UNSIGNED NOT NULL DEFAULT 0,
    `name` VARCHAR(24) NULL COMMENT 'meta名',
    `value` TEXT NULL COMMENT 'meta值',
    INDEX `IDX_MID_NAME` (`mid`,`name`)
)  ENGINE=InnoDB DEFAULT CHARACTER SET=utf8mb4 COMMENT='会员meta记录'";

$tables ['1.0.0'] [] = "ALTER TABLE `{prefix}member` ADD `recommend_code` VARCHAR(32) DEFAULT '' COMMENT '我的推荐码,用于推荐其他人注册.' AFTER `nickname`";
$tables ['1.0.0'] [] = "ALTER TABLE `{prefix}member` ADD `invite_mid` INT(10) UNSIGNED DEFAULT 0 COMMENT '我的邀请人ID.' AFTER `nickname`";
$tables ['1.0.0'] [] = "CREATE INDEX `IDX_M_ICODE` ON `{prefix}member` (`invite_mid`)";
$tables ['1.0.0'] [] = "CREATE INDEX `IDX_R_ICODE` ON `{prefix}member` (`recommend_code`)";

$tables ['2.1.0'] [] = "CREATE TABLE IF NOT EXISTS `{prefix}member_has_role` (
  `mid` INT(10) UNSIGNED NOT NULL COMMENT '会员编号',
  `role_id` INT UNSIGNED NOT NULL COMMENT '角色编号',
  `sort` SMALLINT UNSIGNED NOT NULL DEFAULT 999 COMMENT '授权时排序',
  PRIMARY KEY (`mid`, `role_id`),
  INDEX `IDX_ROLEID` (`role_id` ASC)
)ENGINE = InnoDB COMMENT = '会员的角色'";

$tables ['2.1.2'] [] = "CREATE TABLE `{prefix}member_nickname_black` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `nickname` varchar(128)  NOT NULL,
  `create_time` int(10) unsigned DEFAULT '0',
  `update_time` int(10) unsigned DEFAULT '0',
  `create_uid` int(10) unsigned DEFAULT '0',
  `update_uid` int(10) unsigned DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `NAME_UNQ` (`nickname`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";

$tables ['2.1.3'] [] = "CREATE TABLE `{prefix}member_follower` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `mid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '用户ID',
  `follower` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '被关注用户ID',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '关注时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNI_MID_UID` (`mid`,`follower`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8";

$tables ['2.1.3'] [] = "CREATE TABLE `{prefix}member_oauth` (
  `id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `create_time` INT(10) UNSIGNED NOT NULL COMMENT '创建时间',
  `update_time` INT(10) UNSIGNED NOT NULL COMMENT '登录时间',
  `app` VARCHAR(16) NOT NULL COMMENT '第三方应用名称，wx,qq等',
  `openid` varchar(48) COMMENT '第三方登录时提供的OPENID',
  `nickname` varchar(256) DEFAULT NULL COMMENT '用户在第三方平台的名字',
  `rec_code` varchar(32) DEFAULT NULL COMMENT '推荐码',
  `channel` varchar(32) DEFAULT NULL COMMENT '来源渠道',
  `device` SMALLINT(5) UNSIGNED DEFAULT 0 COMMENT '设备,0未知；1安卓；2苹果；3WEB；4安卓平板；5IPAD；6其它',
  `deviceId` VARCHAR(64) DEFAULT NULL COMMENT '设备ID',
  `ip` VARCHAR(64) DEFAULT NULL COMMENT 'IP',
  `mid` INT(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '绑定到用户ID',
  `token` VARCHAR(256) DEFAULT NULL COMMENT '接口访问token',
  `token_secret` VARCHAR(256) DEFAULT NULL COMMENT '接口TOKEN相关的密钥',
  `avilable` TINYINT(1) UNSIGNED NOT NULL DEFAULT '1' COMMENT '是否可用',
  PRIMARY KEY (`id`),
  KEY `IDX_MID` (`mid`,`app`),
  UNIQUE KEY `UDX_APP_ID` (`app`,`openid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='第三方登录表'";

$tables ['2.1.3'] [] = "CREATE TABLE `{prefix}member_oauth_data` (
  `oauth_id` BIGINT(20) UNSIGNED NOT NULL COMMENT '登录记录值',
  `name` VARCHAR(20) NOT NULL COMMENT '变量名',
  `val` TEXT NOT NULL COMMENT '值',
  PRIMARY KEY (`oauth_id`,`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='可以从第三方取到的值'";

$tables['2.1.3'][] = "INSERT INTO `{prefix}user_group` (`upid`, `group_refid`, `group_name`, `type`, `level`, `coins`, `rank`) VALUES ('0', 'vip0', '新注册', 'vip', '1', '0', '初出茅庐')";
$tables['2.1.3'][] = "INSERT INTO `{prefix}user_role` (`role`, `role_name`, `type`, `priority`) VALUES ('vip', '普通会员', 'vip', '1')";

$tables['2.2.0'][] = "ALTER TABLE `{prefix}member` 
  ADD COLUMN `group_expire` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '用户组有效期,0表示永久有效' AFTER `group_id`";

$tables['2.3.0'][] = "ALTER TABLE `{prefix}member` 
ADD COLUMN `lastip` VARCHAR(64) NULL COMMENT '最后登录IP' AFTER `ip`";
$tables['2.3.0'][] = "ALTER TABLE `{prefix}member` 
ADD COLUMN `lastlogin` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '最后登录时间' AFTER `lastip`";

$tables['2.4.0'][] = "ALTER TABLE `{prefix}member_meta` 
ADD COLUMN `app` TINYINT(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '是否是APP设置的' AFTER `value`";