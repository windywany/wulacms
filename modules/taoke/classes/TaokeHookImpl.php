<?php

namespace taoke\classes;

class TaokeHookImpl {
	/**
	 * 添加导航菜单.
	 *
	 * @param \AdminLayoutManager $layout
	 */
	public static function do_admin_layout($layout) {
		if (icando('m:cms')) {
			$menu     = $layout->getNaviMenu('site');
			$pageMenu = new \AdminNaviMenu ('taoke_menu', '淘宝客', 'fa-picture-o', tourl('taoke', false));
			$pageMenu->addSubmenu(array('taokelist', '淘宝客列表', 'fa-picture-o', tourl('taoke', false)), false, 1);
			$pageMenu->addSubmenu(array('addtaoke', '生成淘口令', 'fa-picture-o', tourl('taoke/generate', false)), false, 2);
			$pageMenu->addSubmenu(array('config', '淘宝客配置', 'fa-picture-o', tourl('taoke/preference', false)), false, 2);
			$menu->addItem($pageMenu, false, 15);
		}
	}

	public static function load_taoke_model($model = null) {
		return new TaokeContentModel();
	}

	public static function get_content_list_page_url($url, $page) {
		if ($page ['model'] == 'taoke') {
			$url = tourl('taoke', false);
		}

		return $url;
	}

	public static function on_destroy_cms_page($ids) {
		dbdelete()->from('{tbk_goods}')->where(array('page_id IN' => $ids))->exec();
	}

	public static function build_page_common_query(\Query $query, $con) {
		if (isset($con['model']) && $con['model'] == 'taoke') {
			$sortby = get_condition_value('sortby', $con);
			if (strpos($sortby, 'TBKG') !== false) {
				$query->join('{tbk_goods} AS TBKG', 'TBKG.page_id = CP.id');
			}
		}

		return $query;
	}

	public static function get_columns_of_tbkGoodsTable($cols) {
		$cols['rate'] = ['name' => '收入比率', 'width' => '80', 'show' => false, 'order' => 70, 'sort' => 'tbk.rate,a'];

		$cols['coupon_c'] = ['name' => '总量/剩余', 'width' => '100', 'show' => true, 'order' => 80, 'sort' => 'tbk.coupon_remain,a', 'render' => function ($v, $data, $extra) {
			return $data['coupon_count'] . '/' . $data['coupon_remain'];
		}];

		$cols['coupon_start'] = ['name' => '开始时间', 'width' => '100', 'show' => false, 'order' => 90, 'sort' => 'tbk.coupon_start,a'];
		$cols['coupon_stop']  = ['name' => '结束时间', 'width' => '100', 'show' => true, 'order' => 91, 'sort' => 'tbk.coupon_stop,a'];

		return $cols;
	}
}
