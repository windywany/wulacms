<?php
defined ( 'KISSGO' ) or exit ( 'No direct script access allowed' );

$tables = array ();

$tables ['1.0.0'] [] = "CREATE TABLE IF NOT EXISTS `{prefix}bbs_member_rank` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(32) NULL COMMENT '会员等名称',
    `level` SMALLINT(8) UNSIGNED NOT NULL COMMENT '会员等级,值越大等级越高',
    `coins` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '所需金币（积分）',
    `rank` VARCHAR(64) NOT NULL COMMENT '等级称号',
    `note` VARCHAR(256) NULL COMMENT '说明',
    PRIMARY KEY (`id`),
    UNIQUE INDEX `UDX_LEVEL` (`level` ASC),
    UNIQUE INDEX `UDX_RANK` (`rank` ASC)
)  ENGINE=INNODB COMMENT='会员等级'";

$tables ['1.0.0'] [] = "INSERT INTO `{prefix}bbs_member_rank` (`id`, `name`, `level`, `coins`, `rank`, `note`) VALUES (1, '普通会员', 1, 0, '普通会员', '普通会员')";

$tables ['1.0.0'] [] = "CREATE TABLE IF NOT EXISTS `bbs_member_ranks` (
    `mid` INT UNSIGNED NOT NULL COMMENT '会员编号',
    `rank_id` INT UNSIGNED NOT NULL COMMENT '等级',
    `update_time` INT UNSIGNED NOT NULL COMMENT '取得时间',
    PRIMARY KEY (`mid`),
    INDEX `FDX_RANK` (`rank_id` ASC)
)  ENGINE=INNODB COMMENT='会员等级记录表'";

$tables ['1.0.0'] [] = "CREATE TABLE IF NOT EXISTS `{prefix}bbs_forums` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `create_time` INT UNSIGNED NOT NULL COMMENT '创建时间',
    `create_uid` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建用户',
    `update_time` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '修改时间',
    `update_uid` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '修改用户',
    `deleted` TINYINT UNSIGNED NOT NULL DEFAULT 0 COMMENT '是否删除',
    `upid` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '上级版块',
    `rank_id` INT UNSIGNED NOT NULL COMMENT '最低发帖等级',
    `cost` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '发贴需要花费',
    `thread_count` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '帖子总数',
    `allow_q` TINYINT(1) UNSIGNED NOT NULL DEFAULT 1 COMMENT '允许问答贴',
    `allow_n` TINYINT(1) UNSIGNED NOT NULL DEFAULT 1 COMMENT '允许一般帖子',
    `allow_v` TINYINT(1) UNSIGNED NOT NULL DEFAULT 1 COMMENT '允许投票贴',
    `allow_html` TINYINT(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '允许HTML代码',
    `allow_markdown` TINYINT(1) UNSIGNED NOT NULL DEFAULT 1 COMMENT '允许MD代码',
    `allow_bbscode` TINYINT(1) UNSIGNED NOT NULL DEFAULT 1 COMMENT '允许BBS代码',
    `allow_anonymous` TINYINT(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '是否允许匿名用户发帖',
    `sort` SMALLINT(8) UNSIGNED NOT NULL DEFAULT 9999 COMMENT '排序',
    `name` VARCHAR(32) NOT NULL COMMENT '版块名称',
    `tag` VARCHAR(32) NULL COMMENT '版块标签',
	`title` VARCHAR(128) NULL COMMENT 'SEO标题',
    `parents` TEXT NULL COMMENT '父级版块',
	`sub_forums` TEXT NULL COMMENT '子版块',
    `tpl` VARCHAR(128) NULL DEFAULT 'forum.tpl' COMMENT '模板',
    `thread_tpl` VARCHAR(128) NULL DEFAULT 'thread.tpl' COMMENT '帖子模板',
    `slug` VARCHAR(64) NOT NULL COMMENT 'URL助记',
    `keywords` VARCHAR(128) NULL COMMENT '关键词',
    `description` VARCHAR(256) NULL COMMENT '描述',
    `masters` TEXT NULL COMMENT '版主，JSON格式[{mid,name,master}]',
    PRIMARY KEY (`id`),
    INDEX `FDX_UPID` (`upid` ASC),
    INDEX `FDX_RANK_ID` (`rank_id` ASC),
    INDEX `UDX_SLUG` (`slug` ASC)
)  ENGINE=INNODB COMMENT='论坛版块'";

$tables ['1.0.0'] [] = "CREATE TABLE IF NOT EXISTS `{prefix}bbs_forum_meta` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `forum_id` INT UNSIGNED NOT NULL COMMENT '版块编号',
    `meta_name` VARCHAR(32) NOT NULL COMMENT '数据名',
    `meta_value` TEXT NULL COMMENT '数据值',
    PRIMARY KEY (`id`),
    UNIQUE INDEX `UDX_F_M` (`forum_id`,`meta_name` ASC)
)  ENGINE=INNODB COMMENT='版块元数据'";

