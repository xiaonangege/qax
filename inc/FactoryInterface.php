<?php
/**
 * init
 *
 * @author chenyong <chenyong@sc-edu.com>
 * @version 2.0
 * @date 2017-06-22
 */


interface FactoryInterface
{
	/**
	 * Default app
	 *
	 * @var string
	 */
	const DEFAULT_APP = 'default';

	/**
	 * Init with specified config
	 *
	 * @param string $app
	 * @return object
	 */
	public static function factory($app = null);

	/**
	 * Alias of factory
	 *
	 * @param string $app
	 * @return object
	 */
	public static function init($app = null);
}
