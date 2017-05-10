<?php
$tables ['1.0.0'] [] = "CREATE TABLE IF NOT EXISTS `{prefix}mobi_channel` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `create_time` INT(10) UNSIGNED NOT NULL DEFAULT '0',
    `create_uid` INT(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '创建用户',
    `update_time` INT(10) UNSIGNED NOT NULL DEFAULT '0',
    `update_uid` INT(10) UNSIGNED NOT NULL DEFAULT '0',
	`deleted` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0',
	`has_carousel` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0',
    `refid` VARCHAR(10) NOT NULL,
    `name` VARCHAR(45) NULL,
	`hidden` TINYINT(1) UNSIGNED NOT NULL DEFAULT 0,
	`sort`  SMALLINT UNSIGNED NOT NULL DEFAULT 999,
	`flags` TEXT NULL,
    PRIMARY KEY (`id`),
	INDEX `MB_CH_DELETED` (`deleted`),
    UNIQUE INDEX `refid_UNIQUE` (`refid` ASC)
)  ENGINE=INNODB DEFAULT CHARACTER SET=UTF8 COMMENT='手机端栏目'";

$tables ['1.0.0'] [] = "CREATE TABLE IF NOT EXISTS `{prefix}mobi_channel_binds` (
    `mobi_refid` varchar(10) NOT NULL COMMENT '移动栏目REFID',
    `cms_refid` varchar(32) NOT NULL COMMENT 'CMS栏目REFID',
    PRIMARY KEY (`mobi_refid` , `cms_refid`),
    INDEX `IDX_CMS_CID` (`cms_refid` ASC)
)  ENGINE=INNODB DEFAULT CHARACTER SET=UTF8 COMMENT='手机端栏目与CMS栏目绑定'";

$tables ['1.0.0'] [] = "CREATE TABLE IF NOT EXISTS `{prefix}mobi_page_view` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `create_time` INT(10) UNSIGNED NOT NULL DEFAULT '0',
    `create_uid` INT(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '创建用户',
    `update_time` INT(10) UNSIGNED NOT NULL DEFAULT '0',
    `update_uid` INT(10) UNSIGNED NOT NULL DEFAULT '0',
	`deleted` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0',
	`name` varchar(45) DEFAULT NULL,
    `refid` VARCHAR(10) NOT NULL COMMENT '标识ID',
	`models` TEXT DEFAULT NULL COMMENT '可以显示的模型',
    `tpl` VARCHAR(128) NOT NULL COMMENT '用于生成详细展示数据的模板',
    `desc` VARCHAR(45) NULL COMMENT '描述',
    PRIMARY KEY (`id`),
	INDEX `MB_PV_DELETED` (`deleted`),
    UNIQUE INDEX `UDX_PAGE_VIEW_ID` (`refid` ASC)
)  ENGINE=INNODB DEFAULT CHARACTER SET=UTF8 COMMENT='手机端内容查看视图'";

$tables ['1.0.0'] [] = "CREATE TABLE IF NOT EXISTS `{prefix}mobi_page` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `create_time` INT(10) UNSIGNED NOT NULL DEFAULT '0',
    `create_uid` INT(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '创建用户',
    `update_time` INT(10) UNSIGNED NOT NULL DEFAULT '0',
    `update_uid` INT(10) UNSIGNED NOT NULL DEFAULT '0',
	`publish_time` INT(10) UNSIGNED NOT NULL DEFAULT '0',
	`deleted` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0',
	`status` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0',
    `page_id` BIGINT UNSIGNED NOT NULL COMMENT '相关的页面编号',
	`title` VARCHAR(256) NULL COMMENT '标题',
    `channel` VARCHAR(10) NOT NULL COMMENT '移动端的栏目',
    `is_carousel` TINYINT(1) NOT NULL DEFAULT 0 COMMENT '是否是轮播图数据',
    `list_view` VARCHAR(10) NULL COMMENT '列表中的样式',
    `page_view` VARCHAR(10) NULL COMMENT '详细页的样式',
	`flags` VARCHAR(1024) NULL COMMENT '页面标识',
    `custom_data` LONGTEXT  NULL COMMENT '自定义数据，由listview提供者决定,json格式',
    PRIMARY KEY (`id`),
    INDEX `IDX_MOBI_CHANNEL` (`deleted` ,`channel` ASC, `is_carousel`),
    INDEX `IDX_UPDATE_TIME` (`update_time` ASC),
	INDEX `IDX_PUBLISH_TIME` (`publish_time` ASC)
)  ENGINE=INNODB DEFAULT CHARACTER SET=UTF8 COMMENT='手机端页面列表'";

