<?php
class SmartSearchForm extends RenderableForm {
	protected $__form_for_table;
	public function __construct($for, $data = array()) {
		parent::__construct ( $data );
		$this->__form_for_table = $for;
	}
	public function render() {
		$html [] = '<div class="panel-body no-padding"><form data-widget="nuiSearchForm" data-for="' . $this->__form_for_table . '" class="smart-form"><fieldset>';
		$render = $this->getRender ();
		$html [] = $render->render ();
		$html [] = '</fieldset></form></div>';
		return implode ( '', $html );
	}
	public static function btn($text = '搜索', $icon = 'fa-search', $type = 'submit', $theme = 'btn-primary') {
		$btn = dashboard_htmltag ( 'button' )->type ( $type )->cls ( 'btn btn-sm ' . $theme );
		$i = dashboard_htmltag ( 'i' )->cls ( 'fa ' . $icon );
		$btn->child ( $i );
		$btn->text ( $text, true );
		return $btn;
	}
}