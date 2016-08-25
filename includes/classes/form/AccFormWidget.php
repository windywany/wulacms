<?php
class AccFormWidget implements IFieldWidget {
	private $id;
	private $widgets = array ();
	public function __construct($id) {
		$this->id = $id;
	}
	public function addWidget($widget) {
		$this->widgets [] = $widget;
	}
	public function getName() {
		return '折叠';
	}
	public function getType() {
		return 'accCol';
	}
	public function getDataProvidor($options) {
		return new EmptyDataProvidor ();
	}
	public function render($definition, $cls = '') {
		$chunks [] = '<div class="panel-group smart-accordion-default" id="' . $this->id . '" style="margin-top:10px;margin-bottom:10px">';
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
			$chunks [] = '<div class="panel panel-default">';
			$tabid = $this->id . '_acc' . $idx;
			$cls = ' class="collapsed"';
			$cls1 = '';
			if ($idx == 0) {
				$cls = '';
				$cls1 = 'in';
			}
			$chunks [] = '<div class="panel-heading">
				<h4 class="panel-title"><a data-toggle="collapse" data-parent="#' . $this->id . '" href="#' . $tabid . '"' . $cls . '> <i class="fa fa-lg fa-angle-down pull-right"></i> <i class="fa fa-lg fa-angle-up pull-right"></i> ' . $widget ['label'] . '</a></h4>
			</div>';
			$chunks [] = '<div id="'.$tabid.'" class="panel-collapse collapse ' . $cls1 . '"><div class="panel-body padding-10">';
			
			if (isset ( $widget ['col'] ) && $widget ['col']) {
				$chunks [] = '<div class="row">';
			}
			unset ( $widget ['label'] );
			$chunks [] = $definition->renderWidget ( $widget, $widget ['col'] );
			if (isset ( $widget ['col'] ) && $widget ['col']) {
				$chunks [] = '</div>';
			}
			
			$chunks [] = '</div></div></div>';
		}
		$chunks [] = '</div>';
		return implode ( '', $chunks );
	}
}

