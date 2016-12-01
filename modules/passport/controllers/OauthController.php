<?php

namespace passport\controllers;

use passport\classes\OauthVendorManager;

class OauthController extends \Controller {

	public function index($type) {
		$vender = OauthVendorManager::getVendor($type);
		if ($vender) {
			$rst = $vender->onLogin();
			if ($rst) {
				//TODO
			} else {
				\Response::respond(500);
			}
		} else {
			\Response::respond(500);
		}
	}
}