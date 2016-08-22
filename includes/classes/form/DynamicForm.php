<?php
/**
 * 动态表单.
 * @author Guangfeng
 *
 */
class DynamicForm extends AbstractForm implements ArrayAccess {
	protected $formName = '';
	protected $fillData = false;
	protected $callbacks = array ();
	/**
	 * 初始化一个动态表单.
	 *
	 * @param string $name
	 *        	动态表单名.
	 * @param array $data
	 *        	初始化数据.
	 * @param boolean $fillData
	 *        	是否需要插件初始化数据.
	 */
	public function __construct($name = null, $data = array(), $fillData = false) {
		$this->formName = $name;
		$this->__form_init_data = $data;
		$this->fillData = $fillData;
		$this->__form_validator = new FormValidator ( $this );
		if ($name) {
			// 使用插件机制添加字段
			fire ( 'on_init_dynamicform_' . $name, $this );
		}
	}
	/**
	 * 是否需要初始化程序填补数据.
	 *
	 * @return boolean
	 */
	public function needFill() {
		return $this->fillData;
	}
	/*
	 * (non-PHPdoc) @see AbstractForm::getName()
	 */
	public function getName() {
		return $this->formName;
	}
	/**
	 * 注册一个验证回调函数.
	 *
	 * @param string $name        	
	 * @param mixed $callback
	 *        	可被call_user_func_array使用的可调用函数
	 */
	public function registerCallback($name, $callback) {
		if (is_callable ( $callback )) {
			$this->callbacks [$name] = $callback;
		}
	}
	public function hasCallback($name) {
		return isset ( $this->callbacks [$name] );
	}
	public function __call($name, $args) {
		if (isset ( $this->callbacks [$name] )) {
			return call_user_func_array ( $this->callbacks [$name], $args );
		}
		return false;
	}
}