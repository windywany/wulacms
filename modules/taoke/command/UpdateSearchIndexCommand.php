<?php
/*
 * This file is part of wulacms.
 *
 * (c) Leo Ning <windywany@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace taoke\command;

use artisan\ArtisanDaemonTask;

class UpdateSearchIndexCommand extends ArtisanDaemonTask {
	private $size = 5000;

	public function cmd() {
		return 'update-searchidx';
	}

	public function desc() {
		return 'update page search index';
	}

	protected function execute($options) {
		$tokenizer = new \XSTokenizerSss();
		$start     = $this->taskId * $this->size;
		$end       = $start + $this->size;
		$cnt       = 0;
		$limit     = 200;
		syslog(LOG_INFO, '#' . $this->taskId . '  start from position ' . $start . ' to ' . $end);
		while ($start < $end) {
			$pages = dbselect('id,title')->from('{cms_page}')->where(['model' => 'taoke'])->asc('id')->limit($start, $limit)->toArray();
			if (!$pages) {
				break;
			}
			foreach ($pages as $page) {
				$title   = $page['title'];
				$keyword = $tokenizer->getMysqlToken($title);
				if ($keyword) {
					dbupdate('{cms_page}')->set(['search_index' => $keyword])->where(['id' => $page['id']])->exec();
					$cnt++;
				}
			}
			unset($pages);
			$start += $limit;
		}
		syslog(LOG_INFO, '#' . $this->taskId . ' updated  ' . $cnt . ' pages， end at positon ' . $start);

		return 0;
	}

	protected function setUp(&$options) {
		$this->setMaxMemory('256M');
		$count = dbselect()->from('{cms_page}')->where(['model' => 'taoke'])->count('id');
		if ($count) {
			$this->workerCount = ceil($count / $this->size);
			syslog(LOG_INFO, $this->workerCount . ' worker(s) will rebuild index for ' . $count . ' page(s)');
		} else {
			$this->workerCount = 0;
		}
	}
}