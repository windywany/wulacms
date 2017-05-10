<?php

class OnePicCellView extends MobiListView {
	public function render() {
		if (func_num_args() > 0) {
			$args = func_get_arg(0);
			if (isset ($args [0] ['custom_data']) && $args [0] ['custom_data']) {
				$p       = $args [0] ['custom_data'];
				$list    = array('<ul class="onepic">');
				$list [] = '<h3>' . $p ['title'] . '</h3>';
				$list [] = '<li><img src="' . the_media_src($p ['image']) . '"/></li>';
				$list [] = '</ul>';
				$list [] = '<p>' . $p ['desc'] . '</p>';

				return implode('', $list);
			}
		}

		return '';
	}

	/**
	 * (non-PHPdoc)
	 *
	 * @param AbstractForm $form
	 *
	 * @see MobiListView::fillEditForm()
	 */
	public function fillEditForm(&$form) {
		$form->addField('image', array('id' => 'mobi_cursouel_img', 'placeholder' => '比例16:9,宽720px-1080px', 'widget' => 'image', 'rules' => array('required' => '请上传图片')));
	}

	public function fillListViewData(&$data, $customData) {
		$data ['img'] = $customData ['image'];
	}

	public function getCustomData($data) {
		$cdata ['image'] = $data ['image'];

		return $cdata;
	}

	public function getName() {
		return '大图';
	}

	public function getId() {
		return '4';
	}
}
