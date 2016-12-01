<?php

namespace db\model;

/**
 * 数据模块基类.
 *
 * @author leo
 * @property-read $table 表名.
 */
abstract class Model {
	private   $tableName;
	public    $table         = null;
	protected $dialect       = null;
	protected $errors        = null;
	protected $primaryKeys   = ['id'];
	protected $autoIncrement = true;
	protected $hasMany       = [];
	protected $oneOne        = [];
	protected $lastSQL       = null;
	protected $lastValues    = null;
	protected $alias         = null;
	protected $rules;

	/**
	 * 创建模型实例.
	 *
	 * @param \DatabaseDialect $dialect
	 *            数据库实例.
	 */
	public function __construct($dialect = null) {
		$this->config();
		$tb          = explode("\\", get_class($this));
		$this->alias = str_replace('Model', '', array_pop($tb));
		if (!$this->table) {
			$tb          = lcfirst($this->alias);
			$this->table = preg_replace_callback('#[A-Z]#', function ($r) {
				return '_' . strtolower($r [0]);
			}, $tb);
		}
		$this->table = '{' . $this->table . '}';
		if ($dialect instanceof \DatabaseDialect) {
			$this->dialect = $dialect;
		} else {
			$this->dialect = \DatabaseDialect::getDialect($dialect);
		}
		$this->tableName = $this->dialect->getTableName($this->table);
	}

	/**
	 * 配置此模型.
	 */
	protected function config() {
	}

	/**
	 * 获取校验规则.
	 *
	 * @return array 检验规则.
	 */
	public function getValidateRules() {
		return $this->rules;
	}

	/**
	 * 设置校验规则.
	 *
	 * @param string       $field
	 *            字段.
	 * @param string|array $rule
	 *            规则.
	 * @param string       $msg
	 *            消息.
	 */
	public function setValidateRule($field, $rule, $msg = null) {
		if (is_array($rule)) {
			// 条件已经解析.
			foreach ($rule as $r => $m) {
				$this->rules [ $field ] [ $r ] = $m;
			}
		} else {
			$this->rules [ $field ] [ $rule ] = $msg;
		}
	}

	/**
	 * 移除校验规则.
	 *
	 * @param string $field
	 *            字段
	 * @param string $rule
	 *            规则，当前规则为null时，删除所有规则.
	 */
	public function removeValidateRule($field, $rule = null) {
		if (!isset($this->rules[ $field ])) {
			return;
		}
		if ($rule) {
			unset ($this->rules [ $field ] [ $rule ]);
		} else {
			unset ($this->rules [ $field ]);
		}
	}

	/**
	 * 取一条记录.
	 *
	 * @param        int :array $id
	 * @param string $fields
	 *                   字段,默认为*.
	 *
	 * @return array 记录.
	 */
	public function get($id, $fields = '*') {
		if (is_array($id)) {
			$where = $id;
		} else {
			$idf   = empty($this->primaryKeys) ? 'id' : $this->primaryKeys[0];
			$where = [$idf => $id];
		}
		$sql = dbselect($fields)->setDialect($this->dialect)->from($this->table)->where($where)->limit(0, 1);

		$rst = $sql->get();
		$this->checkSQL($sql);

		return $rst;
	}

	/**
	 * @param string      $field
	 * @param array|mixed $id
	 * @param null        $default
	 *
	 * @return string
	 */
	public function getField($field, $id, $default = null) {
		if (is_array($id)) {
			$where = $id;
		} else {
			$idf   = empty($this->primaryKeys) ? 'id' : $this->primaryKeys[0];
			$where = [$idf => $id];
		}
		$sql = dbselect()->setDialect($this->dialect)->from($this->table)->where($where)->limit(0, 1);

		$rst = $sql->get($field);
		$this->checkSQL($sql);
		if ($rst == null) {
			return $default;
		} else {
			return $rst;
		}
	}

	/**
	 * 获取key/value数组.
	 *
	 * @param array  $where      条件.
	 * @param string $valueField value字段.
	 * @param string $keyField   key字段.
	 * @param array  $rows       初始数组.
	 *
	 * @return array 读取后的数组.
	 */
	public function getArray($where, $valueField, $keyField = null, $rows = []) {
		$sql = $this->select([$valueField, $keyField])->where($where);
		$rst = $sql->toArray($valueField, $keyField, $rows);
		$this->checkSQL($sql);

		return $rst;
	}

