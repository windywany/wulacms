<?php
/*
 * This file is part of wulacms.
 *
 * (c) Leo Ning <windywany@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
require_once 'lib/alipay_submit.class.php';
require_once 'lib/alipay_notify.class.php';

bind('get_pay_channel', function ($channels) {
	$channels['alipay'] = new \alipay\AlipayChannel();

	return $channels;
});
