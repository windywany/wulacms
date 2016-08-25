<?php

namespace cms\classes;

interface ICmsPageUrlHandler {
	public function getName();
	public function load($url, $page, $params);
}