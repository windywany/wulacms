<?php
/*
 * This file is part of wulacms.
 *
 * (c) Leo Ning <windywany@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace finance\command;

use artisan\ArtisanCommand;

class NotifyCommand extends ArtisanCommand {
	public function cmd() {
		return 'fnotify';
	}

	public function desc() {
		return 'notify the order provider the payment is done.';
	}

	protected function execute($options) {
		return 0;
	}
}