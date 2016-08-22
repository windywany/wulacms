<?php
/**
 * 多文件上传器.
 * @author ngf
 *
 */
class MimageFieldWidget implements IFieldWidget, IFieldWidgetDataProvidor {
	private $options;
	private static $data = array ('extensions' => 'jpg,gif,png,jpeg','water' => 1,'locale' => false,'msize' => '','fullurl' => 0,'userType' => '' );
	public function getDataProvidor($options) {
		return $this;
	}
	public function getType() {
		return 'mimage';
	}
	public function getName() {
		return '多文件上传';
	}
	public function render($definition, $cls = '') {
		$html = array ();
		$id = $definition ['id'];
		if (! $id) {
			$id = $definition ['name'];
		}
		
		$this->setOptions ( $definition ['defaults'] );
		$data = $this->getData ();
		
		$water = isset ( $data ['water'] ) ? $data ['water'] : 0;
		$locale = isset ( $data ['locale'] ) ? $data ['locale'] : 0;
		$userType = isset ( $data ['userType'] ) ? $data ['userType'] : '';
		$fullurl = isset ( $data ['fullurl'] ) ? $data ['fullurl'] : 0;
		$msize = isset ( $data ['msize'] ) ? $data ['msize'] : 0;
		$extensions = isset ( $data ['extensions'] ) ? $data ['extensions'] : 0;
		
		$name = $definition ['name'];
		$value = $definition ['value'];
		
		$html [] = '<div class="m-ajax-uploador">';
		
		if ($value && is_array ( $value )) {
			foreach ( $value as $pic ) {
				$html [] = $this->getOnePic ( $pic, $name );
			}
		}
		
		$html [] = '<div class="up-file add-btn" data-name="' . $name . '" id="upload_adea_' . $id . '" data-multi-upload="true"';
		
		if ($extensions) {
			$html [] = ' data-extensions="' . $extensions . '"';
		}
		
		if ($msize) {
			$html [] = ' data-max-file-size="' . $msize . '"';
		}
		
		if ($fullurl) {
			$html [] = ' data-full-url="true"';
		}
		if ($userType) {
			$html [] = ' data-usertype="' . $userType . '"';
		}
		if ($water) {
			$html [] = ' data-water="' . $water . '"';
		}
		if ($locale) {
			$html [] = ' data-locale="' . $locale . '"';
		}
		$html [] = ' data-widget="nuiAjaxUploader" for="#' . $name . '"></div>';
		
		$html [] = '<b class="clearfix"/></div>';
		return implode ( '', $html );
	}
	public function getData($option = false) {
		$datax = @json_decode ( $this->options, true );
		if ($datax) {
			$datax = array_merge ( self::$data, $datax );
		} else {
			$datax = self::$data;
		}
		return $datax;
	}
	public function setOptions($options) {
		$this->options = $options;
	}
	public function getOptionsFormat() {
		return '{extensions:"",water:[1|0],locale:[0|1],msize:"size",fullurl:[0|1],userType:"[admin|vip]"}';
	}
	private function getOnePic($pic, $name) {
		$html = array ();
		$html [] = '<div class="up-file"><input class="f-name" type="hidden" name="' . $name . '[]" value="' . $pic ['url'] . '"/>';
		
		$html [] = '<a class="close">×</a><div class="img-wrap">';
		$html [] = '<img class="f-img" src="' . the_media_src ( $pic ['url'] ) . '"/></div>';
		$html [] = '<div class="f-notes">';
		$html [] = '<label class="input">';
		$html [] = '<input type="text" name="' . $name . '_alt[]" class="input-xs" value="' . html_escape ( $pic ['alt'] ) . '"/>';
		$html [] = '</label>';
		$html [] = '<label class="textarea">';
		$html [] = '<i class="icon-prepend fa fa-info"></i>';
		$html [] = '<textarea class="custom-scroll" rows="2" name="' . $name . '_desc[]">' . html_escape ( $pic ['desc'] ) . '</textarea>';
		$html [] = '</label>';
		$html [] = '<div class="progress progress-micro">';
		$html [] = '<div style="width: 100%;" role="progressbar" class="progress-bar bg-color-green"></div>';
		$html [] = '</div>';
		$html [] = '</div>';
		$html [] = '</div>';
		return implode ( '', $html );
	}
	public static function alter_mimage_field_value($value, $name) {
		$imgs = array ();
		$images = rqst ( $name );
		$images_alt = rqst ( $name . '_alt' );
		$images_desc = rqst ( $name . '_desc' );
		if ($images) {
			foreach ( $images as $key => $url ) {
				$imgs [] = array ('url' => $url,'alt' => $images_alt [$key],'desc' => $images_desc [$key] );
			}
		}
		return @json_encode ( $imgs );
	}
	public static function parse_mimage_field_value($value) {
		$imgs = array ();
		if ($value) {
			$imgs = @json_decode ( $value, true );
		}
		return $imgs;
	}
}