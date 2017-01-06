<?php
/**
 * @Author     :     FLY
 * @DateTime   :    2016-09-03 11:04:08
 * @Description: 会员积分
 */
defined('KISSGO') or exit('No direct script access allowed');
bind('do_admin_layout', '&FinanceHookImpl', 20);
bind('get_acl_resource', '&FinanceHookImpl');
bind('get_columns_of_depositTable', '&FinanceHookImpl', 1);
bind('get_columns_of_withdrawTable', '&FinanceHookImpl', 1);
bind('get_artisan_commands', '&FinanceHookImpl');
bind('load_member_data_for_passport', '&FinanceHookImpl');

bind('get_desposit_order_handlers', '&finance\classes\DepositOrderHandler');
