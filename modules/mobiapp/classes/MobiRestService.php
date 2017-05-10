<?php

class MobiRestService {
	/**
	 *
	 * @param RestServer $server
	 *
	 * @return RestServer
	 */
	public static function on_init_rest_server($server) {
		$server->registerClass(new MobiRestService (), '1.0', 'cms');
		$server->registerClass(new \mobiapp\classes\AppRestService(), '1.0', 'app');

		return $server;
	}

	/**
	 * 取移动栏目列表.
	 *
	 * @param array  $param
	 * @param string $key
	 * @param string $secret
	 *
	 * @return array
	 */
	public function rest_catalog($param, $key, $secret) {
		$cacher   = Cache::getCache();
		$channels = $cacher->get('mobi_channel_list');
		if (!$channels && !is_array($channels)) {
			$channels = dbselect('refid as id,name,hidden,sort,flags as flag')->from('{mobi_channel}')->where(array('deleted' => 0))->toArray();
			if ($channels) {
				foreach ($channels as $id => $ch) {
					$channels [ $id ] ['expire'] = 600;
				}
				$cacher->add('mobi_channel_list', $channels);
			} else {
				$channels = array();
			}
		}

		return array('error' => 0, 'data' => ['list' => $channels, 'view' => trailingslashit(cfg('murl@mobiapp', DETECTED_ABS_URL)) . tourl('mobiapp', false), 'media' => the_media_src('')]);
	}

	/**
	 * 栏目数据.
	 *
	 * @param array  $param
	 * @param string $key
	 * @param string $secret
	 *
	 * @return array
	 */
	public function rest_catalog_data($param, $key, $secret) {
		$cacher  = Cache::getCache();
		$cacheId = RestClient::chucksum($param, 'catalog_data');
		$cache   = $cacher->get($cacheId);
		if ($cache) {
			$datas = json_decode($cache, true);

			return ['error' => 0, 'data' => $datas];
		}
		$cid   = get_condition_value('cid', $param, 0);
		$page  = intval(get_condition_value('page', $param, 0));
		$limit = intval(get_condition_value('limit', $param, 20));

		$min_behot_time = intval(get_condition_value('min_time', $param, 0));

		if (isset ($param ['max_time'])) {
			$max_behot_time = intval(get_condition_value('max_time', $param, 0));
		}

		$has_carousel = false;
		$flag         = '';
		if (empty ($cid)) {
			$flag         = get_condition_value('flag', $param);
			$tag          = get_condition_value('tag', $param);
			$has_carousel = true;
			if (!empty ($flag) && in_array($flag, array('a', 'h', 'c', 'b'))) {
				$where [ 'CP.flag_' . $flag ] = 1;
			} else if (!empty($tag)) {
				// 标签读取
				$flag = $tag;
				$fs   = bcfg('enable_full@cms', false) && extension_loaded('scws');
				if ($fs) {
					$dict = TagForm::getDictFile();
					$fss  = get_keywords(null, $tag, null, $dict, true);
					if ($fss[0]) {
						$where ['CP.search_index MATCH'] = $fss[1];
					}
				}
				if (!isset($fss) || !$fss[0]) {
					$where ['CP.keywords LIKE'] = '%' . $tag . '%';
				}
			} else {
				return array('error' => 400, 'message' => 'cid,flag,tag必须有一个不为空');
			}
		} else {
			if ($cid == 1) {
				$where ['MCP.is_carousel'] = 0;
			} else {
				$where ['MCP.channel'] = $cid;
			}
			// 只有第一页时才需要查询是否有轮播滚动
			if ($page == 0 && $min_behot_time == 0 && $cid != 1) {
				$has_carousel = dbselect()->from('{mobi_channel}')->where(array('refid' => $cid))->get('has_carousel');
			}
		}

		$where ['MCP.deleted'] = 0;
		$where ['MCP.status']  = 1;
		$where ['CP.deleted']  = 0;
		$where ['CP.hidden']   = 0;
		$orderType             = 'day';
		if ($min_behot_time > 0) {
			$page                         = 0;
			$where ['MCP.publish_time >'] = $min_behot_time;
			$orderType                    = 'time';
		}
		if (isset ($max_behot_time)) {
			$where ['MCP.publish_time <'] = $max_behot_time;
			$orderType                    = 'time';
		}
		$total = 0;
		$datas = array('cid' => $cid ? $cid : $flag, 'last_modified_time' => time(), 'list' => []);
		$views = MobiListView::getListViews();
		if ($page == 0 && $has_carousel) {
			$where ['MCP.is_carousel'] = 1;
			$carsouels                 = $this->getCatagoryData($where, 0, 5, $total);
			if ($total > 0) {
				$row = array('type' => '1', 'items' => array());
				foreach ($carsouels as $p) {
					$row ['items'] [] = $this->getListViewData($p, $views);
				}
				$datas ['list'] [] = $row;
			}
		}
		$where ['MCP.is_carousel'] = 0;
		$rows                      = $this->getCatagoryData($where, $page, $limit, $total, $orderType);
		if ($total > 0) {
			foreach ($rows as $p) {
				$datas ['list'] [] = self::getListViewData($p, $views);
			}
		}
		if ($total == 0 || $total < ($page + 1) * $limit) {
			$datas ['more'] = false;
		} else {
			$datas ['more'] = true;
		}
		$cacher->add($cacheId, json_encode($datas));

		return ['error' => 0, 'data' => $datas];
	}

