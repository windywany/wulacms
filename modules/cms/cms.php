<?php
defined ( 'KISSGO' ) or exit ( 'No direct script access allowed' );
bind ( 'do_admin_layout', 'hook_for_do_admin_layout_admin@hooks/do_admin_layout', 2 );
bind ( 'get_cms_catalog_types', 'filter_for_get_catelog_types@hooks/get_catelog_types', 1 );
bind ( 'get_custom_field_widgets', 'hook_for_get_custom_field_widgets@hooks/get_custom_field_widgets', 2 );
bind ( 'on_init_dynamicform_PageForm', array ('PageForm','init' ) );
bind ( 'on_init_dynamicform_TopicForm', array ('TopicForm','init' ) );
bind ( 'on_init_dynamicform_CPageForm', array ('CPageForm','init' ) );
bind ( 'on_init_dynamicform_AdminPreferenceForm', array ('AdminPreferenceBaseForm','init' ) );
bind ( 'get_page_data', 'cms_get_page_data@hooks/cms_hooks', 1000000, 2 );
bind ( 'on_destroy_cms_page', 'on_destroy_cms_page@hooks/cms_hooks' );
bind ( 'on_destroy_cms_channel', 'on_destroy_cms_channel@hooks/cms_hooks' );
bind ( 'get_activity_log_type', 'hook_for_activity_types_cms@hooks/cms_hooks' );
bind ( 'get_recycle_content_type', 'hook_for_recycle_type_cms@hooks/cms_hooks' );
bind ( 'get_acl_resource', 'filter_for_cms_acl_resource@hooks/get_acl_resource' );
bind ( 'before_save_page', 'hook_for_before_save_page_page@hooks/cms_hooks' );
bind ( 'crontab', 'hook_for_crontab_page@hooks/cms_hooks', 1 );
bind ( 'on_render_navi_btns', 'hook_for_on_render_navi_btns_page@hooks/do_admin_layout' );
if (bcfg ( 'enable_short@cms' )) {
	bind ( 'on_render_dashboard_shortcut', 'hook_for_render_dashboard_shortcut_cms@hooks/do_admin_layout' );
}
bind ( 'on_init_dashboard_ui', 'hook_for_cms_init_dashboard_ui@hooks/do_admin_layout' );
bind ( 'on_init_rest_server', '&CmsRestService' );
bind ( 'dashboard_sparks_bar', 'dashboard_right_bar_page@hooks/cms_hooks' );
bind ( 'render_dashboard_panel', 'render_dashboard_panel_of_cms@hooks/cms_hooks' );
bind ( 'init_template_smarty_engine', 'hook_for_cms_init_smarty_engine@hooks/cms_hooks' );
bind ( 'build_page_common_query', 'build_page_common_query_of_cms', 10, 2 );
register_cts_provider ( 'pages', array ('cms_pages_provider',ksg_include ( 'cms', 'providers/cms_pages_provider.php', true ) ), '页面调用标签', '用于调用文章.', true );
register_cts_provider ( 'chunk', array ('cms_chunk_provider',ksg_include ( 'cms', 'providers/cms_pages_provider.php', true ) ), '碎片调用标签', '用于碎片调用.', true );
register_cts_provider ( 'block', array ('cms_block_provider',ksg_include ( 'cms', 'providers/cms_pages_provider.php', true ) ), '区块调用标签', '用于区块调用.', true );
register_cts_provider ( 'channel', array ('cms_channel_provider',ksg_include ( 'cms', 'providers/cms_pages_provider.php', true ) ), '栏目调用标签', '用于栏目调用.', true );
register_cts_provider ( 'menu', array ('cms_menu_provider',ksg_include ( 'cms', 'providers/cms_pages_provider.php', true ) ), '导航调用标签', '用于导航菜单调用.', true );
/**
 * 取页面的模型处理器.
 *
 * @param string $model        	
 * @return IContentModel
 */
function get_page_content_model($model) {
	static $models = array ();
	
	if (is_string ( $model ) && $model) {
		if (isset ( $models [$model] )) {
			return $models [$model];
		}
		$contentModel = apply_filter ( 'load_' . $model . '_model', null );
		if ($contentModel instanceof IContentModel) {
			$models [$model] = $contentModel;
			return $contentModel;
		}
	}
	return null;
}
/**
 * 全局默认的页面查询字段.
 *
 * @param string $tip        	
 * @return mixed
 */
