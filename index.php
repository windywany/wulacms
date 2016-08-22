<?php
/*
 * the entry of web static page
 */
define ( 'WEB_ROOT', dirname ( __FILE__ ) . DIRECTORY_SEPARATOR );
define ( 'CACHE_ENABLED', true );
define ( 'R_UUID_ENABLED', true );
if (! isset ( $_GET ['_url'] )) {
	$_GET ['_url'] = 'index.html';
}
include_once WEB_ROOT . 'bootstrap.php';
$router = Router::getRouter ();
$router->route ();
?>