<?php
/*
 * This file is part of wulacms.
 *
 * (c) Leo Ning <windywany@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace mobiapp\classes;

class GifCellView extends \MobiListView {
	public function render() {
		if (func_num_args() > 0) {
			$args = func_get_arg(0);
			if (isset ($args [0] ['custom_data']) && $args [0] ['custom_data']) {
				$p       = $args [0] ['custom_data'];
				$img     = $p ['cover'] ? $p['cover'] : $args[0]['image'];
				$list    = array('<ul class="onepic">');
				$list [] = '<h3>' . $p ['title'] . '</h3>';
				if ($img) {
					$list [] = '<li><img src="' . the_media_src($img) . '"/></li>';
				}
				$list [] = '</ul>';
				$list [] = '<p>' . $p ['desc'] . '</p>';

				return implode('', $list);
			}
		}

		return '';
	}

	public function getId() {
		return 6;
	}

	public function getName() {
		return '动图';
	}

	public function fillEditForm(&$form) {
		$form->addField('cover', array('id' => 'mobi_gif_img', 'label' => '封面', 'placeholder' => '比例16:9,宽720px-1080px', 'widget' => 'image'));
	}

	public function getCustomData($data) {
		return ['cover' => $data['cover']];
	}

	public function fillListViewData(&$data, $customData) {
		if ($customData['cover']) {
			$data['img'] = $customData['cover'];
		}
	}
}