<?php
/*
 * This file is part of wulacms.
 *
 * (c) Leo Ning <windywany@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace taoke\command;

use artisan\ArtisanCommand;
use taoke\classes\TaokeXSConfigure;
use xunsou\XSFactory;

class DeleteExpireGoodsCommand extends ArtisanCommand {
	public function cmd() {
		return 'del-goods';
	}

	public function desc() {
		return 'delete expired taobaoke goods';
	}

	protected function execute($options) {
		@ini_set('memory_limit', '256M');
		$date = date('Y-m-d');

		$xs = XSFactory::getXS(new TaokeXSConfigure());
		$xs->index->openBuffer(4);
		$goods = dbselect('page_id')->from('{tbk_goods}')->where(['coupon_stop <' => $date]);
		foreach ($goods as $g) {
			$xs->index->del($g['page_id']);
		}
		$xs->index->closeBuffer();
		$xs->index->flushIndex();
		$sql     = "DELETE CP FROM cms_page AS CP,tbk_goods AS TG WHERE CP.id = TG.page_id and TG.coupon_stop < '{$date}'";
		$sql2    = "DELETE FROM tbk_goods WHERE coupon_stop < '{$date}'";
		$dialect = \DatabaseDialect::getDialect();
		$cnt     = $dialect->exec($sql);
		$cnt2    = $dialect->exec($sql2);
		log_info('共清空' . $cnt . '/' . $cnt2 . '个过期商品', 'expire');
		if ($cnt > 0) {
			// 清空缓存
			$prefix                    = rand_str(3);
			$settings                  = \KissGoSetting::getSetting();
			$settings ['cache_prefix'] = $prefix;
			$settings->saveSettingToFile(APPDATA_PATH . 'settings.php');
			\RtCache::delete('system_preferences');
		}

		return 0;
	}

}