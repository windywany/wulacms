<?php

/**
 * Class QueryBuilder
 *
 * @method forupdate()
 */
abstract class QueryBuilder implements Countable {
	const LEFT  = 'LEFT';
	const RIGHT = 'RIGHT';
	const INNER = '';
	private static $sqlCount = 0;
	protected      $alias;
	protected      $dbconf;
	/**
	 * @var DatabaseDialect
	 */
	protected      $dialect;
	protected      $values;
	protected      $options  = array();
	protected      $from     = array();
	protected      $joins    = array();
	protected      $where    = null;
	protected      $having   = array();
	protected      $limit    = null;
	protected      $group    = array();
	protected      $order    = array();
	protected      $error    = false;
	protected      $errorSQL = '';
	protected      $errorValues;

	public function __construct() {
		$this->dbconf = 'default';
	}

	public function __destruct() {
		$this->close();
	}

	public function close() {
		$this->alias   = null;
		$this->dbconf  = null;
		$this->dialect = null;
		$this->values  = null;
		$this->options = null;
		$this->from    = null;
		$this->joins   = null;
		$this->where   = null;
		$this->having  = null;
		$this->limit   = null;
		$this->group   = null;
		$this->order   = null;
	}

	/**
	 * @param string $table
	 *
	 * @return QueryBuilder
	 */
	public function into($table) {
		return $this;
	}

	/**
	 * @param mixed $table
	 *
	 * @return QueryBuilder
	 */
	public function from($table) {
		$tables = func_get_args();
		foreach ($tables as $table) {
			$this->from [] = self::parseAs($table);
		}

		return $this;
	}

	/**
	 * @param string $table
	 * @param string $on
	 * @param string $type
	 *
	 * @return QueryBuilder
	 */
	public function join($table, $on, $type = QueryBuilder::LEFT) {
		$table          = self::parseAs($table);
		$join           = array($table [0], $on, $type . ' JOIN ', $table [1]);
		$this->joins [] = $join;

		return $this;
	}

	/**
	 * @param null $con
	 * @param bool $append
	 *
	 * @return QueryBuilder
	 */
	public function where($con = null, $append = true) {
		if (is_array($con) && !empty ($con)) {
			$con = new Condition ($con);
		}
		if ($con) {
			if ($append && $this->where) {
				$this->where [] = $con;
			} else {
				$this->where = $con;
			}
		}

		return $this;
	}

	public function getCondition() {
		return $this->where;
	}

	/**
	 * @param $having
	 *
	 * @return QueryBuilder
	 */
	public function having($having) {
		if (!empty ($having)) {
			$this->having [] = $having;
		}

		return $this;
	}

	/**
	 * @param $fields
	 *
	 * @return QueryBuilder
	 */
	public function groupBy($fields) {
		if (!empty ($fields)) {
			$this->group [] = $fields;
		}

		return $this;
	}

	/**
	 * @param $field
	 *
	 * @return QueryBuilder
	 */
	public function asc($field) {
		$this->order [] = array($field, 'ASC');

		return $this;
	}

	/**
	 * @param $field
	 *
	 * @return QueryBuilder
	 */
	public function desc($field) {
		$this->order [] = array($field, 'DESC');

		return $this;
	}

	/**
	 * @param string $rand
	 *
	 * @return QueryBuilder
	 */
	public function rand($rand = 'RAND') {
		$this->order [] = array(imv($rand), '()');

		return $this;
	}

	/**
	 * 排序
	 *
	 * @param string $field
	 *            排序字段，多个字段使用|分隔.
	 * @param string $order
	 *            a or d
	 *
	 * @return QueryBuilder
	 */
	public function sort($field, $order) {
		$order  = strtolower($order) == 'a' ? 'ASC' : 'DESC';
		$fields = explode('|', $field);
		foreach ($fields as $field) {
			$this->order [] = array($field, $order);
		}

		return $this;
	}

	/**
	 * @param int $start
	 * @param int $limit
	 *
	 * @return QueryBuilder
	 */
	public function limit($start, $limit) {
		$start = intval($start);
		$limit = intval($limit);
		if ($start < 0) {
			$start = 0;
		}
		if ($limit == 0) {
			$limit = 1;
		}
		$this->limit = array($start, $limit);

		return $this;
	}

	/**
	 * @param $alias
	 *
	 * @return QueryBuilder
	 */
	public function alias($alias) {
		$this->alias = $alias;

		return $this;
	}

	public function getAlias() {
		return $this->alias;
	}

	/**
	 * @param $database
	 *
	 * @return QueryBuilder
	 */
	public function usedb($database) {
		$this->dbconf = $database;

		return $this;
	}

