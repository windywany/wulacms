<?php
defined ( 'KISSGO' ) or exit ( 'No direct script access allowed' );
$tables ['1.0.2'] [] = "CREATE TABLE `{prefix}weixin_user` (
    `openid` varchar(48) NOT NULL COMMENT 'OPOENID',
    `unionid` varchar(48) NOT NULL DEFAULT '' COMMENT 'UNIONID接入开放平台后不为空',
    `create_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
    `update_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
    `access_token` varchar(512) DEFAULT NULL COMMENT '获取用户信息的access_token',
    `access_token_expire` int(11) DEFAULT NULL COMMENT 'access_token过期时间',
    `refresh_token` varchar(512) DEFAULT NULL COMMENT '刷新access_token的token',
    `refresh_token_expire` int(11) DEFAULT NULL COMMENT '刷新token的过期时间',
    `user_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '对应的用户ID',
    PRIMARY KEY (`openid`,`unionid`),
    KEY `IDX_USER_ID` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='微信用户信息表，具体参见微信开发文档';";


$tables ['1.0.3'] [] = "CREATE TABLE IF NOT EXISTS `{prefix}weixin_menu` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `create_time` int(10) unsigned NOT NULL,
  `create_uid` int(10) unsigned NOT NULL,
  `update_time` int(10) unsigned NOT NULL,
  `update_uid` int(10) unsigned NOT NULL,
  `deleted` tinyint(1) unsigned NOT NULL DEFAULT '0',  
  `upid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '上级栏目',
  `name` varchar(128) NOT NULL COMMENT '栏目名称',
  `menu_type` varchar(32) DEFAULT NULL COMMENT '菜单类型',
  `key` varchar(255) DEFAULT NULL COMMENT '菜单值',
  `sort` smallint(5) unsigned NOT NULL DEFAULT '999' COMMENT '显示排序',
  PRIMARY KEY (`id`),
  KEY `IDX_UPID` (`upid`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='微信导航菜单表'";

$tables ['1.0.4'] [] = "CREATE TABLE IF NOT EXISTS `{prefix}weixin_subscriber` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `create_time` int(10) unsigned NOT NULL DEFAULT 0,
  `create_uid` int(10) unsigned NOT NULL  DEFAULT 0,
  `update_time` int(10) unsigned NOT NULL DEFAULT 0,
  `update_uid` int(10) unsigned NOT NULL DEFAULT 0,
  `deleted` tinyint(1) unsigned NOT NULL DEFAULT 0,  
  `groupid` int(10) unsigned NOT NULL DEFAULT 0 COMMENT '分组ID',
  `subscribe` tinyint(1) unsigned NOT NULL DEFAULT 0 COMMENT '用户是否订阅该公众号标识',
  `subscribe_time` int(10) unsigned NOT NULL DEFAULT 0 COMMENT '订阅时间',
  `weixinid` varchar(48) NOT NULL COMMENT '微信账号',
  `openid` varchar(48) NOT NULL COMMENT '用户的标识，对当前公众号唯一',
  `unionid` varchar(64) NOT NULL COMMENT '只有在用户将公众号绑定到微信开放平台帐号后，才会出现该字段',
  `nickname` varchar(128) DEFAULT NULL COMMENT '用户的昵称',
  `sex` smallint(2)  unsigned NOT NULL DEFAULT 0 COMMENT '为1时是男性，值为2时是女性，值为0时是未知',
  `language` varchar(32)  NOT NULL DEFAULT 'zh_CN' COMMENT '用户的语言，简体中文为zh_CN',
  `city` varchar(32)  DEFAULT NULL COMMENT '用户所在城市',
  `province` varchar(32)  DEFAULT NULL COMMENT '用户所在省份',
  `country` varchar(32)  DEFAULT NULL COMMENT '用户所在国家',
  `headimgurl` varchar(256)  DEFAULT NULL COMMENT '用户头像，最后一个数值代表正方形头像大小',
  `remark` varchar(256)  DEFAULT NULL COMMENT '公众号运营者对粉丝的备注',
  `user_id` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '对应的用户ID',
  PRIMARY KEY (`id`),
  INDEX `IDX_USER_ID` (`user_id` ASC),
  UNIQUE INDEX `UDX_WEIXIN_OPENID` (`weixinid`,`openid`),
  INDEX `IDX_UNIONID` (`unionid` ASC)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COMMENT='微信用户表'";




$tables ['1.0.5'] [] = "CREATE TABLE `{prefix}weixin_msg_auto` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `create_time` int(10) unsigned DEFAULT NULL,
  `update_time` int(10) unsigned DEFAULT NULL,
  `create_uid` int(10) unsigned DEFAULT NULL,
  `update_uid` int(10) unsigned DEFAULT NULL,
  `deleted` tinyint(4) unsigned DEFAULT NULL,
  `msg_type` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='自动回复';";


$tables ['1.0.5'] [] = "CREATE TABLE `{prefix}weixin_msg_keyword` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `create_time` int(10) unsigned DEFAULT NULL,
  `update_time` int(10) unsigned DEFAULT NULL,
  `create_uid` int(10) unsigned DEFAULT NULL,
  `update_uid` int(10) unsigned DEFAULT NULL,
  `deleted` tinyint(4) unsigned DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL COMMENT '规则名称',
  `keyword` varchar(255) DEFAULT NULL COMMENT '关键词，逗号分隔',
  `msg_type` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UDX_NAME` (`name`) USING BTREE,
  KEY `IDX_KEY` (`deleted`,`keyword`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;";


$tables ['1.0.5'] [] = "CREATE TABLE `{prefix}weixin_msg_rp_image` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `create_time` int(10) unsigned DEFAULT NULL,
  `update_time` int(10) unsigned DEFAULT NULL,
  `create_uid` int(10) unsigned DEFAULT NULL,
  `update_uid` int(10) unsigned DEFAULT NULL,
  `msg_id` int(10) unsigned NOT NULL,
  `media_id` varchar(255) DEFAULT NULL,
  `table` varchar(255) DEFAULT NULL COMMENT '表名',
  PRIMARY KEY (`id`),
  KEY `IDX_MSG` (`msg_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='回复类型 image';";

$tables ['1.0.5'] [] = "CREATE TABLE `{prefix}weixin_msg_rp_music` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `create_time` int(10) unsigned DEFAULT NULL,
  `update_time` int(10) unsigned DEFAULT NULL,
  `create_uid` int(10) unsigned DEFAULT NULL,
  `update_uid` int(10) unsigned DEFAULT NULL,
  `msg_id` int(10) unsigned NOT NULL,
  `title` varchar(255) DEFAULT NULL COMMENT '标题',
  `note` varchar(255) DEFAULT NULL COMMENT '描述',
  `url` varchar(255) DEFAULT NULL COMMENT '音乐链接',
  `hq` varchar(255) DEFAULT NULL COMMENT '高质量音乐链接，WIFI环境优先使用该链接播放音乐',
  `media_id` varchar(255) DEFAULT NULL,
  `table` varchar(255) DEFAULT NULL COMMENT '表名',
  PRIMARY KEY (`id`),
  KEY `IDX_MSG` (`msg_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='回复 music类型';";


$tables ['1.0.5'] [] = "CREATE TABLE `{prefix}weixin_msg_rp_news` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `create_time` int(10) unsigned DEFAULT NULL,
  `update_time` int(10) unsigned DEFAULT NULL,
  `create_uid` int(10) unsigned DEFAULT NULL,
  `update_uid` int(10) unsigned DEFAULT NULL,
  `msg_id` int(10) unsigned NOT NULL,
  `block_id` int(10) unsigned NOT NULL COMMENT 'cms 区块 id',
  `table` varchar(255) DEFAULT NULL COMMENT '表名',
  PRIMARY KEY (`id`),
  KEY `IDX_MSG` (`msg_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='回复 news类型';";

$tables ['1.0.5'] [] = "CREATE TABLE `{prefix}weixin_msg_rp_text` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `create_time` int(10) unsigned DEFAULT NULL,
  `update_time` int(10) unsigned DEFAULT NULL,
  `create_uid` int(10) unsigned DEFAULT NULL,
  `update_uid` int(10) unsigned DEFAULT NULL,
  `msg_id` int(10) unsigned NOT NULL,
  `content` text,
  `table` varchar(255) DEFAULT NULL COMMENT '表名',
  PRIMARY KEY (`id`),
  UNIQUE KEY `UDX_MSG_TAB` (`msg_id`,`table`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='回复类型 text';";


$tables ['1.0.5'] [] = "CREATE TABLE `{prefix}weixin_msg_rp_video` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `create_time` int(10) unsigned DEFAULT NULL,
  `update_time` int(10) unsigned DEFAULT NULL,
  `create_uid` int(10) unsigned DEFAULT NULL,
  `update_uid` int(10) unsigned DEFAULT NULL,
  `msg_id` int(10) unsigned NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `note` varchar(255) DEFAULT NULL,
  `media_id` varchar(255) DEFAULT NULL,
  `table` varchar(255) DEFAULT NULL COMMENT '表名',
  PRIMARY KEY (`id`),
  KEY `IDX_MSG` (`msg_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='回复 video类型';";


$tables ['1.0.5'] [] = "CREATE TABLE `{prefix}weixin_msg_rp_voice` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `create_time` int(10) unsigned DEFAULT NULL,
  `update_time` int(10) unsigned DEFAULT NULL,
  `create_uid` int(10) unsigned DEFAULT NULL,
  `update_uid` int(10) unsigned DEFAULT NULL,
  `msg_id` int(10) unsigned NOT NULL,
  `media_id` varchar(255) DEFAULT NULL,
  `table` varchar(255) DEFAULT NULL COMMENT '表名',
  PRIMARY KEY (`id`),
  KEY `IDX_MSG` (`msg_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='回复类型 voice';";


$tables ['1.0.5'] [] = "CREATE TABLE `{prefix}weixin_msg_sub` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `create_time` int(10) unsigned DEFAULT NULL,
  `update_time` int(10) unsigned DEFAULT NULL,
  `create_uid` int(10) unsigned DEFAULT NULL,
  `update_uid` int(10) unsigned DEFAULT NULL,
  `deleted` tinyint(4) unsigned DEFAULT NULL,
  `msg_type` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='订阅回复';";

