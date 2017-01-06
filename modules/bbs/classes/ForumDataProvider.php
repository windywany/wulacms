<?php
/**
 *
 * User: Leo Ning.
 * Date: 9/8/16 20:32
 */

namespace bbs\classes;

use cms\classes\CtsDataProvider;

class ForumDataProvider extends CtsDataProvider {

	public function getVarName() {
		return 'forum';
	}

	protected function getData() {
		return new \CtsData([['id' => 'aa', 'title' => 'aaaa'], ['id' => 'bb', 'title' => 'bbbb']]);
	}
}