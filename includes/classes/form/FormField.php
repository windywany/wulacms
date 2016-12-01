<?php

/**
 * Form Field
 * @author Leo Ning
 *
 */
class FormField {
	protected $id;
	protected $name;
	protected $form;
	protected $rules         = array();
	protected $bind;
	protected $type          = 'string';
	protected $label         = '';
	protected $widget        = 'text';
	protected $value;
	protected $init_value;
	protected $required;
	protected $validates;
	protected $default_value = '';
	protected $defaults      = '';
	protected $value_set     = false;
	protected $scopeClz      = null;
	protected $filter        = null;
	protected $skip          = false;
	protected $note          = '';
	protected $options;
	protected $validator;

	/**
	 * 构建一个表单字段.
	 *
	 * @param string       $name
	 * @param AbstractForm $form
	 * @param array        $options
	 *            array('label'=>'label',
	 *            'id'=>'id',
	 *            'widget'=>'widget',
	 *            'field'=>'fieldname',
	 *            'init'=>'aaa|@func',
	 *            'bind'=>'func|@func',
	 *            'filter'=>'aaa|@func',
	 *            'rules' => array(
	 *            'required(!name)'=>'errormsg',
	 *            'maxlength(10)',
	 *            'minlength(1)',
	 *            'range(1,5)',
	 *            'min(1)',
	 *            'max(10)',
	 *            'email',
	 *            'url',
	 *            'skip',
	 *            'callback(@aaa)'));
	 */
	public function __construct($name, $form, $options, $value_set = false) {
		$this->name       = $name;
		$this->form       = $form;
		$this->label      = $name;
		$this->value_set  = $value_set;
		$this->validator  = $this->form->getValidator();
		$this->init_value = $form->getInitData($this->name);
		$this->options    = array();
		if (isset ($options ['id'])) {
			$this->id = $options ['id'];
		} else {
			$this->id       = $this->name;
			$options ['id'] = $this->name;
		}
		$this->setOptions($options);
	}

	/**
	 * 取这个字段的值.
	 *
	 * @return Ambigous <number, mixed, array, string, boolean>
	 */
	public function getValue($skip = true) {
		if ($skip && $this->skip) {
			return null;
		}
		if (!$this->value_set || $this->value == null) {
			$this->value = rqst($this->name, $this->default_value, true);
		} else {
			return $this->value;
		}
		if (method_exists($this->form, 'get' . ucfirst($this->name) . 'Value')) {
			$this->value = call_user_func_array(array($this->form, 'get' . ucfirst($this->name) . 'Value'), array($this->value));
		} else if (is_callable($this->filter)) {
			$this->value = call_user_func_array($this->filter, array($this->form, $this->name, $this->value));
		}
		switch ($this->type) {
			case 'bool' :
				$this->value = ($this->value === 'on' || !empty ($this->value)) ? 1 : 0;
				break;
			case 'int' :
				$this->value = intval($this->value);
				break;
			case 'float' :
				$this->value = floatval($this->value);
				break;
			case 'date':
				if (empty($this->value)) {
					$this->value = null;
				}
				break;
			case 'timestamp':
				if (empty($this->value)) {
					$this->value = 0;
				} else {
					$this->value = @strtotime($this->value . ' 00:00:00');
				}
				break;
			case 'array' :
				if (is_array($this->value)) {
					$this->value = implode(',', $this->value);
				}
				break;
			case 'json':
				if (is_array($this->value)) {
					$this->value = @json_encode($this->value);
				}
				break;
			default :
				break;
		}
		$this->value_set = true;

		return $this->value;
	}

	public function geDefaultValue() {
		return $this->default_value;
	}

	public function formatValue($value) {
		$func = 'format' . ucfirst($this->name) . 'Value';
		if ($this->form instanceof DynamicForm) {
			if ($this->form->hasCallback($func)) {
				return call_user_func_array(array($this->form, $func), array($value));
			}
		} else if (method_exists($this->form, $func)) {
			return call_user_func_array(array($this->form, $func), array($value));
		}

		return $value;
	}

