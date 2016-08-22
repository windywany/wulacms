<?php
class CmsRestService {
	/**
	 *
	 * @param RestServer $server        	
	 */
	public static function on_init_rest_server($server) {
		$server->registerClass ( new CmsRestService (), '1', 'cms' );
		return $server;
	}
	
	/**
	 * 取栏目树.
	 *
	 * @param array $params
	 *        	<ul>
	 *        	<li>topic - 是否是专题栏目</li>
	 *        	<li>model - 模型</li>
	 *        	</ul>
	 * @param string $key        	
	 * @param string $secret        	
	 */
	public function rest_channel($params, $key, $secret) {
		$is_topic = isset ( $params ['topic'] ) && $params ['topic'];
		$model = isset ( $params ['model'] ) && $params ['model'] ? $params ['model'] : null;
		$channels = ChannelForm::getChannelTree ( $model, $is_topic );
		return array ('error' => 0,'channels' => $channels );
	}
	/**
	 * 取一个页面的数据.id和url二选一做为参数.
	 *
	 * @param array $params
	 *        	<ul>
	 *        	<li>id - 页面编号</li>
	 *        	<li>url- 页面的URL</li>
	 *        	</ul>
	 * @param string $key        	
	 * @param string $secret        	
	 */
	public function rest_page($params, $key, $secret) {
		$id = isset ( $params ['id'] ) ? intval ( $params ['id'] ) : false;
		if (! $id) {
			$url = isset ( $params ['url'] ) ? $params ['url'] : false;
		}
		if (! $id && ! $url) {
			return array ('error' => 1,'message' => 'id和url参数至少提供一个.' );
		}
		if ($id) {
			$page = CmsPage::load ( $id );
		} else {
			$page = CmsPage::load ( $url, true );
		}
		if ($page) {
			$fields = $page->getFields ();
			unset ( $fields ['__this_data'] );
			return array ('error' => 0,'page' => $fields );
		} else {
			return array ('error' => 2,'message' => '页面不存在.' );
		}
	}
	/**
	 * 保存一个基本页面.
	 *
	 * @param array $params
	 *        	<ul>
	 *        	<li>id - 编号,可选</li>
	 *        	<li>author - 作者,可选</li>
	 *        	<li>tag - 标签,可选</li>
	 *        	<li>channel - 栏目,必须填写</li>
	 *        	<li>flag_a - 加粗标志,可选</li>
	 *        	<li>flag_b - 特荐标志,可选</li>
	 *        	<li>flag_c - 推荐标志,可选</li>
	 *        	<li>flag_h - 头条标志,可选</li>
	 *        	<li>image- 插图,可选</li>
	 *        	<li>keywords- 关键词</li>
	 *        	<li>model - 模型,必选</li>
	 *        	<li>source- 来源,可选</li>
	 *        	<li>title - 标题,必选</li>
	 *        	<li>title_color - 标题颜色,可选</li>
	 *        	<li>url - URL,可选</li>
	 *        	<li>related_pages - 相关页面,可选</li>
	 *        	<li>description - 描述,可选</li>
	 *        	<li>img_follow - 图片跟随内容,可选</li>
	 *        	<li>img_pagination - 开启图片分页,可选</li>
	 *        	<li>img_next_page - 图片分页的下一页,可选</li>
	 *        	</ul>
	 * @param unknown $key        	
	 * @param unknown $secret        	
	 */
	public function rest_post_save_page($params, $key, $secret) {
		$form = new PageRestForm ( $params );
		$page = $form->valid ();
		if ($page) {
			$channel = $page ['channel'];
			$channel = dbselect ( '*' )->from ( '{cms_channel}' )->where ( array ('refid' => $channel ) )->get ();
			$page ['gid'] = intval ( $channel ['gid'] );
			$time = time ();
			$id = $page ['id'];
			unset ( $page ['id'] );
			$page ['update_time'] = $time;
			$page ['update_uid'] = 0;
			if (! empty ( $page ['related_pages'] )) {
				$page ['related_pages'] = safe_ids ( $page ['related_pages'] );
			}
			if (empty ( $id )) {
				$page ['create_time'] = $time;
				$page ['create_uid'] = 0;
				$page ['status'] = icfg ( 'page_new_status@cms', 3 );
				$rst = dbinsert ( $page )->into ( '{cms_page}' )->exec ();
				if ($rst) {
					$id = $rst [0];
				}
			} else {
				$page ['status'] = icfg ( 'page_update_status@cms', 1 );
				$rst = dbupdate ( '{cms_page}' )->set ( $page )->where ( array ('id' => $id ) )->exec ();
				if (! $rst) {
					$id = 0;
				}
			}
			if ($id) {
				$urls = dbselect ( 'create_time,url,url_key' )->from ( '{cms_page}' )->where ( array ('id' => $id ) )->get ( 0 );
				$page ['url_key'] = $urls ['url_key'];
				$page ['create_time'] = $urls ['create_time'];
				if (! isset ( $page ['url'] )) {
					$page ['url'] = $urls ['url'];
				}
				if (CmsPage::generateURL ( $id, $page )) {
					CmsPage::generateKeywords ( $id, $page );
					if (! empty ( $page ['author'] )) {
						CmsPage::updateVariables ( $page ['author'], 'author' );
					}
					if (! empty ( $page ['source'] )) {
						CmsPage::updateVariables ( $page ['source'], 'source' );
					}
					$page ['id'] = $id;
					return array ('error' => 0,'page' => $page );
				} else {
					return array ('error' => 3,'message' => 'URL重复,请修改URL.' );
				}
			} else {
				return array ('error' => 2,'message' => '无法保存页面.' );
			}
		} else {
			return array ('error' => 1,'message' => $form->getErrors () );
		}
	}	
}