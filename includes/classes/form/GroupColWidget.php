<?php
class GroupColWidget implements IFieldWidget {
	private $widgets = array ();
	public function addWidget($widget) {
		$this->widgets [] = $widget;
	}
	public function getType() {
		return 'groupCol';
	}
	public function getName() {
		return '分组';
	}
	/**
	 *
	 * @param DefaultFormRender $definition        	
	 * @see IFieldWidget::render()
	 */
	public function render($definition = '', $cls = '') {
		$chunks [] = '<div class="row">';
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
		foreach ( $this->widgets as $widget ) {
			$chunks [] = $definition->renderWidget ( $widget, $widget ['col'] );
		}
		$chunks [] = '</div>';
		return implode ( '', $chunks );
	}
	/*
	 * (non-PHPdoc) @see IFieldWidget::getDataProvidor()
	 */
	public function getDataProvidor($options) {
		return new EmptyDataProvidor ();
	}
}
