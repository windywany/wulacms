<?php
/**
 * 自定义字段组件注册器.
 * @author Guangfeng
 *
 */
class CustomeFieldWidgetRegister implements IteratorAggregate {
	private $widgets = array ();
	public function __construct() {
		$this->register ( new TextFieldWidget () );
		$this->register ( new TextareaFieldWidget () );
		$this->register ( new SelectFieldWidget () );
		$this->register ( new CheckboxFieldWidget () );
		$this->register ( new RadioFieldWidget () );
		$this->register ( new PasswordFieldWidget () );
		$this->register ( new HiddenFieldWidget () );
		$this->register ( new HtmlTagWidget () );
		$this->register ( new ComboxFieldWidget () );
		fire ( 'get_custom_field_widgets', $this );
	}
	/**
	 * 注册一个组件.
	 *
	 * @param IFieldWidget $widget        	
	 */
	public function register($widget) {
		if ($widget instanceof IFieldWidget) {
			$type = $widget->getType ();
			$this->widgets [$type] = $widget;
		}
	}
	/**
	 * 从注册器取类型为$type的组件.
	 *
	 * @param string $type
	 *        	类型.
	 * @return IFieldWidget 组件.
	 */
	public function getWidget($type) {
		if (isset ( $this->widgets [$type] )) {
			$widget = clone $this->widgets [$type];
			return $widget;
		}
		return null;
	}
	/**
	 * 取类型为$type的组件名称.
	 *
	 * @param string $type
	 *        	类型.
	 * @return string 名称.
	 */
	public function getWidgetName($type) {
		$widget = $this->getWidget ( $type );
		if ($widget) {
			return $widget->getName ();
		} else {
			return '';
		}
	}
	/**
	 * 初始化输入字段
	 *
	 * @param array $widgets
	 *        	array ('id'=>'id','name' => 'name','widget' => 'text','label' => '','note' => '','defaults'=>'','default'=>'' );
	 */
	public static function initWidgets($fields, $data = array()) {
		$widgets = array ();
		if ($fields) {
			$widgetsRegister = new CustomeFieldWidgetRegister ();
			foreach ( $fields as $key => $field ) {
				$type = empty ( $field ['widget'] ) ? 'text' : $field ['widget'];
				$name = $field ['name'];
				$clz = $widgetsRegister->getWidget ( $type );
				if ($clz) {
					$field ['widget'] = $clz;
					$field ['value'] = isset ( $data [$name] ) ? $data [$name] : (isset ( $field ['default'] ) || is_numeric ( $field ['default'] ) ? $field ['default'] : '');
					$widgets [$key] = $field;
				}
			}
		}
		return $widgets;
	}
	/*
	 * (non-PHPdoc) @see IteratorAggregate::getIterator()
	 */
	public function getIterator() {
		return new ArrayIterator ( $this->widgets );
	}
}