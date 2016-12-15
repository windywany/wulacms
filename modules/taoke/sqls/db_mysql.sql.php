<?php
defined ( 'KISSGO' ) or exit ( 'No direct script access allowed' );

$tables = array ();



$tables ['1.0.0'] [] = "CREATE TABLE IF NOT EXISTS `{prefix}tbk_goods` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `update_time` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '最后更新时间',
    `page_id` BIGINT UNSIGNED NOT NULL,
    `goods_id` BIGINT UNSIGNED NOT NULL DEFAULT 0 COMMENT '商品ID',
    `goods_url` VARCHAR(1024) NOT NULL COMMENT '商品详情页链接地址',
    `tbk_url` VARCHAR(1024) NOT NULL COMMENT '淘宝客链接',
    `price` DECIMAL(10, 2 ) NOT NULL DEFAULT 0.00 COMMENT '商品价格(单位：元)',
    `discount` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '折扣',
    `sale_count` INT NOT NULL DEFAULT 0 COMMENT '商品月销量',
    `rate` DECIMAL(10, 2 ) NOT NULL DEFAULT 0.00 COMMENT '收入比率(%)',
    `comission` DECIMAL(10 , 2 ) NOT NULL DEFAULT 0.00 COMMENT '佣金',
    `wangwang` VARCHAR(64) NOT NULL COMMENT '卖家旺旺',
    `wangwangid` BIGINT UNSIGNED NOT NULL,
    `shopname` VARCHAR(256) NOT NULL COMMENT '店铺名称',
    `platform` VARCHAR(10) NOT NULL COMMENT '平台类型',
    `coupon_count` INT NOT NULL DEFAULT 0 COMMENT '优惠券总量',
    `coupon_remain` INT NOT NULL DEFAULT 0 COMMENT '优惠券剩余量',
    `coupon_price` VARCHAR(256) NOT NULL COMMENT '优惠券面值',
    `use_price` DECIMAL(10 , 2 ) NOT NULL DEFAULT 0.00 COMMENT '可使用价格',
    `coupon_start` DATE NOT NULL COMMENT '优惠券开始时间',
    `coupon_stop` DATE NOT NULL COMMENT '优惠券结束时间',
    `coupon_url` VARCHAR(1024) NOT NULL COMMENT '商品优惠券推广链接',
    PRIMARY KEY (`id`),
    UNIQUE INDEX `UDX_PAGE_ID` (`page_id` ASC)
)  ENGINE=INNODB DEFAULT CHARACTER SET=UTF8MB4 COMMENT='淘宝客商品优惠券'";

$tables['1.0.0'][] = "CREATE TABLE `tbk_token` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '主键id',
  `logo` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0' COMMENT '淘宝客logo',
  `url` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0' COMMENT 'url地址',
  `user_id` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0' COMMENT '淘宝客user_id',
  `content` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0' COMMENT '内容',
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0' COMMENT '淘口令',
  `create_time` int(10) NOT NULL DEFAULT '0' COMMENT '生成时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='淘宝客淘口令'";


$tables['1.0.1'][] ="ALTER TABLE `tbk_goods`
ADD COLUMN `real_price`  decimal(10,2) NOT NULL DEFAULT 0.00 COMMENT '折扣之后价格' AFTER `discount`";

$tables['1.0.2'][] ="ALTER TABLE `tbk_goods`
ADD COLUMN `reason`  varchar(255) NOT NULL DEFAULT 0 COMMENT '推荐理由' AFTER `coupon_url`";

$tables['1.0.3'][] ="ALTER TABLE `tbk_goods`
ADD COLUMN `token`  varchar(255) NOT NULL DEFAULT '' COMMENT '淘口令' AFTER `reason`";