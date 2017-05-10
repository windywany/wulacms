<?php
class PicTextCellView extends \MobiListView {
	public function render() {
		if (func_num_args () > 0) {
			$args = func_get_arg ( 0 );
			if (isset ( $args [0] ['custom_data'] ) && $args [0] ['custom_data']) {
				$p = $args [0] ['custom_data'];
				$list = array ('<ul class="pictext">' );
				$list [] = '<li class="pic"><img src="' . the_media_src ( $p ['image'] ) . '"/></li>';
				$list [] = '<li class="text">';
				$list [] = '<h3>' . $p ['title'] . '</h3>';
				$list [] = '<p>' . $p ['desc'] . '</p>';
				$list [] = '</li>';
				$list [] = '</ul>';
				return implode ( '', $list );
			}
		}
	}
	/**
	 * (non-PHPdoc)
	 *
	 * @param AbstractForm $form        	
	 * @see MobiListView::fillEditForm()
	 */
	public function fillEditForm(&$form) {
		$form->addField ( 'image', array ('id' => 'mobi_text_img','placeholder' => '比例3:2,宽258*172px','widget' => 'image','rules' => array ('required' => '请上传图片' ) ) );
	}
	public function fillListViewData(&$data, $customData) {
		$data ['img'] = $customData ['image'];
	}
	public function getCustomData($data) {
		$cdata ['image'] = $data ['image'];
		return $cdata;
	}
	public function getName() {
		return '图文';
	}
	public function getId() {
		return '2';
	}
}