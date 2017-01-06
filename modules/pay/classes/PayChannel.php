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

	public function onNotify() {
		return 'success';
	}
}