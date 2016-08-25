<?php
// 集群配置文件
defined ( 'KISSGO' ) or exit ( 'No direct script access allowed' );
$settings = KissGoSetting::getSetting ( 'cluster' );
// 是否启用集群配置，启用集群需要redis支持，请先确保已安装redis扩展。
$settings ['enabled'] = false;
// 集群ID.
$settings ['id'] = 1;
// Redis设置.
$settings ['redis'] = array ('192.168.0.92',6379 );
// 数据库
$settings ['redisDB'] = 10;
