<?php

namespace bbs\classes;

use cms\classes\ICmsPageUrlHandler;

class ForumUrlHandler implements ICmsPageUrlHandler {
	public function getName() {
		return '论坛版块';
	}
	public function load($url, $page, $params) {
	}
}

