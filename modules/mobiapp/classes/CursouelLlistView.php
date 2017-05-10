<?php
class CursouelLlistView extends MobiListView {
	public function render() {
		if (func_num_args () > 0) {
			$args = func_get_arg ( 0 );
			if (isset ( $args [0] ['custom_data'] ) && $args [0] ['custom_data']) {
				$pages = $args [0] ['custom_data'];
				$list = array ('<ul class="cursouel">' );
				
				foreach ( $pages as $i => $p ) {
					$list [] = '<li class="' . ($i > 0 ? 'hidden' : '') . '"><img src="' . the_media_src ( $p ['custom_data'] ['image'] ) . '"/><p class="bak">&nbsp;</p><p>' . $p ['custom_data'] ['title'] . '</p></li>';
				}
				$list [] = '</ul>';
				return implode ( '', $list );
			}
		}
		return '无轮播内容';
	}
	/**
	 * (non-PHPdoc)
	 *
	 * @param AbstractForm $form        	
	 * @see MobiListView::fillEditForm()
	 */
	public function fillEditForm(&$form) {
		$form->addField ( 'image', array ('id' => 'mobi_cursouel_img','placeholder' => '比例16:9,宽720px-1080px','widget' => 'image','rules' => array ('required' => '请上传图片' ) ) );
	}
	public function fillListViewData(&$data, $customData) {
		$data ['img'] = $customData ['image'];
	}
	public function isCarousel() {
		return 1;
	}
	public function getCustomData($data) {
		$cdata ['image'] = $data ['image'];
		return $cdata;
	}
	public function getName() {
		return '轮播';
	}
	public function getId() {
		return 'cursouel';
	}
}