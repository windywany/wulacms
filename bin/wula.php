<?php
define ( 'WEB_ROOT', dirname ( __DIR__ ) . DIRECTORY_SEPARATOR );
@ob_end_clean ();
$appid = rand ( 1, 10000 );
echo "\tgenerated appid: ", $appid, "\n";
flush ();
$content = file_get_contents ( WEB_ROOT . 'bootstrap.php' );
$content = str_replace ( "basename ( WEB_ROOT )", "'A$appid')", $content );
file_put_contents ( WEB_ROOT . 'bootstrap.php', $content );
