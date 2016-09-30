<?php

namespace passport\classes;

class OauthVendorManager {
	private static $INSTANCE = null;
	private        $venders  = [];

	private function __construct() {

	}

	public function register(IOAuthVendor $vendor) {
		$id = $vendor->getID();
		if (empty($id)) {
			return null;
		}
		$this->venders[ $id ] = $vendor;
	}

	/**
	 * 获取所有第三方登录.
	 * @return array
	 */
	public static function getVenders() {
		if (!self::$INSTANCE) {
			self::$INSTANCE = new OauthVendorManager();
		}
		fire('register_oauth_vender', self::$INSTANCE);

		return self::$INSTANCE->venders;
	}

	/**
	 * @param $id
	 *
	 * @return IOAuthVendor
	 */
	public static function getVendor($id) {
		$vendors = self::getVenders();
		if ($id && isset($vendors[ $id ])) {
			return $vendors[ $id ];
		}

		return null;
	}
}