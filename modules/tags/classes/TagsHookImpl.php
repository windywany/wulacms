<?php
class TagsHookImpl {
	public static function on_init_dynamicform_AdminPreferenceForm($form) {
		$form ['combine_tags'] = array ('group' => 'tags','col' => '4','label' => '合并标签','widget' => 'radio','default' => '0','defaults' => "1=是\n0=否" );
		$form ['combine_keywords'] = array ('group' => 'tags','col' => '3','label' => '合并关键词','widget' => 'radio','default' => '0','defaults' => "1=是\n0=否" );
		$form ['tags_tpl'] = array ('group' => 'tags','col' => '3','label' => '标签页模板','default' => 'tags.tpl','rules' => array ('regexp(/^[a-z0-9][a-z0-9_\/\-]*\.tpl$/i)' => '模板文件名格式不正确.' ) );
		$form ['usetag'] = array ('group' => 'tags','col' => '2','label' => '使用内链库','widget' => 'radio','default' => '0','defaults' => "1=是\n0=否" );
	}
	public static function get_common_page_condition_fields($fields) {
		$fields ['search_tags'] = array ('group' => 'tags','col' => '4','name' => 'search_tags','widget' => 'text','label' => '搜索标签','note' => '多个标签使用“,”分隔' );
		return $fields;
	}
	public static function load_page_common_fields($fields) {
		$fields ['my_tags'] = array ('group' => 'tags','col' => '4','name' => 'my_tags','widget' => 'text','label' => '调用标签','note' => '多个标签使用“,”分隔,用条件查询调用页面.' );
		$fields ['search_tags'] = array ('group' => 'tags','col' => '8','name' => 'search_tags','widget' => 'text','label' => '搜索标签','note' => '多个标签使用“,”分隔' );
		return $fields;
	}
	public static function get_page_data($page, $url) {
		global $__kissgo_apps;
		if ($page == null && bcfg ( 'usetag@cms' )) {
			$urlx = explode ( '/', $url );
			if (count ( $urlx ) > 1) {
				$tag = urldecode ( $urlx [1] );
				$app = $urlx [0];
				if ((isset ( Router::$URL2APP [$app] ) && 'tags' == Router::$URL2APP [$app]) || (isset ( $__kissgo_apps [$app] ) && $app == 'tags')) {
					$data ['tag'] = $tag;
					$tpl = cfg ( 'tags_tpl@cms', 'tags.tpl' );
					if (empty ( $tpl )) {
						$tpl = 'tags.tpl';
					}
					$data ['template_file'] = $tpl;
					$page = new CmsPage ( $data, false );
				}
			}
		}
		return $page;
	}
	
	/**
	 * 查询条件.
	 *
	 * @param Query $query        	
	 * @param unknown $con        	
	 * @return unknown
	 */
	public static function build_page_common_query($query, $con = array()) {
		$query->field ( 'PSTAG.tags AS search_tags,PSTAG.my_tags' );
		$query->join ( '{cms_stags} AS PSTAG', 'CP.id = PSTAG.page_id' );
		if ($con) {
			$search_tags = get_condition_value ( 'search_tags', $con );
			if ($search_tags) {
				list ( $tagsAry, $_x ) = get_keywords ( $search_tags );
				$tagsAry = array_unique ( explode ( ',', $tagsAry ) );
				if ($tagsAry) {
					$wheres = array ();
					foreach ( $tagsAry as $tag ) {
						$w = dbselect ( 'page_id' )->from ( '{cms_stags_index} AS SSTAG' )->where ( array ('CP.id' => imv ( 'SSTAG.page_id' ),'SSTAG.tag' => $tag ) );
						$wheres ['@'] [] = $w;
					}
					$query->where ( $wheres );
				}
			}
		}
		return $query;
	}
	public static function save_page_common_data($data) {
		$tags ['page_id'] = $data ['page_id'];
		$tags ['tags'] = rqst ( 'search_tags' );
		$tags ['my_tags'] = rqst ( 'my_tags' );
		if (bcfg ( 'combine_tags@cms' )) {
			$tagx = rqst ( 'tag' );
			if ($tagx) {
				$tags ['tags'] .= ',' . $tagx;
			}
		}
		if (bcfg ( 'combine_keywords@cms' ) && $data ['keywords']) {
			$tags ['tags'] .= ',' . $data ['keywords'];
		}
		dbdelete ()->from ( '{cms_stags_index}' )->where ( array ('page_id' => $data ['page_id'] ) )->exec ();
		if ($tags ['tags']) {
			list ( $tagsAry, $_x ) = get_keywords ( $tags ['tags'] . ($tags ['my_tags'] ? ',' . $tags ['my_tags'] : '') );
			$tagsAry = array_unique ( explode ( ',', $tagsAry ) );
			$tags ['tags'] = '';
			if ($tagsAry) {
				$tags ['tags'] = implode ( ',', $tagsAry );
				$stags = array ();
				foreach ( $tagsAry as $tag ) {
					$page ['page_id'] = $data ['page_id'];
					$page ['tag'] = $tag;
					$stags [] = $page;
				}
				dbinsert ( $stags, true )->into ( '{cms_stags_index}' )->exec ();
			}
		} else {
			$tags ['tags'] = '';
		}
		dbsave ( $tags, array ('page_id' => $tags ['page_id'] ), 'page_id' )->into ( '{cms_stags}' )->exec ();
	}
	public static function load_page_common_data($data) {
		$page_id = $data ['page_id'];
		if ($page_id) {
			$tags = dbselect ( 'tags,my_tags' )->from ( '{cms_stags}' )->where ( array ('page_id' => $page_id ) )->get ();
			if ($tags) {
				$data ['search_tags'] = $tags ['tags'];
				$data ['my_tags'] = $tags ['my_tags'];
			} else {
				$data ['search_tags'] = '';
				$data ['my_tags'] = '';
			}
		}
		return $data;
	}
	public static function show_page_detail($html, $page) {
		if ($page ['search_tags']) {
			$html .= '<p>搜索标签:';
			$html .= $page ['search_tags'];
			$html .= '</p>';
		}
		return $html;
	}
	public static function get_customer_cms_search_field($fields, $type) {
		$fields ['search_tags'] = array ('col' => 3,'placeholder' => '搜索标签' );
		return $fields;
	}
}