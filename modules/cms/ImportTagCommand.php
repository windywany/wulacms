<?php
/*
 * This file is part of wulacms.
 *
 * (c) Leo Ning <windywany@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace cms;

use artisan\ArtisanCommand;

class ImportTagCommand extends ArtisanCommand {
	public function cmd() {
		return 'import-tag';
	}

	public function desc() {
		return 'import tag from txt file';
	}

	protected function getOpts() {
		return ['::p' => 'url prefix'];
	}

	protected function argDesc() {
		return '<file>';
	}

	protected function execute($options) {
		$file = $this->opt(-1);
		if (!$file) {
			$this->log('please give a file in which contains tags to be imported');

			return 1;
		}
		if (!is_file($file)) {
			$this->log($file . ' is not a file');

			return 1;
		}
		$prefix = isset($options['p']) && $options['p'] ? $options['p'] : '/tag/';
		$tags   = file($file);
		if ($tags) {
			$data['create_time'] = time();
			$data['create_uid']  = 1;
			$data['update_time'] = $data['create_time'];
			$data['update_uid']  = $data['create_uid'];
			$data['deleted']     = 0;
			foreach ($tags as $tag) {
				$tag = trim($tag);
				if ($tag) {
					$data['tag']   = $tag;
					$data['title'] = $tag;
					$data['url']   = $prefix . $tag;
					dbinsert($data)->into('{cms_tag}')->exec();
				}
			}
			\TagForm::generateScwsDictFile();
		}

		return 0;
	}
}