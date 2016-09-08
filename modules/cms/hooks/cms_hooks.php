<?php
defined ( 'KISSGO' ) or exit ( 'No direct script access allowed' );
/**
 * 加载页面HOOK.
 *
 * @param unknown $page        	
 * @param unknown $url        	
 * @return Ambigous <CmsPage, NULL, Ambigous, unknown, multitype:>
 */
function cms_get_page_data($page, $url) {
	if ($page == null) {
		// 普通页
		$page = CmsPage::load ( $url, true );
		if ($page != null) {
			return $page;
		}
		// 模板页
		$page = CmsPage::loadTplPage ( $url );
		if ($page) {
			return $page;
		}
		// 搜索
		$urlx = CmsPageSearcher::parseURL ( $url, null, false );
		if ($urlx->prefix) {
			$page = CmsPage::load ( $urlx->prefix, true );
		}
	}
	return $page;
}
/**
 * 回收站类型
 *
 * @param unknown $types        	
 * @return unknown
 */
function hook_for_recycle_type_cms($types) {
	$types ['Page'] = __ ( '页面' );
	$types ['Block'] = __ ( '区块' );
	$types ['Block Item'] = __ ( '区块内容' );
	$types ['Tag'] = __ ( '内链' );
	$types ['Chunk'] = __ ( '碎片' );
	$types ['Model'] = __ ( '模型' );
	$types ['Model Field'] = __ ( '模型字段' );
	$types ['Block Field'] = __ ( '区块字段' );
	$types ['Catalog'] = __ ( '内容分类' );
	$types ['Channel'] = __ ( '栏目' );
	return $types;
}
/**
 * 在保存页面之前.
 *
 * @param unknown $page        	
 * @return string
 */
