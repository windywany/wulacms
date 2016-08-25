<?php
defined ( 'KISSGO' ) or exit ( 'No direct script access allowed' );

$tables = array ();

$tables ['1.0.0'] [] = "CREATE TABLE IF NOT EXISTS `{prefix}cms_digg` (
    `page_id` BIGINT UNSIGNED NOT NULL,
    `digg_total` INT UNSIGNED NOT NULL DEFAULT 0,
    `digg_0` INT UNSIGNED NOT NULL DEFAULT 0,
    `digg_1` INT UNSIGNED NOT NULL DEFAULT 0,
    `digg_2` INT UNSIGNED NOT NULL DEFAULT 0,
    `digg_3` INT UNSIGNED NOT NULL DEFAULT 0,
    `digg_4` INT UNSIGNED NOT NULL DEFAULT 0,
    `digg_5` INT UNSIGNED NOT NULL DEFAULT 0,
    `digg_6` INT UNSIGNED NOT NULL DEFAULT 0,
    `digg_7` INT UNSIGNED NOT NULL DEFAULT 0,
    `digg_8` INT UNSIGNED NOT NULL DEFAULT 0,
    `digg_9` INT UNSIGNED NOT NULL DEFAULT 0,
    PRIMARY KEY (`page_id`)
)  ENGINE=InnoDB DEFAULT CHARACTER SET=utf8 COMMENT='顶踩记录表'";

$tables ['1.0.0'] [] = "CREATE TABLE IF NOT EXISTS `{prefix}cms_digg_log` (
    `page_id` BIGINT UNSIGNED NOT NULL COMMENT '被评分的页面',
    `uuid` CHAR(13) NOT NULL COMMENT '用户标识',
    `create_time` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '评分时间',
    `digg` CHAR(1) NOT NULL COMMENT '评分值(0-9)',
    PRIMARY KEY (`page_id` , `uuid`)
)  ENGINE=InnoDB DEFAULT CHARACTER SET=utf8 COMMENT='简单的保证一个用户不可以评分多次(基于cookie或user id)'";
