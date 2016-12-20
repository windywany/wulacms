<?php
/**
 * @Author     :     FLY
 * @DateTime   :    2016-09-03 11:04:08
 * @Description: 会员积分
 */
defined('KISSGO') or exit('No direct script access allowed');
bind('do_admin_layout', '&FinanceHookImpl', 20);
bind('get_acl_resource', '&FinanceHookImpl');
