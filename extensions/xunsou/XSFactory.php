<?php
/*
 * This file is part of wulacms.
 *
 * (c) Leo Ning <windywany@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace xunsou;

class XSFactory {
	/**
	 * @param \xunsou\XSConfigure $configure
	 *
	 * @return \XS
	 */
	public static function getXS(XSConfigure $configure) {
		$xs   = new \XS($configure->getIniData());
		$file = $configure->getCustomDictFile();

		if ($file && is_file($file)) {
			$xs->index->setCustomDict($file);
		}

		$xs->index->setScwsMulti($configure->getScwsMulti());
		$xs->search->setScwsMulti($configure->getScwsMulti());

		return $xs;
	}
}