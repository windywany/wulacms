<?php
/*
 * This file is part of wulacms.
 *
 * (c) Leo Ning <windywany@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace pay\classes;

class OrderHandlerManager {

	public static function notify($type, $order) {
		return apply_filter('on_' . $type . '_deposit', true, $order);
	}

	/**
	 * @param string $type
	 *
	 * @return IOrderHandler|null
	 */
	public static function getHandler($type) {
		static $handlers = false;
		if ($handlers === false) {
			$handlers = apply_filter('get_desposit_order_handlers', []);
		}

		if (isset($handlers[ $type ])) {
			return $handlers[ $type ];
		}

		return null;
	}
}