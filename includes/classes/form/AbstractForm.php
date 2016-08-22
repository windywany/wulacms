<?php

/**
 * base class for forms.
 * @author Guangfeng Ning
 *
 */
abstract class AbstractForm implements ArrayAccess {
	protected $__form_fields = array ();
	protected $__form_data = array ();
	protected $__form_init_data = array ();
	protected $__form_valid = array ();
	protected $__form_validator = null;
	protected $__form_rules = null;
	protected $__form_callback_args = array ();
	protected $__built_widgets = false;
	public function __construct($data = array(), $init = true) {
		if ($init) {
			if (! is_array ( $data )) {
				$data = array ();
			}
			$this->__form_init_data = $data;
			$refObj = new ReflectionObject ( $this );
			$fields = $refObj->getProperties ( ReflectionProperty::IS_PRIVATE );
			$this->__form_validator = new FormValidator ( $this );
			$value_set = ! empty ( $data );
			foreach ( $fields as $field ) {
				$name = $field->getName ();
				$field->setAccessible ( true );
				$opts = $field->getValue ( $this );
				if (isset ( $data [$name] )) {
					$opts ['value'] = $data [$name];
				} else if ($value_set) {
					$opts ['value'] = null;
				}
				$this->__form_fields [$name] = new FormField ( $name, $this, $opts, $value_set && ! is_null ( $opts ['value'] ) );
				$field->setAccessible ( false );
			}
			$this->init_form_fields ( $data, $value_set );
			if ($data) {
				$this->initValidateRules ();
			}
		}
	}
	public function pack(&$data, $formData) {
		// nothing todo.
	}
	public function upack(&$data) {
		// nothing todo.
	}
	public function setCallbackArgs($func, $args) {
		$this->__form_callback_args [$func] = $args;
	}
	public function getCallbackArgs($func) {
		if (isset ( $this->__form_callback_args [$func] )) {
			return $this->__form_callback_args [$func];
		} else {
			return array ();
		}
	}
	/**
	 * get the validate url.
	 *
	 * @return string
	 */
	public static function getValidateUrl() {
		static $url = false;
		if (! $url) {
			$url = tourl ( 'system/validateform' );
		}
		return $url;
	}
	/**
	 * 准备可输出的组件列表.
	 *
	 * @param array $widgets
	 * @return array
	 */
	public static function prepareWidgets($widgets) {
		$widgets_groups = array ();
		foreach ( $widgets as $w ) {
			if(isset($w['tab_acc']) && preg_match('#^(tab|acc):(.+)$#', $w['tab_acc'],$m)){
				$type = $m[1];
				$g = $type.'_'.$m[2];
				if($type == 'tab'){
					if (! isset ( $widgets_groups [$g] )) {
						$widgets_groups [$g] = new TabFormWidget($g);
					}
					$widgets_groups [$g]->addWidget($w);
				}else{
					if (! isset ( $widgets_groups [$g] )) {
						$widgets_groups [$g] = new AccFormWidget($g);
					}
					$widgets_groups [$g]->addWidget($w);
				}
			}else if (isset ( $w ['group'] ) && !empty($w['group'])) {
				$g = 'g_' . $w ['group'];
				if (! isset ( $widgets_groups [$g] )) {
					$widgets_groups [$g] = new GroupColWidget ();
				}
				$widgets_groups [$g]->addWidget ( $w );
			} else {
				$widgets_groups [] = $w;
			}
		}
		return $widgets_groups;
	}
	public static function seperator($name) {
		return array ('skip' => true,'widget' => 'htmltag','defaults' => '<section class="timeline-seperator"><span>' . $name . '</span></section>' );
	}
	/**
	 * get the Filed
	 *
	 * @param string $name
	 * @return FormField
	 */
	public function getField($name) {
		if (isset ( $this->__form_fields [$name] )) {
			return $this->__form_fields [$name];
		}
		return null;
	}
	/**
	 * 添加一个字段.
	 *
	 * @param string $name
	 * @param array $field
	 */
	public function addField($name, $field) {
		$this->offsetSet ( $name, $field );
	}
	/**
	 * 获取当前表单的验证器.
	 *
	 * @return FormValidator
	 */
	public function getValidator() {
		return $this->__form_validator;
	}
	/**
	 * 构建这个表单的所有字段.
	 *
	 * @param array $data
	 * @return array
	 */
	public function buildWidgets($data = array()) {
		$widgets = $this->__built_widgets;
		if (! $widgets) {
			$definitions = array ();
			foreach ( $this->__form_fields as $name => $field ) {
				$definition = $field->getOptions ();
				if (isset ( $definition ['norender'] )) {
					continue;
				}
				$definition ['id'] = $field->getId ();
				$definition ['name'] = $name;
				$definition ['defaults'] = $field->getBindData ();
				$definition ['default'] = $field->geDefaultValue ();
				if (isset ( $data [$definition ['name']] )) {
					$definition ['value'] = $data [$definition ['name']] = $field->formatValue ( $data [$definition ['name']] );
				}
				$definitions [] = $definition;
			}
			if ($definitions) {
				$widgets = CustomeFieldWidgetRegister::initWidgets ( $definitions, $data );
				$widgets = AbstractForm::prepareWidgets ( $widgets );
			}
		}
		return $widgets;
	}
	/**
	 * 删除一个验证规则.
	 *
	 * @param string $name
	 *        	字段名.
	 * @param string $rule
	 *        	验证规则.
	 */
	public function removeRlue($name, $rule) {
		$field = $this->getField ( $name );
		if ($field) {
			$field->removeValidate ( $rule );
		}
	}
	public function initValidateRules($reinit = false) {
		if ($this->__form_rules == null || $reinit) {
			$vrules = array ();
			$messages = array ();
			foreach ( $this->__form_fields as $key => $field ) {
				$rule = $field->getValidateRule ();
				$scope = $field->getScopeClz ();
				list ( $r, $m ) = $this->__form_validator->getRuleClass ( $rule, $this->__form_init_data, $key, $scope );
				if ($r) {
					$vrules [$key] = $r;
					$messages [$key] = $m;
				}
			}
			$this->__form_rules = array ('rules' => $vrules,'messages' => $messages );
		}
		return $this->__form_rules;
	}
	/**
	 * 获取表单验证规则.
	 *
	 * @param boolean $reinit
	 *        	是否重新生成验证规则.
	 * @param AbstractForm $form
	 *        	..
	 *        	要合并的表单.
	 * @return string
	 */
	public function rules() {
		$reinit = false;
		$rules = array ();
		$args = func_get_args ();
		array_push ( $args, $this );
		foreach ( $args as $form ) {
			if ($form instanceof AbstractForm) {
				$rules1 = $form->initValidateRules ( $reinit );
				if ($rules1) {
					$rules = array_merge_recursive ( $rules, $rules1 );
				}
			} else if (is_bool ( $form )) {
				$reinit = $form;
			}
		}
		return json_encode ( $rules );
	}
	public function valid($reinit = false) {
		$this->initValidateRules ( $reinit );
		$data = $this->toArray ( false );
		$this->__form_valid = array ();
		foreach ( $this->__form_fields as $key => $field ) {
			$rst = $field->isValid ( $this->__form_validator, $data );
			if (true !== $rst) {
				$this->__form_valid [$key] = $rst;
			} else if ($field->isSkip ()) {
				unset ( $data [$key] );
			}
		}
		return empty ( $this->__form_valid ) ? $data : false;
	}
	/**
	 * 验证指定的字段.
	 *
	 * @param string $field
	 * @param array $data
	 * @return boolean
	 */
	public function validateField($field, $data = array()) {
		$field = $this->getField ( $field );
		if ($field) {
			return $field->valid ( $data );
		} else {
			return false;
		}
	}
	public function getErrors() {
		return $this->__form_valid;
	}
	public function getValue($name) {
		if (isset ( $this->__form_data [$name] )) {
			return $this->__form_data [$name];
		} else if (isset ( $this->__form_fields [$name] )) {
			$this->__form_data [$name] = $this->__form_fields [$name]->getValue ( false );
			return $this->__form_data [$name];
		} else {
			return null;
		}
	}
	
