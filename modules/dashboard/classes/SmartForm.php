<?php
class SmartForm extends RenderableForm {
	protected $__form_footer;
	public function __construct($id='', $action = '.', $method = 'POST') {
		parent::__construct ( $id );
		$this->__form_params->name ( get_class ( $this ) )->action ( $action );
		if ($method) {
			$this->__form_params->method ( $method );
		}
	}
	public function render() {
		$args = $this->__form_params->toArray ();
		$args ['class'] = 'smart-form ' . $args ['class'];
		if (! isset ( $args ['method'] )) {
			$args ['method'] = 'POST';
		}
		$fieldset = dashboard_htmltag ( 'fieldset' );
		$form = dashboard_htmltag ( 'form', $args );
		$form->child ( $fieldset );
		$fieldset->child ( $this->getRender () );
		if ($this->__form_footer) {
			$form->child ( $this->__form_footer );
		}
		$rules = $this->rules ();
		$html = $form->render ();
		if ($rules) {
			$html .= '<script type="text/javascript">nUI.validateRules[\'' . $args ['name'] . '\'] = ' . $rules . '</script>';
		}
		return $html;
	}
	public function footer($tag) {
		if (! $this->__form_footer) {
			$this->__form_footer = dashboard_htmltag ( 'footer' );
		}
		$this->__form_footer->child ( $tag );
	}
}