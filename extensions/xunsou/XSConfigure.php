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

abstract class XSConfigure {
	/**
	 * 配置，INI文件格式.
	 *
	 * @return string INI文件格式的配置内容.
	 */
	abstract public function getIniData();

	public function getScwsMulti() {
		return 8;
	}

	/**
	 * 获取自定义字典.
	 *
	 * @return string 自定义字典文件.
	 */
	public function getCustomDictFile() {
		return null;
	}
}