function get_common_page_condition_fields($tip = '页面') {
	$fields ['id'] = array ('name' => 'id','widget' => 'text','label' => $tip . '编号','note' => '多个ID使用“,”分隔' );
	$fields ['keywords'] = array ('name' => 'keywords','widget' => 'text','label' => '关键词','note' => '通过关键词调用' . $tip . ',多个关键词以逗号分开.' );
	$fields ['title'] = array ('name' => 'title','widget' => 'text','label' => '页面标题','note' => '通过页面标题调用' . $tip . '.' );
	$fields ['model'] = array ('name' => 'model','widget' => 'model_select','label' => '内容模型','note' => $tip . '的内容模型' );
	$fields ['channel'] = array ('name' => 'channel','widget' => 'channel_select','label' => '栏目','note' => $tip . '所在的栏目' );
	$fields ['subch'] = array ('name' => 'subch','widget' => 'checkbox','label' => '包括下级栏目' . $tip,'note' => '','defaults' => "是" );
	$fields ['type'] = array ('name' => 'type','widget' => 'radio','label' => $tip . '类型','note' => '专题或普通' . $tip,'default' => '','defaults' => "=不指定\ntopic=专题\npage=页面" );
	$fields ['flags'] = array ('name' => 'flags','widget' => 'checkbox','label' => $tip . '属性','note' => '可以多选.','defaults' => "h=头条\nc=推荐\na=特荐\nb=加粗\nj=跳转" );
	$fields ['nflags'] = array ('name' => 'nflags','widget' => 'checkbox','label' => $tip . '无此属性','note' => '可以多选.','defaults' => "h=头条\nc=推荐\na=特荐\nb=加粗\nj=跳转" );
	$fields ['tags'] = array ('name' => 'tags','widget' => 'text','label' => '标签','note' => '多个标签使用“,”分隔' );
	$fields ['image'] = array ('name' => 'image','widget' => 'radio','label' => '缩略图','note' => $tip . '是否有插图','default' => '','defaults' => "=不指定\non=是\noff=否" );
	$fields ['publish'] = array ('name' => 'publish','widget' => 'radio','label' => '发布时间','note' => $tip . '发布时间选项','default' => '','defaults' => "=不指定\ntoday=当天\n-1 day=一天内\n-1 week=一周内\n-1 month=一月内\n-6 months=半年内" );
	$fields ['random'] = array ('name' => 'random','widget' => 'checkbox','label' => '如果未取到则随机取数据' . $tip,'note' => '','defaults' => "是" );
	$fields = apply_filter ( 'get_common_page_condition_fields', $fields );
	$fields ['sortby'] = array ('name' => 'sortby','widget' => 'select','label' => '排序字段','note' => '按哪个字段排序','defaults' => "publish_time=发布时间\nupdate_time=最后修改时间\ncreate_time=创建时间\nview_count=浏览次数\nid=页面编号\ndisplay_sort=显示顺序" );
	$fields ['order'] = array ('name' => 'order','widget' => 'radio','label' => '排序','note' => '升序还是降序','defaults' => "a=升序\nd=降序",'default' => 'd' );
	$fields ['limit'] = array ('name' => 'limit','widget' => 'text','label' => '获取多少条数据','note' => '格式为:start,limit[如:0,15]','default' => '10' );
	$fields ['pp'] = array ('name' => 'pp','widget' => 'radio','label' => '启用分页','note' => '只有在列表页才需要启用分页','default' => 'off','defaults' => "on=是\noff=否" );
	return $fields;
}
/**
 * 全局默认查询条件.
 *
 * @param array $con        	
 */