	/**
	 * 记数
	 *
	 * @param array  $con
	 *            条件.
	 * @param string $id
	 *            字段.
	 *
	 * @return int 记数.
	 */
	public function count($con, $id = null) {
		$sql = $this->select()->where($con);
		if ($id) {
			return $sql->count($id);
		} else if (count($this->primaryKeys) == 1) {
			return $sql->count($this->primaryKeys [0]);
		} else {
			return $sql->count('*');
		}
	}

	/**
	 * 是否存在满足条件的记录.
	 *
	 * @param array  $con
	 *            条件.
	 * @param string $id
	 *            字段.
	 *
	 * @return boolean 有记数返回true,反之返回false.
	 */
	public function exist($con, $id = null) {
		return $this->count($con, $id) > 0;
	}

	/**
	 * 查询.
	 *
	 * @param string $fields 字段.
	 * @param string $alias  SQL别名.
	 *                       字段.
	 *
	 * @return \Query 查询SQL实例.
	 */
	public function select($fields = '*', $alias = null) {
		$alias = $alias ? $alias : $this->alias;
		$sql   = dbselect($fields)->from($this->table . ' AS ' . $alias)->setDialect($this->dialect);

		return $sql;
	}

	/**
	 * 删除记录.
	 *
	 * @param array $con
	 *            条件.
	 *
	 * @return boolean 成功true，失败false.
	 */
	public function delete($con) {
		$rst = false;
		if (is_int($con)) {
			$con[ $this->primaryKeys[0] ] = $con;
		}
		if ($con) {
			$sql = dbdelete()->from($this->table)->setDialect($this->dialect)->where($con);
			$rst = $sql->exec();
			$this->checkSQL($sql);
		}

		return $rst;
	}

	/**
	 * 回收内容，适用于软删除(将deleted置为1).
	 *
	 * @param array    $con
	 *            条件.
	 * @param int      $uid
	 *            如果大于0，则表中必须包括update_time和update_uid字段.
	 * @param \Closure $cb
	 *            回调.
	 *
	 * @return boolean 成功true，失败false.
	 */
	public function recycle($con, $uid = 0, $cb = null) {
		if (!$con) {
			return false;
		}
		$data ['deleted'] = 1;
		if ($uid) {
			$data ['update_time'] = time();
			$data ['update_uid']  = $uid;
		}
		$rst = $this->update($data, $con);
		if ($rst && $cb instanceof \Closure) {
			$cb ($con, $this);
		}

		return $rst;
	}

	/**
	 * 更新数据.
	 *
	 * @param array|\AbstractForm $data
	 *            数据.
	 * @param array               $con
	 *            更新条件.
	 * @param \Closure            $cb
	 *            数据处理器.
	 *
	 * @return bool 成功true，失败false.
	 */
	public function update($data, $con = null, $cb = null) {
		if ($data instanceof \AbstractForm) {
			$d = $data->valid();
			if (!$d) {
				$this->errors = $data->getErrors();

				return false;
			}
			$data = $d;
		}
		if ($con && !is_array($con)) {
			$con = [$this->primaryKeys[0] => $con];
		}
		if (!$con) {
			$con = $this->getWhere($data);
			if (count($con) != count($this->primaryKeys)) {
				$this->errors = '未提供更新条件';

				return false;
			}
		}
		if (empty ($con)) {
			$this->errors = '更新条件为空';

			return false;
		}
		if ($cb && $cb instanceof \Closure) {
			$data = $cb ($data, $con, $this);
		}
		if ($this->rules) {
			$rst = $this->validate1($data, $this->rules);
			if (!$rst) {
				return false;
			}
		}
		if ($data) {
			$sql = dbupdate($this->table)->setDialect($this->dialect)->set($data)->where($con);
			$rst = $sql->exec();
			$this->checkSQL($sql);

			return $rst;
		} else {
			return false;
		}
	}

	/**
	 * 创建记录.
	 *
	 * @param array|\AbstractForm $data
	 *            数据或表单.
	 * @param \Closure            $cb
	 *            数据处理函数.
	 *
	 * @return bool|int 成功返回true或主键值,失败返回false.
	 */
	public function create($data, $cb = null) {
		if ($data instanceof \AbstractForm) {
			$d = $data->valid();
			if (!$d) {
				$this->errors = $data->getErrors();

				return false;
			}
			$data = $d;
		}
		if ($cb && $cb instanceof \Closure) {
			$data = $cb ($data, $this);
		}
		if ($this->rules) {
			$rst = $this->validate($data, $this->rules);
			if (!$rst) {
				return false;
			}
		}
		if ($data) {
			if ($this->autoIncrement) {
				$sql = dbinsert($data)->setDialect($this->dialect)->into($this->table);
				$rst = $sql->exec();
				if ($rst && $rst [0]) {
					$rst = $rst [0];
				}
			} else {
				$sql = dbinsert($data)->setDialect($this->dialect)->into($this->table);
				$rst = $sql->exec(true);
			}
			if ($rst) {
				return $rst;
			} else {
				$this->checkSQL($sql);

				return false;
			}
		} else {
			$this->errors = '数据为空.';

			return false;
		}
	}

