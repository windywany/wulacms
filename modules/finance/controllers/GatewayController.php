<?php
namespace finance\controllers;

use finance\classes\IPayChannel;
use finance\classes\PayChannelManager;

class GatewayController extends \NonSessionController {
	/**
	 * @param $channel
	 *
	 * @return mixed
	 */
	public function index($channel) {
		$c = PayChannelManager::getChannel($channel);
		if ($c instanceof IPayChannel) {
			return $c->onCallback();
		}

		\Response::respond(403);
	}

	public function index_post($channel) {
		return $this->index($channel);
	}
}