	/**
	 * 取绑定的值.
	 *
	 * @param string $name
	 * @param mixed $initData
	 */
	public function getBindData($name, $initData = null) {
		$data = array ();
		if (isset ( $this->__form_fields [$name] )) {
			$data = $this->__form_fields [$name]->getBindData ( $initData );
		}
		return $data;
	}
	public function toArray($skip = true) {
		foreach ( $this->__form_fields as $name => $field ) {
			$value = $field->getValue ( $skip );
			if (! is_null ( $value )) {
				$this->__form_data [$name] = $value;
			}
		}
		return $this->__form_data;
	}
	public function getInitData($name = null) {
		if (is_null ( $name )) {
			return $this->__form_init_data;
		}
		if (isset ( $this->__form_init_data [$name] )) {
			return $this->__form_init_data [$name];
		}
		return null;
	}
	/**
	 * 获取表单名.
	 *
	 * @return string
	 */
	public function getName() {
		return str_replace ( '\\', '.', get_class ( $this ) );
	}
	public function offsetExists($name) {
		return isset ( $this->__form_fields [$name] );
	}
	public function offsetGet($name) {
		if (isset ( $this->__form_fields [$name] )) {
			return $this->__form_fields [$name]->getValue ( false );
		} else {
			return null;
		}
	}
	public function offsetSet($offset, $value) {
		if (is_string ( $offset ) && is_array ( $value )) {
			$data = $this->__form_init_data;
			$value_set = ! empty ( $data );
			if (isset ( $data [$offset] )) {
				$value ['value'] = $data [$offset];
			} else if ($value_set) {
				$value ['value'] = null;
			}
			$this->__form_fields [$offset] = new FormField ( $offset, $this, $value, $value_set );
		}
	}
	public function offsetUnset($offset) {
		unset ( $this->__form_fields [$offset] );
		$this->initValidateRules ( true );
	}
	/**
	 * 初始化表单字段.
	 *
	 * @param array $data
	 * @param boolean $value_set
	 */
	protected function init_form_fields($data, $value_set) {
	}
	public function getScripts() {
		return array ();
	}
}