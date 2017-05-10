<?php
class ThreePicCellView extends MobiListView {
	public function getCustomData($data) {
		$cdata ['image'] = $data ['image'];
		$cdata ['image1'] = $data ['image1'];
		$cdata ['image2'] = $data ['image2'];
		return $cdata;
	}
	public function getName() {
		return '三图';
	}
	public function fillEditForm(&$form) {
		$form->addField ( 'image', array ('id' => 'mobi_img','group' => 'p1','col' => 4,'placeholder' => '宽326*206px','widget' => 'image','rules' => array ('required' => '请上传图片' ) ) );
		$form->addField ( 'image1', array ('id' => 'mobi_img1','group' => 'p1','col' => 4,'placeholder' => '宽326*206px','widget' => 'image','rules' => array ('required' => '请上传图片' ) ) );
		$form->addField ( 'image2', array ('id' => 'mobi_img2','group' => 'p1','col' => 4,'placeholder' => '宽326*206px','widget' => 'image','rules' => array ('required' => '请上传图片' ) ) );
	}
	public function fillListViewData(&$data, $customData) {
		$data ['img'] = $customData ['image'];
		$data ['imgs'] [] = $customData ['image'];
		$data ['imgs'] [] = $customData ['image1'];
		$data ['imgs'] [] = $customData ['image2'];
	}
	public function render() {
		if (func_num_args () > 0) {
			$args = func_get_arg ( 0 );
			if (isset ( $args [0] ['custom_data'] ) && $args [0] ['custom_data']) {
				$p = $args [0] ['custom_data'];
				$list = array ('<ul class="threepic clearfix">' );
				$list [] = '<li><img src="' . the_media_src ( $p ['image'] ) . '"/></li>';
				$list [] = '<li><img src="' . the_media_src ( $p ['image1'] ) . '"/></li>';
				$list [] = '<li><img src="' . the_media_src ( $p ['image2'] ) . '"/></li>';
				$list [] = '</ul>';
				$list [] = '<p class="clearfix">' . $p ['title'] . '</p>';
				return implode ( '', $list );
			}
		}
	}
	public function getId() {
		return '3';
	}
}