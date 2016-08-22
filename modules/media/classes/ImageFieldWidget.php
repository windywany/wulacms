<?php
/**
 * 图片上传组件.
 * @author Guangfeng
 *
 */
class ImageFieldWidget implements IFieldWidget, IFieldWidgetDataProvidor {
	private $options;
	private static $data = array ('extensions' => 'jpg,gif,png,jpeg','water' => 1,'locale' => 0,'msize' => '','fullurl' => 0,'userType' => '' );
	/*
	 * (non-PHPdoc) @see IFieldWidget::getName()
	 */
	public function getName() {
		return '图片&附件';
	}
	
	/*
	 * (non-PHPdoc) @see IFieldWidget::getType()
	 */
	public function getType() {
		return 'image';
	}
	
	/*
	 * (non-PHPdoc) @see IFieldWidget::render()
	 */
	public function render($definition, $cls = '') {
		$id = $definition ['id'];
		if (! $id) {
			$id = $definition ['name'];
		}
		$this->setOptions ( $definition ['defaults'] );
		$data = $this->getData ();
		$definition = array_merge ( $definition, $data );
		
		$water = isset ( $definition ['water'] ) ? $definition ['water'] : 0;
		$locale = isset ( $definition ['locale'] ) ? $definition ['locale'] : 0;
		$userType = isset ( $definition ['userType'] ) ? $definition ['userType'] : '';
		$fullurl = isset ( $definition ['fullurl'] ) ? $definition ['fullurl'] : 0;
		$msize = isset ( $definition ['msize'] ) ? $definition ['msize'] : 0;
		$extensions = isset ( $definition ['extensions'] ) ? $definition ['extensions'] : 0;
		$readonly = isset ( $definition ['readonly'] ) ? ' readonly="readonly" ' : '';
		$disabled = isset ( $definition ['disabled'] ) ? ' disabled="disabled" ' : '';
		$placeholder = isset ( $definition ['placeholder'] ) ? ' placeholder="' . $definition ['placeholder'] . '" ' : '';
		$html = '<label class="input input-file" for="' . $id . '"><div id="uploadImg_' . $id . '"';
		if ($readonly || $disabled) {
			$html .= ' class="button disabled" ';
		} else {
			$html .= ' class="button" ';
		}
		
		if ($userType) {
			$html .= ' data-usertype="' . $userType . '"';
		}
		if ($water) {
			$html .= ' data-water="' . $water . '"';
		}
		if ($locale) {
			$html .= ' data-locale="' . $locale . '"';
		}
		
		if ($extensions) {
			$html .= ' data-extensions="' . $extensions . '"';
		}
		
		if ($msize) {
			$html .= ' data-max-file-size="' . $msize . '"';
		}
		
		if ($fullurl) {
			$html .= ' data-full-url="true"';
		}
		if ($definition ['value'] && preg_match ( '/.+(png|gif|jpg|jpeg|bmp)$/i', $definition ['value'] )) {
			$preview = 'href="' . the_media_src ( $definition ['value'] ) . '" rel="superbox[image]"';
		} else {
			$preview = 'href="javascript:;" style="display:none"';
		}
		
		$pp = '<a for="' . $id . '" class="button" ' . $preview . '><i class="fa fa-lg fa-eye txt-color-blue"></i></a>';
		
		$html .= ' data-widget="nuiAjaxUploader" for="#' . $id . '"><i class="fa fa-lg fa-cloud-upload"></i></div>' . $pp . '
		<input type="text" name="' . $definition ['name'] . '" id="' . $id . '" value="' . $definition ['value'] . '" ' . $readonly . $disabled . $placeholder . '/>
		</label>';
		return $html;
	}
	public function getDataProvidor($options) {
		return $this;
	}
	/*
	 * (non-PHPdoc) @see IFieldWidgetDataProvidor::getData()
	 */
	public function getData($option = false) {
		$datax = self::$data;
		if ($this->options) {
			$datax = @json_decode ( $this->options, true );
			if ($datax) {
				$datax = array_merge ( self::$data, $datax );
			}
		}
		return $datax;
	}
	/*
	 * (non-PHPdoc) @see IFieldWidgetDataProvidor::getOptionsFormat()
	 */
	public function getOptionsFormat() {
		return '{extensions:"",water:[1|0],locale:[0|1],msize:"size",fullurl:[0|1],userType:"[admin|vip]"}';
	}
	
	/*
	 * (non-PHPdoc) @see IFieldWidgetDataProvidor::setOptions()
	 */
	public function setOptions($options) {
		$this->options = $options;
	}
}