function get_common_page_condition_where($con) {
	$where = array ();
	$id = get_condition_value ( 'id', $con );
	if ($id) {
		$idin = 'IN';
		if ($id {0} == '~') {
			$id = substr ( $id, 1 );
			$idin = '!IN';
		}
		$id = safe_ids ( $id, ',', true );
		$where ['CP.id ' . $idin] = $id;
	}
	$nid = get_condition_value ( 'nid', $con );
	if ($nid) {
		$id = safe_ids ( $nid, ',', true );
		$where ['CP.id !IN'] = $id;
	}
	$model = get_condition_value ( 'model', $con );
	if ($model) {
		if ($model {0} == '~') {
			$where ['CP.model <>'] = substr ( $model, 1 );
		} else {
			$where ['CP.model'] = $model;
		}
	}
	$channel = get_condition_value ( 'channel', $con );
	if ($channel) {
		$cin = 'IN';
		if ($channel {0} == '~') {
			$channel = substr ( $channel, 1 );
			$cin = '!IN';
		}
		$channel = explode ( ',', $channel );
		$subch = get_condition_value ( 'subch', $con );
		if ($subch) {
			$sids = dbselect ( 'subchannels' )->from ( '{cms_channel}' )->where ( array ('refid IN' => $channel ) )->toArray ();
			$subids = array ();
			foreach ( $sids as $id ) {
				$subids [] = $id ['subchannels'];
			}
			$subids = safe_ids ( implode ( ',', $subids ), ',', true );
			if (empty ( $subids )) {
				$where ['CP.channel ' . $cin] = $channel;
			} else {
				$where ['CC.id ' . $cin] = $subids;
				$where ['CC.deleted'] = 0;
			}
		} else {
			$where ['CP.channel ' . $cin] = $channel;
		}
	}
	$type = get_condition_value ( 'type', $con );
	
	if ($type == 'topic') {
		$where ['CC.is_topic_channel'] = 1;
	} else if ($type == 'page') {
		$where ['CC.is_topic_channel'] = 0;
	}
	
	$title = get_condition_value ( 'title', $con );
	if ($title) {
		$where ['CP.title LIKE'] = '%' . $title . '%';
	} else {
		$keywords = get_condition_value ( 'keywords', $con );
		if ($keywords) {
			$keywords = str_replace ( array (' ',',','，' ), ' ', $keywords );
			$t = '%' . $keywords . '%';
			$keywords = convert_search_keywords ( $keywords );
			$where [] = array ('search_index MATCH' => $keywords,'||CP.title LIKE' => $t,'||CP.title2 LIKE' => $t );
		}
	}
	$flags = get_condition_value ( 'flags', $con );
	if ($flags) {
		$flags = explode ( ',', $flags );
		foreach ( $flags as $flag ) {
			$where ['flag_' . $flag] = 1;
		}
	}
	$flags = get_condition_value ( 'nflags', $con );
	if ($flags) {
		$flags = explode ( ',', $flags );
		foreach ( $flags as $flag ) {
			$where ['flag_' . $flag] = 0;
		}
	}
	$tags = get_condition_value ( 'tags', $con );
	if ($tags) {
		$tags = explode ( ',', $tags );
		$where ['CP.tag IN'] = $tags;
	}
	$image = get_condition_value ( 'image', $con );
	if ($image == 'on') {
		$where ['CP.image <>'] = '';
	} else if ($image == 'off') {
		$where ['CP.image'] = '';
	}
	$publish = get_condition_value ( 'publish', $con );
	if ($publish) {
		parse_time_condition ( 'CP.publish_time', $publish, $where );
	}
	$publish = get_condition_value ( 'ctime', $con );
	if ($publish) {
		parse_time_condition ( 'CP.create_time', $publish, $where );
	}
	$publish = get_condition_value ( 'mtime', $con );
	if ($publish) {
		parse_time_condition ( 'CP.update_time', $publish, $where );
	}
	return $where;
}
function parse_time_condition($field, $cstr, &$where) {
	if ($cstr) {
		if ($cstr == 'today') {
			$day = $day1 = date ( 'Y-m-d' );
		} else {
			$day = date ( 'Y-m-d', strtotime ( trim ( $cstr ) ) );
			$day1 = date ( 'Y-m-d' );
		}
		$timec [] = strtotime ( $day . ' 00:00:00' );
		$timec [] = strtotime ( $day1 . ' 23:59:59' );
		$where [$field . ' BETWEEN'] = $timec;
	}
}
/**
 * 取limit。
 *
 * @param array $con        	
 * @return multitype:number Ambigous <string, unknown>
 */
function get_common_page_limit($con) {
	static $router = false;
	if ($router === false) {
		$router = Router::getRouter ();
	}
	$limit = 0;
	$start = 0;
	// limit
	$limitStr = get_condition_value ( 'limit', $con, '10' );
	if ($limitStr) {
		$limit = 10;
		if (isset ( $con ['offset'] )) {
			$start = intval ( get_condition_value ( 'offset', $con ) );
		} else {
			$start = $router->getCurrentPageNo ();
		}
		$limits = explode ( ',', $limitStr );
		if (count ( $limits ) == 1) {
			$limit = intval ( $limits [0] );
			$start = $start * $limit;
		} else if (count ( $limits ) > 1) {
			$start = intval ( $limits [0] );
			$limit = intval ( $limits [1] );
		}
		$limitStr = $start . ',' . $limit;
	}
	return array ($limit,$start,$limitStr );
}
/**
 * 取随机文章.
 *
 * @param unknown $model        	
 * @param unknown $limit        	
 * @return multitype:number multitype:
 */
