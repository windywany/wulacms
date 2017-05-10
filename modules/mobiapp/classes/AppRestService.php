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

class AppRestService {
	/**
	 * 版本控制
	 *
	 *
	 * @param array  $param
	 * @param string $key
	 * @param static $secret
	 *
	 * @return array upgrade info and ads info.
	 */
	public function rest_update($param, $key, $secret) {
		$os     = isset ($param ['os']) && $param ['os'] ? $param ['os'] : '';
		$market = isset ($param ['channel']) && $param ['channel'] ? $param ['channel'] : 'guanfang';
		// 版本号，判断是否更新
		$vername = isset ($param ['vercode']) ? $param ['vercode'] : '';

		$os = $os == 'android' ? 1 : 2;
		if (!$os || !$vername) {
			return array('error' => 404, 'message' => '错误参数！os 和 vercode不能为空.');
		}
		$dataUpdate = $dataAds = array();
		$where      = array('os' => $os, 'vername >' => $vername, 'deleted' => 0, 'update_type <' => 2);
		$db         = dbselect('*')->from('{app_version} AS AV')->desc('AV.vername')->limit(0, 1);
		$rs         = $db->where($where)->get(0);
		if ($rs) {
			$dataUpdate ['version'] = $rs ['version'];
			$dataUpdate ['desc']    = $rs ['desc'];
			$dataUpdate ['vercode'] = $rs ['vername'];
			$dataUpdate ['type']    = $rs ['update_type'];
			$dataUpdate ['size']    = $rs ['size'];
			$dataUpdate ['url']     = $rs ['url'];
			$mdata                  = dbselect('id,url')->from('{app_version_market}')->where(array('version_id' => $rs ['id'], 'market' => $market, 'deleted' => 0))->limit(0, 1)->get(0);
			if ($mdata) { // 渠道配置存在
				if ($mdata ['url']) { // 但不允许升级
					$dataUpdate ['url'] = $mdata ['url'];
				} else if ($os == 1) {
					$dataUpdate ['url'] = $rs ['url'];
				}
			} else if ($market != 'guanfang') {
				$market = 'guanfang';
				$apkurl = dbselect()->from('{app_version_market}')->where(array('version_id' => $rs ['id'], 'market' => $market, 'deleted' => 0))->limit(0, 1)->get('url');
				if ($apkurl) {
					$dataUpdate ['url'] = $apkurl;
				} else if ($os == 1) {
					$dataUpdate ['url'] = $rs ['url'];
				}
			}

			if ($dataUpdate ['url']) {
				$dataUpdate ['url'] = the_media_src($dataUpdate ['url']);
			} else {
				$dataUpdate = false;
			}
		}
		$ads = cfg('ads@mobiapp', 0);
		if ($ads && !$dataUpdate) {
			// 指定市场但是未找到升级
			$where       = array('os' => $os, 'vername' => $vername, 'deleted' => 0);
			$db          = dbselect('id')->from('{app_version} AS AV')->desc('AV.vername')->limit(0, 1);
			$where ['@'] = dbselect('id')->from('{app_version_market} AS AVM')->where(array('AVM.version_id' => imv('AV.id'), 'AVM.deleted' => 0, 'AVM.market' => $market));
			$rs          = $db->where($where)->get();
			if ($rs) {
				$versionId = $rs ['id'];
				$dataAds   = array();
				$whereAds  = array('version_id' => $versionId, 'market' => $market, 'deleted' => 0);
				$rsAds     = dbselect('ad_config_id')->from('{app_version_market}')->where($whereAds)->limit(0, 1)->get();
				$banner    = $bottom = $screen = $stream = $click = array();
				if ($rsAds) {
					$rsAdContent = dbselect('*')->from('{app_ads}')->where(array('id' => $rsAds ['ad_config_id'], 'deleted' => 0))->desc('id')->get();
					if ($rsAdContent ['banner']) {
						$temp   = explode(':', $rsAdContent ['banner']);
						$banner = array('banner' => array('id' => $temp [0], 'from' => $temp [1]));
					}
					if ($rsAdContent ['bottom']) {
						$temp   = explode(':', $rsAdContent ['bottom']);
						$bottom = array('bottom' => array('id' => $temp [0], 'from' => $temp [1]));
					}
					if ($rsAdContent ['screen']) {
						$temp   = explode(':', $rsAdContent ['screen']);
						$screen = array('screen' => array('id' => $temp [0], 'from' => $temp [1]));
					}
					if ($rsAdContent ['stream']) {
						$temp   = explode(':', $rsAdContent ['stream']);
						$stream = array('stream' => array('id' => $temp [0], 'from' => $temp [1]));
					}
					if ($rsAdContent ['clickinsert']) {
						$temp  = explode(':', $rsAdContent ['clickinsert']);
						$click = array('click' => array('id' => $temp [0], 'from' => $temp [1], 'rand' => $rsAdContent ['probability']));
					}
					$dataAds = array_merge($banner, $bottom, $screen, $stream, $click);
				}
			}
		}

		$rtn = array('error' => 0, 'data' => array());
		if ($dataUpdate) {
			$rtn ['data'] ['update'] = $dataUpdate;
		}
		if ($dataAds) {
			$rtn ['data'] ['ads'] = $dataAds;
		}

		return $rtn;
	}
}