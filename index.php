<?php
$s = microtime(true);
defined('DS') or define('DS', DIRECTORY_SEPARATOR);
define('SP_ROOT', 'D:\spphp');
define('APP_ROOT', 'D:\wwwroot\credit');

defined('SP_DEBUG') or define('SP_DEBUG', true);

if(PHP_SAPI == 'cli')
{
	$GLOBALS['argc'] = $argc;
	$GLOBALS['argv'] = $argv;
}

require SP_ROOT.DS.'SP.php';
SP::init()->run();
var_dump(microtime(true)-$s);
