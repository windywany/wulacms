<?php

namespace taoke\classes;

use weixin\classes\Wechat;
use xunsou\XSFactory;

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
			$pageMenu->addSubmenu(array('addtaoke', '生成淘口令', 'fa-picture-o', tourl('taoke/generate', false)), 'preference:taoke/setting', 2);
			$pageMenu->addSubmenu(array('config', '淘宝客配置', 'fa-picture-o', tourl('taoke/preference', false)), 'preference:taoke/setting', 3);
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
		static $url = false;
		if (!$url) {
			$url = tourl('taoke/createtoken');
		}
		$cols['comission']    = ['name' => '佣金', 'width' => '90', 'show' => false, 'order' => 40, 'sort' => 'comission,a'];
		$cols['coupon_price'] = ['name' => '券面值', 'width' => '90', 'show' => false, 'order' => 50, 'sort' => 'coupon_price,a'];
		$cols['real_price']   = ['name' => '券后价', 'width' => '90', 'show' => false, 'order' => 55, 'sort' => 'real_price,a'];

		$cols['token'] = ['name' => '淘口令', 'width' => '120', 'show' => false, 'order' => 60, 'sort' => 'tbk.token,d', 'render' => function ($v, $data, $e) use ($url) {
			if ($v) {
				return $v;
			} else {
				return "<a id=\"gbtn-{$data['cid']}\" class=\"btn btn-xs btn-primary\" href=\"{$url}{$data['cid']}\" target=\"ajax\" data-confirm=\"你确定要生成淘口令吗?\"> <i class=\"fa fa-pencil-square-o\"></i> 生成</a>";
			}
		}];

		$cols['rate'] = ['name' => '收入比率', 'width' => '80', 'show' => false, 'order' => 70, 'sort' => 'tbk.rate,a'];

		$cols['coupon_c'] = ['name' => '总量/剩余', 'width' => '100', 'show' => true, 'order' => 80, 'sort' => 'tbk.coupon_remain,a', 'render' => function ($v, $data, $extra) {
			return $data['coupon_count'] . '/' . $data['coupon_remain'];
		}];

		$cols['coupon_start'] = ['name' => '开始时间', 'width' => '100', 'show' => false, 'order' => 90, 'sort' => 'tbk.coupon_start,a'];
		$cols['coupon_stop']  = ['name' => '结束时间', 'width' => '100', 'show' => true, 'order' => 91, 'sort' => 'tbk.coupon_stop,a'];

		return $cols;
	}

	public static function after_save_page_taoke($page) {
		$title     = $page['title'];
		$tokenizer = new \XSTokenizerSss();
		$keyword   = $tokenizer->getMysqlToken($title);
		if ($keyword) {
			dbupdate('{cms_page}')->set(['search_index' => $keyword])->where(['id' => $page['id']])->exec();
		}
	}

	public static function weixin_auto_reply_text_message($msg, $xml, Wechat $chat) {
		if ($msg === null) {
			$cont = $chat->getRevContent();
			if ($cont) {
				$text           = $cont;
				$msg['MsgType'] = 'text';
				$tokenizer      = new \XSTokenizerSss();
				$content        = $tokenizer->getTokens($cont);
				$cnt            = 0;
				$chs            = [];
				if ($content) {
					$surl = trailingslashit(cfg('search_url@taoke', DETECTED_ABS_URL . 'ss'));
					if (bcfg('fuzzy@taoke', true)) {
						$keyword = implode(' OR ', $content);
					} else {
						$keyword = implode(' AND ', $content);
					}
					try {
						$xs  = XSFactory::getXS(new TaokeXSConfigure());
						$rst = $xs->search->setCollapse('ch')->setLimit(10)->search($keyword);
						foreach ($rst as $r) {
							$cc = $r->ccount();
							if ($cc) {
								$chs[ $r->ch ] = [$surl . urlencode('ch:' . $r->ch . ' ' . $cont), $cc];
								$cnt += $cc;
							}
						}
					} catch (\Exception $e) {
						log_error($e->getMessage(), 'xunso');
					}
				}

				if ($cnt > 0) {
					$tpl     = cfg('replyTpl@taoke', "亲，一共为你找到{total}件与『{goods}』有关的商品有优惠券可用哦，\n{result}\n亲，拿优惠券手要快，手慢无哦^_^");
					$ary     = ['{goods}', '{result}', '{total}'];
					$results = [];
					foreach ($chs as $n => $ch) {
						$results[] = "<a href=\"{$ch[0]}\">【{$n}】中找到{$ch[1]}件</a>";
					}
					$results        = implode("\n\n", $results);
					$rp             = [$text, $results, $cnt];
					$msg['Content'] = str_replace($ary, $rp, $tpl);
				} else {
					$tpl            = cfg('replyTpl1@taoke', '亲，奴家找不到与『{goods}』相关的商品哇，商家太扣门，没放出优惠券，试试其它商品呗(」ﾟヘﾟ)」');
					$msg['Content'] = str_replace('{goods}', $text, $tpl);
				}
			}
		}

		return $msg;
	}

	public static function weixin_auto_reply_voice_message($msg, $xml, Wechat $chat) {
		return self::weixin_auto_reply_text_message($msg, $xml, $chat);
	}
}