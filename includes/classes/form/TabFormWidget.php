<?php
class TabFormWidget implements IFieldWidget {
	private $id;
	private $widgets = array ();
	public function __construct($id) {
		$this->id = $id;
	}
	public function addWidget($widget) {
		$this->widgets [] = $widget;
	}
	public function getName() {
		return '标签';
	}
	public function getType() {
		return 'tabCol';
	}
	public function getDataProvidor($options) {
		return new EmptyDataProvidor ();
	}
	public function render($definition, $cls = '') {
		$chunks [] = '<ul class="nav nav-tabs bordered" id="' . $this->id . '" style="margin-top:10px">';
		$contents [] = '<div id="' . $this->id . 'Content" class="tab-content padding-10" style="margin-bottom:10px">';
		if (isset ( $this->widgets [0] ['sort'] )) {
			usort ( $this->widgets, function ($a, $b) {
				if (isset ( $a ['sort'] )) {
					if (isset ( $b ['sort'] )) {
						if ($a ['sort'] < $b ['sort']) {
							return - 1;
						} else if ($a ['sort'] == $b ['sort']) {
							return 0;
						}
						return 1;
					}
					return 1;
				}
				if (isset ( $b ['sort'] )) {
					return - 1;
				}
				return 0;
			} );
		}
		
		foreach ( $this->widgets as $idx => $widget ) {
			$chunks [] = '<li';
			$contents [] = '<div class="tab-pane fade';
			$tabid = $this->id . '_tab' . $idx;
			if ($idx == 0) {
				$chunks [] = ' class="active"';
				$contents [] = ' in active';
			}
			$chunks [] = '><a href="#' . $tabid . '" data-toggle="tab">' . $widget ['label'] . '</a></li>';
			
			$contents [] = '" id="' . $tabid . '">';
			if (isset ( $widget ['col'] ) && $widget ['col']) {
				$contents [] = '<div class="row">';
			}
			unset ( $widget ['label'] );
			$contents [] = $definition->renderWidget ( $widget, $widget ['col'] );
			if (isset ( $widget ['col'] ) && $widget ['col']) {
				$contents [] = '</div>';
			}
			$contents [] = '</div>';
		}
		
		$chunks [] = '</ul>';
		$contents [] = '</div>';
		return implode ( '', $chunks ) . implode ( '', $contents );
	}
}