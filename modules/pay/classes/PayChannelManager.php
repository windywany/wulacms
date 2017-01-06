<?php

namespace pay\classes;

class PayChannelManager {
	/**
	 * 依据名称获取channel
	 *
	 * @param $channel
	 *
	 * @return IPayChannel
	 */
	public static function getChannel($channel) {
		$channels = self::getChannels();
		if (isset($channels[ $channel ])) {
			return $channels[ $channel ];
		} else {
			return null;
		}
	}

	public static function getChannels() {
		$channels = apply_filter('get_pay_channel', []);

		return $channels;
	}

	public static function getCfg($channel, $name, $default = '') {
		return cfg($name . '@gateway_' . $channel, $default);
	}

	public static function getSuccessURL($order_id) {
		$url = cfg('success_url@finance');
		if (!$url) {
			$url = '/';
		}

		return url_append_args($url, ['order_id' => $order_id]);
	}

	public static function getFailureURL() {
		$url = cfg('failure_url@finance');
		if (!$url) {
			$url = '/';
		}

		return $url;
	}
}