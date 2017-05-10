<?php
/*
 * This file is part of wulacms.
 *
 * (c) Leo Ning <windywany@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace taoke\classes;

use xunsou\XSConfigure;

class TaokeXSConfigure extends XSConfigure {
	public function getIniData() {
		return "project.name = taoke
				[id]
				type = id
				[ch]
				index = self
				[ctime]
				type = numeric
				[title]
				tokenizer = sss
				type = title";
	}

	public function getScwsMulti() {
		return 15;
	}

	public function getCustomDictFile() {
		return TMP_PATH . 'tag_dict.txt';
	}
}