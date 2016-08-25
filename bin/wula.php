<?php
define ( 'WEB_ROOT', dirname ( __DIR__ ) . DIRECTORY_SEPARATOR );
@ob_end_clean ();
$appid = rand ( 1, 100000 );
echo "\tgenerated appid: ", $appid, "\n";
flush ();
echo "\tapply appid: ", $appid, " into bootrap.php\n";
flush ();
$content = file_get_contents ( WEB_ROOT . 'bootstrap.php' );
$content = str_replace ( "basename ( WEB_ROOT )", "'A$appid'", $content );
file_put_contents ( WEB_ROOT . 'bootstrap.php', $content );
echo "\tChanging the permission of  appdata\n";
flush ();
chmod ( WEB_ROOT . 'appdata', 0777 );
if (! is_dir ( WEB_ROOT . 'appdata/tmp' )) {
	mkdir ( WEB_ROOT . 'appdata/tmp', 0777, true );
}
if (! is_dir ( WEB_ROOT . 'appdata/logs' )) {
	mkdir ( WEB_ROOT . 'appdata/logs', 0777, true );
}
chmod ( WEB_ROOT . 'appdata/tmp', 0777 );
chmod ( WEB_ROOT . 'appdata/logs', 0777 );
echo "\tDone! Enjoy it ^_^\n";
echo "Read INSTALL.txt to install it with Apache or Nginx!\n";
flush ();