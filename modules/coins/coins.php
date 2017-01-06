<?php
/**
 * @Author     :     FLY
 * @DateTime   :    2016-09-03 11:04:08
 * @Description: 会员积分
 */
defined('KISSGO') or exit('No direct script access allowed');
bind('do_admin_layout', '&CoinsHookImpl', 21);
bind('get_acl_resource', '&CoinsHookImpl');
bind('get_columns_of_coinsRecords', '&CoinsHookImpl');
bind('sub_conis', '&\coins\classes\CoinsAccount', 1);
bind('load_member_data_for_passport', '&\coins\classes\CoinsAccount');