	/**
	 * 设置定义参数.
	 *
	 * @param array $options
	 */
	public function setOptions($options) {
		if (!is_array($options)) {
			$options = array();
		}
		if (isset ($options ['rules']) && is_array($options ['rules'])) {
			$this->validates = $options ['rules'];
		}
		if (isset ($options ['type'])) {
			$this->type = $options ['type'];
		}
		if (isset ($options ['bind'])) {
			$this->bind = $options ['bind'];
		}
		if (isset ($options ['default'])) {
			$this->default_value = $options ['default'];
		}
		if (isset ($options ['defaults'])) {
			$this->defaults = $options ['defaults'];
		}
		if (isset ($options ['scope'])) {
			$this->scopeClz = $options ['scope'];
		}
		if (isset ($options ['filter'])) {
			$this->filter = $options ['filter'];
		}
		if (isset ($options ['value'])) {
			$this->value = $options ['value'];
		}
		if (isset ($options ['widget'])) {
			$this->widget = $options ['widget'];
		}
		if (isset ($options ['label'])) {
			$this->label = $options ['label'];
		}
		if (isset ($options ['note'])) {
			$this->note = $options ['note'];
		}
		if (isset ($options ['skip'])) {
			$this->skip = $options ['skip'];
		}
		if (isset ($options ['id'])) {
			$this->id = $options ['id'];
		}
		$this->options = array_merge($this->options, $options);
	}

	/**
	 * 取这个字段对应的输入组件.
	 *
	 * @return string
	 */
	public function getWidget() {
		return $this->widget;
	}

	/**
	 * 取这个字段的label.
	 *
	 * @return string
	 */
	public function getLabel() {
		return $this->label;
	}

	public function getNote() {
		return $this->note;
	}

	public function isSkip() {
		return $this->skip;
	}

	public function getOptions() {
		return $this->options;
	}

	public function getId() {
		return $this->id;
	}

	/**
	 * 取这个字段的验证规则.
	 *
	 * @return array
	 */
	public function getValidateRule() {
		if (is_array($this->validates)) {
			foreach ($this->validates as $rule => $message) {
				if (is_numeric($rule)) {
					$rule    = $message;
					$message = '';
				}
				$this->addValidate($rule, $message);
			}
		}

		return $this->rules;
	}

	/**
	 * 取这个字段的验证scope class名.
	 *
	 * @return array
	 */
	public function getScopeClz() {
		return $this->scopeClz;
	}

	/**
	 * 为这个字段添加一个验证规则.
	 *
	 * @param string $rule
	 * @param string $message
	 */
	public function addValidate($rule, $message) {
		$exp = '';
		if (preg_match('#([a-z_][a-z_0-9]+)(\s*\((.*)\))#i', $rule, $rules)) {
			$rule = $rules [1];
			if (isset ($rules [3])) {
				$exp = $rules [3];
			}
		}
		$this->rules [ $rule ] = array('message' => $message, 'option' => $exp, 'form' => $this->form);
		if ($rule == 'required' && empty ($exp)) {
			$this->required = true;
		}
	}

	public function removeValidate($rule) {
		unset ($this->rules [ $rule ], $this->validates [ $rule ]);
		$this->required = isset ($this->rules ['required']) ? true : false;
	}

	public function getBindData($initData = null) {
		$data = array();
		if ($this->bind) {
			if ($this->bind [0] == '@') {
				$func = array($this->form, substr($this->bind, 1));
			} else {
				$func = $this->bind;
			}
			if (is_callable($func)) {
				$data = call_user_func_array($func, array($this->init_value));
			}
		} else if ($this->defaults) {
			$data = $this->defaults;
		}

		return $data;
	}

	public function isValid($valiator, $data) {
		if (empty ($this->rules)) {
			return true;
		} else {
			if ($this->scopeClz) {
				$scope = $this->scopeClz;
			} else {
				$scope = $this->form;
			}

			return $valiator->valid($this->value, $data, $this->rules, $scope);
		}
	}

	/**
	 * 验证这个字段是否有效.
	 *
	 * @param array $data
	 *
	 * @return boolean
	 */
	public function valid($data = array()) {
		if (empty ($this->rules)) {
			return true;
		} else {
			if ($this->scopeClz) {
				$scope = $this->scopeClz;
			} else {
				$scope = $this->form;
			}

			return $this->validator->valid($this->value, $data, $this->rules, $scope);
		}
	}

	public function getName() {
		return $this->name;
	}
}