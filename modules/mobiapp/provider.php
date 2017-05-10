<?php
/**
 * 用于调用软件数据.
 *
 * @param array $con
 *
 * @return CtsData
 */
function mobiapp_page_provider($con, $tplvars) {
	list ($limit, $start, $limitStr) = get_common_page_limit($con);
	ksort($con);
	$cache_id = md5(serialize($con) . 'mobiapp' . $limitStr);
	$cacher   = Cache::getCache();
	$data     = $cacher->get($cache_id);
	if ($data) {
		// 缓存命中
		list ($d, $t, $hasMore) = $data;
		$cdata             = new CtsData ($d, $t);
		$cdata ['hasMore'] = $hasMore;

		return $cdata;
	}

	$service                   = new MobiRestService ();
	$params                    = array('cid' => get_condition_value('channel', $con));
	$params ['limit']          = $limit;
	$params ['min_behot_time'] = get_condition_value('min_behot_time', $con, 0);
	$max_behot_time            = get_condition_value('max_behot_time', $con, 0);
	if ($max_behot_time > 0) {
		$params ['max_behot_time'] = get_condition_value('max_behot_time', $con, 0);
	}
	$datas   = $service->rest_catalog_data($params, null, null);
	$hasMore = false;
	if (isset ($datas ['data'])) {
		$data    = $datas ['data'];
		$hasMore = $datas ['more'];
	} else {
		$data = array();
	}
	$cacher->add($cache_id, array($data, count($data), $hasMore), 1800);
	$cdata             = new CtsData ($data, count($data));
	$cdata ['hasMore'] = $hasMore;

	return $cdata;
}

/**
 * 调用mobiapp数据条件.
 *
 * @return array
 */
function get_condition_for_mobiapp() {
	$fields   = get_common_page_condition_fields();
	$channels = dbselect('refid,name')->from('{mobi_channel}')->where(array('deleted' => 0))->toArray('name', 'refid');
	$chs      = array('请选择移动栏目');
	foreach ($channels as $id => $n) {
		$chs [] = $id . '=' . $n;
	}
	$chs                = implode("\n", $chs);
	$fields ['channel'] = array('name' => 'channel', 'widget' => 'select', 'label' => '移动栏目', 'defaults' => $chs);
	unset ($fields ['model']);

	return $fields;
}
