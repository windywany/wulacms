<?php
/**
 * kissgo framework that keep it simple and stupid, go go go ~~
 *
 * @author Windywany
 * @package kissgo
 * @date 12-9-16 下午6:16
 * $Id$
 */
defined ( 'KISSGO' ) or exit ( 'No direct script access allowed' );
/**
 * execute the $callable in a transaction
 *
 * @param mixed $callable        	
 * @param array $param        	
 * @param string $name
 *        	database or config
 * @return mixed
 */
function runintran($callable, $param = array(), $name = null) {
	if (start_tran ( $name )) {
		if (call_user_func_array ( $callable, $param )) {
			return commit_tran ( $name );
		} else {
			rollback_tran ( $name );
		}
	}
	return false;
}
/**
 * start a database transaction
 *
 * @param string $name
 *        	database or config
 * @return boolean
 */
function start_tran($name = null) {
	$dialect = DatabaseDialect::getDialect ( $name );
	try {
		return $dialect->beginTransaction ();
	} catch ( Exception $e ) {
		DatabaseDialect::$lastErrorMassge = $e->getMessage ();
		return false;
	}
}
/**
 * commit a transaction
 *
 * @param string $name
 *        	database or config
 */
function commit_tran($name = null) {
	$dialect = DatabaseDialect::getDialect ( $name );
	try {
		return $dialect->commit ();
	} catch ( PDOException $e ) {
		DatabaseDialect::$lastErrorMassge = $e->getMessage ();
		return false;
	}
}
/**
 * rollback a transaction
 *
 * @param string $name
 *        	database or config
 */
function rollback_tran($name = null) {
	$dialect = DatabaseDialect::getDialect ( $name );
	try {
		return $dialect->rollBack ();
	} catch ( PDOException $e ) {
		DatabaseDialect::$lastErrorMassge = $e->getMessage ();
		return false;
	}
}

/**
 * insert data into table
 *
 * @param array $datas        	
 * @param array $batch        	
 */
function dbinsert($datas, $batch = false) {
	return new InsertSQL ( $datas, $batch );
}
/**
 * insert or update a record recording to the $where or id value.
 *
 * @param array $data        	
 * @param array $where        	
 * @param string $idf        	
 * @return SaveQuery
 */
function dbsave($data, $where, $idf = 'id') {
	return new SaveQuery ( $data, $where, $idf );
}
/**
 * shortcut for new Query
 *
 * @param string $fields        	
 * @return Query
 */
function dbselect($fields = '*') {
	return new Query ( func_get_args () );
}
/**
 * 锁定表.
 * 
 * @param string $table        	
 */
function dblock($table, $dialect = null) {
	if ($dialect == null) {
		$dialect = DatabaseDialect::getDialect ();
	}
	$table = $dialect->getTableName ( $table );
	$dialect->query ( "LOCK TABLES `" . $table . "` " );
}
function dbunlock($dialect = null) {
	if ($dialect == null) {
		$dialect = DatabaseDialect::getDialect ();
	}
	$dialect->query ( "UNLOCK TABLES" );
}
/**
 * update data
 *
 * @param string $table        	
 * @return UpdateSQL
 */
function dbupdate($table) {
	return new UpdateSQL ( $table );
}
/**
 * delete data from table(s)
 *
 * @return DeleteSQL
 */
function dbdelete() {
	return new DeleteSQL ( func_get_args () );
}
/**
 * execute a ddl SQL.
 *
 * @param string $sql        	
 * @param mixed $name        	
 * @return mixed
 */
function dbexec($sql, $name = null) {
	$dialect = DatabaseDialect::getDialect ( $name );
	if (is_null ( $dialect )) {
		return false;
	}
	try {
		$dialect->exec ( $sql );
	} catch ( Exception $e ) {
		DatabaseDialect::$lastErrorMassge = $e->getMessage ();
		return false;
	}
	return true;
}
function dbquery($sql, $name = null) {
	$dialect = DatabaseDialect::getDialect ( $name );
	if (is_null ( $dialect )) {
		return null;
	}
	try {
		$options [PDO::ATTR_CURSOR] = PDO::CURSOR_SCROLL;
		$statement = $dialect->prepare ( $sql, $options );
		$rst = $statement->execute ();
		if ($rst) {
			$result = $statement->fetchAll ( PDO::FETCH_ASSOC );
			return $result;
		}
	} catch ( Exception $e ) {
		DatabaseDialect::$lastErrorMassge = $e->getMessage ();
	}
	return null;
}
/**
 * execute sqls.
 *
 * @return bool
 */
function execSQL() {
	$rst = false;
	$args = func_get_args ();
	if ($args) {
		foreach ( $args as $sql ) {
			if (is_subclass_of2 ( $sql, 'QueryBuilder' )) {
				$rst = $sql->exec ();
				if ($rst === false) {
					DatabaseDialect::$lastErrorMassge = $sql->error ();
					break;
				}
			}
		}
	}
	return $rst;
}
/**
 * short call for creating a ImmutableValue instance
 *
 * @param string $val        	
 * @return ImmutableValue
 */
function imv($val, $alias = null) {
	return new ImmutableValue ( $val, $alias );
}
/**
 * 同步保存一个主键对应的多个数据.
 *
 * @param string $table        	
 * @param array $values        	
 * @param string $id        	
 * @param string $vkey        	
 * @param string $pkey        	
 */
function db_multi_save($table, $values, $id, $vkey = 'value', $pkey = 'id') {
	$pkeys = explode ( ',', $pkey );
	$pkey = array_shift ( $pkeys );
	if ($values) {
		$datas = array ();
		$cfgs = dbselect ( '*' )->from ( $table )->where ( array ($pkey => $id ) )->toArray ( null, $vkey );
		foreach ( $values as $value ) {
			if (! is_array ( $value )) {
				$value = array ($vkey => $value );
			}
			$where [$pkey] = $id;
			if ($pkeys) {
				foreach ( $pkeys as $pk ) {
					$where [$pk] = $value [$pk];
				}
			}
			$val = $value [$vkey];
			if (isset ( $cfgs [$val] )) {
				$vals = $cfgs [$val];
				$update = false;
				foreach ( $value as $k => $v ) {
					if ($vals [$k] != $v) {
						$update = true;
						break;
					}
				}
				if ($update) {
					$value [$pkey] = $id;
					$rst = dbupdate ( $table )->set ( $value )->where ( $where )->exec ();
				} else {
					unset ( $cfgs [$val] );
				}
			} else {
				$value [$pkey] = $id;
				$datas [] = $value;
			}
		}
		if ($datas) {
			$rst = dbinsert ( $datas, true )->into ( $table )->exec ();
		}
		if ($cfgs) {
			dbdelete ()->from ( $table )->where ( array ($pkey => $id,"$vkey IN" => array_keys ( $cfgs ) ) )->exec ();
		}
	} else {
		dbdelete ()->from ( $table )->where ( array ($pkey => $id ) )->exec ();
	}
}
//end of phpcrud.php