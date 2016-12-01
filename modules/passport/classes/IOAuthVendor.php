<?php

namespace passport\classes;
/**
 * OAuth 接口.
 * @package passport\classes
 */
interface IOAuthVendor {
	function getID();

	function getName();

	function onLogin();

	function getURL();
}