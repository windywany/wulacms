<?php
defined ( 'KISSGO' ) or exit ( 'No direct script access allowed' );

$tables ['1.0.0'] [] = "CREATE TABLE IF NOT EXISTS `{prefix}cms_stags_index` (
    `page_id` BIGINT UNSIGNED NOT NULL,
    `tag` VARCHAR(16) NOT NULL,
    PRIMARY KEY (`page_id`,`tag`)
)  ENGINE=InnoDB DEFAULT CHARACTER SET=utf8 COMMENT='标签索引记录'";

$tables ['1.0.0'] [] = "CREATE TABLE IF NOT EXISTS `{prefix}cms_stags` (
    `page_id` BIGINT UNSIGNED NOT NULL,
    `tags` text NULL,
    PRIMARY KEY (`page_id`)
)  ENGINE=InnoDB DEFAULT CHARACTER SET=utf8 COMMENT='标签索'";

$tables ['1.0.1'] [] = "ALTER TABLE `{prefix}cms_stags` ADD `my_tags` TEXT NULL COMMENT '可用用于调用其它页面的标签,模板中使用.' AFTER `tags`";