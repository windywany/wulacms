<?php

namespace bbs\classes;

use cms\classes\ICmsPageUrlHandler;

class ThreadUrlHandler implements ICmsPageUrlHandler {
	public function getName() {
		return '论坛帖子';
	}
	public function load($url, $page, $params) {
	}
}

