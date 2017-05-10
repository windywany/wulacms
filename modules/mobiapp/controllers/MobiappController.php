<?php

/**
 * 移动端详情页.
 * @author 宁广丰.
 *
 */
class MobiappController extends Controller {
	public function index($id = '') {
		$id = intval($id);
		if (empty ($id)) {
			Response::respond(404);
		}
		$page = dbselect('page_id,flags,MPV.tpl,custom_data,publish_time')->from('{mobi_page} AS MP')->join('{mobi_page_view} AS MPV', 'MP.page_view = MPV.refid')->where(array('MP.id' => $id, 'MP.status' => 1, 'MP.deleted' => 0, 'MPV.deleted' => 0))->get();
		if (!$page) {
			Response::respond(404);
		}
		$page_id      = $page ['page_id'];
		$custom_data  = @json_decode($page ['custom_data'], true);
		$publish_time = $page ['publish_time'];
		$paged        = CmsPage::load($page_id, false, false, false);
		if (!$paged) {
			Response::respond(404);
		}
		$fileds = $paged->getFields();
		if (!isset ($page ['tpl'])) {
			Response::respond(404);
		}
		if (empty ($custom_data ['title'])) {
			unset ($custom_data ['title']);
		}
		if (empty ($custom_data ['desc'])) {
			unset ($custom_data ['desc']);
		} else {
			$custom_data ['description'] = $custom_data ['desc'];
		}
		$fileds ['content']      = $this->lazyload($fileds ['content']);
		$fileds ['id']           = $id;
		$fileds ['publish_time'] = $publish_time;
		$fileds                  = array_merge($fileds, $custom_data);
		$tag                     = $fileds ['tag'];
		$keywords                = $fileds ['keywords'];
		$tags                    = array();
		if ($keywords) {
			$tags = explode(',', str_replace(array(' ', ',', '，'), ',', $keywords));
		}
		if ($tag) {
			$tags [] = $tag;
		}
		$fileds ['related_pages'] = array();
		if ($tags) {
			$tags                         = array_unique($tags);
			$keywords                     = convert_search_keywords($tags);
			$where ['CP.deleted']         = 0;
			$where ['CP.hidden']          = 0;
			$where ['MP.id <>']           = $id;
			$where ['MP.deleted']         = 0;
			$where ['MP.is_carousel']     = 0;
			$where ['MP.status']          = 1;
			$where ['search_index MATCH'] = $keywords;
			$related                      = dbselect('MP.*')->from('{mobi_page} AS MP')->join('{cms_page} AS CP', 'MP.page_id = CP.id')->where($where)->sort('MP.publish_time', 'd')->limit(0, 5);
			if ($related) {
				$views = MobiListView::getListViews();
				$rs    = array();
				foreach ($related as $p) {
					$rs [] = MobiRestService::getListViewData($p, $views);
				}
				$fileds ['related_pages'] = $rs;
			}
		}
		$fileds ['detailURL'] = trailingslashit(DETECTED_ABS_URL) . tourl('mobiapp', false);
		$router               = Router::getRouter();
		$router->setCurrentURL($fileds ['detailURL'] . $id);

		return template($page ['tpl'], $fileds);
	}

	public function view($id = '') {
		$view = $this->index($id);
		$view->assign('viewByWebView', true);
		$router = Router::getRouter();
		$router->setCurrentURL(trailingslashit(DETECTED_ABS_URL) . tourl('mobiapp/view', false) . $id);

		return $view;
	}

	private function lazyload($content) {
		$content = preg_replace('#(<img.+?)src\s*=\s*[\'"](.+?)[\'"]([^>]+?>)#ims', '\1src="/assets/s.png" data-src1="\2"\3', $content);

		return $content;
	}
}