	/**
	 * 新增或修改数据.
	 *
	 * @param array $data  数据.
	 * @param array $where 更新条件.
	 *
	 * @return bool 结果.
	 */
	public function save($data, $where) {
		$sql = dbsave($data, $where, $this->primaryKeys[0]);
		$rst = $sql->setDialect($this->dialect)->into($this->table)->exec();
		$this->checkSQL($sql);

		return $rst;
	}

	/**
	 * 获取错误信息.
	 *
	 * @return string|array 错误信息.
	 */
	public function getErrors() {
		return $this->errors;
	}

	/**
	 * 出错的SQL语句.
	 *
	 * @return string
	 */
	public function lastSQL() {
		return $this->lastSQL;
	}

	/**
	 * 出错SQL值.
	 *
	 * @return mixed 出错的SQL变量值.
	 */
	public function lastSQLValues() {
		return $this->lastValues;
	}

	/**
	 * 获取表名
	 *
	 * @return string
	 */
	public function getName() {
		return $this->tableName;
	}

	/**
	 * 从数据中根据主键获取条件.
	 *
	 * @param array $data
	 *            数据.
	 *
	 * @return array 条件.
	 */
	protected function getWhere($data) {
		$con = [];
		foreach ($this->primaryKeys as $f) {
			if (isset ($data [ $f ])) {
				$con [ $f ] = $data [ $f ];
			}
		}

		return $con;
	}

	/**
	 * 检测SQL执行.
	 *
	 * @param \QueryBuilder $sql
	 */
	protected function checkSQL($sql) {
		if ($sql instanceof \QueryBuilder) {
			$this->errors     = $sql->lastError();
			$this->lastSQL    = $sql->lastSQL();
			$this->lastValues = $sql->lastValues();
		}
	}

	/**
	 * 验证数据的有效性,以规则为主.
	 *
	 * @param array $data
	 *            要验证的数据.
	 * @param array $rules
	 *            验证规则.
	 *
	 * @return boolean 数据合格返回true.
	 */
	protected function validate($data, $rules) {
		$v  = new \FormValidator ($this);
		$vs = [];
		foreach ($rules as $f => $r) {
			$rs  = $this->prepareValidateRules($r);
			$rst = $v->valid(isset ($data [ $f ]) ? $data [ $f ] : '', $data, $rs, $this);
			if ($rst !== true) {
				$vs [ $f ] = $rst;
			}
		}
		if (empty ($vs)) {
			return true;
		}
		$this->errors = $vs;

		return false;
	}

	/**
	 * 以数据为主进行校验.
	 *
	 * @param array $data
	 *            要验证的数据.
	 * @param array $rules
	 *            验证规则.
	 *
	 * @return boolean 数据合格返回true.
	 */
	protected function validate1($data, $rules) {
		$v  = new \FormValidator ($this);
		$vs = [];
		foreach ($data as $key => $val) {
			if (isset ($rules [ $key ])) {
				$r   = $rules [ $key ];
				$rs  = $this->prepareValidateRules($r);
				$rst = $v->valid($val, $data, $rs, $this);
				if ($rst !== true) {
					$vs [ $key ] = $rst;
				}
			}
		}
		if (empty ($vs)) {
			return true;
		}
		$this->errors = $vs;

		return false;
	}

	/**
	 * 格式化验证规则.
	 *
	 * @param array $validates
	 *
	 * @return array 验证规则.
	 */
	private function prepareValidateRules($validates) {
		$rules = [];
		foreach ($validates as $rule => $message) {
			if (is_string($message)) {
				$exp = '';
				if (preg_match('#([a-z_][a-z_0-9]+)(\s*\((.*)\))#i', $rule, $rs)) {
					$rule = $rs [1];
					if (isset ($rs [3])) {
						$exp = $rs [3];
					}
				}
				$rules [ $rule ] = array('message' => $message, 'option' => $exp, 'form' => $this);
			} else {
				$rules [ $rule ] = $message;
			}
		}

		return $rules;
	}
}