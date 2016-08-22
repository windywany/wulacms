<?php
/*
 * Kisscms core sqls for mysql.
 */
defined ( 'KISSGO' ) or exit ( 'No direct script access allowed' );

$tables = array ();

$tables ['0.0.1'] [] = "CREATE TABLE `{prefix}user_group` (
  `group_id` SMALLINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `upid` SMALLINT UNSIGNED NOT NULL COMMENT '上级组编号',
  `group_refid` VARCHAR(32) NOT NULL COMMENT '应用引用ID',
  `group_name` VARCHAR(64) NOT NULL COMMENT '组名',
  `subgroups` TEXT NULL COMMENT '下级组编号列表',
  `note` VARCHAR(512) NULL COMMENT '说明',
  PRIMARY KEY (`group_id`),
  INDEX `IDX_UPID` (`upid` ASC),
  UNIQUE INDEX `UDX_REFID` (`group_refid` ASC)
)ENGINE = InnoDB COMMENT = '用户组'";

$tables ['0.0.1'] [] = "CREATE TABLE `{prefix}user` (
  `user_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `registered` INT UNSIGNED NOT NULL COMMENT '注册时间',
  `update_time` INT UNSIGNED NOT NULL COMMENT '最后更新时间',
  `group_id` SMALLINT UNSIGNED NOT NULL COMMENT '用户组',
  `username` VARCHAR(32) NOT NULL COMMENT '用户名',
  `email` VARCHAR(64) NOT NULL COMMENT '邮件',
  `passwd` VARCHAR(64) NOT NULL COMMENT '密码',
  `status` SMALLINT(4) NOT NULL COMMENT '状态1:正常,0:禁用',
  `ip` VARCHAR(64) NOT NULL COMMENT '注册IP',
  `nickname` VARCHAR(64) NULL DEFAULT '' COMMENT '昵称',
  PRIMARY KEY (`user_id`),
  UNIQUE INDEX `UDX_USERNAME` (`username` ASC),
  UNIQUE INDEX `UDX_EMAIL` (`email` ASC),
  INDEX `IDX_STATUS` (`status` ASC),
  INDEX `IDX_GROUPID` (`group_id` ASC)
)ENGINE = InnoDB COMMENT = '用户表'";

$tables ['0.0.1'] [] = "CREATE TABLE `{prefix}user_role` (
  `role_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `role` VARCHAR(32) NOT NULL,
  `role_name` VARCHAR(64) NOT NULL,
  `note` VARCHAR(512) NULL,
  PRIMARY KEY (`role_id`),
  UNIQUE INDEX `UDX_ROLE` (`role` ASC)
)ENGINE = InnoDB COMMENT = '用户角色'";

$tables ['0.0.1'] [] = "CREATE TABLE `{prefix}user_meta` (
  `meta_id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` INT UNSIGNED NOT NULL COMMENT '用户编号',
  `meta_name` VARCHAR(16) NOT NULL COMMENT '变量名',
  `meta_value` TEXT NULL COMMENT '值',
  PRIMARY KEY (`meta_id`),
  INDEX `IDX_USERID` (`user_id` ASC),
  INDEX `IDX_META` (`user_id` ASC, `meta_name` ASC)
)ENGINE = InnoDB COMMENT = '用户元数据'";

$tables ['0.0.1'] [] = "CREATE TABLE `{prefix}user_role_acl` (
  `role_id` INT UNSIGNED NOT NULL COMMENT '角色编号',
  `resource` VARCHAR(32) NOT NULL COMMENT '被访问资源',
  `allowed` TINYINT(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '是否允许. 1:允许;0:不允许',
  `priority` SMALLINT(5) unsigned NOT NULL DEFAULT '0' COMMENT '优先级',
  PRIMARY KEY (`role_id`,`resource`)
)ENGINE = InnoDB COMMENT = '访问控制列表'";

$tables ['0.0.1'] [] = "CREATE TABLE `{prefix}preferences` (
  `preference_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` INT UNSIGNED NOT NULL COMMENT '用户编号',
  `update_time` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '最后更新时间',
  `preference_group` VARCHAR(16) NOT NULL DEFAULT 'core' COMMENT '配置组',
  `name` VARCHAR(32) NOT NULL COMMENT '配置项',
  `value` LONGTEXT NULL COMMENT '值',
  PRIMARY KEY (`preference_id`),
  UNIQUE INDEX `UDX_PREFERENCE` (`preference_group` ASC, `name` ASC),
  INDEX `IDX_USERID` (`user_id` ASC)
)ENGINE = InnoDB COMMENT = '系统配置'";

