<?php
class TagsInstaller extends AppInstaller {
	public function getVersionLists() {
		$v ['1.0.0'] = '20150129001';
		$v ['1.0.1'] = '20150202002';
		return $v;
	}
	public function getName() {
		return '搜索标签';
	}
	public function getDscription() {
		return '为每页文章提供全搜索标签功能,可以通过此功能来做页面调用.它不同于关键词,它不应该被用于SEO中的关键词.';
	}
	public function getWebsite() {
		return 'http://www.kisscms.org/plugins/tags';
	}
	public function getAuthor() {
		return 'Leo Ning';
	}
	public function getDependences() {
		$d ['cms'] = '[1.5.1,0)';
		return $d;
	}	
	public function upgradeTo20150202002($dialect) {
		$where ['!@'] = dbselect ( 'page_id' )->setDialect ( $dialect )->from ( '{cms_stags}' )->where ( array ('CP.id' => imv ( 'page_id' ) ) );
		$total = dbselect ()->setDialect ( $dialect )->from ( '{cms_page} AS CP' )->where ( $where )->count ( 'CP.id' );
		if ($total > 0) {
			$cp = ceil ( $total / 200 );
			for($i = 0; $i < $cp; $i ++) {
				$pages = dbselect ( 'id,tag,keywords' )->setDialect ( $dialect )->from ( '{cms_page} AS CP' )->where ( $where )->limit ( $i * 200, 200 );
				foreach ( $pages as $p ) {
					$this->addTags ( $p, $dialect );
				}
			}
		}
		return true;
	}
	private function addTags($page, $dialect) {
		$tags ['page_id'] = $page ['id'];
		$tags ['tags'] = $page ['tag'];
		$tags ['my_tags'] = $page ['tag'];
		if ($page ['keywords']) {
			$tags ['tags'] .= ',' . $page ['keywords'];
		}
		dbdelete ()->setDialect ( $dialect )->from ( '{cms_stags_index}' )->where ( array ('page_id' => $tags ['page_id'] ) )->exec ();
		if ($tags ['tags']) {
			list ( $tagsAry, $_x ) = get_keywords ( $tags ['tags'] );
			$tagsAry = array_unique ( explode ( ',', $tagsAry ) );
			$tags ['tags'] = '';
			if ($tagsAry) {
				$tags ['tags'] = implode ( ',', $tagsAry );
				$stags = array ();
				foreach ( $tagsAry as $tag ) {
					$page ['page_id'] = $page ['id'];
					$page ['tag'] = $tag;
					$stags [] = $page;
				}
				dbinsert ( $stags, true )->setDialect ( $dialect )->into ( '{cms_stags_index}' )->exec ();
			}
		} else {
			$tags ['tags'] = '';
			$tags ['my_tags'] = '';
		}
		dbsave ( $tags, array ('page_id' => $tags ['page_id'] ), 'page_id' )->setDialect ( $dialect )->into ( '{cms_stags}' )->exec ();
	}
}
