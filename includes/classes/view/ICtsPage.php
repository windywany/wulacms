<?php
interface ICtsPage {
	/**
	 * 取模板中可使用的页面变量.
	 * 
	 * @return array
	 */
	function getFields();
}