$tables ['0.0.1'] [] = "CREATE TABLE `{prefix}apps` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` INT UNSIGNED NOT NULL COMMENT '用户编号',
  `update_time` INT UNSIGNED NOT NULL COMMENT '最后更新时间',
  `app` VARCHAR(32) NOT NULL COMMENT '应用名称',
  `status` TINYINT UNSIGNED NOT NULL DEFAULT 1 COMMENT '1:启用;0:禁用',
  `urlmapping` VARCHAR(32) NOT NULL COMMENT 'URL映射',
  `version` VARCHAR(16) NULL COMMENT '版本',
  `system` TINYINT(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '是否是系统应用',
  PRIMARY KEY (`id`),
  UNIQUE INDEX `UDX_MAPPING` (`urlmapping` ASC),
  UNIQUE INDEX `UDX_APPNAME` (`app` ASC),
  INDEX `IDX_USERID` (`user_id` ASC)
)ENGINE = InnoDB COMMENT = '系统应用'";

$tables ['0.0.1'] [] = "CREATE TABLE `{prefix}user_has_role` (
  `user_id` INT UNSIGNED NOT NULL COMMENT '用户编号',
  `role_id` INT UNSIGNED NOT NULL COMMENT '角色编号',
  `sort` SMALLINT UNSIGNED NOT NULL DEFAULT 999 COMMENT '授权时排序',
  PRIMARY KEY (`user_id`, `role_id`),
  INDEX `IDX_ROLEID` (`role_id` ASC)
)ENGINE = InnoDB COMMENT = '用户的角色'";

$tables ['0.0.1'] [] = "CREATE TABLE `{prefix}recycle` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` INT UNSIGNED NOT NULL COMMENT '执行删除的用户',
  `recycle_time` INT UNSIGNED NOT NULL COMMENT '放入回收站时间',
  `recycle_type` VARCHAR(64) NOT NULL COMMENT '收回的内容类型',
  `meta` VARCHAR(256) NOT NULL COMMENT '简短描述',
  `restore_clz` VARCHAR(24) NOT NULL COMMENT '还原类',
  `restore_value` TEXT NULL COMMENT '还原时使用的参数',
  PRIMARY KEY (`id`),
  INDEX `IDX_USERID` (`user_id` ASC)
)ENGINE = InnoDB COMMENT = '回收站'";

$tables ['0.0.1'] [] = "CREATE TABLE `{prefix}activity_log` (
  `id` BIGINT NOT NULL AUTO_INCREMENT,
  `create_time` INT UNSIGNED NOT NULL COMMENT '记录时间',
  `user_id` INT UNSIGNED NOT NULL COMMENT '用户编号',
  `meta` VARCHAR(128) NOT NULL COMMENT '简短说明',
  `level` VARCHAR(16) NOT NULL COMMENT '级别',
  `ip` VARCHAR(64) NOT NULL COMMENT 'IP',
  PRIMARY KEY (`id`),
  INDEX `IDX_USERID` (`user_id` ASC)
)ENGINE = InnoDB COMMENT = '活动日志'";
$tables ['0.0.1'] [] = "INSERT INTO `{prefix}user_group` VALUES (1, 0, 'sa', '超级管理组', '1', '')";
$tables ['0.0.1'] [] = "INSERT INTO `{prefix}user_role` (`role_id`, `role`, `role_name`, `note`) VALUES (1, 'superadmin', '超级管理员', NULL),(2, 'admin', '管理员', NULL),(3, 'anonymous', '匿名用户', NULL)";
// 0.0.2
$tables ['0.0.2'] [] = "ALTER TABLE `{prefix}activity_log` ADD activity VARCHAR(32) DEFAULT '' COMMENT '活动' AFTER `user_id`";

$tables ['0.0.3'] [] = "ALTER TABLE `{prefix}user` ADD `update_uid` int unsigned not null default 0 AFTER `update_time`";
$tables ['0.0.3'] [] = "ALTER TABLE `{prefix}user` ADD `deleted` tinyint(1) NOT NULL DEFAULT 0 AFTER `update_uid`";

$tables ['0.0.4'] [] = "ALTER TABLE `{prefix}user_role_acl` modify `priority` INT UNSIGNED NOT NULL DEFAULT 0";

$tables ['0.0.5'] [] = "CREATE TABLE `{prefix}catalog` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `create_time` INT UNSIGNED NOT NULL DEFAULT 0,
    `update_time` INT UNSIGNED NOT NULL DEFAULT 0,
    `create_uid` INT UNSIGNED NOT NULL DEFAULT 0,
    `update_uid` INT UNSIGNED NOT NULL DEFAULT 0,
    `deleted` TINYINT(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '是否删除',
    `upid` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '上级分类',
    `type` VARCHAR(32) NOT NULL COMMENT '分类的种类',
    `name` VARCHAR(64) NOT NULL COMMENT '分类名',
    `note` TEXT NULL COMMENT '说明',
    PRIMARY KEY (`id`),
    UNIQUE INDEX `UDX_TYPE` (`type` ASC , `name` ASC),
    INDEX `IDX_UPID` (`upid` ASC),
    INDEX `IDX_DELETE` (`deleted` ASC)
)  ENGINE=InnoDB COMMENT='系统分类表'";

