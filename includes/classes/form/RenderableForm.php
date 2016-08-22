<?php
/**
 * 可绘制的表单.
 * @author ngf
 *
 */
abstract class RenderableForm extends AbstractForm implements Renderable {
	protected $__form_params;
	public function __construct($id) {
		parent::__construct ();
		$this->__form_params = new NamedArray ();
		$this->__form_params->id ( $id );
	}
	/**
	 * 设置参数.
	 *
	 * @param NamedArray $args        	
	 */
	public function params($prefix='') {
		return $this->__form_params->prefix($prefix);
	}
	
	/**
	 * 取当前表单的绘制器.
	 *
	 * @return DefaultFormRender
	 */
	public function getRender() {
		$values = $this->getInitData ();
		$render = new DefaultFormRender ( $this->buildWidgets ( $values ) );
		return $render;
	}
}