	public function rest_page_data($param, $key, $secret) {
		if (empty($param['id'])) {
			return ['error' => 400, 'message' => '参数错误'];
		}
		$where['MCP.page_id']  = $param['id'];
		$where ['MCP.deleted'] = 0;
		$where ['MCP.status']  = 1;
		$where ['CP.deleted']  = 0;
		$where ['CP.hidden']   = 0;
		$rows                  = dbselect('MCP.id,MCP.page_view,MCP.list_view,MCP.custom_data,MCP.flags AS flag,MCP.view_url AS url,MCP.publish_time,MCP.title,MCP.page_id');
		$rows->field('CP.view_count,CP.allow_comment,CP.keywords as tags,CP.image,CP.model');
		$rows->from('{mobi_page} AS MCP')->join('{cms_page} AS CP', 'MCP.page_id = CP.id');

		$rows->where($where);

		$page = $rows->get(0);
		if (!$page) {
			return ['error' => 404, 'message' => '页面不存在'];
		}
		$views    = MobiListView::getListViews();
		$listdata = self::getListViewData($page, $views);

		$page = CmsPage::loadCustomerFieldValues($page['page_id'], $listdata, $page['model']);
		unset($page['search_tags'], $page['my_tags'], $page['page_id']);

		return ['error' => 0, 'data' => $page];
	}

	/**
	 * 取当前栏目里的内容.
	 *
	 * @param array  $where
	 * @param int    $start
	 * @param int    $limit
	 * @param number $total
	 * @param string $orderType 排序方式
	 *
	 * @return QueryBuilder
	 */
	private function getCatagoryData($where, $start = 0, $limit = 20, &$total = null, $orderType = 'day') {
		$rows = dbselect('MCP.id,MCP.page_view,MCP.list_view,MCP.custom_data,MCP.flags AS flag,MCP.view_url AS url,MCP.publish_time,MCP.title,MCP.page_id');
		$rows->field('CP.view_count,CP.allow_comment,CP.keywords as tags,CP.image');
		$rows->from('{mobi_page} AS MCP')->join('{cms_page} AS CP', 'MCP.page_id = CP.id');
		fire('mobiapp_page_query', $rows, $where);
		$rows->where($where);
		if ($orderType == 'day') {
			$rows->sort('MCP.publish_day', 'd');
			$rows->sort('MCP.sort', 'a');
		} else {
			$rows->sort('MCP.publish_time', 'd');
		}
		$rows->limit($start * $limit, $limit);
		// 总数
		if (!is_null($total)) {
			$total = $rows->count('MCP.id');
		}

		return $rows;
	}

	/**
	 * 取列表数据.
	 *
	 * @param array $p
	 * @param array $views
	 *
	 * @return array
	 */
	public static function getListViewData($p, $views) {
		$lv         = $p ['list_view'];
		$customData = @json_decode($p ['custom_data'], true);
		$cnt        = array('id' => $p ['page_id'], 'title' => $p ['title'], 'time' => $p ['publish_time'], 'type' => $lv, 'view' => $p ['page_view'], 'acmt' => $p['allow_comment']);
		if ($customData ['desc']) {
			$cnt ['desc'] = $customData ['desc'];
		}
		if ($p ['url']) {
			$cnt ['url'] = $p ['url'];
		}
		if ($p ['flag']) {
			$cnt ['flag'] = $p ['flag'];
		}
		if (isset($p ['comments'])) {
			$cnt ['ccnt'] = $p ['comments'];
		}
		if (isset($p['model'])) {
			$cnt['model'] = $p['model'];
		}
		$cnt['vcnt'] = $p['view_count'];
		$cnt['img']  = $p['image'];
		if (isset ($views [ $lv ])) {
			$views [ $lv ] ['clz']->fillListViewData($cnt, $customData);
		}
		if ($p['tags']) {
			$cnt['tags'] = explode(',', $p['tags']);
		} else {
			$cnt['tags'] = [];
		}
		$cnt = apply_filter('mobiapp_view_data', $cnt, $p, $views);

		return $cnt;
	}
}