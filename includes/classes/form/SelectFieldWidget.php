<?php
/**
 * 选择框输入组件.
 * @author Guangfeng
 *
 */
class SelectFieldWidget implements IFieldWidget {
	private $providor;
	/*
	 * (non-PHPdoc) @see IFieldWidget::getName()
	 */
	public function getName() {
		return '选择框';
	}
	
	/*
	 * (non-PHPdoc) @see IFieldWidget::getType()
	 */
	public function getType() {
		return 'select';
	}
	
	/*
	 * (non-PHPdoc) @see IFieldWidget::render()
	 */
	public function render($definition, $cls = '') {
		$defaults = $definition ['defaults'];
		$data = $this->getDataProvidor ( $defaults )->getData ();
		$id = isset ( $definition ['id'] ) ? $definition ['id'] : $definition ['name'];
		$readonly = isset ( $definition ['readonly'] ) ? ' readonly="readonly" ' : '';
		$disabled = isset ( $definition ['disabled'] ) ? ' disabled="disabled" ' : '';
		$placeholder = isset ( $definition ['placeholder'] ) ? ' placeholder="' . $definition ['placeholder'] . '" ' : '';
		$html [] = '<label class="select"><select id="' . $id . '" name="' . $definition ['name'] . '"' . $readonly . $disabled . $placeholder . '>';
		
		if ($data) {
			foreach ( $data as $key => $d ) {
				if ($key == $definition ['value']) {
					$html [] = '<option value="' . $key . '" selected="selected">' . $d . '</option>';
				} else {
					$html [] = '<option value="' . $key . '">' . $d . '</option>';
				}
			}
		}
		$html [] = '</select><i></i></label>';
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