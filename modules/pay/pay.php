<?php
/*
 * This file is part of wulacms.
 *
 * (c) Leo Ning <windywany@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

bind('do_admin_layout', '&pay\classes\PayHookImpl', 20);
bind('on_init_rest_server', '&pay\classes\PayRestService');