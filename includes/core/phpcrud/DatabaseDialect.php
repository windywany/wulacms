<?php

/**
 *
 * deal with the difference between various databases
 * @author guangfeng.ning
 *
 */
abstract class DatabaseDialect extends PDO {
	private static $INSTANCE        = array();
	private        $tablePrefix;
	public static  $lastErrorMassge = '';

	public function __construct($options) {
		list ($dsn, $user, $passwd, $attr) = $this->prepareConstructOption($options);
		if (!isset ($attr [ PDO::ATTR_EMULATE_PREPARES ])) {
			$attr [ PDO::ATTR_EMULATE_PREPARES ] = false;
		}
		parent::__construct($dsn, $user, $passwd, $attr);
		$this->tablePrefix = isset ($options ['prefix']) && !empty ($options ['prefix']) ? $options ['prefix'] : '';
	}

	/**
	 * get the database dialect by the $name
	 *
	 * @param string $name
	 *
	 * @return DatabaseDialect
	 */
	public final static function getDialect($name = null) {
		if (is_array($name)) {
			$setting = KissGoSetting::getSetting();
			$dbcnfs  = $setting ['database'];
			if (empty ($name ['port'])) {
				unset ($name ['port']);
			}
			if (isset ($name ['dialect'])) {
				$tmpname = $name ['dialect'];
			} else {
				$tmpname = 'tmp' . '_' . $name ['host'] . '_' . $name ['dbname'];
			}
			$dbcnfs [ $tmpname ]  = $name;
			$setting ['database'] = $dbcnfs;
			$name                 = $tmpname;
		} else if (is_subclass_of2($name, 'DatabaseDialect')) {
			return $name;
		}
		try {
			$name = $name ? $name : 'default';
			if (defined('KISS_CLI_PID')) {
				self::$lastErrorMassge = false;
				if (!isset (self::$INSTANCE [ KISS_CLI_PID ][ $name ])) {
					$settings = KissGoSetting::getSetting();
					if (!isset ($settings ['database'])) {
						trigger_error('the configuration for database is not found!', E_USER_ERROR);
					}
					$database_settings = $settings ['database'];
					if (!isset ($database_settings [ $name ])) {
						trigger_error('the configuration for database: ' . $name . ' is not found!', E_USER_ERROR);
					}
					$options   = $database_settings [ $name ];
					$driver    = isset ($options ['driver']) && !empty ($options ['driver']) ? $options ['driver'] : 'MySQL';
					$driverClz = $driver . 'Dialect';
					if (!is_subclass_of2($driverClz, 'DatabaseDialect')) {
						trigger_error('the dialect ' . $driverClz . ' is not found!', E_USER_ERROR);
					}
					$dr = new $driverClz ($options);
					$dr->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
					$dr->onConnected();
					self::$INSTANCE [ KISS_CLI_PID ][ $name ] = $dr;
				}

				return self::$INSTANCE [ KISS_CLI_PID ][ $name ];
			} else {
				self::$lastErrorMassge = false;
				if (!isset (self::$INSTANCE [0] [ $name ])) {
					$settings = KissGoSetting::getSetting();
					if (!isset ($settings ['database'])) {
						trigger_error('the configuration for database is not found!', E_USER_ERROR);
					}
					$database_settings = $settings ['database'];
					if (!isset ($database_settings [ $name ])) {
						trigger_error('the configuration for database: ' . $name . ' is not found!', E_USER_ERROR);
					}
					$options   = $database_settings [ $name ];
					$driver    = isset ($options ['driver']) && !empty ($options ['driver']) ? $options ['driver'] : 'MySQL';
					$driverClz = $driver . 'Dialect';
					if (!is_subclass_of2($driverClz, 'DatabaseDialect')) {
						trigger_error('the dialect ' . $driverClz . ' is not found!', E_USER_ERROR);
					}
					$dr = new $driverClz ($options);
					$dr->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
					$dr->onConnected();
					self::$INSTANCE [0][ $name ] = $dr;
				}

				return self::$INSTANCE[0] [ $name ];
			}
		} catch (PDOException $e) {
			self::$lastErrorMassge = $e->getMessage();

			return null;
		}
	}

	/**
	 * 重置PDO实例,长时间后台运行的脚本需要在每次循环结束时调用此方法，以保证可以正常连接数据库.
	 *
	 * @param string $name 配置(名).
	 */
	public static function resetDialect($name = null) {
		$pid = defined('KISS_CLI_PID') ? KISS_CLI_PID : 0;
		if (is_array($name)) {
			$setting = KissGoSetting::getSetting();
			$dbcnfs  = $setting ['database'];
			if (empty ($name ['port'])) {
				unset ($name ['port']);
			}
			if (isset ($name ['dialect'])) {
				$tmpname = $name ['dialect'];
			} else {
				$tmpname = 'tmp' . '_' . $name ['host'] . '_' . $name ['dbname'];
			}
			$dbcnfs [ $tmpname ]  = $name;
			$setting ['database'] = $dbcnfs;
			$name                 = $tmpname;
		} else if (is_subclass_of2($name, 'DatabaseDialect')) {
			return;
		}

		$name                  = $name ? $name : 'default';
		self::$lastErrorMassge = false;
		if (isset (self::$INSTANCE [ $pid ] [ $name ])) {
			unset (self::$INSTANCE [ $pid ][ $name ]);
		}
	}

