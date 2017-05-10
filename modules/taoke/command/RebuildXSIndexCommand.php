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
use taoke\classes\TaokeXSConfigure;
use xunsou\XSFactory;

class RebuildXSIndexCommand extends ArtisanDaemonTask {
	private $size = 5000;

	public function cmd() {
		return 'rebuild-taokeidx';
	}

	public function desc() {
		return 'rebuild taoke xunsearch index';
	}

	protected function execute($options) {
		$xs    = XSFactory::getXS(new TaokeXSConfigure());
		$start = $this->taskId * $this->size;
		$end   = $start + $this->size;
		$cnt   = 0;
		$limit = 200;
		$xs->index->openBuffer();
		syslog(LOG_INFO, '#' . $this->taskId . '  start from position ' . $start . ' to ' . $end);
		while ($start < $end) {
			$pages = dbselect('id,title,channel as ch,update_time as ctime')->from('{cms_page}')->where(['model' => 'taoke'])->asc('id')->limit($start, $limit)->toArray();
			if (!$pages) {
				break;
			}

			foreach ($pages as $page) {
				$ch = dbselect()->from('{cms_channel}')->where(['refid' => $page['ch']])->get('root');
				if ($ch) {
					$page['ch'] = $ch;
				}
				$doc = new \XSDocument($page);
				$xs->index->update($doc);
				$cnt++;
			}
			unset($pages);
			$start += $limit;
		}
		$xs->index->closeBuffer();
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

	protected function tearDown(&$options) {
		$xs = XSFactory::getXS(new TaokeXSConfigure());
		$xs->index->flushIndex();
	}

}