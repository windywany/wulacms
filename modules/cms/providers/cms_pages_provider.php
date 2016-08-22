<?php
/*
 * page datasource provider。
 */
defined ( 'KISSGO' ) or exit ( 'No direct script access allowed' );
/**
 * 用于调用文章(页面)数据.
 *
 * @param array $conditions        	
 * @return CtsData
 */
function cms_pages_provider($con, $tplvars) {
	static $model_fields = array ();
	list ( $limit, $start, $limitStr ) = get_common_page_limit ( $con );
	ksort ( $con );
	$cache_id = md5 ( serialize ( $con ) . 'page' . $limitStr . NCACHE_PREFIX );
	if (! isset ( $con ['cts_no_cache'] )) {
		$cacher = Cache::getCache ();
		$data = $cacher->get ( $cache_id );
		if ($data) {
			list ( $d, $t ) = $data;
			return new CtsData ( $d, $t );
		}
	}
	$where = array ();
	$where ['CP.deleted'] = 0;
	$where ['CP.hidden'] = 0;
	$clen = count ( $where );
	
	$where = array_merge ( $where, get_common_page_condition_where ( $con ) );
	
	$data = CmsPage::query ( $con );
	$w = $data->getCondition ();
	
	if (! $w && $clen == count ( $where )) {
		return new CtsData ( array (), 0 );
	}
	$model = get_condition_value ( 'model', $con );
	
	if ($model) {
		if (isset ( $model_fields [$model] )) {
			$fields = $model_fields [$model];
		} else {
			$fields = dbselect ( 'name,id' )->from ( '{cms_model_field} AS CMF' )->where ( array ('model' => $model,'deleted' => 0,'cstore' => 0 ) )->toArray ();
			$model_fields [$model] = $fields;
		}
		foreach ( $fields as $f ) {
			$v = get_condition_value ( $f ['name'], $con, null );
			$alias = 'CPF_' . strtoupper ( $f ['name'] );
			if ($v != null) {
				$data->join ( '{cms_page_field} AS ' . $alias, $alias . '.page_id = CP.id AND ' . $alias . '.field_id = ' . $f ['id'], Query::INNER );
				// $where ['@'] [] = dbselect ( 'CPF.id' )->from ( '{cms_page_field} AS CPF' )->where ( array ('CPF.page_id' => imv ( 'CP.id' ),'CPF.deleted' => 0,'CPF.field_id' => $f ['id'],'CPF.val' => $v ) );
				$where [$alias . '.val'] = $v;
				$where [$alias . '.deleted'] = 0;
			} else {
				$v1 = get_condition_value ( 'regexp_' . $f ['name'], $con, null );
				if ($v1) {
					$data->join ( '{cms_page_field} AS ' . $alias, $alias . '.page_id = CP.id AND ' . $alias . '.field_id = ' . $f ['id'], Query::INNER );
					$v = '[[:<:]](' . str_replace ( ',', '|', $v1 ) . ')[[:>:]]';
					// $where ['@'] [] = dbselect ( $alias.'.id' )->from ( '{cms_page_field} AS '.$alias )->where ( array ($alias.'.page_id' => imv ( 'CP.id' ),$alias.'.deleted' => 0,$alias.'.field_id' => $f ['id'],$alias.'.val ~' => $v ) );
					$where [$alias . '.val ~'] = $v;
					$where [$alias . '.deleted'] = 0;
				}
			}
		}
		$data = apply_filter ( 'build_pages_query_for_' . $model, $data, $con );
	}
	
	$data->where ( $where );
	$sortby = get_condition_value ( 'sortby', $con );
	if ($sortby) {
		$sortbys = explode(',', $sortby);
		$order = get_condition_value ( 'order', $con, 'd' );
		$orders = explode(',', $order);
		foreach ($sortbys as $idx => $sortby){
			$order = isset($orders[$idx])?$orders[$idx]:$orders[0];
			if (! strpos ( $sortby, '.' )) {
				$data->sort ( 'CP.' . $sortby, $order );
			} else {
				$data->sort ( $sortby, $order );
			}
		}
	}
	if ($limit) {
		$data->limit ( $start, $limit );
	}
	$pp = strtolower ( get_condition_value ( 'pp', $con ) );
	if ($pp == 'false' || $pp === '0' || $pp == 'off') {
		$pp = false;
	} else {
		$pp = true;
	}
	
	if ($pp) {
		$total = $data->count ( 'CP.id' );
		$random = get_condition_value ( 'random', $con );
		if ($total == 0 && $random == 'on') {
			list ( $total, $data ) = get_random_pages ( $model, $limit, $con );
		} else {
			$data = $data->toArray ();
		}
	} else {
		$data = $data->toArray ();
		$total = count ( $data );
		$random = get_condition_value ( 'random', $con );
		if ($total == 0 && $random == 'on') {
			list ( $total, $data ) = get_random_pages ( $model, $limit, $con );
		}
	}
	if ($data) {
		$data = get_common_page_data ( $data );
	}
	if (! isset ( $con ['cts_no_cache'] )) {
		$cacher->add ( $cache_id, array ($data,$total ), 1800 );
	}
	return new CtsData ( $data, $total );
}
/**
 * 调用文章(页面)数据条件.
 *
 * @return multitype:
 */
