<?php
/*
 * This file is part of wulacms.
 *
 * (c) Leo Ning <windywany@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace pay\controllers;

use pay\classes\IPayChannel;
use pay\classes\PayChannelManager;

class NotifyController extends \NonSessionController {
	/**
	 * @param $channel
	 *
	 * @return mixed
	 */
	public function index($channel) {
		$c = PayChannelManager::getChannel($channel);
		if ($c instanceof IPayChannel) {
			return $c->onNotify();
		}

		\Response::respond(403);
	}

	public function index_post($channel) {
		return $this->index($channel);
	}
}