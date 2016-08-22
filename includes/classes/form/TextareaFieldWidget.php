<?php
/**
 * 多行文本.
 * @author Guangfeng
 *
 */
class TextareaFieldWidget implements IFieldWidget {
	/*
	 * (non-PHPdoc) @see IFieldWidget::getName()
	 */
	public function getName() {
		return '多行文件';
	}
	
	/*
	 * (non-PHPdoc) @see IFieldWidget::getType()
	 */
	public function getType() {
		return 'textarea';
	}
	
	/*
	 * (non-PHPdoc) @see IFieldWidget::render()
	 */
	public function render($definition, $cls = '') {
		$id = isset ( $definition ['id'] ) ? $definition ['id'] : $definition ['name'];
		$row = isset ( $definition ['row'] ) ? $definition ['row'] : '4';
		if (isset ( $definition ['expandable'] )) {
			$cls .= ' custom-scroll';
			$html [] = '<label class="textarea textarea-expandable">';
		} else {
			$html [] = '<label class="textarea">';
		}
		$definition ['value'] = html_escape ( $definition ['value'] );
		$readonly = isset ( $definition ['readonly'] ) ? ' readonly="readonly" ' : '';
		$disabled = isset ( $definition ['disabled'] ) ? ' disabled="disabled" ' : '';
		$placeholder = isset ( $definition ['placeholder'] ) ? ' placeholder="' . $definition ['placeholder'] . '" ' : '';
		$html [] = '<textarea id="' . $id . '" rows="' . $row . '"' . $readonly . $disabled . $placeholder . ' name="' . $definition ['name'] . '" class="' . $cls . '">' . $definition ['value'] . '</textarea>';
		$html [] = '</label>';
		return implode ( '', $html );
	}
	public function getDataProvidor($options) {
		return new EmptyDataProvidor ();
	}
}