function get_random_pages($model, $limit, $con = array()) {
	if ($model) {
		$where = array ('CP.model' => $model );
	} else {
		$where = array ('CP.model !IN' => array ('channel_index','channel_list','_customer_page' ) );
	}
	$data = CmsPage::query ();
	$channel = get_condition_value ( 'channel', $con );
	if ($channel) {
		$channel = explode ( ',', $channel );
		$subch = get_condition_value ( 'subch', $con );
		if ($subch) {
			$sids = dbselect ( 'subchannels' )->from ( '{cms_channel}' )->where ( array ('refid IN' => $channel ) )->toArray ();
			$subids = array ();
			foreach ( $sids as $id ) {
				$subids [] = $id ['subchannels'];
			}
			$subids = safe_ids ( implode ( ',', $subids ), ',', true );
			if (empty ( $subids )) {
				$where ['CP.channel IN'] = $channel;
			} else {
				$where ['CC.id IN'] = $subids;
				$where ['CC.deleted'] = 0;
			}
		} else {
			$where ['CP.channel IN'] = $channel;
		}
	}
	$data->rand ();
	
	$data->where ( $where, false );
	$data->limit ( 0, $limit ? $limit : 1 );
	$data = $data->toArray ();
	$total = count ( $data );
	return array ($total,$data );
}
/**
 * 取页面的状态值.
 *
 * @return array
 */