	/**
	 * @param $dialect
	 *
	 * @return QueryBuilder
	 */
	public function setDialect($dialect) {
		if ($dialect instanceof DatabaseDialect) {
			$this->dialect = $dialect;
		} else {
			$this->dialect = DatabaseDialect::getDialect($dialect);
		}

		return $this;
	}

	/**
	 * get the dialect binding with this query.
	 *
	 * @return DatabaseDialect
	 */
	public function getDialect() {
		$this->checkDialect();

		return $this->dialect;
	}

	protected function checkDialect() {
		if (!$this->dialect) {
			$this->dialect = DatabaseDialect::getDialect($this->dbconf);
		}
		if (!$this->dialect) {
			if (DEBUG == DEBUG_DEBUG) {
				die ('Cannot connect to the database!');
			} else {
				status_header(404);
				exit ();
			}
		}
	}

	public function getBindValues() {
		return $this->values;
	}

	public function setBindValues($values) {
		$this->values = $values;
	}

	public function setPDOOptions($options) {
		$this->options = $options;
	}

	public function lastError() {
		return $this->error;
	}

	public function error() {
		return $this->error;
	}

	public function lastSQL() {
		return $this->errorSQL;
	}

	public function lastValues() {
		return $this->errorValues;
	}

	public function success() {
		return empty ($this->error) ? true : false;
	}

	/**
	 * 执行update,insert,delete语句.
	 *
	 * @param boolean $checkNum
	 *            false 不检测,null直接返回影响的数量
	 *            是否检测影响的条数.
	 *
	 * @return boolean
	 */
	public function exec($checkNum = false) {
		$cnt                              = $this->count();
		DatabaseDialect::$lastErrorMassge = $this->error;
		$this->close();
		if ($cnt === false) {
			return false;
		} else if (get_class($this) == 'InsertSQL') {
			if ($checkNum || is_null($checkNum)) {
				return $cnt > 0;
			} else {
				$ids = $this->lastInsertIds();

				return $ids;
			}
		} else if (is_null($checkNum)) {
			return $cnt;
		} else if ($checkNum) {
			return $cnt > 0;
		} else {
			return true;
		}
	}

	public function get($index = 0, $field = null) {
		return null;
	}

	public function exist($id) {
	}

	public function toArray($var = null, $key = null, $rows = array()) {
		return array();
	}

	public static function addSqlCount() {
		self::$sqlCount++;
	}

	public static function getSqlCount() {
		return self::$sqlCount;
	}

	protected function sanitize($var) {
		$this->checkDialect();
		if (is_string($var)) {
			return $this->dialect->sanitize($var);
		} else if (is_array($var)) {
			array_walk_recursive($var, array($this, 'sanitizeAry'));

			return $var;
		} else {
			return $var;
		}
	}

	/**
	 * work through an array to sanitize it, do not call this function directly.
	 * it is used internally.
	 *
	 * @see        sanitize()
	 *
	 * @param mixed $item
	 * @param mixed $key
	 *
	 * @deprecated .
	 */
	public function sanitizeAry(&$item, $key) {
		if (is_string($item)) {
			$item = $this->dialect->sanitize($item);
		}
	}

	protected static function parseAs($str) {
		$table = preg_split('#\b(as|\s+)\b#i', trim($str));
		if (count($table) == 1) {
			$name  = $table [0];
			$alias = null;
		} else {
			$name  = $table [0];
			$alias = trim(array_pop($table));
		}

		return array(trim($name), $alias);
	}

	protected function prepareFrom($froms) {
		$_froms = array();
		if ($froms) {
			foreach ($froms as $from) {
				$table     = $this->dialect->getTableName($from [0]);
				$alias     = empty ($from [1]) ? $table : $from [1];
				$_froms [] = array($table, $alias);
			}
		}

		return $_froms;
	}

	protected function prepareJoins($joins) {
		$_joins = array();
		if ($joins) {
			foreach ($joins as $join) {
				$table     = $this->dialect->getTableName($join [0]);
				$alias     = empty ($join [3]) ? $table : $join [3];
				$_joins [] = array($table, $join [1], $join [2], $alias);
			}
		}

		return $_joins;
	}

	/**
	 * prepare the fields in select SQL
	 *
	 * @param array      $fields
	 * @param BindValues $values
	 *
	 * @return string
	 */
	protected function prepareFields($fields, $values) {
		$_fields = array();
		foreach ($fields as $field) {
			if ($field instanceof Query) { // sub-select SQL as field
				$field->setDialect($this->dialect);
				$field->setBindValues($values);
				$as = $field->getAlias();
				if ($as) {
					$_fields [] = '(' . $field->__toString() . ') AS ' . $this->sanitize('`' . $as . '`');
				}
			} else { // this is simple field
				$_fields [] = $this->sanitize($field);
			}
		}
		if ($_fields) {
			return implode(',', $_fields);
		} else {
			return false;
		}
	}
}