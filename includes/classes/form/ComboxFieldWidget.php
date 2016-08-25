<?php
class ComboxFieldWidget implements IFieldWidget, IFieldWidgetDataProvidor {
	private $options;
	private static $data = array ('parent' => '','url' => '','tag' => false,'mnl' => 0 );
	public function getDataProvidor($options) {
		return $this;
	}
	public function getType() {
		return 'combox';
	}
	public function getName() {
		return '级联选择框';
	}
	public function render($definition, $cls = '') {
		$this->setOptions ( $definition ['defaults'] );
		$data = $this->getData ();
		$name = $definition ['name'];
		$value = $definition ['value'];
		$url = $data ['url'];
		$combox = dashboard_htmltag ( 'input' );
		$combox->prefix ( 'data-' )->widget ( 'nuiCombox' )->source ( $url );
		if ($data ['parent']) {
			$combox->parent ( $data ['parent'] );
		}
		if ($data ['tag']) {
			$combox->tagMode ( 'true' );
		}
		if ($data ['mnl']) {
			$combox->mnl ( $data ['mnl'] );
		}
		$combox->prefix ( '' )->id ( $definition ['id'] )->name ( $name )->value ( $value )->type ( 'hidden' )->style ( 'width:100%' );
		if ($definition ['disabled']) {
			$combox->disabled ( 'disabled' );
		}
		if ($definition ['readonly']) {
			$combox->readonly ( 'readonly' );
		}
		if ($definition ['placeholder']) {
			$combox->placeholder ( $definition ['placeholder'] );
		}
		$html = '<label class="input" for="' . $definition ['id'] . '">' . $combox->render () . '</label>';
		return $html;
	}
	/*
	 * (non-PHPdoc) @see IFieldWidgetDataProvidor::getData()
	 */
	public function getData($option = false) {
		$datax = @json_decode ( $this->options, true );
		if ($datax) {
			$datax = array_merge ( self::$data, $datax );
		} else {
			$datax = self::$data;
		}
		return $datax;
	}
	
	/*
	 * (non-PHPdoc) @see IFieldWidgetDataProvidor::getOptionsFormat()
	 */
	public function getOptionsFormat() {
		return '{[parent:"filed_id"],url:"your_url",tag:[true|false],mnl:[0-9],placeholder:""}';
	}
	
	/*
	 * (non-PHPdoc) @see IFieldWidgetDataProvidor::setOptions()
	 */
	public function setOptions($options) {
		$this->options = $options;
	}
}