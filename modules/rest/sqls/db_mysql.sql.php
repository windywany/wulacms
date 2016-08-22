<?php
$tables ['0.0.1'] [] = "CREATE TABLE `{prefix}rest_apps` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `create_time` INT UNSIGNED NOT NULL,
    `create_uid` INT UNSIGNED NOT NULL,
    `update_time` INT UNSIGNED NOT NULL,
    `update_uid` INT UNSIGNED NOT NULL,
    `name` VARCHAR(128) NOT NULL COMMENT '应用名称',
    `appkey` VARCHAR(64) NOT NULL COMMENT 'APP ID.',
    `appsecret` VARCHAR(32) NOT NULL COMMENT '安全码',
    `note` VARCHAR(256) NULL COMMENT '说明',
    PRIMARY KEY (`id`),
    UNIQUE INDEX `UDX_APPKEY` (`appkey` ASC)
)  ENGINE=InnoDB DEFAULT CHARACTER SET=utf8 COMMENT='可通过RESTful接入的应用'";

$tables['0.0.2'][] = "ALTER TABLE `{prefix}rest_apps` ADD callback_url varchar(1024) DEFAULT '' COMMENT '回调URL' AFTER `appsecret`";
