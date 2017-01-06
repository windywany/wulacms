<?php
/**
 * author: FLY
 * Date: 2016/9/12
 * Time: 14:18
 */

namespace pay\classes;
abstract class PayChannel implements IPayChannel {
	/**
	 * @param $form
	 */
	public function getSettingForm($form) {
	}

	public function checkForm() {
		return null;
	}

	public function doCheck() {
		return true;
	}

	public function getPayForm($order) {
		return null;
	}

	public function onNotify() {
		return 'success';
	}
}