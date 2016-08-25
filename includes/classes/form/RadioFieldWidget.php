<?php
/**
 * radio input widget.
 * @author Guangfeng
 *
 */
class RadioFieldWidget implements IFieldWidget {
	private $providor;
	/*
	 * (non-PHPdoc) @see IFieldWidget::getName()
	 */
	public function getName() {
		return '单选框';
	}
	
	/*
	 * (non-PHPdoc) @see IFieldWidget::getType()
	 */
	public function getType() {
		return 'radio';
	}
	
	/*
	 * (non-PHPdoc) @see IFieldWidget::render()
	 */
	public function render($definition, $cls = '') {
		$defaults = $definition ['defaults'];
		$data = $this->getDataProvidor ( $defaults )->getData ();
		$html [] = '<div class="inline-group">';
		$id = isset ( $definition ['id'] ) ? $definition ['id'] : $definition ['name'];
		if ($data) {
			$readonly = isset ( $definition ['readonly'] ) ? ' readonly="readonly" ' : '';
			$disabled = isset ( $definition ['disabled'] ) ? ' disabled="disabled" ' : '';
			foreach ( $data as $key => $d ) {
				$checked = '';
				if ($d) {
					$val = ' value="' . $key . '" ';
					if ($key == $definition ['value']) {
						$checked = ' checked="checked" ';
					}
				} else {
					if ($definition ['value']) {
						$checked = ' checked="checked" ';
					}
					$d = $key;
					$val = '';
				}
				$html [] = '<label class="radio"><input id="' . $id . '_' . $key . '" type="radio" ' . $checked . $readonly . $disabled . ' name="' . $definition ['name'] . '"' . $val . '/><i></i>' . $d . '</label>';
			}
		}
		$html [] = '</div>';
		return implode ( '', $html );
	}
	/*
	 * (non-PHPdoc) @see IFieldWidget::getDataProvidor()
	 */
	public function getDataProvidor($options) {
		if (! $this->providor) {
			$this->providor = new NewLineDataProvidor ();
			$this->providor->setOptions ( $options );
		}
		return $this->providor;
	}
}
