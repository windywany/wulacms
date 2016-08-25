<?php
$tables ['0.0.1'] [] = "CREATE TABLE `{prefix}media` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,  
  `uid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '上传用户',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '上传时间',
  `filename` varchar(1024) NOT NULL COMMENT '文件名',
  `type` varchar(45) NOT NULL COMMENT '类型',
  `ext` varchar(32) NOT NULL COMMENT '扩展名',
  `alt` varchar(128) NOT NULL COMMENT '替换文本',
  `url` varchar(1024) NOT NULL COMMENT '可直接访问的URL',
  `filepath` varchar(1024) NOT NULL COMMENT '实际物理路径',
  `note` text COMMENT '说明',
  PRIMARY KEY (`id`),  
  KEY `IDX_UID` (`uid`),
  KEY `IDX_TYPE` (`type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='媒体库'";
