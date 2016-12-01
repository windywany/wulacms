<?php
$tables ['1.0.0'] [] = "CREATE TABLE IF NOT EXISTS `{prefix}sms_log` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `create_time` INT UNSIGNED NOT NULL COMMENT '发送时间',
    `tid` VARCHAR(64) NOT NULL COMMENT '业务ID',
	`phone` VARCHAR(11) NOT NULL COMMENT '手机号码',
    `vendor` VARCHAR(16) NOT NULL COMMENT '提供商',
    `status` TINYINT UNSIGNED NOT NULL COMMENT '状态,1:成功，0：失败',
    `content` VARCHAR(256) NOT NULL COMMENT '内容',
    `note` VARCHAR(512) NULL COMMENT '发送失败时错误信息',
    PRIMARY KEY (`id`)
)  ENGINE=INNODB COMMENT='短信发送日志'";
