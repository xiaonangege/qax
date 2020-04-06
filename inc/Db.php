<?php
/**
 * db
 *
 * @author chenyong <chenyong@sc-edu.com>
 * @version 2.0
 * @date 2017-06-22
 */


require_once __DIR__.'/Base.php';

class Db extends Base
{
	/**
	 * 数据库配置
	 *
	 * @var string/bool
	 */
	protected $server = null;
	protected $username = null;
	protected $password = null;
	protected $dbname = null;
	protected $persistent = false;

	/**
	 * 数据库连接
	 *
	 * @var resource
	 */
	protected $conn = null;

	/**
	 * 操作执行数目
	 *
	 * @var int
	 */
	protected $query_num = 0;

	/**
	 * 上一条执行过的sql
	 *
	 * @var string
	 */
	protected $query_sql = null;

	/**
	 * 重载快速初始化
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
			global $gdbconf;
			if (array_key_exists($app, $gdbconf)) {
				$var = $gdbconf[$app];
				$obj = new self();
				$obj->server = $var['host'].':'.$var['port'];
				$obj->username = $var['username'];
				$obj->password = $var['password'];
				$obj->dbname = $var['dbname'];
				$obj->persistent = $var['persistent'];
				$obj->connect();
				$obj->selectDb();
				$obj->query('set names utf8');
				self::$instance[$app] = $obj;
			} else {
				self::$instance[$app] = null;
				throw new \Exception('db config not found');
			}
		}
		return self::$instance[$app];
	}

	/**
	 * 重载快速初始化
	 *
	 * @param string app
	 * @return resource
	 */
	public static function init($app = null)
	{
		return self::factory($app);
	}

	/**
	 * 构造函数
	 *
	 * @param mixed var
	 * @return none
	 */
	public function __construct($var = null)
	{
		if (is_resource($var)) {
			$this->conn = $var;
		} elseif (is_array($var)) {
			$this->server = $var['host'].':'.$var['port'];
			$this->username = $var['username'];
			$this->password = $var['password'];
			$this->dbname = $var['dbname'];
			$this->persistent = $var['persistent'];
			$this->connect();
			$this->selectDb();
		}
	}

	/**
	 * 连接数据库
	 *
	 * @return bool
	 */
	public function connect()
	{
		if(!$this->server) {
			$this->halt('db config not found');
			return false;
		}
		if ($this->persistent) {
			$this->conn = mysql_pconnect($this->server, $this->username, $this->password);
		} else {
			$this->conn = mysql_connect($this->server, $this->username, $this->password, true);
		}
		if(!$this->conn) {
			$this->halt('db connect failed : '.mysql_error());
			return false;
		}
		return true;
	}

	/**
	 * 选择数据库
	 *
	 * @return bool
	 */
	public function selectDb()
	{
		if (!$this->dbname) {
			$this->halt('dbname not defined');
			return false;
		}
		if(!mysql_select_db($this->dbname, $this->conn)) {
			$this->halt('db selectdb failed : '.mysql_error());
			return false;
		}
		return true;
	}

	/**
	 * 关闭连接
	 *
	 * @return bool
	 */
	public function close()
	{
		if ($this->conn) {
			return mysql_close($this->conn);
		}
		return true;
	}

	/**
	 * 执行sql
	 *
	 * @param string sql
	 * @param bool return affect num
	 * @return resource
	 */
	public function query($sql, $affectNum = false)
	{
		if (!$sql || !$this->conn) {
			return false;
		}
		$result = mysql_query($sql, $this->conn);
		if (!$result) {
			if (mysql_errno($this->conn) == 2006) {//超时重连
				$this->connect();
				$this->selectDb();
				$this->query('set names utf8');
				$result = mysql_query($sql, $this->conn);
			}
		}
		if (!$result) {
			$this->halt("sql error : ".$sql."\n".mysql_error($this->conn));
			return false;
		}
		$this->query_num++;
		$this->query_sql = $sql;
		if ($affectNum) {
			return $this->affectNum();
		}
		return $result;
	}