function get_cms_page_status() {
	static $status = false;
	if ($status === false) {
		$status = apply_filter ( 'get_cms_page_status', CmsPage::$PAGE_STATUS );
	}
	return $status;
}
function get_common_page_data($data) {
	global $__ksg_rtk_hooks;
	$ids = array ();
	foreach ( $data as $key => $d ) {
		if (isset ( $d ['page_id'] ) && $d ['page_id']) {
			$d ['id'] = $d ['page_id'];
			$data [$key] = $d;
			$ids [] = $d ['id'];
		} else if (isset ( $d ['id'] ) && $d ['id']) {
			$ids [] = $d ['id'];
		} else {
			continue;
		}
		if (isset ( $d ['model'] )) {
			$contentModel = get_page_content_model ( $d ['model'] );
			if ($contentModel) {
				$contentModel->load ( $data [$key], $d ['id'] );
			}
		}
	}
	if (! $ids) {
		return $data;
	}
	$values = dbselect ( 'val,name,type,page_id' )->from ( '{cms_page_field} AS CPF' )->where ( array ('page_id IN' => $ids,'CPF.deleted' => 0,'CMF.deleted' => 0,'CMF.cstore' => 0 ) );
	$values->join ( '{cms_model_field} AS CMF', 'CPF.field_id = CMF.id' );
	$datas = array ();
	foreach ( $values as $v ) {
		$hk = 'parse_' . $v ['type'] . '_field_value';
		if (isset ( $__ksg_rtk_hooks [$hk] )) {
			$datas [$v ['page_id']] [$v ['name']] = apply_filter ( $hk, $v ['val'] );
		} else {
			$datas [$v ['page_id']] [$v ['name']] = $v ['val'];
		}
	}
	if ($datas) {
		foreach ( $data as $idx => $dx ) {
			if (! isset ( $dx ['id'] )) {
				continue;
			}
			$pid = $dx ['id'];
			if (isset ( $datas [$pid] )) {
				$data [$idx] = array_merge ( $data [$idx], $datas [$pid] );
				unset ( $datas [$pid] );
			}
			if (! $datas) {
				break;
			}
		}
	}
	return $data;
}
if (! function_exists ( 'smarty_function_page' )) {
	/**
	 * 支持使用id直接调取页面.
	 *
	 * @param unknown $params        	
	 * @param unknown $template        	
	 */
	function smarty_function_page($params, $template) {
		if (empty ( $params ['id'] )) {
			trigger_error ( "[plugin] page parameter 'id' cannot be empty", E_USER_NOTICE );
			
			return;
		}
		if (empty ( $params ['var'] )) {
			trigger_error ( "[plugin] page parameter 'var' cannot be empty", E_USER_NOTICE );
			
			return;
		}
		$page = CmsPage::load ( $params ['id'], false, false, false );
		if ($page) {
			$template->assign ( $params ['var'], $page->getFields () );
		} else {
			$template->assign ( $params ['var'], array () );
		}
	}
	function smarty_function_page_content($params, $template) {
		if (empty ( $params ['id'] )) {
			trigger_error ( "[plugin] page parameter 'id' cannot be empty", E_USER_NOTICE );
			return;
		}
		if (empty ( $params ['var'] )) {
			trigger_error ( "[plugin] page parameter 'var' cannot be empty", E_USER_NOTICE );
			return;
		}
		$page = dbselect ()->from ( '{cms_page}' )->where ( array ('id' => $params ['id'] ) )->get ( 'content' );
		if ($page) {
			if (isset ( $params ['chunk'] )) {
				$page = str_ireplace ( array ('[page]',' ','　',"\t","\r","\n",'#p#副标题#e#','<p>','&nbsp;' ), '', $page );
				$page = preg_replace ( '#<img[^>]+?>#i', '</p>\0</p>', $page );
				$page = preg_replace ( '#<p[^>]*?>#i', '', $page );
				$page = preg_split ( '#</p>#i', $page );
				$datas = array ();
				
				foreach ( $page as $i => $v ) {
					$v = trim ( $v );
					if (preg_match ( '#<img.+?src="([^"]+?)".+?height="(\d+)".+?width="(\d+)"[^>]*?>#i', $v, $m )) {
						$datas [] = array ('type' => 'image','data' => the_media_src ( $m [1] ),'height' => $m [2],'width' => $m [3] );
					} else if (preg_match ( '#<img.+?src="([^"]+?)"[^>]*?>#i', $v, $m )) {
						$datas [] = array ('type' => 'image','data' => the_media_src ( $m [1] ),'height' => '400','width' => '600' );
					} else {
						$mx = trim ( preg_replace ( '#</?[a-z0-9]\s*[^>]*?>#i', '', $v ) );
						if ($mx) {
							$datas [] = array ('type' => 'text','data' => $mx );
						}
					}
				}
				unset ( $page );
				$page = $datas;
				unset ( $datas );
			}
			$template->assign ( $params ['var'], $page );
		} else {
			$template->assign ( $params ['var'], array () );
		}
	}
}
function smarty_function_channel_tree($params, $template) {
	if (empty ( $params ['name'] )) {
		trigger_error ( "[plugin] page parameter 'name' cannot be empty", E_USER_NOTICE );
		return;
	}
	$name = $params ['name'];
	if (empty ( $params ['id'] )) {
		$id = $name;
	} else {
		$id = $params ['id'];
	}
	if (empty ( $params ['value'] )) {
		$value = '';
	} else {
		$value = $params ['value'];
	}
	$type = 0;
	if (! empty ( $params ['type'] )) {
		$type = 1;
	}
	if (! empty ( $params ['placeholder'] )) {
		$placeholder = $params ['placeholder'];
	}
	if (! empty ( $params ['cid'] )) {
		$defaults ['cid'] = $params ['cid'];
	}
	if (! empty ( $params ['multi'] )) {
		$defaults ['multi'] = $params ['multi'];
	}
	$defaults ['table'] = 'cms_channel';
	$defaults ['params'] ['is_topic_channel'] = $type;
	if ($value) {
		$defaults ['cid'] = $value;
	}
	$field = array ('name' => $name,'id' => $id,'value' => $value,'placeholder' => $placeholder,'widget' => 'treeview','defaults' => json_encode ( $defaults ) );
	
	$widget = CustomeFieldWidgetRegister::initWidgets ( array ($name => $field ), array ($name => $value ) );
	if ($widget) {
		echo $widget [$name] ['widget']->render ( $widget [$name] );
	}
}
/**
 *
 * @param Query $pages        	
 * @param array $con        	
 */
function build_page_common_query_of_cms($pages, $con) {
	static $model_info;
	if (! isset ( $con ['model'] ) || empty ( $con ['model'] )) {
		return $pages;
	}
	$model = $con ['model'];
	if (isset ( $model_info [$model] )) {
		$m = $model_info [$model];
	} else {
		$m = dbselect ( 'id,addon_table' )->from ( '{cms_model}' )->where ( array ('refid' => $model,'deleted' => 0 ) )->get ();
		$model_info [$model] = $m;
	}
	if ($m && $m ['addon_table'] && preg_match ( '#^cms_modeltb_.+$#', $m ['addon_table'] )) {
		$pages->join ( '{' . $m ['addon_table'] . '} AS ' . $model, "CP.id = {$model}.page_id" );
	}
	return $pages;
}