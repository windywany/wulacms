<?php
namespace cms\classes;
/**
 * 抽象数据提供器.
 * User: Leo Ning
 * Date: 9/8/16
 * Time: 20:17
 */
abstract class CtsDataProvider {
	protected $fields   = [];
	protected $fieldmap = ['id' => 'id', 'name' => 'title'];
	private $con = [];
	public function __construct() {
		$this->fields ['limit'] = array('name' => 'limit', 'widget' => 'text', 'label' => '获取多少条数据', 'note' => '格式为:start,limit[如:0,15]', 'default' => '10');
		$this->fields ['pp']    = array('name' => 'pp', 'widget' => 'radio', 'label' => '启用分页', 'note' => '只有在列表页才需要启用分页', 'default' => 'off', 'defaults' => "on=是\noff=否");
	}
	/**
	 * 获取数据.
	 *
	 * @param array $con     条件.
	 * @param array $tplvars 模板变量.
	 *
	 * @return \CtsData 数据.
	 */
	public function getList($con,$tplvars){
		$this->con = array_merge($tplvars,$con);
		return $this->getData();
	}
	/**
	 * 获取数据.
	 *
	 * @return \CtsData 数据.
	 */
	protected abstract function getData();

	/**
	 * 定义变量名.
	 * @return string 变量名.
	 */
	public abstract function getVarName();
	/**
	 * 条件字段定义.
	 * @return array 条件字段定义数组.
	 */
	public function getConditions() {
		return $this->fields;
	}

	public function getFieldmap() {
		return $this->fieldmap;
	}
	protected function get($name, $conditions, $default = '') {
		if (isset ( $conditions [$name] )) {
			return $conditions [$name];
		}
		return $default;
	}
}