	/**
	 * 取一条数据
	 *
	 * @param string sql
	 * @return array
	 */
	public function fetchRow($sql)
	{
		$result = $this->query($sql);
		if(!$result) {
			return false;
		}
		$row = mysql_fetch_assoc($result);
		$this->free($result);
		return $row;
	}

	/**
	 * 取一条数据，别名
	 *
	 * @param string sql
	 * @return array
	 */
	public function getRow($sql)
	{
		return $this->fetchRow($sql);
	}

	/**
	 * 取多条数据
	 *
	 * @param string sql
	 * @return array
	 */
	public function fetchRows($sql)
	{
		$result = $this->query($sql);
		if(!$result) {
			return false;
		}
		$data = array();
		while ($row = mysql_fetch_assoc($result)) {
			$data[] = $row;
		}
		$this->free($result);
		return $data;
	}

	/**
	 * 取多条数据，别名
	 *
	 * @param string sql
	 * @return array
	 */
	public function getRows($sql)
	{
		return $this->fetchRows($sql);
	}

	/**
	 * 取一个数据
	 *
	 * @param string sql
	 * @return array
	 */
	public function fetchOne($sql)
	{
		$row = $this->fetchRow($sql);
		if (!is_array($row)) {
			return false;
		}
		return array_shift($row);
	}

	/**
	 * 取一个数据，别名
	 *
	 * @param string sql
	 * @return array
	 */
	public function getOne($sql)
	{
		return $this->fetchOne($sql);
	}

	/**
	 * 取一组数据
	 *
	 * @param string sql
	 * @return array
	 */
	public function fetchCol($sql)
	{
		$data = $this->fetchRows($sql);
		if (!is_array($data)) {
			return false;
		}
		$col = array();
		foreach ($data as $v) {
			$col[] = array_shift($v);
		}
		return $col;
	}

	/**
	 * 取一组数据，别名
	 *
	 * @param string sql
	 * @return array
	 */
	public function getCol($sql)
	{
		return $this->fetchCol($sql);
	}

	/**
	 * 插入
	 *
	 * @param string table
	 * @param array data to be inserted
	 * @param bool return insert id
	 * @param bool debug mode (return sql instead of query it)
	 * @return mixed
	 */
	public function insert($table, $data, $insertId = false, $debug = false)
	{
		if (!$table) {
			return false;
		}
		$fields = array();
		$values = array();
		foreach ($data as $k => $v){
			$fields[] = $k;
			$values[] = $v;
		}
		if(!$fields || !$values) {
			return false;
		}
		$sql = "insert into `$table` (`".implode("`,`", $fields)."`) values ('".implode("','", $values)."')";
		if($debug){
			return $sql;
		}
		$result = $this->query($sql);
		return $insertId ? $this->insertId() : $result;
	}

	/**
	 * 更新
	 *
	 * @param string table
	 * @param array data to be updated
	 * @param string condition
	 * @param int limit num
	 * @return bool
	 */
	public function update($table, $data, $cond, $limit = null)
	{
		if (!$table || !is_array($data)) {
			return false;
		}
		$set = array();
		foreach ($data as $k => $v) {
			$set[] = "`{$k}`='{$v}'";
		}
		if (!$set) {
			return false;
		}
		$sql = "update `$table` set ".implode(',', $set)." where $cond".($limit ? " limit $limit" : '');
		return $this->query($sql);
	}

	// 更新 别名
	public function updateRow($table, $data, $cond, $limit = 1) {
		return $this->update($table, $data, $cond, $limit);
	}


	/**
	 * 上次插入的id
	 *
	 * @return int
	 */
	public function insertId()
	{
		return mysql_insert_id($this->conn);
	}

	/**
	 * 影响的记录数
	 *
	 * @return int
	 */
	public function affectNum()
	{
		return mysql_affected_rows($this->conn);
	}

	/**
	 * 结果集的数量
	 *
	 * @param resuouce query result
	 * @return int
	 */
	public function numRows($result)
	{
		if (!is_resource($result)) {
			return false;
		}
		return mysql_num_rows($result);
	}

	/**
	 * 释放结果
	 *
	 * @param resource query result
	 * @return bool
	 */
	public function free($result)
	{
		if (!is_resource($result)) {
			return false;
		}
		return mysql_free_result($result);
	}
}