function get_condition_for_pages() {
	$fields = get_common_page_condition_fields ();
	return $fields;
}

/**
 * 调取碎片的数据源.
 *
 * @param array $con        	
 * @return CtsData
 */
function cms_chunk_provider($con, $tplvars) {
	$cache_id = md5 ( serialize ( $con ) . 'chunk' . CCACHE_PREFIX );
	if (! isset ( $con ['cts_no_cache'] )) {
		$cacher = Cache::getCache ();
		$data = $cacher->get ( $cache_id );
		if ($data) {
			list ( $d, $t ) = $data;
			return new CtsData ( $d, $t );
		}
	}
	$id = intval ( get_condition_value ( 'id', $con, 0 ) );
	$keywords = get_condition_value ( 'keywords', $con );
	$where ['deleted'] = 0;
	$ck = dbselect ( 'id,istpl,inline,html,name' )->from ( 'cms_chunk' );
	$chunk = array ();
	if ($id) {
		$where ['id'] = $id;
		$chunk = $ck->where ( $where )->get ();
	} else if ($keywords) {
		$where ['search_index'] = convert_search_keywords ( $keywords );
		$chunk = $ck->where ( $where )->get ();
	}
	if ($chunk) {
		if ($chunk ['istpl']) { // 启用了模板
			$smarty = View::getSmarty ();
			$smarty->assign ( $tplvars );
			$chunk ['html'] = $smarty->fetch ( 'string:' . $chunk ['html'] );
		}
		if ($chunk ['inline']) {
			$chunk ['html'] = TagForm::applyTags ( $chunk ['html'] );
		}
		if (! isset ( $con ['cts_no_cache'] )) {
			$cacher->add ( $cache_id, array ($chunk,$chunk ? 1 : 0 ), 1800 );
		}
	}
	
	return new CtsData ( $chunk, $chunk ? 1 : 0 );
}
/**
 * 调取碎片的数据源条件.
 *
 * @return array
 */
function get_condition_for_chunk() {
	$fields = array ();
	$fields [] = array ('name' => 'id','widget' => 'text','label' => 'ID','note' => '碎片的ID号.' );
	$fields [] = array ('name' => 'keywords','widget' => 'text','label' => '关键词','note' => '通过关键词调用碎片,多个关键词以逗号分开.' );
	
	return $fields;
}
function get_fieldmap_for_chunk() {
	return array ('id' => 'id','name' => 'name' );
}

/**
 * 调取区块的数据源.
 *
 * @param array $con        	
 * @return CtsData
 */