	/**
	 * get the full table name( prepend the prefix to the $table)
	 *
	 * @param string $table
	 *
	 * @return string
	 */
	public function getTableName($table) {
		if (preg_match('#^\{[^\}]+\}.*$#', $table)) {
			return str_replace(array('{', '}'), array($this->tablePrefix, ''), $table);
		} else {
			return $table;
		}
	}

	public function getTablePrefix() {
		return $this->tablePrefix;
	}

	/**
	 * get tables from sql.
	 *
	 * @param string $sqls
	 *
	 * @return array array('tables'=>array(),'views'=>array()) name array of tables and views defined in sql.
	 */
	public function getTablesFromSQL($sql) {
		$p      = '/CREATE\s+TABLE\s+(IF\s+NOT\s+EXISTS\s+)?([^\(]+)/mi';
		$tables = array();
		$views  = array();
		if (preg_match_all($p, $sql, $ms, PREG_SET_ORDER)) {
			foreach ($ms as $m) {
				if (count($m) == 3) {
					$table = $m [2];
				} else {
					$table = $m [1];
				}
				if ($table) {
					$table     = trim(trim($table, '` '));
					$tables [] = str_replace('{prefix}', $this->tablePrefix, $table);
				}
			}
		}
		$p = '/CREATE\s+VIEW\s+(IF\s+NOT\s+EXISTS\s+)?(.+?)\s+AS/mi';
		if (preg_match_all($p, $sql, $ms, PREG_SET_ORDER)) {
			foreach ($ms as $m) {
				if (count($m) == 3) {
					$table = $m [2];
				} else {
					$table = $m [1];
				}
				if ($table) {
					$table    = trim(trim($table, '` '));
					$views [] = str_replace('{prefix}', $this->tablePrefix, $table);
				}
			}
		}

		return array('tables' => $tables, 'views' => $views);
	}

	protected function onConnected() {
	}

	/**
	 * get a select SQL for retreiving data from database.
	 *
	 * @param array      $fields
	 * @param array      $from
	 * @param array      $joins
	 * @param Condition  $where
	 * @param array      $having
	 * @param array      $group
	 * @param array      $order
	 * @param array      $limit
	 * @param BindValues $values
	 * @param bool       $forupdate
	 *
	 * @return string
	 */
	public abstract function getSelectSQL($fields, $from, $joins, $where, $having, $group, $order, $limit, $values, $forupdate);

	/**
	 * get a select sql for geting the count from database
	 *
	 * @param array      $field
	 * @param array      $from
	 * @param array      $joins
	 * @param Condition  $where
	 * @param array      $having
	 * @param array      $group
	 * @param BindValues $values
	 *
	 * @return string
	 */
	public abstract function getCountSelectSQL($field, $from, $joins, $where, $having, $group, $values);

	/**
	 * get the insert SQL
	 *
	 * @param string     $into
	 * @param array      $data
	 * @param BindValues $values
	 *
	 * @return string
	 */
	public abstract function getInsertSQL($into, $data, $values);

	/**
	 * get the update SQL
	 *
	 * @param array      $table
	 * @param array      $data
	 * @param Condition  $where
	 * @param BindValues $values
	 *
	 * @return string
	 */
	public abstract function getUpdateSQL($table, $data, $where, $values);

	/**
	 * get the delete SQL
	 *
	 * @param string     $from
	 * @param array      $using
	 * @param Condition  $where
	 * @param BindValues $values
	 *
	 * @return string
	 */
	public abstract function getDeleteSQL($from, $using, $where, $values);

	/**
	 * list the databases.
	 *
	 * @return array
	 */
	public abstract function listDatabases();

	/**
	 * create a database.
	 *
	 * @param string $database
	 * @param string $charset
	 *
	 * @return bool
	 */
	public abstract function createDatabase($database, $charset);

	/**
	 * get driver name.
	 *
	 * @return string
	 */
	public abstract function getDriverName();

	/**
	 * transfer the char ` to a proper char.
	 *
	 * @param string $string
	 *
	 * @return string
	 */
	public abstract function sanitize($string);

	/**
	 * prepare the construct option, the return must be an array, detail listed following:
	 * 1.
	 * dsn
	 * 2. username
	 * 3. password
	 * 4. attributes
	 *
	 * @param array $options
	 *
	 * @return array array ( dsn, user,passwd, attr )
	 */
	protected abstract function prepareConstructOption($options);
}
