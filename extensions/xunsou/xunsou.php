<?php

/*
 * This file is part of wulacms.
 *
 * (c) Leo Ning <windywany@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
if (class_exists('XSTokenizer')) {
	/**
	 * Class XSTokenizerSss
	 * 自定义分词器，将数字，英文，汉字分离出来.
	 *
	 */
	class XSTokenizerSss implements XSTokenizer {
		public function getTokens($value, XSDocument $doc = null) {
			static $dict = false;
			if ($dict === false) {
				$dict = TagForm::getDictFile();
			}
			$ret = [];
			if (!empty($value)) {
				$keys = get_keywords(null, $value, null, $dict, true);
				if ($keys[0]) {
					$ret = explode(',', $keys[0]);
				}
				$value = preg_replace_callback('/\d+/', function ($m) use (&$ret) {
					$ret[] = $m[0];

					return ' ';
				}, $value);
				$value = preg_replace_callback('/[a-z]+/i', function ($m) use (&$ret) {
					$ret[] = $m[0];

					return ' ';
				}, $value);
				$value = str_replace(' ', '', trim($value));
				$len   = mb_strlen($value);
				for ($i = 0; $i < $len; $i++) {
					$c = mb_substr($value, $i, 1);
					if (preg_match('/^[\x{4e00}-\x{9fa5}]+$/u', $c)) {
						$ret[] = $c;
					}
				}
			}
			if ($ret) {
				$ret = array_unique($ret);
			}

			return $ret;
		}

		public function getMysqlToken($value, $sep = ' ') {
			$ret = $this->getTokens($value);
			if ($ret) {
				$ret = implode($sep, $ret);
				$ret = convert_search_keywords($ret);

				return $ret;
			}

			return '';
		}
	}
}