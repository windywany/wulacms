<?php
defined ( 'KISSGO' ) or exit ( 'No direct script access allowed' );

$tables ['0.0.1'] [] = "CREATE TABLE `{prefix}passport_session` (
    `session_id` VARCHAR(32) NOT NULL COMMENT '会话ID',
    `user_id` INT UNSIGNED NOT NULL COMMENT '用户ID',
    `create_time` INT UNSIGNED NOT NULL COMMENT '创建时间',
    `expire_time` INT UNSIGNED NOT NULL COMMENT '过期时间',
    PRIMARY KEY (`session_id`)
)  ENGINE=InnoDB DEFAULT CHARACTER SET=utf8 COMMENT='通行证会话表'";

$tables ['0.0.2'] [] = "CREATE TABLE `{prefix}member` (
    `mid` INT UNSIGNED NOT NULL AUTO_INCREMENT,
	`registered` INT UNSIGNED NOT NULL COMMENT '注册时间',
    `update_time` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '最后修改时间',
    `update_uid` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '最后修改用户',
	`deleted` tinyint(1) NOT NULL DEFAULT 0,
    `type` VARCHAR(32) NOT NULL DEFAULT 'U' COMMENT '会员类型',
    `group_id` SMALLINT UNSIGNED NOT NULL COMMENT '用户组',
	`role_id` INT UNSIGNED NOT NULL COMMENT '角色',
	`invite_mid` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '邀请用户ID',
    `username` VARCHAR(32) DEFAULT NULL COMMENT '用户名',
    `email` VARCHAR(64) DEFAULT NULL COMMENT '邮件',
	`phone` VARCHAR(32) DEFAULT NULL COMMENT '手机号',
    `passwd` VARCHAR(64) NOT NULL COMMENT '密码',
    `status` SMALLINT(4) NOT NULL COMMENT '状态1:正常,0:禁用,2:未激活',
    `ip` VARCHAR(64) NOT NULL COMMENT '注册IP',
    `nickname` VARCHAR(64) NULL DEFAULT '' COMMENT '昵称',
	`avatar` varchar(1024) NULL COMMENT '头像',
	`avatar_big` varchar(1024) NULL COMMENT '大头像',
	`avatar_small` varchar(1024) NULL COMMENT '小头像',
    `auth_status` SMALLINT UNSIGNED NOT NULL DEFAULT 0 COMMENT '0:未认证,1:认证中,2:已经认证,3:认证失败',
    `auth_error` TEXT NULL COMMENT '认证失败原因',
    PRIMARY KEY (`mid`),
	INDEX `IDX_USERNAME` (`username` ASC),
    INDEX `IDX_EMAIL` (`email` ASC),
    INDEX `IDX_STATUS` (`status` ASC),
	INDEX `IDX_DELETED` (`deleted` ASC),
    INDEX `IDX_GROUPID` (`group_id` ASC),
	INDEX `IDX_ROLEID` (`role_id` ASC),
	INDEX IDX_INVITE_UID (`invite_mid`),
	INDEX IDX_MOBILE_PHONE (`phone`),
    INDEX `IDX_AUTH_STATUS` (`auth_status` ASC)
)  ENGINE=InnoDB COMMENT='会员表'";

$tables ['0.0.2'] [] = "CREATE TABLE `{prefix}member_activation` (
    `mid` INT UNSIGNED NOT NULL,
    `create_time` INT UNSIGNED NOT NULL DEFAULT 0,
    `update_time` INT UNSIGNED NOT NULL DEFAULT 0,
    `update_uid` INT UNSIGNED NOT NULL DEFAULT 0,
	`mail_actived_time` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '邮件激活时间',
    `mail_active_code` VARCHAR(128) NULL COMMENT '邮件激活码',
	`mail_active_code_expire` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '邮件激活码过期时间',
	`phone_actived_time` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '手机号激活时间',
    `phone_active_code` VARCHAR(16) NULL COMMENT '手机激活码',
	`phone_active_code_expire` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '手机激活码过期时间',
    PRIMARY KEY (`mid`)
)  ENGINE=InnoDB DEFAULT CHARACTER SET=utf8 COMMENT='会员激活记录'";

$tables ['0.0.2'] [] = "CREATE TABLE `{prefix}member_meta` (
    `mid` INT UNSIGNED NOT NULL,
    `create_time` INT UNSIGNED NOT NULL DEFAULT 0,
    `update_time` INT UNSIGNED NOT NULL DEFAULT 0,
    `update_uid` INT UNSIGNED NOT NULL DEFAULT 0,
    `name` VARCHAR(24) NULL COMMENT 'meta名',
    `value` TEXT NULL COMMENT 'meta值',
    INDEX `IDX_MID_NAME` (`mid`,`name`)
)  ENGINE=InnoDB DEFAULT CHARACTER SET=utf8 COMMENT='会员meta记录'";

$tables ['1.0.0'] [] = "ALTER TABLE `{prefix}member` ADD `invite_code` VARCHAR(32) DEFAULT '' COMMENT '会员的邀请码' AFTER `nickname`";
$tables ['1.0.0'] [] = "ALTER TABLE `{prefix}member` ADD `recommend_code` VARCHAR(32) DEFAULT '' COMMENT '我的推荐码,用于推荐其他人注册.' AFTER `nickname`";
$tables ['1.0.0'] [] = "CREATE INDEX `IDX_M_ICODE` ON `{prefix}member` (`invite_code`)";
$tables ['1.0.0'] [] = "CREATE INDEX `IDX_R_ICODE` ON `{prefix}member` (`recommend_code`)";

$tables ['1.5.0'] [] = "ALTER TABLE `{prefix}member` ADD `oauth_id` BIGINT unsigned NOT NULL DEFAULT 0 COMMENT '第三方登录记录编号' AFTER `invite_mid`";

$tables ['1.5.0'] [] = "CREATE TABLE `{prefix}passport_oauth` (
  `id` BIGINT unsigned NOT NULL AUTO_INCREMENT,
  `create_time` int unsigned NOT NULL,
  `app` varchar(100) NOT NULL COMMENT '第三方平台标识',
  `app_id` varchar(100) NOT NULL COMMENT '授权用户的ID',
  `app_nick` varchar(256) NOT NULL COMMENT '第三方平台上的昵称',
  `token` varchar(256) NOT NULL COMMENT '第三方平台提供的ACCESS TOKEN',
  `token_secret` varchar(256) COMMENT '访问第三方平台可能要用到的密码',
  `avilable` tinyint(1) NOT NULL DEFAULT 1 COMMENT '登录授权是否有效',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT '第三方登录授权记录.'";

$tables ['1.5.0'] [] = "CREATE TABLE `{prefix}passport_oauth_data` (
  `oauth_id` BIGINT unsigned NOT NULL COMMENT '登录记录值',
  `name` varchar(20) NOT NULL COMMENT '变量名',
  `create_time` int unsigned NOT NULL,
  `val` text NOT NULL COMMENT '值',
  PRIMARY KEY (`oauth_id`,`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT '可以从第三方取到的值'";

$tables ['2.1.0'] [] = "CREATE TABLE IF NOT EXISTS `{prefix}member_has_role` (
  `mid` INT UNSIGNED NOT NULL COMMENT '会员编号',
  `role_id` INT UNSIGNED NOT NULL COMMENT '角色编号',
  `sort` SMALLINT UNSIGNED NOT NULL DEFAULT 999 COMMENT '授权时排序',
  PRIMARY KEY (`mid`, `role_id`),
  INDEX `IDX_ROLEID` (`role_id` ASC)
)ENGINE = InnoDB COMMENT = '会员的角色'";
