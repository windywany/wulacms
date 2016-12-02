<?php
defined ( 'KISSGO' ) or exit ( 'No direct script access allowed' );

$tables = array ();

$tables ['0.0.1'] [] = "CREATE TABLE `{prefix}cms_block` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `create_time` int(10) unsigned NOT NULL,
  `create_uid` int(10) unsigned NOT NULL,
  `update_time` int(10) unsigned NOT NULL,
  `update_uid` int(10) unsigned NOT NULL,
  `deleted` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `catelog` int(10) unsigned NOT NULL COMMENT '区块',
  `refid` varchar(64) NOT NULL COMMENT '模板中引用编号',
  `name` varchar(1024) NOT NULL COMMENT '名称',
  `note` text COMMENT '说明',
  PRIMARY KEY (`id`),
  UNIQUE KEY `UDX_REFID` (`refid`),
  KEY `IDX_CATELOG` (`catelog`),
  KEY `IDX_DELETED` (`deleted`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='区块位置'";

$tables ['0.0.1'] [] = "CREATE TABLE `{prefix}cms_block_items` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `create_time` int(10) unsigned NOT NULL,
  `create_uid` int(10) unsigned NOT NULL,
  `update_time` int(10) unsigned NOT NULL,
  `update_uid` int(10) unsigned NOT NULL,
  `deleted` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `block` int(10) unsigned NOT NULL COMMENT '区块位置',
  `title` varchar(256) NOT NULL COMMENT '标题',
  `url` varchar(1024) NOT NULL COMMENT 'URL',
  `image` varchar(1024) DEFAULT NULL COMMENT '插图',
  `description` text COMMENT '说明',
  PRIMARY KEY (`id`),
  KEY `IDX_BLOCK` (`block`),
  KEY `IDX_DELETED` (`deleted`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='区块内容'";

$tables ['0.0.1'] [] = "CREATE TABLE `{prefix}cms_catelog` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `create_time` int(10) unsigned NOT NULL DEFAULT '0',
  `update_time` int(10) unsigned NOT NULL DEFAULT '0',
  `create_uid` int(10) unsigned NOT NULL DEFAULT '0',
  `update_uid` int(10) unsigned NOT NULL DEFAULT '0',
  `upid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '上级分类',
  `type` varchar(32) NOT NULL COMMENT '分类的种类',
  `name` varchar(64) NOT NULL COMMENT '分类名',
  `note` text COMMENT '说明',
  `deleted` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  PRIMARY KEY (`id`),
  UNIQUE KEY `UDX_TYPE` (`type`,`name`),
  KEY `IDX_UPID` (`upid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='分类记录表'";

$tables ['0.0.1'] [] = "CREATE TABLE `{prefix}cms_channel` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `deleted` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `create_time` int(10) unsigned NOT NULL,
  `create_uid` int(10) unsigned NOT NULL,
  `update_time` int(10) unsigned NOT NULL,
  `update_uid` int(10) unsigned NOT NULL,
  `upid` int(10) unsigned NOT NULL COMMENT '上级栏目',
  `is_topic_channel` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '是不是专题栏目',
  `isfinal` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '这个栏目下是否可以发布文章',
  `refid` varchar(32) NOT NULL COMMENT '模板引用ID',
  `name` varchar(128) NOT NULL COMMENT '栏目名称',
  `default_model` varchar(32) NOT NULL COMMENT '内容模型',
  `default_template` varchar(1024) DEFAULT NULL COMMENT '默认模板',
  `default_url_pattern` varchar(1024) DEFAULT NULL COMMENT 'URL生成规则',
  `path` varchar(1024) NOT NULL DEFAULT '/' COMMENT '父路径',
  `basedir` varchar(45) NOT NULL COMMENT '路径名',
  `list_page` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT '列表页',
  `index_page` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT '用户自定义封面页',
  `default_page` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT '默认主页',
  `list_page_url` varchar(1024) NOT NULL COMMENT '列表页URL',
  `url` varchar(1024) NOT NULL COMMENT '封面URL',
  `page_name` varchar(128) DEFAULT NULL COMMENT '页面名称',
  `title` varchar(1024) DEFAULT NULL COMMENT 'SEO标题',
  `keywords` varchar(1024) DEFAULT NULL COMMENT 'SEO关键词',
  `description` varchar(1024) DEFAULT NULL COMMENT 'SEO描述',
  PRIMARY KEY (`id`),
  UNIQUE KEY `UDX_REFID` (`refid`),
  KEY `IDX_UPID` (`upid`),
  KEY `IDX_MODEL` (`default_model`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='栏目，频道'";

$tables ['0.0.1'] [] = "CREATE TABLE `{prefix}cms_chunk` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `create_time` int(10) unsigned NOT NULL,
  `create_uid` int(10) unsigned NOT NULL,
  `update_time` int(10) unsigned NOT NULL,
  `update_uid` int(10) unsigned NOT NULL,
  `deleted` tinyint(1) unsigned NOT NULL COMMENT '是否删除',
  `catelog` int(10) unsigned NOT NULL COMMENT '分类',
  `name` varchar(64) NOT NULL COMMENT '名称',
  `istpl` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '是否启用模型',
  `keywords` text COMMENT '关键词',
  `search_index` text COMMENT '搜索索引',
  `html` text NOT NULL COMMENT '代码片断',
  PRIMARY KEY (`id`),
  KEY `IDX_CATELOG` (`catelog`),
  KEY `IDX_DELETED` (`deleted`),
  FULLTEXT KEY `FULLIDX_SEARCH` (`search_index`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='碎片'";

$tables ['0.0.1'] [] = "CREATE TABLE `{prefix}cms_model` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `upid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '上级模型',
  `create_time` int(10) unsigned NOT NULL,
  `create_uid` int(10) unsigned NOT NULL,
  `update_time` int(10) unsigned NOT NULL,
  `update_uid` int(10) unsigned NOT NULL,
  `deleted` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `is_topic_model` tinyint(1) unsigned DEFAULT '0' COMMENT '是否是专题模型',
  `refid` varchar(32) NOT NULL COMMENT '识别ID',
  `status` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '状态',
  `hidden` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '是否隐藏，不可直接创建',
  `name` varchar(64) NOT NULL COMMENT '内容模型名称',
  `addon_table` varchar(32) DEFAULT NULL COMMENT '附加数据表',
  `template` varchar(1024) DEFAULT NULL COMMENT '默认模板',
  `note` text COMMENT '说明',
  PRIMARY KEY (`id`),
  UNIQUE KEY `UDX_REFID` (`refid`),
  KEY `IDX_DELETED` (`deleted`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='内容模型'";

$tables ['0.0.1'] [] = "CREATE TABLE `{prefix}cms_model_field` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `model` varchar(32) NOT NULL COMMENT '内容模型',
  `create_time` int(11) NOT NULL,
  `create_uid` int(11) NOT NULL,
  `update_time` int(11) NOT NULL,
  `update_uid` int(11) NOT NULL,
  `deleted` tinyint(1) NOT NULL,
  `required` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否必须填写值',
  `searchable` tinyint(1) unsigned NOT NULL COMMENT '是否可以搜索',
  `name` varchar(32) NOT NULL COMMENT '字段名',
  `type` varchar(32) NOT NULL COMMENT '类型，决定输入组件',
  `label` varchar(64) DEFAULT NULL COMMENT '标签',
  `tip` varchar(256) DEFAULT NULL COMMENT '提示',
  `defaults` text COMMENT '默认值，不同类型的输入组件会使用不同的值',
  PRIMARY KEY (`id`),
  KEY `IDX_MODEL` (`model`),
  KEY `IDX_DELETED` (`deleted`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='内容模型自定义字段'";

$tables ['0.0.1'] [] = "CREATE TABLE `{prefix}cms_page` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `create_time` int(10) unsigned NOT NULL DEFAULT '0',
  `create_uid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建用户',
  `update_time` int(10) unsigned NOT NULL DEFAULT '0',
  `update_uid` int(10) unsigned NOT NULL DEFAULT '0',
  `publish_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '发布时间',
  `publish_uid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '发布者',
  `gid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '所属组',
  `deleted` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `hidden` tinyint(1) unsigned DEFAULT '0' COMMENT '列表中隐藏,不可被搜索',
  `status` smallint(5) unsigned NOT NULL DEFAULT '1' COMMENT '状态',
  `channel` varchar(32) NOT NULL COMMENT '栏目',
  `model` varchar(32) NOT NULL COMMENT '内容模型',
  `chunk` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '碎片',
  `topic` bigint(20) unsigned DEFAULT '0' COMMENT '绑定专题',
  `flag_a` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '属性a',
  `flag_h` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `flag_c` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `flag_b` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `flag_j` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `view_count` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '查看次数',
  `allow_comment` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '允许评论',
  `title` varchar(256) DEFAULT NULL COMMENT '标题',
  `title2` varchar(256) DEFAULT NULL COMMENT '短标题',
  `title_color` varchar(10) DEFAULT NULL COMMENT '标题颜色',
  `image` varchar(1024) DEFAULT NULL COMMENT '插图',
  `author` varchar(128) DEFAULT NULL COMMENT '作者',
  `source` varchar(128) DEFAULT NULL COMMENT '来源',
  `url` varchar(1024) DEFAULT NULL COMMENT 'URL',
  `url_key` char(32) DEFAULT NULL COMMENT 'URL MD5',
  `url_pattern` varchar(1024) DEFAULT NULL COMMENT 'URL生成规则',
  `template_file` varchar(1024) DEFAULT NULL,
  `keywords` varchar(1024) DEFAULT NULL COMMENT '关键词',
  `description` varchar(1024) DEFAULT NULL COMMENT '描述',
  `related_pages` text COMMENT '相关页面',
  `search_index` text COMMENT '关键词索引',
  `content` text COMMENT '正文',
  PRIMARY KEY (`id`),
  KEY `IDX_MODEL` (`model`),
  KEY `IDX_CHANNEL` (`channel`),
  KEY `IDXC_DELETED_STATUS` (`deleted`,`status`),
  KEY `IDX_UID` (`create_uid`),
  KEY `IDX_GID` (`gid`),
  FULLTEXT KEY `FULLIDX_PAGEINDEX` (`search_index`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='页面'";

$tables ['0.0.1'] [] = "CREATE TABLE `{prefix}cms_tag` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `create_time` int(10) unsigned NOT NULL,
  `create_uid` int(10) unsigned NOT NULL,
  `update_time` int(10) unsigned NOT NULL,
  `update_uid` int(10) unsigned NOT NULL,
  `deleted` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `tag` varchar(64) NOT NULL COMMENT '标签',
  `title` varchar(1024) NOT NULL COMMENT '标题',
  `url` varchar(1024) NOT NULL COMMENT 'url',
  PRIMARY KEY (`id`),
  UNIQUE KEY `UDX_TAG` (`tag`),
  KEY `IDX_DELETED` (`deleted`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='标签库'";

$tables ['0.0.1'] [] = "INSERT INTO `{prefix}cms_model` VALUES (1, 0, 1401797346, 1, 1402125584, 1, 0, 0, 'news', 1, 0, '新闻', '', 'page_form.tpl', ''),
		(2, 0, 1401805431, 1, 1402125592, 1, 0, 1, 'topic', 1, 0, '专题', '', 'topic_form.tpl', ''),
		(3, 0, 1401980064, 1, 1402125557, 1, 0, 0, 'channel_index', 1, 1, '栏目封面页', '', 'page_form.tpl', ''),
		(4, 0, 1402057167, 1, 1402125567, 1, 0, 0, 'channel_list', 1, 1, '栏目列表页', '', 'page_form.tpl', '')";
$tables ['0.0.1'] [] = "CREATE TABLE `{prefix}cms_variables` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `type` VARCHAR(32) NOT NULL,
    `create_time` INT UNSIGNED NOT NULL,
    `create_uid` INT UNSIGNED NOT NULL,
    `update_time` INT UNSIGNED NOT NULL,
    `update_uid` INT UNSIGNED NOT NULL,
    `deleted` INT UNSIGNED NOT NULL DEFAULT 0,
    `val` TEXT NULL COMMENT '值',
    PRIMARY KEY (`id`),
	INDEX IDX_DT (`deleted`,`type`)
)  ENGINE=InnoDB COMMENT='CMS常用变量表'";

$tables ['0.0.1'] [] = "ALTER TABLE `{prefix}cms_page` ADD `tag` VARCHAR(32) DEFAULT NULL COMMENT '标签' AFTER `source`";
$tables ['0.0.1'] [] = "CREATE INDEX IDX_TAG ON `{prefix}cms_page` (`tag`)";

$tables ['0.0.1'] [] = "ALTER TABLE `{prefix}cms_model` ADD `creatable` TINYINT(1) DEFAULT 1 COMMENT '是否可以直接创建' AFTER `hidden`";
$tables ['0.0.1'] [] = "ALTER TABLE `{prefix}cms_model_field` ADD `default_value` TEXT NULL COMMENT '默认值' AFTER `tip`";

$tables ['0.0.1'] [] = "CREATE TABLE IF NOT EXISTS `{prefix}cms_page_field` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `create_time` INT UNSIGNED NOT NULL,
    `create_uid` INT UNSIGNED NOT NULL,
    `update_time` INT UNSIGNED NOT NULL,
    `update_uid` INT UNSIGNED NOT NULL,
    `deleted` INT UNSIGNED NOT NULL DEFAULT 0,
    `page_id` BIGINT UNSIGNED NOT NULL COMMENT '页面ID',
    `field_id` INT UNSIGNED NOT NULL COMMENT '字段',
    `val` TEXT NULL COMMENT '值',
    INDEX `IDX_FIELD_NAME` (`field_id` ASC),
    INDEX `IDX_PAGE_ID` (`page_id` ASC),
    PRIMARY KEY (`id`)
)  ENGINE=InnoDB COMMENT='页面自定义字段值'";

$tables ['0.0.1'] [] = "ALTER TABLE `{prefix}cms_chunk` ADD `inline` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '启用内链解析' AFTER `istpl`";

$tables ['0.0.1'] [] = "ALTER TABLE `{prefix}cms_channel` ADD `subchannels` text NULL COMMENT '下级栏目编号列表' AFTER `upid`";

$tables ['0.0.1'] [] = "INSERT INTO `{prefix}cms_model`(
	`upid`,`create_time`,`create_uid`,`update_time`,`update_uid`,`refid`,`hidden`,`creatable`,`name`,`note`
) VALUES (0, 1402057167, 1, 1402125567, 1, '_customer_page', 1, 0, '自定义页面', '')";

$tables ['0.0.4'] [] = "CREATE INDEX `IDX_PAGE_FIELD` ON `{prefix}cms_page_field` (`page_id`,`field_id`)";
$tables ['0.0.4'] [] = "ALTER TABLE `{prefix}cms_model` ADD search_page_prefix VARCHAR(64) NULL COMMENT '搜索页面前缀' AFTER `template`";
$tables ['0.0.4'] [] = "ALTER TABLE `{prefix}cms_model` ADD search_page_tpl VARCHAR(1024) NULL COMMENT '搜索页面模板' AFTER `search_page_prefix`";

$tables ['0.0.5'] [] = "ALTER TABLE `{prefix}cms_model_field` ADD `group` INT UNSIGNED DEFAULT 0 COMMENT '分组' AFTER `searchable`";
$tables ['0.0.5'] [] = "ALTER TABLE `{prefix}cms_model_field` ADD `col` SMALLINT(3) UNSIGNED DEFAULT 0 COMMENT '宽度' AFTER `group`";

$tables ['0.0.6'] [] = "ALTER TABLE `{prefix}cms_model` ADD search_page_limit INT UNSIGNED NOT NULL default 10 COMMENT '搜索页面每页记录数' AFTER `search_page_prefix`";

$tables ['0.0.7'] [] = "CREATE TABLE `{prefix}cms_navi_menu` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `create_uid` INT UNSIGNED NOT NULL DEFAULT 0,
    `create_time` INT UNSIGNED NOT NULL DEFAULT 0,
    `update_uid` INT UNSIGNED NOT NULL DEFAULT 0,
    `update_time` INT UNSIGNED NOT NULL DEFAULT 0,
    `upid` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '上级菜单ID',
    `deleted` TINYINT UNSIGNED NOT NULL DEFAULT 0,
    `hidden` TINYINT UNSIGNED NOT NULL DEFAULT 1 COMMENT '是否显示',
    `navi` VARCHAR(32) NOT NULL COMMENT '导航',
    `page_id` BIGINT UNSIGNED NOT NULL DEFAULT 0 COMMENT '绑定的页面',
    `sort` SMALLINT UNSIGNED NOT NULL COMMENT '排序',
    `name` VARCHAR(128) NOT NULL COMMENT '名称',
    `url` VARCHAR(1024) NULL COMMENT 'url',
    `target` ENUM('_blank', '_self') NULL DEFAULT '_blank' COMMENT '打开方式',
    INDEX `IDX_PAGEID` (`page_id` ASC),
    PRIMARY KEY (`id`)
)  ENGINE=InnoDB DEFAULT CHARACTER SET=utf8 COMMENT='导航菜单项'";

$tables ['0.0.7'] [] = "ALTER TABLE `{prefix}cms_model_field` ADD `cstore` TINYINT UNSIGNED NOT NULL DEFAULT 0 COMMENT '用户自定义存储' AFTER `searchable`";

$tables ['0.0.8'] [] = "ALTER TABLE `{prefix}cms_model_field` ADD `sort` SMALLINT UNSIGNED NOT NULL DEFAULT 999 COMMENT '搜索条件排序' AFTER `cstore`";

$tables ['0.0.9'] [] = "ALTER TABLE `{prefix}cms_catelog` ADD `alias` varchar(32) NULL COMMENT 'alias' AFTER `type`";

$tables ['0.0.10'] [] = "ALTER TABLE `{prefix}cms_channel` ADD `hidden` TINYINT(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '隐藏' AFTER `deleted`";
$tables ['0.0.10'] [] = "ALTER TABLE `{prefix}cms_channel` ADD `sort` SMALLINT UNSIGNED NOT NULL DEFAULT 999 COMMENT '显示排序' AFTER `hidden`";

$tables ['0.0.11'] [] = "ALTER TABLE `{prefix}cms_page` ADD `img_pagination` TINYINT(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '点击图片分页' AFTER `image`";
$tables ['0.0.11'] [] = "ALTER TABLE `{prefix}cms_page` ADD `img_follow` TEXT NULL COMMENT '图片跟随内容' AFTER `content`";
$tables ['0.0.11'] [] = "ALTER TABLE `{prefix}cms_page` ADD `img_next_page` VARCHAR(1024) DEFAULT '' COMMENT '无下一页时点击图片跳转地址' AFTER `img_pagination`";

$tables ['1.0.0'] [] = "ALTER TABLE `{prefix}cms_channel` ADD `root` varchar(32) NOT NULL DEFAULT '' COMMENT '顶级分类' AFTER `upid`";
$tables ['1.0.0'] [] = "ALTER TABLE `{prefix}cms_channel` ADD `parents` text NULL COMMENT '下级栏目编号列表' AFTER `root`";
$tables ['1.0.0'] [] = "CREATE INDEX IDX_URLKEY ON `{prefix}cms_page` (`url_key`)";

$tables ['1.0.1'] [] = "ALTER TABLE `{prefix}cms_channel` ADD `gid` SMALLINT UNSIGNED DEFAULT 0 COMMENT '绑定到用户组' AFTER `upid`";

$tables ['1.0.3'] [] = "ALTER TABLE `{prefix}cms_model` ADD `role` VARCHAR(16) DEFAULT '' COMMENT '菜单分组' AFTER `template`";

$tables ['1.2.0'] [] = "CREATE TABLE `{prefix}cms_seo_data` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `create_time` INT UNSIGNED NOT NULL DEFAULT 0,
    `site` VARCHAR(32) NOT NULL COMMENT '导航哪个网站的数据',
    `data` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '监测到的量',
	`data1` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '监测到的量2',
	`data2` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '监测到的量3',
	`data3` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '监测到的量4',
	`data4` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '监测到的量5',
    INDEX `IDX_SITE` (`site`,`create_time`),
    PRIMARY KEY (`id`)
)  ENGINE=InnoDB DEFAULT CHARACTER SET=utf8 COMMENT='SEO监测数据'";

$tables ['2.0.1'] [] = "ALTER TABLE `{prefix}cms_page_field` MODIFY `val` longtext DEFAULT NULL COMMENT '值'";

$tables ['2.5.0'] [] = "ALTER TABLE `{prefix}cms_page` ADD `expire` INT DEFAULT 0 COMMENT '-1表示不缓存，0表示使用系统设置' AFTER `status`";

$tables ['2.5.0'] [] = "ALTER TABLE `{prefix}cms_block_items` ADD `sort` SMALLINT UNSIGNED DEFAULT 999 COMMENT '排序' AFTER `deleted`";
$tables ['2.5.0'] [] = "ALTER TABLE `{prefix}cms_block_items` ADD `page_id` BIGINT UNSIGNED DEFAULT 0 COMMENT '绑定到页面' AFTER `deleted`";

$tables ['2.5.1'] [] = "ALTER TABLE `{prefix}cms_channel` ADD `page_cache` INT NOT NULL DEFAULT 0 COMMENT '封面页缓存时间' AFTER `default_url_pattern`";
$tables ['2.5.1'] [] = "ALTER TABLE `{prefix}cms_channel` ADD `list_cache` INT NOT NULL DEFAULT 0 COMMENT '列表页缓存时间' AFTER `default_url_pattern`";
$tables ['2.5.1'] [] = "ALTER TABLE `{prefix}cms_channel` ADD `default_cache` INT NOT NULL DEFAULT 0 COMMENT '页面缓存时间' AFTER `default_url_pattern`";

$tables ['3.0.1'] [] = "ALTER TABLE `{prefix}cms_model` ADD `is_list_model` TINYINT(1) NOT NULL DEFAULT 0 COMMENT '页面支持自定义分页' AFTER `is_topic_model`";

$tables ['3.5.0'] [] = "ALTER TABLE `{prefix}cms_channel` ADD `catalog` TINYINT(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '隐藏' AFTER `hidden`";

$tables ['4.0.0'] [] = "ALTER TABLE `{prefix}cms_block_items` ADD `cvalue` LONGTEXT DEFAULT NULL COMMENT '自定义字段值'";

$tables ['4.0.0'] [] = "CREATE TABLE `cms_block_field` (
    `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
    `block` VARCHAR(64) NOT NULL COMMENT '区块',
    `create_time` INT(11) NOT NULL,
    `create_uid` INT(11) NOT NULL,
    `update_time` INT(11) NOT NULL,
    `update_uid` INT(11) NOT NULL,
    `deleted` TINYINT(1) NOT NULL,
    `required` TINYINT(1) NOT NULL DEFAULT '0' COMMENT '是否必须填写值',
    `sort` SMALLINT(5) UNSIGNED NOT NULL DEFAULT '999' COMMENT '搜索条件排序',
    `group` INT(10) UNSIGNED DEFAULT '0' COMMENT '分组',
    `col` SMALLINT(3) UNSIGNED DEFAULT '0' COMMENT '宽度',
    `name` VARCHAR(32) NOT NULL COMMENT '字段名',
    `type` VARCHAR(32) NOT NULL COMMENT '类型，决定输入组件',
    `label` VARCHAR(64) DEFAULT NULL COMMENT '标签',
    `tip` VARCHAR(256) DEFAULT NULL COMMENT '提示',
    `default_value` TEXT COMMENT '默认值',
    `defaults` TEXT COMMENT '默认值，不同类型的输入组件会使用不同的值',
    PRIMARY KEY (`id`),
    KEY `IDX_MODEL` (`block`),
    KEY `IDX_DELETED` (`deleted`)
)  ENGINE=INNODB COMMENT='区块自定义字段'";

$tables ['4.0.2'] [] = "ALTER TABLE `{prefix}cms_page_field` 
DROP INDEX `IDX_PAGE_FIELD` ,
ADD UNIQUE INDEX `IDX_PAGE_FIELD` (`page_id` ASC, `field_id` ASC)  COMMENT '字段唯一'";

$tables ['4.1.0'] [] = "ALTER TABLE `{prefix}cms_page` 
ADD COLUMN `display_sort` INT(10) UNSIGNED NOT NULL DEFAULT 9999 COMMENT '显示排序' AFTER `flag_j`";

$tables ['4.1.1'] [] = "ALTER TABLE `{prefix}cms_model_field`
ADD COLUMN `data_type` VARCHAR(8) NOT NULL DEFAULT 'text' COMMENT '值类型' AFTER `tip`,
ADD COLUMN `tab_acc` VARCHAR(32) NULL COMMENT '标签或acc组' AFTER `data_type`";

$tables ['4.5.0'] [] = "ALTER TABLE `{prefix}cms_model`
ADD COLUMN `is_delegated` TINYINT(1) UNSIGNED NOT NULL DEFAULT 1 COMMENT '是否托管' AFTER `deleted`";

$tables ['4.5.0'] [] = "ALTER TABLE `{prefix}cms_page`
ADD COLUMN `url_handler` VARCHAR(16) NULL COMMENT '自定义页面处理器' AFTER `model`";


$tables ['4.6.0'] [] = "ALTER TABLE `{prefix}cms_page` 
CHANGE COLUMN `channel` `channel` VARCHAR(48) NOT NULL COMMENT '栏目'";

$tables ['4.6.0'] [] = "ALTER TABLE `{prefix}cms_channel` 
CHANGE COLUMN `refid` `refid` VARCHAR(48) NOT NULL COMMENT '模板引用ID'";