$tables ['1.1.0'] [] = "ALTER TABLE `{prefix}mobi_page` ADD `view_url` VARCHAR(1024) NOT NULL DEFAULT '' COMMENT '指定跳转页面地址' AFTER `flags`";
$tables ['1.1.0'] [] = "ALTER TABLE `{prefix}mobi_page` ADD `publish_day` DATE NOT NULL DEFAULT '2015-07-20' COMMENT '发布日期' AFTER `publish_time`";
$tables ['1.1.0'] [] = "ALTER TABLE `{prefix}mobi_page` ADD `sort` SMALLINT UNSIGNED NOT NULL DEFAULT 999 COMMENT '排序，越小越靠前' AFTER `publish_day`";

$tables ['1.2.0'] [] = "CREATE TABLE `{prefix}app_ads` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL COMMENT '备注名称',
  `os` tinyint(4) DEFAULT NULL COMMENT '1 android 2 iOS',
  `banner` varchar(255) DEFAULT NULL COMMENT '横屏广告',
  `screen` varchar(255) DEFAULT NULL COMMENT '全屏广告',
  `stream` varchar(255) DEFAULT NULL COMMENT '信息流广告',
  `clickinsert` varchar(255) DEFAULT NULL COMMENT '点击插屏广告',
  `probability` tinyint(3) unsigned DEFAULT '0' COMMENT '插屏广告概率 百分比',
  `create_uid` int(11) unsigned DEFAULT NULL,
  `update_uid` int(11) unsigned DEFAULT NULL,
  `create_time` int(11) unsigned DEFAULT NULL,
  `update_time` int(11) unsigned DEFAULT NULL,
  `deleted` tinyint(4) unsigned DEFAULT '0' COMMENT '0正常 1删除',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='app广告控制';";

$tables ['1.2.0'] [] = "CREATE TABLE `{prefix}app_version` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `update_type` tinyint(3) unsigned DEFAULT '0' COMMENT '是否强制升级 0 否 1 强制升级',
  `create_uid` int(11) unsigned DEFAULT NULL,
  `update_uid` int(11) unsigned DEFAULT NULL,
  `create_time` int(11) unsigned DEFAULT NULL,
  `update_time` int(11) unsigned DEFAULT NULL,
  `deleted` tinyint(4) unsigned DEFAULT '0' COMMENT '0正常 1删除',
  `app_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '应用名称 关联rest_app表',
  `version` varchar(255) DEFAULT NULL COMMENT '版本',
  `vername` int(10) unsigned DEFAULT NULL,
  `os` tinyint(4) DEFAULT NULL COMMENT '1 android 2 iOS',
  `apk_file` varchar(1024) DEFAULT NULL COMMENT '母包文件路径',
  `size` int(10) unsigned DEFAULT '0' COMMENT '下载包大小',
  `desc` text COMMENT '更新描述',
  `prefix` varchar(255) DEFAULT NULL COMMENT 'apk 生成包名前缀',
  `attr` text COMMENT '附件配置信息',
  `url` varchar(255) DEFAULT NULL COMMENT '默认下载地址',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='app版本控制';";

$tables ['1.2.0'] [] = "CREATE TABLE `{prefix}app_version_market` (
    `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
    `version_id` int(10) unsigned DEFAULT NULL COMMENT 'app_version 表id',
    `market_name` varchar(255) DEFAULT NULL,
    `market` varchar(255) DEFAULT NULL,
    `ad_config_id` int(10) unsigned DEFAULT '0' COMMENT '广告配置表id',
    `url` varchar(255) DEFAULT NULL COMMENT '下载地址',
    `create_uid` int(10) unsigned DEFAULT NULL,
    `update_uid` int(10) unsigned DEFAULT NULL,
    `create_time` int(10) unsigned DEFAULT NULL,
    `update_time` int(10) unsigned DEFAULT NULL,
    `deleted` tinyint(3) unsigned DEFAULT '0',
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='版本信息和市场对应关系表';";

$tables ['1.2.1'] [] = "ALTER TABLE `{prefix}app_ads`
ADD COLUMN `bottom`  varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL AFTER `banner`;";