$tables ['0.0.6'] [] = "CREATE TABLE `{prefix}catalog_type` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `create_time` int(10) unsigned NOT NULL,
  `create_uid` int(10) unsigned NOT NULL,
  `update_time` int(10) unsigned NOT NULL,
  `update_uid` int(10) unsigned NOT NULL,
  `deleted` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `type` varchar(32) NOT NULL COMMENT '识别ID',
  `name` varchar(64) NOT NULL COMMENT '分类名称',
  `note` VARCHAR(512) NULL COMMENT '说明',
  PRIMARY KEY (`id`),
  UNIQUE KEY `UDX_REFID` (`type`),
  KEY `IDX_DELETED` (`deleted`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='分类类型'";

$tables ['0.0.7'] [] = "ALTER TABLE `{prefix}catalog` ADD `alias` varchar(32) NULL COMMENT '另名' AFTER `type`";

$tables ['0.0.8'] [] = "CREATE TABLE `{prefix}notification` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `create_time` INT UNSIGNED NOT NULL DEFAULT 0,
    `create_uid` INT UNSIGNED NOT NULL DEFAULT 0,
    `update_time` INT UNSIGNED NOT NULL DEFAULT 0,
    `update_uid` INT UNSIGNED NOT NULL DEFAULT 0,
    `expire_time` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '过期时间',
    `deleted` TINYINT(1) UNSIGNED NOT NULL DEFAULT 0,
	`title` VARCHAR(256) NOT NULL COMMENT '通知标题',
    `message` TEXT NULL COMMENT '通知正文',
    PRIMARY KEY (`id`),
    INDEX `IDX_DELETE_EXPIRE` (`deleted` ASC , `expire_time` ASC)
)  ENGINE=InnoDB DEFAULT CHARACTER SET=utf8 COMMENT='系统通知'";

$tables ['1.0.0'] [] = "ALTER TABLE `{prefix}user_group` ADD `parents` text NULL COMMENT '上级用户组列表' AFTER `group_name`";

$tables ['1.0.1'] [] = "ALTER TABLE `{prefix}user_role` ADD `priority` INT UNSIGNED DEFAULT 0 COMMENT '权重'";

$tables ['1.0.3'] [] = "DROP INDEX `UDX_TYPE` ON `{prefix}catalog`";

$tables ['1.1.1'] [] = "ALTER TABLE `{prefix}catalog_type` ADD `is_enum` TINYINT(1) UNSIGNED DEFAULT 0 COMMENT '是否是枚举' AFTER `deleted`";

$tables ['1.5.0'] [] = "CREATE TABLE `{prefix}widgets` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `create_time` INT UNSIGNED NOT NULL DEFAULT 0,
    `create_uid` INT UNSIGNED NOT NULL DEFAULT 0,
    `update_time` INT UNSIGNED NOT NULL DEFAULT 0,
    `update_uid` INT UNSIGNED NOT NULL DEFAULT 0,
    `page` VARCHAR(32) NOT NULL COMMENT '页面',
    `pos` VARCHAR(32) NOT NULL COMMENT '位置',
	`wid` VARCHAR(32) NOT NULL COMMENT '编号',
	`name` VARCHAR(128) NOT NULL COMMENT '名称',
    `hidden` TINYINT(1) NOT NULL DEFAULT 0 COMMENT '是否隐藏',
    `sort` SMALLINT UNSIGNED NOT NULL DEFAULT 999 COMMENT '排序',
    `viewcls` VARCHAR(128) NOT NULL COMMENT '视图类',
    `datacls` VARCHAR(128) NOT NULL COMMENT '数据源类',
    `view_options` TEXT NULL COMMENT '视图选项',
    `data_options` TEXT NULL COMMENT '数据源选项',
    PRIMARY KEY (`id`),
    INDEX `IDX_PAGEID` (`page`)
)  ENGINE=InnoDB COMMENT='页面小部件'";

$tables ['1.5.1'] [] = "ALTER TABLE `{prefix}user_group` ADD `type` VARCHAR(16) DEFAULT 'admin' COMMENT '组类型' AFTER `group_name`";
$tables ['2.5.1'] [] = "CREATE UNIQUE INDEX `UDX_ALIAS` ON `{prefix}catalog` (`upid`,`alias`)";
$tables ['2.5.2'] [] = "ALTER TABLE `{prefix}user_role` ADD `type` VARCHAR(16) DEFAULT 'admin' COMMENT '角色类型' AFTER `role_name`";

$tables ['3.1.0'] [] = "DROP INDEX UDX_ALIAS ON `{prefix}catalog`";
$tables ['3.1.0'] [] = "CREATE UNIQUE INDEX UDX_ALIAS ON `{prefix}catalog`(`type`,`upid`,`alias`)";

$tables ['3.1.1'] [] = "ALTER TABLE `{prefix}preferences` MODIFY COLUMN `name`  varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '配置项' AFTER `preference_group`;";

$tables ['3.1.3'] [] = "ALTER TABLE `{prefix}catalog`
ADD COLUMN `parents`  varchar(500) NULL AFTER `type`,
ADD COLUMN `sub`  varchar(500) NULL AFTER `parents`";

