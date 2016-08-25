<?php
/**
 * for postgres SQL.
 *
 * @author guangfeng.ning
 *
 */
class PostgreSQLDialect extends DatabaseDialect {
	public function __construct($options) {
		parent::__construct ( $options );
		$charset = isset ( $options ['encoding'] ) && ! empty ( $options ['encoding'] ) ? $options ['encoding'] : 'UTF8';
		$rst = $this->query ( "SET NAMES '{$charset}'" );
		if (! $rst) {
			log_debug ( "Cannot perform the SQL: SET NAMES '{$charset}'" );
		}
	}
	/**
	 * (non-PHPdoc)
	 * 
	 * @see DatabaseDialect::getSelectSQL()
	 * @param Query $query        	
	 */
	public function getSelectSQL($fields, $from, $joins, $where, $having, $group, $order, $limit, $values) {
		$sql = array ('SELECT',$fields,'FROM' );
		$this->generateSQL ( $sql, $from, $joins, $where, $having, $group, $values );
		if ($order) {
			$_orders = array ();
			foreach ( $order as $o ) {
				$_orders [] = $o [0] . ' ' . $o [1];
			}
			$sql [] = 'ORDER BY ' . implode ( ' , ', $_orders );
		}
		if ($limit) {
			$limit1 = $values->addValue ( 'limit', $limit [0] );
			$limit2 = $values->addValue ( 'limit', $limit [1] );
			$sql [] = 'LIMIT ' . $limit2 . ' OFFSET ' . $limit1;
		}
		$sql = implode ( ' ', $sql );
		return $sql;
	}
	/**
	 * (non-PHPdoc)
	 * 
	 * @see DatabaseDialect::getCountSelectSQL()
	 */
	public function getCountSelectSQL($fields, $from, $joins, $where, $having, $group, $values) {
		$sql = array ('SELECT',$fields,'FROM' );
		$this->generateSQL ( $sql, $from, $joins, $where, $having, $group, $values );
		$sql = implode ( ' ', $sql );
		return $sql;
	}
	/**
	 * (non-PHPdoc)
	 * 
	 * @see DatabaseDialect::getInsertSQL()
	 */
	public function getInsertSQL($into, $data, $values) {
		$sql = "INSERT INTO $into (\"";
		$fields = $_values = array ();
		foreach ( $data as $field => $value ) {
			$fields [] = $field;
			if ($value instanceof ImmutableValue) { // a immutable value
				$_values [] = $this->sanitize ( $value->__toString () );
			} else if ($value instanceof Query) { // a sub-select SQL as a value
				$value->setBindValues ( $values );
				$value->setDialect ( $this );
				$_values [] = '(' . $value->__toString () . ')';
			} else {
				$_values [] = $values->addValue ( $field, $value );
			}
		}
		$sql .= implode ( '" , "', $fields ) . '") VALUES (' . implode ( ' , ', $_values ) . ')';
		return $sql;
	}
	/**
	 * (non-PHPdoc)
	 * 
	 * @see DatabaseDialect::getDeleteSQL()
	 */
	public function getDeleteSQL($from, $using, $where, $values) {
		$sql [] = 'DELETE FROM ' . $from [0];
		if ($using) {
			$us = array ();
			foreach ( $using as $u ) {
				$us [] = $u [0];
			}
			$sql [] = 'USING';
			$sql [] = implode ( ' , ', $us );
		}
		if ($where && count ( $where ) > 0) {
			$sql [] = 'WHERE';
			$sql [] = $where->getWhereCondition ( $this, $values );
		}
		return implode ( ' ', $sql );
	}
	/**
	 * (non-PHPdoc)
	 * 
	 * @see DatabaseDialect::getUpdateSQL()
	 */
	public function getUpdateSQL($table, $data, $where, $values) {
		$sql = array ('UPDATE',$table,'SET' );
		$fields = array ();
		foreach ( $data as $field => $value ) {
			if ($value instanceof Query) {
				$value->setBindValues ( $values );
				$value->setDialect ( $this );
				$fields [] = $this->sanitize ( $field ) . ' =  (' . $value->__toString () . ')';
			} else if ($value instanceof ImmutableValue) {
				$fields [] = $this->sanitize ( $field ) . ' =  ' . $this->sanitize ( $value->__toString () );
			} else {
				$fields [] = $this->sanitize ( $field ) . ' = ' . $values->addValue ( $field, $value );
			}
		}
		$sql [] = implode ( ' , ', $fields );
		if ($where && count ( $where ) > 0) {
			$sql [] = 'WHERE';
			$sql [] = $where->getWhereCondition ( $this, $values );
		}
		return implode ( ' ', $sql );
	}
	/**
	 *
	 * @see DatabaseDialect::prepareConstructOption()
	 */
	protected function prepareConstructOption($options) {
		$opts = array_merge ( array ('host' => 'localhost','port' => 5432,'user' => 'root','password' => 'root','driver_options' => array () ), $options );
		$dsn = "pgsql:dbname={$opts['dbname']};host={$opts['host']};port={$opts['port']}";
		return array ($dsn,$opts ['user'],$opts ['password'],$opts ['driver_options'] );
	}
	public function sanitize($string) {
		return str_replace ( '`', '"', $string );
	}
	
	/*
	 * (non-PHPdoc) @see DatabaseDialect::createDatabase()
	 */
	public function createDatabase($database, $charset) {
		// TODO create database in postgresql.
	}
	/*
	 * (non-PHPdoc) @see DatabaseDialect::getDriverName()
	 */
	public function getDriverName() {
		return 'pgsql';
	}
	
	/*
	 * (non-PHPdoc) @see DatabaseDialect::listDatabases()
	 */
	public function listDatabases() {
		// TODO list postgresql databases
	}
	
	/**
	 * generate the common SQL for select and select count
	 *
	 * @param array $sql        	
	 * @param array $from        	
	 * @param array $joins        	
	 * @param Condition $where        	
	 * @param array $having        	
	 * @param array $group        	
	 * @param BindValues $values        	
	 */
	private function generateSQL(&$sql, $from, $joins, $where, $having, $group, $values) {
		$froms = array ();
		foreach ( $from as $f ) {
			$froms [] = $f [0] . ' AS ' . $f [1];
		}
		$sql [] = implode ( ',', $froms );
		if ($joins) {
			foreach ( $joins as $join ) {
				$sql [] = $join [2] . ' ' . $join [0] . ' AS ' . $join [3] . ' ON (' . $join [1] . ')';
			}
		}
		if ($where && count ( $where ) > 0) {
			$sql [] = 'WHERE ' . $where->getWhereCondition ( $this, $values );
		}
		if ($group) {
			$sql [] = 'GROUP BY ' . implode ( ' , ', $group );
		}
		if ($having) {
			$sql [] = 'HAVING ' . implode ( ' AND ', $having );
		}
	}
}