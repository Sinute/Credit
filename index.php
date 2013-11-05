<?php
defined('DS') or define('DS', DIRECTORY_SEPARATOR);
define('SP_ROOT', 'YOUR_SP_ROOT');
define('APP_ROOT', 'YOUR_APP_ROOT');

defined('SP_DEBUG') or define('SP_DEBUG', true);

if(PHP_SAPI == 'cli')
{
	$GLOBALS['argc'] = $argc;
	$GLOBALS['argv'] = $argv;
}

require SP_ROOT.DS.'SP.php';
SP::init()->run();