$tables ['1.0.0'] [] = "CREATE TABLE IF NOT EXISTS `{prefix}bbs_forum_admin` (
    `mid` INT UNSIGNED NOT NULL COMMENT '会员编号',
    `forum_id` INT UNSIGNED NOT NULL COMMENT '版块编号',
    `master` TINYINT(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '1版主0其它角色',
    `role_id` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '角色ID',
    PRIMARY KEY (`mid` , `forum_id`),
    INDEX `FDX_FORUM_ID` (`forum_id` ASC)
)  ENGINE=INNODB COMMENT='版主'";

$tables ['1.0.0'] [] = "CREATE TABLE IF NOT EXISTS `{prefix}bbs_thread_type` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `type` VARCHAR(16) NOT NULL COMMENT '类型',
    `name` VARCHAR(64) NOT NULL COMMENT '类型名称',
    `funcs` ENUM('Q', 'V', 'N') NOT NULL DEFAULT 'N' COMMENT '帖子功能Q:问答,V:投票,N:普通',
    `status` TINYINT(1) UNSIGNED NOT NULL DEFAULT 1 COMMENT '1:启用；0:禁用',
    `note` VARCHAR(256) NULL COMMENT '说明',
    PRIMARY KEY (`id`),
    UNIQUE INDEX `UDX_TYPE` (`type` ASC)
)  ENGINE=INNODB COMMENT='帖子类型'";

$tables ['1.0.0'] [] = "INSERT INTO `{prefix}bbs_thread_type` (`id`, `type`, `name`, `funcs`, `status`, `note`) VALUES (1, 'normal', '帖子', 'N', 1, NULL),(2, 'ask', '问答', 'Q', 0, NULL),(3, 'vote', '投票', 'V', 0, NULL)";