function hook_for_before_save_page_page($page) {
	if (empty ( $page ['description'] ) && $page ['content']) {
		$content = preg_replace ( '#<[^>]+>#ms', '', $page ['content'] );
		$content = preg_replace ( '#&.+?;#', '', $content );
		$content = preg_replace ( '#[\s　]+#u', '', $content );
		if (function_exists ( 'mb_substr' )) {
			$page ['description'] = trim ( mb_substr ( $content, 0, 100 ) );
		} else {
			$page ['description'] = trim ( substr ( $content, 0, 200 ) );
		}
	}
	return $page;
}
function hook_for_activity_types_cms($types) {
	$types ['Approve'] = __ ( '页面审核' );
	$types ['Auto Publish'] = __ ( '自动发布' );
	return $types;
}
function on_destroy_cms_page($ids) {
	if ($ids) {
		dbdelete ()->from ( '{cms_page_field}' )->where ( array ('page_id IN' => $ids ) )->exec ();
	}
}
function on_destroy_cms_channel($ids) {
	if ($ids) {
		$where ['id IN'] = $ids;
		$page_ids = dbselect ( 'CP.id' )->from ( '{cms_page} AS CP' )->join ( '{cms_channel} AS CH', 'CP.channel = CH.refid' )->where ( array ('CP.hidden' => 1,'CH.id IN' => $ids ) )->toArray ( 'id' );
		if ($page_ids) {
			dbdelete ()->from ( '{cms_page}' )->where ( array ('id IN' => $page_ids ) )->exec ();
			on_destroy_cms_page ( $page_ids );
		}
	}
}
function dashboard_right_bar_page($html) {
	$domain = cfg ( 'site_domain@cms', '' );
	if (! $domain) {
		return $html;
	}
	$time = date ( 'Y-m-d' ) . ' 00:00:00 -7 days';
	$time = strtotime ( $time );
	$today = strtotime ( date ( 'Y-m-d' ) . ' 00:00:00' );
	$sites = array ('baidu','so','chinaz' );
	foreach ( $sites as $site ) {
		$data [$site . 'Datas'] = dbselect ( 'data,create_time' )->from ( '{cms_seo_data}' )->where ( array ('site' => $site,'create_time >=' => $time ) )->asc ( 'create_time' )->toArray ( 'data', 'create_time' );
	}
	$data ['baiduNums'] = readable_num ( $data ['baiduDatas'] [$today] ? $data ['baiduDatas'] [$today] : 0 );
	$data ['baiduDatasp'] = implode ( ', ', $data ['baiduDatas'] );
	$data ['chinazNums'] = readable_num ( $data ['chinazDatas'] [$today] ? $data ['chinazDatas'] [$today] : 0 );
	$data ['chinazDatasp'] = implode ( ', ', $data ['chinazDatas'] );
	$data ['soNums'] = readable_num ( $data ['soDatas'] [$today] ? $data ['soDatas'] [$today] : 0 );
	$data ['soDatasp'] = implode ( ', ', $data ['soDatas'] );
	$data ['domain'] = $domain;
	
	$html .= view ( 'cms/views/sparks.tpl', $data )->render ();
	return $html;
}
function render_dashboard_panel_of_cms($html) {
	if (bcfg ( 'enable_report@cms', true ) && icando ( 'm:cms' )) {
		$data = array ();
		$days = array ();
		$today = date ( 'Y-m-d' ) . ' 12:00:00';
		$cwhere = array ();
		$pwhere = array ();
		$cacher = Cache::getCache ();
		for($i = 13, $j = 0; $i >= 0; $i --) {
			$days [$j] = array ($j,date ( 'm-d', strtotime ( $today . ' -' . $i . ' days' ) ) );
			$st = array (strtotime ( $today . ' -' . $i . ' days -12 hours' ),strtotime ( $today . ' -' . $i . ' days +12 hours' ) );
			$cwhere [$j] = array ('CP.create_time BETWEEN' => $st );
			$pwhere [$j] = array ('CP.publish_time BETWEEN' => $st,'CP.status' => 2 );
			$j ++;
		}
		$data ['days'] = json_encode ( $days );
		$where ['CP.deleted'] = 0;
		$where ['CP.hidden'] = 0;
		$user = whoami ();
		$uid = $user->getUid ();
		if (isset ( $_COOKIE ['only_show_my'] ) && $_COOKIE ['only_show_my']) {
			$where ['CP.create_uid'] = $uid;
			$data ['only_show_my'] = 1;
		} else {
			if (bcfg ( 'enable_group_bind@cms' )) {
				$subgroups = $user->getAttr ( 'subgroups', array () );
				$where ['CP.gid IN'] = $subgroups;
			}
			if ($uid == '1') {
				unset ( $where ['CP.gid IN'] );
			}
			$data ['only_show_my'] = 0;
		}
		$data ['maxY'] = 10;
		$data ['total'] = dbselect ()->from ( '{cms_page} AS CP' )->where ( $where )->count ( 'CP.id' );
		if ($data ['total']) {
			$where ['status'] = 0;
			$data ['total0'] = dbselect ()->from ( '{cms_page} AS CP' )->where ( $where )->count ( 'CP.id' );
			$data ['totalp0'] = $data ['total0'] / $data ['total'] * 100;
			$where ['status'] = 1;
			$data ['total1'] = dbselect ()->from ( '{cms_page} AS CP' )->where ( $where )->count ( 'CP.id' );
			$data ['totalp1'] = $data ['total1'] / $data ['total'] * 100;
			$where ['status'] = 2;
			$data ['total2'] = dbselect ()->from ( '{cms_page} AS CP' )->where ( $where )->count ( 'CP.id' );
			$data ['totalp2'] = $data ['total2'] / $data ['total'] * 100;
			$where ['status'] = 3;
			$data ['total3'] = dbselect ()->from ( '{cms_page} AS CP' )->where ( $where )->count ( 'CP.id' );
			$data ['totalp3'] = $data ['total3'] / $data ['total'] * 100;
			$where ['status'] = 4;
			$data ['total4'] = dbselect ()->from ( '{cms_page} AS CP' )->where ( $where )->count ( 'CP.id' );
			$data ['totalp4'] = $data ['total4'] / $data ['total'] * 100;
			unset ( $where ['status'] );
			$where ['CP.model'] = '_customer_page';
			$data ['cp_total'] = dbselect ()->from ( '{cms_page} AS CP' )->where ( $where )->count ( 'CP.id' );
			unset ( $where ['CP.model'] );
			$where ['CH.is_topic_channel'] = 1;
			$data ['tp_total'] = dbselect ()->from ( '{cms_page} AS CP' )->join ( '{cms_channel} AS CH', 'CP.channel=CH.refid' )->where ( $where )->count ( 'CP.id' );
			$data ['pg_total'] = $data ['total'] - $data ['tp_total'] - $data ['cp_total'];
			$data ['cp_total'] = $data ['cp_total'] / $data ['total'] * 100;
			$data ['pg_total'] = $data ['pg_total'] / $data ['total'] * 100;
			$data ['tp_total'] = $data ['tp_total'] / $data ['total'] * 100;
			unset ( $where ['CH.is_topic_channel'] );
			
			$datas = array ();
			$pdatas = array ();
			foreach ( $cwhere as $i => $w ) {
				$day = $days [$i] [1] . '-' . ($_COOKIE ['only_show_my'] ? '1' : '0');
				if ($i < 13) {
					$total = $cacher->get ( 'cms_stat_day_' . $day, false );
					$ptotal = $cacher->get ( 'cms_pstat_day_' . $day, false );
				} else {
					$total = false;
					$ptotal = false;
				}
				if ($total === false) {
					$total = dbselect ()->from ( '{cms_page} AS CP' )->where ( $where )->where ( $w, true )->count ( 'CP.id' );
					if ($i < 13) {
						$cacher->add ( 'cms_stat_day_' . $day, $total, 31536000 );
					}
				}
				if ($ptotal === false) {
					$ptotal = dbselect ()->from ( '{cms_page} AS CP' )->where ( $where )->where ( $pwhere [$i], true )->count ( 'CP.id' );
					if ($i < 13) {
						$cacher->add ( 'cms_pstat_day_' . $day, $ptotal, 31536000 );
					}
				}
				$datas [$i] = array ($i,$total );
				$pdatas [$i] = array ($i,$ptotal );
				
				$data ['maxY'] = max ( $total, $data ['maxY'], $ptotal );
			}
			$data ['chartDatas'] = json_encode ( $datas );
			$data ['chartPDatas'] = json_encode ( $pdatas );
		} else {
			$data ['total0'] = $data ['total1'] = $data ['total2'] = $data ['total3'] = $data ['total4'] = 0;
			$data ['pg_total'] = $data ['tp_total'] = $data ['cp_total'] = 0;
			$data ['chartPDatas'] = $data ['chartDatas'] = '[]';
			$data ['total'] = 0;
		}
		$html .= view ( 'cms/views/reports.tpl', $data )->render ();
	}
	return $html;
}
// 定时发布功能
function hook_for_crontab_page($time) {
	// 定时发布
	if (bcfg ( 'enable_bentch_publish@cms' )) {
		$time = time ();
		$rst = dbupdate ( '{cms_page}' )->set ( array ('status' => 2,'update_time' => time () ) )->where ( array ('status' => 4,'publish_time <=' => $time ) )->exec ( null );
		if ($rst === false) {
			ActivityLog::info ( '自动发布出错啦,数据库更新失败.', 'Auto Publish' );
		}
	}
	if (bcfg ( 'enable_cron_update@cms' )) {
		$channels = cfg ( 'update_chs@cms' );
		if ($channels) {
			$channels = explode ( ',', $channels );
			$update_sub = bcfg ( 'update_sub@cms' );
			$update_cnt = icfg ( 'update_cnt@cms', 10 );
			if ($update_cnt <= 0) {
				$update_cnt = 10;
			}
			$update_method = bcfg ( 'update_method@cms' );
			$where ['refid'] = '';
			$where1 ['CP.deleted'] = 0;
			if (bcfg ( 'disable_approving@cms' )) {
				$where1 ['CP.status'] = 2;
			}
			$where1 ['CH.id IN'] = null;
			if (! $update_method) {
				$max_id = dbselect ( imv ( 'MAX(id)' ) )->from ( '{cms_page} AS CPMAX' )->__toString ();
				$min_id = dbselect ( imv ( 'MIN(id)' ) )->from ( '{cms_page} AS CPMIN1' )->__toString ();
				$min_id1 = dbselect ( imv ( 'MIN(id)' ) )->from ( '{cms_page} AS CPMIN2' )->__toString ();
				$rand = '((' . $max_id . ') - (' . $min_id . ')) * RAND() + (' . $min_id1 . ')';
				$where1 ['CP.id >='] = imv ( $rand );
			}
			foreach ( $channels as $c ) {
				$where ['refid'] = $c;
				if ($update_sub) {
					$c = dbselect ( 'subchannels' )->from ( '{cms_channel}' )->where ( $where )->get ( 'subchannels' );
				} else {
					$c = dbselect ( 'id' )->from ( '{cms_channel}' )->where ( $where )->get ( 'id' );
				}
				if ($c) {
					$c = safe_ids2 ( $c );
					$pages = dbselect ( 'CP.id' )->from ( '{cms_page} AS CP' )->join ( '{cms_channel} AS CH', 'CP.channel = CH.refid' );
					$where1 ['CH.id IN'] = $c;
					if ($update_method) {
						$pages->asc ( 'CP.update_time' );
					}
					$pages->where ( $where1 )->limit ( 0, $update_cnt );
					foreach ( $pages as $page ) {
						$data ['update_time'] = time () - rand ( 0, 30 );
						dbupdate ( '{cms_page}' )->set ( $data )->where ( $page )->exec ();
					}
				}
			}
		}
	}
	// 抓取监测数据
	$domain = cfg ( 'site_domain@cms', '' );
	if ($domain) {
		$today = date ( 'Y-m-d' ) . ' 00:00:00';
		$last_grabed_date = cfg ( 'last_grabbed_date@cms', '' );
		// 已经抓取过啦
		if ($today != $last_grabed_date) {
			
			$data ['create_time'] = strtotime ( $today );
			// 百度
			$content = @file_get_contents ( 'http://www.baidu.com/s?ie=utf-8&wd=site%3A' . $domain );
			if (preg_match ( '#<div class="nums">[^\d]+([, \d]+).*</div>#', $content, $m )) {
				$data ['data'] = intval ( preg_replace ( '#[^\d]#', '', $m [1] ) );
			} else {
				$data ['data'] = 0;
			}
			$data ['site'] = 'baidu';
			dbsave ( $data, array ('site' => 'baidu','create_time' => $data ['create_time'] ) )->into ( '{cms_seo_data}' )->save ();
			// 搜搜
			$content = @file_get_contents ( 'http://www.so.com/s?q=site%3A' . $domain );
			if (preg_match ( '#<span class="nums"[^>]*>[^\d]+([, \d]+).*?</span>#', $content, $m )) {
				$data ['data'] = intval ( preg_replace ( '#[^\d]#', '', $m [1] ) );
			} else {
				$data ['data'] = 0;
			}
			$data ['site'] = 'so';
			dbsave ( $data, array ('site' => 'so','create_time' => $data ['create_time'] ) )->into ( '{cms_seo_data}' )->save ();
			// 爱站
			$content = @file_get_contents ( 'http://mytool.chinaz.com/baidusort.aspx?host=' . $domain );
			$idx = strpos ( $content, '"siteinfo"' );
			if ($idx > 0) {
				$content = substr ( $content, $idx, 500 );
				if (preg_match_all ( '#font[^>]+>([^<]+)</font>#i', $content, $ms )) {
					$data ['data'] = intval ( preg_replace ( '#[^\d]#', '', $ms [1] [1] ) );
				}
			} else {
				$data ['data'] = 0;
			}
			$data ['site'] = 'chinaz';
			dbsave ( $data, array ('site' => 'chinaz','create_time' => $data ['create_time'] ) )->into ( '{cms_seo_data}' )->save ();
			set_cfg ( 'last_grabbed_date', $today, 'cms' );
		}
	}
	// 根据TAG生成分词词典
	TagForm::generateScwsDictFile ();
}