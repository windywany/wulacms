<?php
class SmartTab implements Renderable {
	private $id;
	private $pos;
	private $tabs = array ();
	public function __construct($id, $pos = 'right') {
		$this->id = $id;
		$this->pos = $pos;
	}
	public function add($title, $icon = '', $active = false) {
		$tab = dashboard_htmltag ( 'li' );
		if ($active) {
			$tab->cls ( 'active' );
		}
		$a = dashboard_htmltag ( 'a' );
		if ($icon) {
			$a->child ( '<i class="' . $icon . '"></i>' );
		}
		$span = dashboard_htmltag ( 'span' )->text ( $title );
		$a->child ( $span, 'title' );
		$tab->child ( $a );
		$this->tabs [] = $tab;
		return $a;
	}
	public function render() {
		$html [] = '<ul id="' . $this->id . '" class="nav nav-tabs pull-' . $this->pos . '">';
		foreach ( $this->tabs as $tab ) {
			$html [] = $tab->render ();
		}
		$html [] = '</ul>';
		return implode ( '', $html );
	}
}