$tables ['1.0.0'] [] = "CREATE TABLE IF NOT EXISTS `{prefix}bbs_threads` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `create_time` INT UNSIGNED NOT NULL COMMENT '发表时间',
    `deleted` TINYINT(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '是否删除',
    `forum_id` INT UNSIGNED NOT NULL COMMENT '版块编号',
    `mid` INT UNSIGNED NOT NULL COMMENT '用户编号',
    `type` VARCHAR(16) NOT NULL DEFAULT 'N' COMMENT '帖子类型',
    `status` SMALLINT(2) UNSIGNED NOT NULL DEFAULT 1 COMMENT '状态1:正常0:关闭',
    `post_id` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '正文',
    `post_count` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '回复数量',
    `last_post_id` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '最后一个回复编号',
    `allow_post` TINYINT(1) UNSIGNED NOT NULL DEFAULT 1 COMMENT '允许回复',
    `topic` VARCHAR(64) NULL COMMENT '话题',
    `subject` VARCHAR(256) NOT NULL COMMENT '帖子主题',
    `flag0` TINYINT(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '标志0',
    `flag1` TINYINT(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '标志1',
    `flag2` TINYINT(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '标志2',
    `flag3` TINYINT(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '标志3',
    `flag4` TINYINT(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '标志4',
    `flag5` TINYINT(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '标志5',
    `flag6` TINYINT(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '标志6',
    `flag7` TINYINT(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '标志7',
    `flag8` TINYINT(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '标志8',
    `flag9` TINYINT(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '标志9',
    `closeat` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '在指定时间关闭，0不自动关闭',
    `reply_view` TINYINT(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '回复才能查看',
    `cost` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '金币查看',
    `cost_amount` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '金币总量',
    `view_passwd` VARCHAR(32) NULL COMMENT '密码查看',
    `search_tags` TEXT NULL COMMENT '全文索引',
    PRIMARY KEY (`id`),
    INDEX `FK_MID` (`mid` ASC),
    INDEX `FK_FORUM_ID` (`forum_id` ASC),
    INDEX `FK_TYPE` (`type` ASC),
    FULLTEXT INDEX `FDX_SEARCH_TAG` ( `search_tags` ASC )
)  ENGINE=INNODB COMMENT='论坛帖子'";

$tables ['1.0.0'] [] = "CREATE TABLE IF NOT EXISTS `{prefix}bbs_posts` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `create_time` INT UNSIGNED NOT NULL COMMENT '回复时间',
    `create_uid` INT UNSIGNED NOT NULL COMMENT '回复用户',
    `create_username` VARCHAR(128) NULL COMMENT '回复用户名',
    `update_time` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '修改时间',
    `update_uid` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '修改用户',
    `update_username` VARCHAR(128) NULL COMMENT '修改用户名',
    `deleted` TINYINT(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '是否删除',
    `status` SMALLINT UNSIGNED NOT NULL DEFAULT 1 COMMENT '状态，1:正常，0：关闭',
    `thread_id` INT UNSIGNED NOT NULL COMMENT '帖子编号',
    `replyto` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '回复@编号',
    `accept` TINYINT(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '问题贴采纳的答案',
    `up` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '顶',
    `down` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '踩',
    `search_tag` TEXT NULL,
    `content` LONGTEXT NULL COMMENT '回复正文，其中包括引用标签',
    PRIMARY KEY (`id`),
    INDEX `FK_C_UID` (`create_uid` ASC),
    INDEX `FK_THREAD_ID` (`thread_id` ASC),
    INDEX `FK_REPLYTO` (`replyto` ASC),
    FULLTEXT INDEX `FDX_SEARCH_TAG` ( `search_tag` ASC )
)  ENGINE=INNODB COMMENT='论坛帖子回复'";

$tables ['1.0.0'] [] = "CREATE TABLE IF NOT EXISTS `{prefix}bbs_thread_votes` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `thread_id` INT UNSIGNED NOT NULL COMMENT '帖子ID',
    `name` VARCHAR(64) NOT NULL COMMENT '投票选项名称',
    `single` TINYINT(1) UNSIGNED NOT NULL DEFAULT 1 COMMENT '是否是单选',
    `vote_count` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '此项数量',
    PRIMARY KEY (`id`),
    INDEX `FK_THREAD_ID` (`thread_id` ASC)
)  ENGINE=INNODB COMMENT='帖子投票'";

$tables ['1.0.0'] [] = "CREATE TABLE IF NOT EXISTS `{prefix}bbs_member_votes` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `create_time` INT UNSIGNED NOT NULL COMMENT '投票时间',
    `mid` INT UNSIGNED NOT NULL COMMENT '会员ID',
    `thread_id` INT UNSIGNED NOT NULL COMMENT '帖子ID',
    `vote_id` INT UNSIGNED NOT NULL COMMENT '选项ID',
    PRIMARY KEY (`id`),
    INDEX `FK_THREAD_ID` (`thread_id` , `mid` , `vote_id` ASC)
)  ENGINE=INNODB COMMENT='用户投票'";

$tables ['1.0.0'] [] = "CREATE TABLE IF NOT EXISTS `{prefix}bbs_thread_view` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `view_time` INT UNSIGNED NOT NULL COMMENT '查看时间',
    `thread_id` INT UNSIGNED NOT NULL COMMENT '帖子',
    `mid` INT UNSIGNED NOT NULL COMMENT '会员ID',
    `cost` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '花费',
    PRIMARY KEY (`id`),
    INDEX `FK_THREAD_ID` (`thread_id` , `mid` ASC)
)  ENGINE=INNODB COMMENT='付费查看'";
