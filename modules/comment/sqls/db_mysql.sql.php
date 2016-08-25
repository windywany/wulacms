<?php
defined ( 'KISSGO' ) or exit ( 'No direct script access allowed' );
$tables = array ();

$tables ['1.0.0'] [] = "CREATE TABLE IF NOT EXISTS `{prefix}comments` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `create_time` INT UNSIGNED NOT NULL DEFAULT 0,
    `create_uid` INT UNSIGNED NOT NULL DEFAULT 0,
    `update_time` INT UNSIGNED NOT NULL DEFAULT 0,
    `update_uid` INT UNSIGNED NOT NULL DEFAULT 0,	
    `deleted` TINYINT(1) UNSIGNED NOT NULL DEFAULT 0,
    `page_id` BIGINT UNSIGNED NOT NULL DEFAULT 0,	
    `parent` BIGINT UNSIGNED NOT NULL DEFAULT 0 COMMENT '被回复的评论ID',
    `status` SMALLINT UNSIGNED NOT NULL DEFAULT 0 COMMENT '0 => 待审,1 => 获准,2 => 垃圾',
    `author` VARCHAR(128) NOT NULL,
    `author_ip` VARCHAR(100) NOT NULL,
    `author_email` VARCHAR(256) NULL,
    `author_url` VARCHAR(256) NULL,    
	`subject` VARCHAR(512) NULL COMMENT '主题',
    `content` TEXT NULL,
    PRIMARY KEY (`id`),
    INDEX `IDX_PAGE_ID` (`page_id` ASC),
    INDEX `IDX_PARENT` (`parent` ASC),	
    INDEX `IDX_STATUS` (`deleted` ASC , `status` ASC),
    INDEX `IDX_USER_ID` (`create_uid` ASC)
)  ENGINE=InnoDB DEFAULT CHARACTER SET=utf8 COMMENT='评论'";

$tables ['1.0.1'] [] = "CREATE TABLE IF NOT EXISTS `{prefix}comments_msg` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `create_time` INT UNSIGNED NOT NULL DEFAULT 0,
    `create_uid` INT UNSIGNED NOT NULL DEFAULT 0,
    `update_time` INT UNSIGNED NOT NULL DEFAULT 0,
    `update_uid` INT UNSIGNED NOT NULL DEFAULT 0,
	`user_id` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '会员编号',
    `deleted` TINYINT(1) UNSIGNED NOT NULL DEFAULT 0,
    `page_id` BIGINT UNSIGNED NOT NULL DEFAULT 0,
    `parent` BIGINT UNSIGNED NOT NULL DEFAULT 0 COMMENT '第一个留言(起始留言)',
	`replyto` BIGINT UNSIGNED NOT NULL DEFAULT 0 COMMENT '回复留言ID',
    `status` SMALLINT UNSIGNED NOT NULL DEFAULT 0 COMMENT '0 => 新留言,1 => 已经处理,2 => 垃圾',	
    `author` VARCHAR(128) NOT NULL,
    `author_ip` VARCHAR(100) NOT NULL,
    `author_email` VARCHAR(256) NULL,
    `author_url` VARCHAR(256) NULL,
	`author_phone` VARCHAR(256) NULL COMMENT '联系电话',	
	`author_qq` VARCHAR(256) NULL COMMENT 'QQ',
	`author_weixin` VARCHAR(256) NULL COMMENT '微信',
	`author_weibo`  VARCHAR(256) NULL COMMENT '微博',
	`author_address` VARCHAR(1024) NULL COMMENT '联系地址',	
	`subject` VARCHAR(512) NULL COMMENT '主题或处理结果',
    `content` TEXT NULL,
    PRIMARY KEY (`id`),
    INDEX `IDX_PAGE_ID` (`page_id` ASC),
    INDEX `IDX_PARENT` (`parent` ASC),
    INDEX `IDX_STATUS` (`deleted` ASC , `status` ASC),
    INDEX `IDX_USER_ID` (`create_uid` ASC)
)  ENGINE=InnoDB DEFAULT CHARACTER SET=utf8 COMMENT='留言'";
