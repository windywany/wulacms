<?php
defined ( 'KISSGO' ) or exit ( 'No direct script access allowed' );

$tables = array ();

$tables ['1.0.0'] [] = "CREATE TABLE IF NOT EXISTS `{prefix}album` (
    `page_id` BIGINT UNSIGNED NOT NULL COMMENT '关联的页面编号',
    `user_id` BIGINT UNSIGNED NOT NULL DEFAULT 0 COMMENT '用户编号',
    PRIMARY KEY (`page_id`),
    UNIQUE INDEX `UDX_USER_ALBUM` (`user_id` ASC , `page_id` ASC)
)  ENGINE=INNODB DEFAULT CHARACTER SET=UTF8 COMMENT='相册'";

$tables ['1.0.0'] [] = "CREATE TABLE IF NOT EXISTS `{prefix}album_item` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '',
    `create_time` INT NOT NULL DEFAULT 0 COMMENT '创建时间',
    `create_uid` INT NOT NULL DEFAULT 0 COMMENT '创建用户 ',
    `update_time` INT NOT NULL DEFAULT 0 COMMENT '最后修改时间',
    `update_uid` INT NOT NULL DEFAULT 0 COMMENT '最后更新用户 ',
    `deleted` TINYINT UNSIGNED NOT NULL DEFAULT 0 COMMENT '1:删除；0:未删除',
    `album_id` BIGINT UNSIGNED NOT NULL COMMENT '相册ID（同页面ID）',
	`is_hot` TINYINT UNSIGNED NOT NULL DEFAULT 0 COMMENT '是否是推荐',
    `url` VARCHAR(1024) NOT NULL COMMENT '图片URL',
	`url1` VARCHAR(1024) NOT NULL COMMENT '图片缩略图URL',
    `title` VARCHAR(128) NOT NULL COMMENT '标题',
    `note` TEXT NULL COMMENT '描述',
    `search_index` TEXT NULL COMMENT '全文搜索索引',
    PRIMARY KEY (`id`),
    INDEX `IDX_ALBUM_ID` (`album_id` ASC),
	INDEX `IDX_IS_HOT` (`is_hot` ASC),
    FULLTEXT INDEX `FDX_SEARCH_IDX` ( `search_index` ASC )
)  ENGINE=INNODB DEFAULT CHARACTER SET=UTF8 COMMENT='相册中的相片'";

$tables ['1.5.0'] [] = "ALTER TABLE `{prefix}album_item` ADD `width` INT NOT NULL DEFAULT 0 COMMENT '图片宽' AFTER `is_hot`";
$tables ['1.5.0'] [] = "ALTER TABLE `{prefix}album_item` ADD `height` INT NOT NULL DEFAULT 0 COMMENT '图片高' AFTER `width`";
$tables ['1.5.0'] [] = "ALTER TABLE `{prefix}album_item` ADD `size` INT NOT NULL DEFAULT 0 COMMENT '体积' AFTER `height`";

$tables ['1.6.0'] [] = "ALTER TABLE `{prefix}album_item` ADD `size1` INT NOT NULL DEFAULT 0 COMMENT '体积' AFTER `height`";
