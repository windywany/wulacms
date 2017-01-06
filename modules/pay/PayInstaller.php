<?php

/*
 * This file is part of wulacms.
 *
 * (c) Leo Ning <windywany@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class PayInstaller extends AppInstaller {
	public function getName() {
		return '支付网关';
	}

	public function getAuthor() {
		return '宁广丰';
	}

	public function getWebsite() {
		return 'http://www.kisscms.cn/plugins/pay';
	}

	public function getDscription() {
		return '可以接入多种第三方支付，完成用户充值，消费';
	}

	public function getDependences() {
		$d['finance'] = '[2.1.0,)';
	}

	public function getVersionLists() {
		$v['1.0.0'] = '20170103001';

		return $v;
	}
}