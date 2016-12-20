<?php
/**
 * @Author     :     FLY
 * @DateTime   :    2016-09-03 11:04:08
 * @Description: 会员积分
 */
defined('KISSGO') or exit('No direct script access allowed');
bind('do_admin_layout', '&PointsHookImpl', 22);
bind('get_acl_resource', '&PointsHookImpl');
bind('get_columns_of_pointsRecords', '&PointsHookImpl');