function cms_block_provider($con, $tplvars) {
	ksort ( $con );
	$cache_id = md5 ( serialize ( $con ) . 'block' . BCACHE_PREFIX );
	if (! isset ( $con ['cts_no_cache'] )) {
		$cacher = Cache::getCache ();
		$data = $cacher->get ( $cache_id );
		if ($data) {
			list ( $d, $t ) = $data;
			return new CtsData ( $d, $t );
		}
	}
	$refid = get_condition_value ( 'refid', $con );
	
	$where ['CBI.deleted'] = 0;
	$ck = dbselect ( 'CBI.title,CBI.image,CBI.url,CBI.page_id,CBI.description,CBI.cvalue,
					CP.create_time,
					CP.create_uid,
					CP.update_time,
					CP.update_uid,
					CP.publish_time,
					CP.publish_uid,
					CP.status,
					CP.channel,
					CP.model,
					CP.flag_a,
					CP.flag_h,
					CP.flag_c,
					CP.flag_b,
					CP.flag_j,
					CP.view_count,
					CP.title2,
				    CP.title AS page_title,
					CP.title_color,
					CP.image AS image1,
					CP.author,
					CP.source,
					CP.tag,
					CC.url AS channel_index_url,
					CC.list_page_url AS channel_list_url,
					CC.root,
					CC.name AS channel_name,
					CM.name AS model_name' )->from ( '{cms_block_items} AS CBI' );
	$ck->join ( '{cms_block} AS CB', 'CBI.block = CB.id' );
	$ck->join ( '{cms_page} AS CP', 'CBI.page_id = CP.id' );
	$ck->join ( '{cms_channel} AS CC', 'CP.channel = CC.refid' );
	$ck->join ( '{cms_model} AS CM', 'CP.model = CM.refid' );
	$block = array ();
	if ($refid) {
		$where ['CB.refid'] = $refid;
		$bind = get_condition_value ( 'bind', $con );
		if ($bind) {
			$where ['CP.id >'] = 0;
		}
		$sortby = get_condition_value ( 'sortby', $con );
		if ($sortby) {
			$order = get_condition_value ( 'order', $con, 'd' );
			$ck->sort ( 'CBI.' . $sortby, $order );
		}
		// limit
		$limitStr = get_condition_value ( 'limit', $con );
		if ($limitStr) {
			$start = 0;
			$limit = 10;
			$limits = explode ( ',', $limitStr );
			if (count ( $limits ) == 1) {
				$limit = intval ( $limits [0] );
			} else if (count ( $limits ) > 1) {
				$start = intval ( $limits [0] );
				$limit = intval ( $limits [1] );
			}
			$ck->limit ( $start, $limit );
		}
		$block = $ck->where ( $where );
		$block = $block->toArray ();
		if ($block) {
			$form = new BlockItemForm ();
			$widgets = BlockFieldForm::loadCustomerFields ( $form, $refid );
			foreach ( $block as $key => $b ) {
				$cvalues = @json_decode ( $b ['cvalue'], true );
				if ($cvalues) {
					foreach ( $cvalues as $cf => $cv ) {
						if (isset ( $widgets [$cf] )) {
							$block [$key] [$cf] = apply_filter ( 'parse_' . $widgets [$cf] ['type'] . '_field_value', $cv );
						} else {
							$block [$key] [$cf] = $cv;
						}
					}
				}
				unset ( $block [$key] ['cvalue'] );
			}
			$block = get_common_page_data ( $block );
			if (! isset ( $con ['cts_no_cache'] )) {
				$cacher->add ( $cache_id, array ($block,count ( $block ) ), 1800 );
			}
		}
	}
	return new CtsData ( $block, count ( $block ) );
}
/**
 * 调取区块的数据源条件.
 *
 * @return array
 */
function get_condition_for_block() {
	$fields = array ();
	$fields [] = array ('name' => 'refid','widget' => 'BlockItemSelect','label' => '引用ID','note' => '区块的引用ID.' );
	$fields [] = array ('name' => 'bind','widget' => 'radio','label' => '绑定页面','note' => '是否绑定了页面','defaults' => "on=是\n=否",'default' => '' );
	$fields [] = array ('name' => 'sortby','widget' => 'select','label' => '排序字段','note' => '按哪个字段排序','defaults' => "sort=自定义\nid=编号\nupdate_time=最后修改时间\ncreate_time=创建时间" );
	$fields [] = array ('name' => 'order','widget' => 'radio','label' => '排序','note' => '升序还是降序','defaults' => "a=升序\nd=降序",'default' => 'd' );
	
	$fields [] = array ('name' => 'limit','widget' => 'text','label' => '获取多少条数据','note' => '格式为:start,limit[如:0,15]','default' => '10' );
	
	return $fields;
}
function get_fieldmap_for_block() {
	return array ('id' => 'id','name' => 'title' );
}
/**
 * 调取系统栏目的数据源.
 *
 * @param array $con        	
 * @return CtsData
 */
function cms_channel_provider($con, $tplvars) {
	$upid = get_condition_value ( 'upid', $con );
	$id = get_condition_value ( 'refid', $con );
	if (! empty ( $id )) {
		$ids = explode ( ',', trim ( $id ) );
		if ($ids) {
			$query = dbselect ( '*' )->from ( '{cms_channel} AS CH' )->where ( array ('deleted' => 0,'hidden' => 0,'refid IN' => $ids ) );
		}
	} else {
		$where = array ();
		if (is_numeric ( $upid )) {
			$where ['upid'] = abs ( $upid );
		}
		$where ['deleted'] = 0;
		$where ['hidden'] = 0;
		$query = dbselect ( '*' )->from ( '{cms_channel} AS CH' )->where ( $where );
	}
	if (isset ( $query )) {
		$cnt = dbselect ( imv ( 'COUNT(*)' ) )->from ( '{cms_page} AS CP' )->join ( '{cms_channel} AS CH1', 'CH1.refid = CP.channel' );
		$cnt->where ( array ('CH1.id IN' => imv ( 'CH.subchannels' ),'CP.deleted' => 0,'CP.hidden' => 0 ) );
		
		$query->field ( $cnt, 'page_total' );
		return new CtsData ( $query->toArray () );
	} else {
		return new CtsData ();
	}
}
/**
 * 调取系统分类的数据源条件.
 *
 * @return array
 */
function get_condition_for_channel() {
	$fields = array ();
	$fields [] = array ('name' => 'refid','label' => '编号','note' => '加载指定的栏目' );
	$fields [] = array ('name' => 'upid','label' => '上级栏目','note' => '上级栏目编号.' );
	return $fields;
}
function get_fieldmap_for_channel() {
	return array ('id' => 'refid','name' => 'name' );
}

/**
 * 调取导航菜单的数据源.
 *
 * @param array $con        	
 * @return CtsData
 */
function cms_menu_provider($con, $tplvars) {
	$upid = get_condition_value ( 'upid', $con );
	$navi = get_condition_value ( 'navi', $con );
	if (! empty ( $navi )) {
		$query = dbselect ( 'CNM.*,CP.channel,CP.url as url1' )->from ( '{cms_navi_menu} AS CNM' )->where ( array ('CNM.navi' => $navi,'CNM.deleted' => 0,'CNM.upid' => 0,'CNM.hidden' => 0 ) );
	}
	$where = array ();
	if (is_numeric ( $upid )) {
		$where ['CNM.upid'] = abs ( $upid );
	}
	if ($where) {
		$where ['CNM.deleted'] = 0;
		$where ['CNM.hidden'] = 0;
		$query = dbselect ( 'CNM.*,CP.channel,CP.url as url1' )->from ( '{cms_navi_menu} AS CNM' )->where ( $where );
	}
	if (isset ( $query )) {
		$query->join ( '{cms_page} AS CP', 'CP.id = CNM.page_id' );
		$menus = $query->asc ( 'CNM.sort' )->toArray ();
		array_walk ( $menus, '_menu_walk_filter' );
		return new CtsData ( $menus );
	} else {
		return new CtsData ();
	}
}
/**
 * 调取系统分类的数据源条件.
 *
 * @return array
 */
function get_condition_for_menu() {
	$fields = array ();
	$fields [] = array ('name' => 'navi','widget' => 'cms_catalog','defaults' => 'navi','label' => '菜单名','note' => '加载指定的菜单' );
	$fields [] = array ('name' => 'upid','label' => '上级菜单项','note' => '上级菜单项编号.' );
	return $fields;
}
function get_fieldmap_for_menu() {
	return array ('id' => 'name','name' => 'url' );
}
function _menu_walk_filter(&$menus, $id) {
	if ($menus [$id] ['url1']) {
		$menus [$id] ['url'] = $menus [$id] ['url1'];
	}
}
