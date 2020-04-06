<?php
/**
 * debug
 *
 * @author chenyong <chenyong@sc-edu.com>
 * @version 2.0
 * @date 2017-06-22
 */


interface DebugInterface
{
	/**
	 * Default error message
	 *
	 * @var string
	 */
	const DEFAULT_ERROR = 'Unknown error';

	/**
	 * Set debug
	 *
	 * @param bool $debug
	 * @return bool
	 */
	public function setDebug($debug);

	/**
	 * Get error message
	 *
	 * @param string $format
	 * @return mixed
	 */
	public function getError($format = 'json');

	/**
	 * Catch error message
	 *
	 * @param string message
	 * @return int
	 */
	public function catchError($message = null);
}
