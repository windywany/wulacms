<?php
class CmsPageSelectWidget implements IFieldWidget, IFieldWidgetDataProvidor {
	private $options;
	public function getDataProvidor($options) {
		$this->setOptions ( $options );
		return $this;
	}
	public function getType() {
		return 'page_select';
	}
	public function getName() {
		return '页面选择框';
	}
	/*
	 * (non-PHPdoc) @see IFieldWidget::render()
	 */
	public function render($definition, $cls = '') {
		$name = $definition ['name'];
		$value = $definition ['value'];
		$model = $definition ['defaults'];
		$data = $this->getDataProvidor ( $model )->getData ( true );
		$id = isset ( $definition ['id'] ) ? $definition ['id'] : $definition ['name'];
		$html [] = '<label class="select"><select id="' . $id . '" name="' . $definition ['name'] . '"><option value="">-请选择-</option>';
		
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
	 * (non-PHPdoc) @see IFieldWidgetDataProvidor::getData()
	 */
	public function getData($option = false) {
		if ($option) {
			$model = $this->options;
			if ($model) {
				return dbselect ( 'title2,id' )->from ( '{cms_page}' )->where ( array ('hidden' => 0,'deleted' => 0,'model' => $model ) )->toArray ( 'title2', 'id' );
			}
		}
		return array ();
	}
	
	/*
	 * (non-PHPdoc) @see IFieldWidgetDataProvidor::getOptionsFormat()
	 */
	public function getOptionsFormat() {
		return '参数格式为：[model_name]。model_name为页面模型名.';
	}
	
	/*
	 * (non-PHPdoc) @see IFieldWidgetDataProvidor::setOptions()
	 */
	public function setOptions($options) {
		$this->options = $options;
	}
}