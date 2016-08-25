<?php
/**
 * checkbox
 * @author Guangfeng
 *
 */
class CheckboxFieldWidget implements IFieldWidget {
	private $providor;
	/*
	 * (non-PHPdoc) @see IFieldWidget::getName()
	 */
	public function getName() {
		return '多选框';
	}
	
	/*
	 * (non-PHPdoc) @see IFieldWidget::getType()
	 */
	public function getType() {
		return 'checkbox';
	}
	
	/*
	 * (non-PHPdoc) @see IFieldWidget::render()
	 */
	public function render($definition, $cls = '') {
		$defaults = $definition ['defaults'];
		$data = $this->getDataProvidor ( $defaults )->getData ();
		$id = isset ( $definition ['id'] ) ? $definition ['id'] : $definition ['name'];
		$html [] = '<div class="inline-group">';
		if ($data) {
			$values = $definition ['value'];
			if (! is_array ( $values )) {
				$values = explode ( ',', $values );
			}
			$readonly = isset ( $definition ['readonly'] ) ? ' readonly="readonly" ' : '';
			$disabled = isset ( $definition ['disabled'] ) ? ' disabled="disabled" ' : '';
			foreach ( $data as $key => $d ) {
				$checked = '';
				if ($d) {
					$val = ' value="' . $key . '" ';
					if (in_array ( $key, $values )) {
						$checked = ' checked="checked" ';
					}
				} else {
					if ($definition ['value']) {
						$checked = ' checked="checked" ';
					}
					$d = $key;
					$val = '';
				}
				$html [] = '<label class="checkbox"><input id="' . $id . '_' . $key . '" type="checkbox"' . $readonly . $disabled . $checked . ' name="' . $definition ['name'] . '[]"' . $val . '/><i></i>' . $d . '</label>';
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