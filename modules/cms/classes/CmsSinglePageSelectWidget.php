<?php
class CmsSinglePageSelectWidget implements IFieldWidget, IFieldWidgetDataProvidor {
	private $options;
	public function getDataProvidor($options) {
		$this->setOptions ( $options );
		return $this;
	}
	public function getType() {
		return 'cms_single_select';
	}
	public function getName() {
		return '页面自选器';
	}
	/*
	 * (non-PHPdoc) @see IFieldWidget::render()
	 */
	public function render($definition, $cls = '') {
		$name = $definition ['name'];
		$value = $definition ['value'];
		$model = $definition ['defaults'];
		if ($model) {
			$url = tourl ( 'cms/page/auto_page/' . $model );
		} else {
			$url = tourl ( 'cms/page/auto_page' );
		}
		if ($value) {
			$value .= ':' . dbselect ()->from ( '{cms_page}' )->where ( array ('id' => $value ) )->get ( 'title2' );
		} else {
			$value = '0:-请选择-';
		}
		?>
<input type="hidden" data-widget="nuiCombox" style="width: 100%"
	data-source="<?php echo $url?>" value="<?php echo $value?>"
	id="<?php echo $name?>" name="<?php echo $name?>" />
<?php
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