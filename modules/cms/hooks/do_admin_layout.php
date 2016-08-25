<?php
defined ( 'KISSGO' ) or exit ( 'No direct script access allowed' );
/**
 * 注册导航菜单.
 *
 * @param AdminLayoutManager $layout
 */
function hook_for_do_admin_layout_admin($layout) {
	// 网站菜单
	if (icando ( 'm:cms' )) {
		$menu = new AdminNaviMenu ( 'site', '网站', 'fa-globe' );
		
		if (icando ( 'r:cms/channel' ) || icando ( 'r:cms/catalog' )) {
			$catelogMenu = new AdminNaviMenu ( 'catelog_menu', '分类', 'fa-sitemap' );
			if (icando ( 'r:cms/channel' )) {
				$modelMenu = new AdminNaviMenu ( 'catalog_menu', '栏目频道', '', tourl ( 'cms/channel', false ) );
				$catelogMenu->addItem ( $modelMenu, false, 0 );
			}
			if (icando ( 'r:cms/catalog' )) {
				
				$catelogTypes = apply_filter ( 'get_cms_catalog_types', array () );
				$i = 1;
				foreach ( $catelogTypes as $key => $val ) {
					$type = $key;
					$name = $val ['name'];
					$catelogMenu->addSubmenu ( array ($type . '_catelog',$name,'',tourl ( 'cms/catelog/' . $type, false ) ), false, $i ++ );
				}
			}
			$menu->addItem ( $catelogMenu, false, 1 );
		}
		
		$cntMenu = new AdminNaviMenu ( 'page_menu', '文章', 'fa-copy' );
		$addPageURL = tourl ( 'cms/page/add/page', false );
		
		if (icando ( 'c:cms/page' )) {
			$addPageMenu = new AdminNaviMenu ( 'addpages', '发布文章', '' );
			$models = dbselect ( 'refid,name,role,is_delegated' )->from ( '{cms_model}' )->where ( array ('deleted' => 0,'is_topic_model' => 0,'hidden' => 0,'creatable' => 1 ) )->asc ( 'upid' );
			foreach ( $models as $model ) {
				if(bcfg('enable_s_role@cms')){
					$layout->addModelLink ( $model ['name'], $addPageURL . $model ['refid'], false, false, $model ['role'] );
				}else if (! $model ['role']) {
					$layout->addModelLink ( '[文章]' . $model ['name'], $addPageURL . $model ['refid'] );
				}
				if($model['is_delegated']){
					$addPageMenu->addSubmenu ( array ('page_' . $model ['refid'],$model ['name'],'',$addPageURL . $model ['refid'] ), 'c:cms/page' );
				}
			}
			$cntMenu->addItem ( $addPageMenu, 'c:cms/page', 0 );
		}
		$cntMenu->addSubmenu ( array ('mypages','我的文章','',tourl ( 'cms/page/my/page/', false ) ), 'r:cms/page', 1 );
		$cntMenu->addSubmenu ( array ('allpages','所有文章','',tourl ( 'cms/page', false ) ), 'all:cms/page', 2 );
		
		$menu->addItem ( $cntMenu, 'r:cms/page', 11 );
		
		$topicMenu = new AdminNaviMenu ( 'topic_menu', '专题', 'fa-suitcase' );
		if (icando ( 'c:cms/topic' )) {
			$addTopicMenu = new AdminNaviMenu ( 'addtopic', '新建专题', '' );
			$addTopicURL = tourl ( 'cms/page/add/topic', false );
			$topics = dbselect ( 'refid,name,role,is_delegated' )->from ( '{cms_model}' )->where ( array ('deleted' => 0,'is_topic_model' => 1,'hidden' => 0,'creatable' => 1 ) )->asc ( 'upid' );
			foreach ( $topics as $topic ) {
				if(bcfg('enable_s_role@cms')){
					$layout->addModelLink ( $topic ['name'], $addTopicURL . $topic ['refid'], false, false, $topic ['role'] );
				}else if (! $topic ['role']) {
					$layout->addModelLink ( '[专题]' . $topic ['name'], $addTopicURL . $topic ['refid'] );
				}
				if($topic['is_delegated']){
					$addTopicMenu->addSubmenu ( array ('topic_' . $topic ['refid'],$topic ['name'],'',$addTopicURL . $topic ['refid'] ), 'c:cms/topic' );
				}
			}
			$topicMenu->addItem ( $addTopicMenu, 'c:cms/topic', 0 );
		}
		$topicMenu->addSubmenu ( array ('topiclist','我的专题','',tourl ( 'cms/page/my/topic/', false ) ), 'r:cms/topic', 1 );
		$topicMenu->addSubmenu ( array ('topicall','所有专题','',tourl ( 'cms/page/all/topic', false ) ), 'all:cms/topic', 2 );
		$topicMenu->addSubmenu ( array ('topictype','专题分类','',tourl ( 'cms/channel/1', false ) ), 'r:cms/channel', 3 );
		$menu->addItem ( $topicMenu, 'r:cms/topic', 14 );
		
		$pageMenu = new AdminNaviMenu ( 'cpage_menu', '页面', 'fa-file-text-o' );
		
		$pageMenu->addSubmenu ( array ('addcpage','新增页面','',tourl ( 'cms/cpage/add', false ) ), 'c:cms/cpage', 1 );
		$pageMenu->addSubmenu ( array ('cpagelist','所有页面','',tourl ( 'cms/cpage', false ) ), 'r:cms/cpage', 2 );
		
		$menu->addItem ( $pageMenu, 'r:cms/cpage', 15 );
		
		if (icando ( 'r:cms/navi' )) {
			$navis = dbselect ( '*' )->from ( '{cms_catelog}' )->where ( array ('deleted' => 0,'type' => 'navi' ) )->toArray ();
			if ($navis) {
				$naviMenu = new AdminNaviMenu ( 'navi_menu', '导航', 'fa-sitemap' );
				foreach ( $navis as $i => $navi ) {
					if (! empty ( $navi ['alias'] )) {
						$naviMenu->addSubmenu ( array ($navi ['alias'] . '_navim',$navi ['name'],'',tourl ( 'cms/navi/' . $navi ['alias'], false ) ), false, $i ++ );
					}
				}
				$menu->addItem ( $naviMenu, false, 17 );
			}
		}
		
		if (bcfg ( 'disable_approving@cms' ) && icando ( 'approve:cms' )) {
			$approveMenu = new AdminNaviMenu ( 'approvem', '审核', 'fa-legal txt-color-orange', tourl ( 'cms/approve', false ) );
			$menu->addItem ( $approveMenu, 'approve:cms', 18 );
		}
		
		$blockMenu = new AdminNaviMenu ( 'block_menu', '区块', 'fa-list-ul', tourl ( 'cms/block', false ) );
		$menu->addItem ( $blockMenu, 'r:cms/block', 60 );
		if (icando ( 'r:cms/block' )) {
			$layout->addDivider ( 'model' );
			$layout->addModelLink ( '区块', tourl ( 'cms/block', false ) );
		}
		$chunkMenu = new AdminNaviMenu ( 'chunk_menu', '碎片', 'fa-code', tourl ( 'cms/chunk', false ) );
		$menu->addItem ( $chunkMenu, 'r:cms/chunk', 70 );
		
		$inlineMenu = new AdminNaviMenu ( 'inline_menu', '内链', 'fa-link', tourl ( 'cms/tag', false ) );
		$menu->addItem ( $inlineMenu, 'r:cms/tag', 80 );
		
		$modelMenu = new AdminNaviMenu ( 'model_menu', '模型', 'fa-list-alt', tourl ( 'cms/model', false ) );
		$menu->addItem ( $modelMenu, 'r:cms/model', 90 );
		
		$tplMenu = new AdminNaviMenu ( 'tpl_data', '模板', 'fa-columns', tourl ( 'cms/cts', false ) );
		$menu->addItem ( $tplMenu, 'tpl:cms', 100 );
		
		$layout->addNaviMenu ( $menu, 0 );
	}
	// 完成网站菜单配置
	if (icando ( 'm:system/preference' )) {
		$sysMenu = $layout->getNaviMenu ( 'system' );
		$settingMenu = $sysMenu->getItem ( 'preferences' );
		$settingMenu->addSubmenu ( array ('cmsSettting','内容管理设置','fa-cog',tourl ( 'cms/preference', false ) ), 'cms:system/preference' );
	}
}
function hook_for_on_render_navi_btns_page($btns) {
	if (icando ( 'all:cms/topic' )) {
		$btns .= '<div class="btn-header transparent pull-right">
    			<span>
    				<a href="#' . tourl ( 'cms/page/all/topic', false ) . '" title="所有专题">
    					<i class="fa fa-fw fa-suitcase"></i>
    				</a>
    			</span>
    		</div>';
	}
	if (icando ( 'all:cms/page' )) {
		$btns .= '<div class="btn-header transparent pull-right">
    			<span>
    				<a  href="#' . tourl ( 'cms/page', false ) . '" title="所有文章">
    					<i class="fa fa-fw fa-copy"></i>
    				</a>
    			</span>
    		</div>';
	}
	return $btns;
}
function hook_for_render_dashboard_shortcut_cms($shortcut) {
	$ss = array ();
	if (icando ( 'm:cms' )) {
		if (icando ( 'all:cms/page' )) {
			$ss [] = '<li>
				<a class="jarvismetro-tile big-cubes bg-color-red" href="#' . tourl ( 'cms/page', false ) . '">
					<span class="iconbox">
						<i class="fa fa-copy fa-5x"></i>
						<span class="text-center">所有文章</span>
					</span>
				</a>
			</li>';
		}
		if (icando ( 'all:cms/topic' )) {
			$ss [] = '<li>
				<a class="jarvismetro-tile big-cubes bg-color-orange" href="#' . tourl ( 'cms/page/all/topic', false ) . '">
					<span class="iconbox">
						<i class="fa fa-suitcase fa-5x"></i> <span class="text-center">所有专题</span>
					</span>
				</a>
			</li>';
		}
		if (icando ( 'r:cms/block' )) {
			$ss [] = '<li>
				<a class="jarvismetro-tile big-cubes bg-color-blue" href="#' . tourl ( 'cms/block', false ) . '">
					<span class="iconbox">
						<i class="fa fa-list-alt fa-5x"></i> <span class="text-center">区块(幻灯片)管理</span>
					</span>
				</a>
			</li>';
		}
		if (icando ( 'r:cms/chunk' )) {
			$ss [] = '<li>
				<a class="jarvismetro-tile big-cubes bg-color-green" href="#' . tourl ( 'cms/chunk', false ) . '">
					<span class="iconbox">
						<i class="fa fa-code fa-5x"></i> <span class="text-center">碎片管理</span>
					</span>
				</a>
			</li>';
		}
		if (icando ( 'r:cms/tag' )) {
			$ss [] = '<li>
				<a class="jarvismetro-tile big-cubes bg-color-teal" href="#' . tourl ( 'cms/tag', false ) . '">
					<span class="iconbox">
						<i class="fa fa-link fa-5x"></i> <span class="text-center">内链库管理</span>
					</span>
				</a>
			</li>';
		}
		if (icando ( 'tpl:cms' )) {
			$ss [] = '<li>
				<a class="jarvismetro-tile big-cubes bg-color-pink" href="#' . tourl ( 'cms/cts', false ) . '">
					<span class="iconbox">
						<i class="fa fa-columns fa-5x"></i> <span class="text-center">模板调用</span>
					</span>
				</a>
			</li>';
		}
	}
	return $shortcut . implode ( '', $ss );
}
function hook_for_cms_init_dashboard_ui($ui) {
	return $ui;
}