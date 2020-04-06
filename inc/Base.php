<?php
/**
 * base
 *
 * @author chenyong <chenyong@sc-edu.com>
 * @version 2.0
 * @date 2017-06-22
 */


require_once __DIR__.'/DebugInterface.php';
require_once __DIR__.'/FactoryInterface.php';

class Base implements DebugInterface, FactoryInterface
{
	/**
	 * Debug on or off
	 *
	 * @var bool
	 */
	protected $debug = false;

	/**
	 * Error message
	 *
	 * @var array
	 */
	protected $err_msg = array();

	/**
	 * Error count
	 *
	 * @var array
	 */
	protected $err_count = 0;

	/**
	 * inited object
	 *
	 * @var array
	 */
	protected static $instance = array();

	/**
	 * quick init
	 *
	 * @param string app
	 * @return resource
	 */
	public static function factory($app = null)
	{
		if (!$app) {
			$app = self::DEFAULT_APP;
		}
		if (!array_key_exists($app, self::$instance)) {
			self::$instance[$app] = new self();
		}
		return self::$instance[$app];
	}

	/**
	 * quick init, another name
	 *
	 * @param string app
	 * @return resource
	 */
	public static function init($app = null)
	{
		return self::factory($app);
	}

	/**
	 * quick test
	 *
	 * @param string app
	 * @return bool
	 */
	public static function test($app = null)
	{
		$obj = self::factory($app);
		$error = $obj->getError('');
		return count($error) == 0 ? true : false;
	}

	/**
	 * Set debug
	 *
	 * @param bool debug
	 * @return bool
	 */
	public function setDebug($debug)
	{
		$this->debug = $debug;
		return true;
	}

	/**
	 * Stop with error
	 *
	 * @param string message
	 * @paran bool output
	 * @return none
	 */
	public function halt($message = '', $output = false)
	{
		$this->catchError($message);
		$result = $this->getError('json');
		if ($this->debug || $output) {
			echo $result;
			exit;
		} else {
			$file = PATH_TEMP.'/error.txt';
			$str = date('Y-m-d H:i:s')."\t".$_SERVER['REQUEST_URI']."\n".$result."\n\n";
			file_put_contents($file, $str, FILE_APPEND);
		}
	}

	/**
	 * Get error message
	 *
	 * @param string format
	 * @return mixed
	 */
	public function getError($format = 'json')
	{
		switch ($format) {
			case 'string':
				return implode("<br>\n", $this->err_msg);
				break;
			case 'json':
				return json_encode($this->err_msg);
				break;
			default:
				return $this->err_msg;
				break;
		}
	}

	/**
	 * Catch error message
	 *
	 * @param string message
	 * @return int
	 */
	public function catchError($message = '')
	{
		$this->err_count++;
		array_push($this->err_msg, $message);
		if ($this->err_count > 10) {
			array_shift($this->err_msg);
		}
		return $this->err_count;
	}
}
