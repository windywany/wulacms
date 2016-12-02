<?php
/*
 * the entry of artisan script
 */
use artisan\ArtisanHelpCommand;

define('WEB_ROOT', dirname(__DIR__) . DIRECTORY_SEPARATOR);
include_once WEB_ROOT . 'bootstrap.php';
@ob_end_clean();
// comand list
$commands = apply_filter('get_artisan_commands', ['help' => new ArtisanHelpCommand()]);
set_time_limit(0);
$cmd = isset($argv[1]) ? $argv[1] : 'help';

if (!isset($commands[ $cmd ])) {
	$commands['help']->help("Unkown Command: " . $cmd);
} else {
	exit($commands[ $cmd ]->run());
}

