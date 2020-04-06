<?php
/**
 * config
 *
 * @author chenyong <chenyong@sc-edu.com>
 * @version 2.0
 * @date 2017-05-19
 */


/**
 * Path
 */
define('PATH_ROOT', dirname(__DIR__));
define('PATH_TEMP', PATH_ROOT.'/tmp');

/**
 * Database
 */
$gdbconf = array(
	'database' => array(
		'host' => '127.0.0.1',
		'port' => '3306',
		'username' => 'root',
		'password' => 'toor@1234',
		'dbname' => 'jhy',
		'persistent' => false,
	),
);

