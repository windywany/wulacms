<?php

/**
 * 页面.
 * @author Guangfeng
 *
 */
class CmsPage implements ArrayAccess, ICtsPage {
	private static $PAGE_FIELDS = 'CP.id,
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
					CP.title,
					CP.description,
					CP.title2,
					CP.title_color,
					CP.image,
					CP.author,
					CP.source,
					CP.tag,
					CP.url,
					CP.url_handler,
					CP.display_sort,
					CC.url AS channel_index_url,
					CC.list_page_url AS channel_list_url,
			 		CC.default_page AS channel_pageid,
					CC.index_page AS channel_index_pageid,
					CC.list_page AS channel_list_pagepid,
					CC.root,
					CC.name AS channel_name,
					CM.name AS model_name';
	private static $sfields     = '*';
	public static  $PAGE_STATUS = array('' => '请选择状态', 0 => '审核未通过', 1 => '待审核', '4' => '待发布', '2' => '已发布', '3' => '草稿');
	private        $fields      = array();

	/**
	 * 构建一个页面数据.
	 *
	 * @param array $page
	 * @param bool  $load
	 *            是否加载channel,related pages,breadcrum, next and pre page link.
	 */
	public function __construct($page, $load = true, $pagination = true) {
		$this->fields = $page;
		if ($load) {
			$this->loadChannelInfo();
			$this->loadRelatedPages();
			$this->loadBreadcrumb();
			$this->loadPreAndNextPages();
		} else {
			$this->fields ['is_list_model'] = true;
		}
		$this->fields = apply_filter('on_load_page_fields', $this->fields);
		if (!empty ($this->fields ['content'])) {
			$this->fields ['origion_content'] = $this->fields ['content'];
			$router                           = Router::getRouter();
			$cp                               = $ocp = $router->getCurrentPageNo();
			if ($cp == PHP_INT_MAX) {
				$pagination = false;
			}
			if (isset ($this->fields ['total_pages'])) {
				// 插件已经计算出页数
				$total         = $this->fields ['total_pages'];
				$pages [ $cp ] = $this->fields ['content'];
			} else {
				if ($pagination) {
					if (strpos($this->fields ['content'], '#p#副标题#e#') > 0) {
						$pages = explode('#p#副标题#e#', $this->fields ['content']);
					} else {
						$pages = explode('[page]', $this->fields ['content']);
					}
					$total = count($pages);
				} else {
					if (strpos($this->fields ['content'], '#p#副标题#e#') > 0) {
						$pages [0]                 = str_replace('#p#副标题#e#', '', $this->fields ['content']);
						$this->fields ['contents'] = explode('#p#副标题#e#', $this->fields ['content']);
					} else {
						$pages [0]                 = str_replace('[page]', '', $this->fields ['content']);
						$this->fields ['contents'] = explode('[page]', $this->fields ['content']);
					}
					// 在不启用分页的情况下，将分页内容也提供给模板
					foreach ($this->fields ['contents'] as $idx => $cts) {
						$this->fields ['contents'] [ $idx ] = CmsPage::applyMediaURL(CmsPage::trimPtag($cts));
					}
					$total = 1;
					$cp    = 0;
				}
			}
			//启用列表功能
			if (!isset ($pages [ $cp ]) && $this->fields ['is_list_model']) {
				$pages [ $cp ] = $this->fields ['content'];
			}
			if (isset ($pages [ $cp ])) {
				$content = $pages [ $cp ];
				if ($pagination || $ocp == PHP_INT_MAX) {
					$content               = CmsPage::trimPtag($content);
					$content               = apply_filter('alter_article_content', $content);
					$default_img_follow    = cfg('img_follow@cms');
					$default_img_next_page = cfg('img_next_page@cms', false);
					$urlInfo               = $router->getParsedURL();
					if ($cp == 0) {
						$this->fields ['prev_page_url'] = false;
					} else {
						$pp = $cp;
						if ($pp == 1) {
							$this->fields ['prev_page_url'] = safe_url($urlInfo ['orgin'], true);
						} else {
							$this->fields ['prev_page_url'] = safe_url($urlInfo ['prefix'] . '_' . $pp . $urlInfo ['suffix'], true);
						}
					}
					$tp = $total - 1;
					if ($cp == $tp) {
						$this->fields ['next_page_url']  = safe_url($this->fields ['img_next_page'] ? $this->fields ['img_next_page'] : $default_img_next_page, true);
						$this->fields ['next_page_url1'] = false;
					} else {
						$np                              = $cp + 2;
						$this->fields ['next_page_url1'] = $this->fields ['next_page_url'] = safe_url($urlInfo ['prefix'] . '_' . $np . $urlInfo ['suffix'], true);
					}
					$img_follow = $this->fields ['img_follow'] ? $this->fields ['img_follow'] : $default_img_follow;
					if ($img_follow) {
						$img_follow = str_replace('[/script]', '</script>', $img_follow);
						$img_follow = preg_replace('#\[(script[^\]]*)\]#', '<\1>', $img_follow);
						$content    = preg_replace('/(<img[^>]+?>)/ims', '\1' . $img_follow, $content);
					}
					if ($this->fields ['img_pagination'] && $this->fields ['next_page_url'] && $this->fields ['next_page_url'] != '#') {
						$content = preg_replace('/(<img[^>]+?>)/ims', '<a href="' . $this->fields ['next_page_url'] . '" title="点击进入下一页">\1</a>', $content);
					}
					$content = TagForm::applyTags($content);
				}
				$this->fields ['content'] = $content;
			} else {
				Response::respond(404);
			}
			$this->fields ['content']     = CmsPage::applyMediaURL($this->fields ['content']);
			$this->fields ['content']     = preg_replace('#<p>\s*<br\s*/?>\s*</p>#i', '', $this->fields ['content']);
			$this->fields ['content']     = apply_filter('before_render_content', $this->fields ['content'], $this->fields);
			$this->fields ['total_pages'] = $total;
			$this->fields ['__this_data'] = new CtsData (array(), $total);
			$this->fields ['url']         = safe_url($this->fields);
		} else {
			$this->fields ['__this_data'] = new CtsData (array(), 0);
		}
		$this->fields = apply_filter('after_load_page_fields', $this->fields);
	}

	public function setField($name, $value) {
		$this->fields [ $name ] = $value;
	}

	public function setFields($fields) {
		$this->fields                 = $fields;
		$this->fields ['__this_data'] = new CtsData (array(), isset ($fields ['total_pages']) ? $fields ['total_pages'] : 0);
	}

	/**
	 * 加载栏目信息.
	 */
	private function loadChannelInfo() {
		$channel = dbselect('CM.is_list_model,CH.url,CH.list_page_url,CH.name,CH.default_template,CH.default_model,CH.upid,CH.id,CH.root,CH.parents,CH.page_cache,CH.list_cache,CH.default_cache,CH.list_page,CH.index_page,CH.default_page')->from('{cms_channel} AS CH');
		$channel->join('{cms_model} AS CM', 'CH.default_model = CM.refid');
		$channel = $channel->where(array('CH.deleted' => 0, 'CH.refid' => $this->fields ['channel']))->get();
		if ($channel) {
			$this->fields ['channel_index_url']    = $channel ['url'];
			$this->fields ['channel_list_url']     = $channel ['list_page_url'];
			$this->fields ['channel_name']         = $channel ['name'];
			$this->fields ['channel_upid']         = $channel ['upid'];
			$this->fields ['channel_id']           = $channel ['id'];
			$this->fields ['channel_pageid']       = $channel ['default_page'];
			$this->fields ['channel_index_pageid'] = $channel ['index_page'];
			$this->fields ['channel_list_pageid']  = $channel ['list_page'];
			$this->fields ['root']                 = $channel ['root'];
			$this->fields ['is_list_model']        = $channel ['is_list_model'];
			$this->fields ['parents']              = $channel ['parents'];
			$this->fields ['page_cache']           = $channel ['page_cache'];
			$this->fields ['list_cache']           = $channel ['list_cache'];
			$this->fields ['default_cache']        = $channel ['default_cache'];
			$this->fields ['channel_url']          = $channel ['default_page'] ? $channel ['list_page_url'] : $channel ['url'];
			$this->fields ['channel_page']         = array('root' => $channel ['root'], 'url' => $this->fields ['channel_url']);
			if (empty ($this->fields ['template_file'])) {
				$this->fields ['template_file'] = $channel ['default_template'];
			}
			if (empty ($this->fields ['template_file'])) {
				$this->fields ['template_file'] = '404.tpl';
			}
		}
	}

	private function loadBreadcrumb() {
		$crumbs = array();
		if (isset ($this->fields ['channel_upid'])) {
			$crumbs [] = array('is_current' => true, 'page_cache' => $this->fields ['page_cache'], 'list_cache' => $this->fields ['list_cache'], 'default_cache' => $this->fields ['default_cache'], 'root' => $this->fields ['root'], 'name' => $this->fields ['channel_name'], 'id' => $this->fields ['channel_id'], 'upid' => $this->fields ['channel_upid'], 'channel' => $this->fields ['channel'], 'url' => $this->fields ['channel_url'], 'list_url' => $this->fields ['channel_list_url']);
			dbselect('id,name,list_page_url as list_url,url,upid,refid as channel,root,page_cache,list_cache,default_cache,index_page,list_page,default_page')->from('{cms_channel}')->recurse($crumbs, 'id', 'upid');
		}

		$channels = array();
		foreach ($crumbs as $key => $cr) {
			$channels [] = $cr ['channel'];
			if (!isset ($cr ['is_current'])) {
				$crumbs [ $key ] ['url'] = $cr ['default_page'] ? $cr ['list_url'] : $cr ['url'];
			}
		}
		$this->fields ['crumbs'] = $crumbs;
		// 加载缓存
		if ((!isset ($this->fields ['expire']) || empty ($this->fields ['expire'])) && $crumbs) {
			$cnt         = count($crumbs) - 1;
			$cache_field = $this->fields ['model'] == 'channel_index' ? 'page_cache' : ($this->fields ['model'] == 'channel_list' ? 'list_cache' : 'default_cache');
			while ($cnt >= 0) {
				$cache = intval($crumbs [ $cnt ] [ $cache_field ]);
				if ($cache != 0) {
					$this->fields ['expire'] = $cache;
					break;
				}
				$cnt--;
			}
		}
		$this->fields ['channels'] = $channels;
	}

	/**
	 * 加载相关页面.
	 */
	private function loadRelatedPages() {
		if (!empty ($this->fields ['related_pages'])) {
			$pages = CmsPage::query();
			$pages->where(array('CP.deleted' => 0, 'CP.id IN' => explode(',', $this->fields ['related_pages'])))->limit(0, 50);
			$this->fields ['related_pages'] = $pages;
		}
	}

	private function loadPreAndNextPages() {
		$root    = $this->fields ['root'];
		$channel = $this->fields ['channel'];
		$id      = $this->fields ['id'];
		$where   = array('channel' => $channel, 'deleted' => 0, 'hidden' => 0, 'id >' => $id);
		if (bcfg('disable_approving@cms', false)) {
			$where ['status'] = 2;
		}
		$next_page = dbselect(CmsPage::$sfields)->from('{cms_page}')->where($where)->asc('id')->limit(0, 1)->get(0);
		if ($next_page) {
			$next_page ['root'] = $root;
		}
		$this->fields ['next_page'] = $next_page;
		unset ($where ['id >']);
		$where ['id <'] = $id;
		$prev_page      = dbselect(CmsPage::$sfields)->from('{cms_page}')->where($where)->desc('id')->limit(0, 1)->get(0);
		if ($prev_page) {
			$prev_page ['root'] = $root;
		}
		$this->fields ['prev_page'] = $prev_page;
	}

	/**
	 * 通过URL或ID加载页面.
	 *
	 * @param mixed $key
	 *
	 * @return CmsPage
	 */
	public static final function load($key, $isURL = false, $load = true, $pagination = true) {
		if (!$isURL) {
			$where ['id'] = intval($key);
		} else {
			if (!preg_match('#.+\.(s?html?|xml|jsp|json)$#i', $key)) {
				$key = $key . '/index.html';
			}
			$key               = md5($key);
			$where ['url_key'] = $key;
		}
		$where ['deleted'] = 0;
		$where ['status']  = 2;
		if (isset ($_GET ['preview']) || !bcfg('disable_approving@cms', false)) {
			unset ($where ['status']);
		}
		if (isset ($where ['status']) && bcfg('view_in_time@cms')) {
			unset ($where ['status']);
			$where ['status IN'] = array(2, 4);
		}
		$page = dbselect(self::$sfields)->from('{cms_page}')->where($where)->get();
		if ($page) {
			$page = self::loadCustomerFieldValues($page ['id'], $page, $page ['model']);
			$page = new CmsPage ($page, $load, $pagination);
		}

		return $page;
	}

	/**
	 * 加载模板页.
	 */
	public static final function loadTplPage($url) {
		$tpls = dbselect('url,url_handler')->from('{cms_page}')->where(array('channel' => '_t', 'deleted' => 0));
		$ms   = null;
		$max  = 0;
		$url  = urldecode($url);
		if (!preg_match('#.+\.(s?html?|xml|jsp|json)$#i', $url)) {
			$url = $url . '/index.html';
		}
		foreach ($tpls as $tpl) {
			$p = '#^' . $tpl ['url'] . '$#i';
			if (preg_match($p, $url, $m)) {
				$mc = count($m);
				if ($mc > $max) {
					$max          = $mc;
					$tpl ['args'] = array_filter($m, function ($k) {
						return !is_numeric($k);
					}, ARRAY_FILTER_USE_KEY);
					$ms           = $tpl;
				}
			}
		}
		if ($ms) {
			if ($ms ['url_handler']) {
				$handlers = apply_filter('get_cms_url_handlers', array());
				if (isset ($handlers [ $ms ['url_handler'] ])) {
					$cp      = Router::getRouter()->getCurrentPageNo();
					$handler = $handlers [ $ms ['url_handler'] ];
					$page    = $handler->load($url, $cp, $ms ['args']);
				}
			} else {
				$page = self::load($ms ['url'], true, false);
			}
			if ($page != null) {
				$page->setField('KIS_URL_ARGS', $ms ['args']);

				return $page;
			}
		}

		return null;
	}

	public static function applyMediaURL($content) {
		$url     = trim(cfg('media_url@media', DETECTED_ABS_URL), '/');
		$content = preg_replace('#(<img.+?src\s*=\s*[\'"])(/.+?)([\'"][^>]+?>)#ims', '\1' . $url . '\2\3', $content);

		return $content;
	}

	public static function trimPtag($content) {
		// 去除空格
		$content = str_replace(array("\r\n", "\n", "\r"), '', trim($content));
		$content = preg_replace('#<br\s*/?\s*>#i', '', $content);
		$content = preg_replace('#<p[^>]*>\s*(&nbsp;\s*)*\s*</p>#i', '', $content);
		$content = preg_replace('#<span></span>\s*#i', '', $content);
		$ps1     = stripos($content, '<p');
		$ps1     = $ps1 === false ? 9999999 : $ps1;
		$pss1    = stripos($content, '</p>');
		if ($pss1 !== false && $pss1 < $ps1) {
			if ($pss1 == 0) {
				// </p><p>....
				$content = substr($content, 4);
			} else {
				// .....</p><p>.....
				$content1 = substr($content, 0, $pss1);
				if ($content1) {
					$content1 = trim(preg_replace('#(</?[a-z0-9]+>|&nbsp;)#i', '', $content1));
					if ($content1) {
						// asdfasdfasdfas</p><p>
						$content = '<p>' . $content;
					} else {
						// <span></span></p><p>
						$content = substr($content, $pss1 + 4);
					}
				}
			}
		}
		$ps2  = strripos($content, '<p');
		$pss2 = strripos($content, '</p>');
		// <p>...........
		if ($pss2 === false && $ps2 !== false) {
			$content .= '</p>';
			$pss2 = 0;
		}
		if ($ps2 !== false && $ps2 && $ps2 > $pss2) {
			$content1 = substr($content, $ps2);
			if ($content1) {
				$content1 = trim(preg_replace('#(</?[a-z0-9]+>|&nbsp;)#i', '', $content1));
				// <p>asdfsadfasfd
				if ($content1) {
					$content = $content . '</p>';
				} else {
					// <p>
					$content = substr($content, 0, $ps2);
				}
			}
		}

		return $content;
	}

	/**
	 * 通用页面查询.
	 *
	 * @param array $where
	 *
	 * @return Query
	 */
	public static function query($where = array()) {
		$pages = dbselect(CmsPage::$PAGE_FIELDS)->from('{cms_page} AS CP');
		$pages->join('{cms_channel} AS CC', 'CP.channel = CC.refid');
		$pages->join('{cms_model} AS CM', 'CP.model = CM.refid');

		if (bcfg('disable_approving@cms', false)) {
			if (isset ($where ['cts_no_will_publish'])) {
				$pages->where(array('CP.status' => 2));
			} else {
				if (bcfg('view_in_time@cms')) {
					$pages->where(array('CP.status IN' => array(2, 4)));
				} else {
					$pages->where(array('CP.status' => 2));
				}
			}
		}

		$pages = apply_filter('build_page_common_query', $pages, $where);

		return $pages;
	}

	/**
	 * 加载自定义字段值.
	 *
	 * @param int $id
	 *            页面id.
	 *
	 * @return array
	 */
	public static function loadCustomerFieldValues($id, $data = array(), $model = null) {
		$values = dbselect('val,name,type')->from('{cms_page_field} AS CPF')->where(array('page_id' => $id, 'CPF.deleted' => 0, 'CMF.deleted' => 0, 'CMF.cstore' => 0));
		$values->join('{cms_model_field} AS CMF', 'CPF.field_id = CMF.id')->limit(0, 100);
		foreach ($values as $v) {
			$data [ $v ['name'] ] = apply_filter('parse_' . $v ['type'] . '_field_value', $v ['val']);
		}
		$values       = $data;
		$contentModel = get_page_content_model($model);
		if ($contentModel) {
			$contentModel->load($values, $id);
		}
		$values ['page_id'] = $id;
		$values             = apply_filter('load_page_common_data', $values);

		return $values;
	}

	/**
	 * 保存自定义字段值.
	 *
	 * @param int $id
	 *            页面id.
	 */
	public static function saveCustomerFieldValues($id, $page, $form = null) {
		if (empty ($id) || empty ($page)) {
			return;
		}
		$model = $page ['model'];
		if (empty ($model)) {
			return;
		}
		$user   = whoami();
		$uid    = $user->getUid();
		$time   = time();
		$datas  = array();
		$id     = intval($id);
		$fields = dbselect('CMF.id,name,type,CPF.val as val,CPF.id as pid,CMF.default_value AS `default`')->from('{cms_model_field} AS CMF')->where(array('CMF.deleted' => 0, 'CMF.cstore' => 0, 'CMF.model' => $model));
		$fields->join('{cms_page_field} AS CPF', 'CMF.id = CPF.field_id AND CPF.page_id = ' . $id)->limit(0, 100);
		$data ['update_time'] = $time;
		$data ['update_uid']  = $uid;
		$data ['page_id']     = $id;
		$data ['deleted']     = 0;
		$hasFields            = array();
		foreach ($fields as $field) {
			$val                      = apply_filter('alter_' . $field ['type'] . '_field_value', rqst($field ['name'], $field ['default']), $field ['name']);
			$data ['val']             = is_array($val) ? implode(',', $val) : $val;
			$page [ $field ['name'] ] = $val;
			if ($field ['pid'] && ($val != $field ['val'])) {
				dbupdate('{cms_page_field}')->set($data)->where(array('id' => $field ['pid']))->exec();
			} else if (!$field ['pid']) {
				$data ['create_time'] = $time;
				$data ['create_uid']  = $uid;
				$data ['field_id']    = $field ['id'];
				$datas []             = $data;
				unset ($data ['create_time'], $data ['create_uid'], $data ['field_id']);
			}
			$hasFields [] = $field ['id'];
		}
		if (!empty ($datas)) {
			dbinsert($datas, true)->into('{cms_page_field}')->exec();
		}
		if (!empty ($hasFields)) {
			dbdelete()->from('{cms_page_field}')->where(array('page_id' => $id, 'field_id !IN' => $hasFields))->exec();
		}
		$contentModel = get_page_content_model($model);
		if ($contentModel) {
			try {
				$contentModel->save($page, $form);
			} catch (Exception $e) {
				log_error($e->getMessage(), 'page_' . $model);
			}
		}
		$page ['page_id'] = $id;

		fire('save_page_common_data', $page);
		fire('save_page_common_data_' . $model, $page);
	}

	public function getFields() {
		return $this->fields;
	}

	/**
	 * 更新栏目对应的列表页，封面页与默认页面url，列表页URl。
	 *
	 * @param array  $channel
	 * @param string $index_page_tpl
	 * @param string $list_page_tpl
	 * @param string $list_page_name
	 * @param string $linktolist
	 */
	public static function updateChannelPage($channel, $index_page_tpl, $list_page_tpl, $list_page_name) {
		$index_page            = $channel ['index_page'];
		$list_page             = $channel ['list_page'];
		$page ['title']        = $channel ['title'] ? $channel ['title'] : $channel ['name'];
		$page ['title2']       = $channel ['name'];
		$page ['keywords']     = $channel ['keywords'];
		$page ['description']  = $channel ['description'];
		$page ['channel']      = $channel ['refid'];
		$page ['hidden']       = 1;
		$user                  = whoami();
		$uid                   = $user->getUid();
		$gid                   = $user->getAttr('group_id', 0);
		$time                  = time();
		$page ['update_time']  = $time;
		$page ['update_uid']   = $uid;
		$page ['status']       = 2; // 已发布
		$page ['publish_time'] = time();
		$page ['deleted']      = 0;

		$chData            = array();
		$index_page_exists = $index_page && dbselect('id')->from('{cms_page}')->where(array('id' => $index_page))->count('id') > 0;
		if (!$index_page_exists) { // 新增栏目封面页
			if ($index_page > 0) {
				$page ['id'] = $index_page;
			}
			$page ['image']         = '';
			$page ['model']         = 'channel_index';
			$page ['template_file'] = $index_page_tpl;
			$page ['url']           = $channel ['url'];
			$page ['url_key']       = md5($page ['url']);
			$page ['create_time']   = $time;
			$page ['create_uid']    = $uid;
			$page ['gid']           = $gid;
			$rst                    = dbinsert($page)->into('{cms_page}')->exec();
			if ($rst) {
				$chData ['index_page'] = $rst [0];
			}
		} else {
			$page ['template_file'] = $index_page_tpl;
			$page ['url']           = $channel ['url'];
			$page ['url_key']       = md5($page ['url']);
			$rst                    = dbupdate('{cms_page}')->set($page)->where(array('id' => $index_page))->exec();
		}
		unset ($page ['create_time'], $page ['create_uid'], $page ['gid']);
		$list_page_exists = $list_page && dbselect('id')->from('{cms_page}')->where(array('id' => $list_page))->count('id') > 0;
		if (!$list_page_exists) {
			if ($list_page > 0) {
				$page ['id'] = $list_page;
			}
			$page ['image']         = '';
			$page ['model']         = 'channel_list';
			$page ['template_file'] = $list_page_tpl;
			$page ['url_pattern']   = $list_page_name;
			$page ['url']           = $channel ['list_page_url'];
			$page ['url_key']       = md5($page ['url']);
			$page ['create_time']   = $time;
			$page ['create_uid']    = $uid;
			$page ['gid']           = $gid;
			$rst                    = dbinsert($page)->into('{cms_page}')->exec();
			if ($rst) {
				$chData ['list_page'] = $rst [0];
			}
		} else {
			unset ($page ['image']);
			$page ['template_file'] = $list_page_tpl;
			$page ['url_pattern']   = $list_page_name;
			$page ['url']           = $channel ['list_page_url'];
			$page ['url_key']       = md5($page ['url']);
			$rst                    = dbupdate('{cms_page}')->set($page)->where(array('id' => $list_page))->exec();
		}
		if (!empty ($chData)) {
			dbupdate('{cms_channel}')->set($chData)->where(array('id' => $channel ['id']))->exec();
		}
	}

	public static function generateURL($id, &$page) {
		$rtn         = false;
		$url_changed = false;
		if (empty ($page ['url']) || strpos($page ['url'], '}')) {
			$channel = dbselect('id,default_url_pattern,path,basedir')->from('{cms_channel}')->where(array('refid' => $page ['channel']))->get();
			$arg     = array('aid' => $id, 'tid' => $channel ['id'], 'trid' => $page ['channel'], 'model' => $page ['model'], 'create_time' => $page ['create_time'], 'name' => $page ['title'], 'path' => $channel ['path'], 'basedir' => $channel ['basedir'], 'page' => 1, 'title' => $page ['title'], 'title2' => $page ['title2']);
			if (strpos($page ['url'], '}')) {
				$channel ['default_url_pattern'] = $page ['url'];
			}
			if ($channel ['default_url_pattern']) {
				$url              = parse_page_url($channel ['default_url_pattern'], $arg);
				$page ['url']     = $data ['url'] = $url;
				$data ['url_key'] = md5($url);
				$rst              = PageForm::checkURL($url, array('id' => $id), false);
				if ($rst) {
					dbupdate('{cms_page}')->set($data)->where(array('id' => $id))->exec();
					$page ['url_key'] = $data ['url_key'];
					$rtn              = true;
					$url_changed      = true;
				}
			}
		} else if ($page ['url']) {
			$rtn              = true;
			$data ['url_key'] = md5($page ['url']);
			if ($data ['url_key'] != $page ['url_key']) {
				$rst = PageForm::checkURL($page ['url'], array('id' => $id), false);
				if ($rst) {
					dbupdate('{cms_page}')->set($data)->where(array('id' => $id))->exec();
					$page ['url_key'] = $data ['url_key'];
					$url_changed      = true;
				} else {
					$rtn = false;
				}
			}
		}
		if (!$rtn) {
			$url              = uniqid('page_') . '.' . cfg('default_suffix@cms');
			$page ['url']     = $data ['url'] = $url;
			$data ['url_key'] = md5($url);
			$rst              = PageForm::checkURL($url, array('id' => $id), false);
			if ($rst) {
				dbupdate('{cms_page}')->set($data)->where(array('id' => $id))->exec();
				$page ['url_key'] = $data ['url_key'];
				$url_changed      = true;
				$rtn              = true;
			}
		}
		if ($url_changed) {
			fire('on_page_url_changed', $page);
		}

		return $rtn;
	}

	public static function generateKeywords($id, &$page) {
		$okeywords = rqst('okeywords');
		$gkeywords = rqst('gkeywords');

		if (!empty ($page ['keywords']) && $page ['keywords'] == $okeywords) {
			return;
		}
		$keywords = array(false, false);
		if (!empty ($page ['keywords'])) {
			$keywords = get_keywords($page ['keywords']);
		} else if ($gkeywords == 'on') {
			$keywords = get_keywords(null, $page ['title']);
		}
		list ($keywords, $key_index) = $keywords;
		if ($keywords) {
			$data ['keywords'] = $keywords;
			$page ['keywords'] = $keywords;
		} else {
			$data ['keywords'] = '';
			$page ['keywords'] = '';
		}
		if ($key_index) {
			$data ['search_index'] = $key_index;
		} else {
			$data ['search_index'] = '';
		}
		if (isset ($data)) {
			dbupdate('{cms_page}')->set($data)->where(array('id' => $id))->exec();
		}
	}

	public static function updateVariables($variable, $type) {
		$r                    = dbselect('id,deleted')->from('{cms_variables}')->where(array('type' => $type, 'val' => $variable))->limit(0, 1)->get();
		$user                 = whoami();
		$uid                  = $user->getUid();
		$time                 = time();
		$data ['update_time'] = $time;
		$data ['update_uid']  = $uid;
		if ($r && $r ['deleted']) {
			$data ['deleted'] = 0;
			dbupdate('{cms_variables}')->set($data)->where(array('id' => $r ['id']))->exec();
		} else if (!$r) {
			$data ['type']        = $type;
			$data ['val']         = $variable;
			$data ['create_uid']  = $uid;
			$data ['create_time'] = $time;
			dbinsert($data)->into('{cms_variables}')->exec();
		}
	}

	public static function checkGroupPrevilige($chanenl, $groups = false) {
		if (bcfg('enable_group_bind@cms')) {
			$I   = whoami();
			$uid = $I->getUid();
			if ($uid == 1) {
				return true;
			}
			if (empty ($groups)) {
				$groups = $I->getAttr('subgroups');
			}
			if (!is_array($groups) || empty ($groups)) {
				return false;
			}
			if (is_array($chanenl)) {
				if (isset ($chanenl ['gid'])) {
					return in_array($chanenl ['gid'], $groups);
				} else {
					return false;
				}
			}

			return dbselect()->from('{cms_channel}')->where(array('refid' => $chanenl, 'gid IN' => $groups))->exist('id');
		}

		return true;
	}

	public static function save($page_type, $model, $user, $ajax = true) {
		$formName = ucfirst($page_type) . 'Form';
		$form     = new DynamicForm ($formName);
		$cform    = null;
		$widgets  = ModelFieldForm::loadCustomerFields($form, $model);
		$page     = $form->valid();
		if ($page) {
			$contentModel = get_page_content_model($model);
			$cform        = $contentModel ? $contentModel->getForm() : false;
			if ($cform) {
				$cdata = $cform->valid();
				if ($cdata === false) {
					log_warn('无法保存页面:' . var_export($cform->getErrors(), true));

					return $ajax ? NuiAjaxView::validate($formName, '数据验证失败。', $cform->getErrors()) : false;
				}
			}
		}
		if ($page) {
			if ($user) {
				$gid = $user->getAttr('group_id', 0);
				$uid = $user->getUid();
			} else {
				$gid = 0;
				$uid = 1;
			}
			if (bcfg('enable_group_bind@cms')) {
				if (!CmsPage::checkGroupPrevilige($page ['channel'])) {
					log_warn('无法保存页面:你没有权限在此栏目下新增或编辑页面.');

					return $ajax ? NuiAjaxView::error('你没有权限在此栏目下新增或编辑页面.') : false;
				}
			}
			$form = new DynamicForm ($formName);
			$page = $form->toArray();
			$time = time();

			$id = $page ['id'];
			unset ($page ['id'], $page ['page_type'], $page ['redirect']);
			$page ['update_time'] = $time;
			$page ['update_uid']  = $uid;
			if (empty ($page ['view_count'])) {
				unset ($page ['view_count']);
			}

			if (empty ($page ['publish_time'])) {
				unset ($page ['publish_time']);
			}
			if (!empty ($page ['related_pages'])) {
				$page ['related_pages'] = safe_ids($page ['related_pages']);
			}
			$page = apply_filter('before_save_page', $page);
			$page = apply_filter('before_save_page_' . $page ['model'], $page);
			if ($page_type == 'page' && empty ($page ['image']) && rqst('firstimage') == 'on' && !empty ($page ['content'])) {
				if (preg_match_all('/<img\s+.*src\s*=\s*[\'"]([^\'"]+)[\'"].*/i', $page ['content'], $ms)) {
					// 生成缩略图
					$page ['image'] = $ms [1] [0];
					$w              = icfg('thumb_w@cms', 0);
					$h              = icfg('thumb_h@cms', 0);
					if ($w > 0 && $h > 0) {
						$uploader = MediaUploadHelper::getUploader(0);
						$img_url  = the_media_src($page ['image']);
						$rst      = ImageUtil::downloadRemotePic(array($img_url), $uploader, cfg('timeout@media', 30), false, array($w, $h));
						if ($rst) {
							$page ['image'] = $rst [ $img_url ] [0];
						}
					}
				}
			}
			if (empty ($page ['author'])) {
				$page ['author'] = $user ? $user->getDisplayName() : '';
			}
			if (empty ($page ['expire'])) {
				$page ['expire'] = 0;
			}
			// 去除空格
			foreach ($page as $fk => $val) {
				$page [ $fk ] = trim($val);
			}
			if (empty ($id)) {
				$page ['create_time'] = $time;
				$page ['create_uid']  = $uid;
				$page ['gid']         = $gid;
				$page ['status']      = icfg('page_new_status@cms', 3);
				// 发布或待发布且发布时间为空,则使用当前时间做为发布时间.
				if (in_array($page ['status'], array(2, 4)) && !isset ($page ['publish_time'])) {
					$page ['publish_time'] = time();
				}
				$rst = dbinsert($page)->into('{cms_page}')->exec();
				if ($rst) {
					$page ['is_new'] = true;
					$id              = $rst [0];
				}
			} else {
				$page ['status'] = icfg('page_update_status@cms', 1);
				if (in_array($page ['status'], array(2, 4)) && !isset ($page ['publish_time'])) {
					$page ['publish_time'] = time();
				}
				$rst                  = dbupdate('{cms_page}')->set($page)->where(array('id' => $id))->exec();
				$page ['create_time'] = irqst('create_time', time());
				if (!$rst) {
					$id = 0;
				}
			}
			if ($id) {
				// 保存页面自定义字段值
				$page ['id']      = $id;
				$page ['url_key'] = rqst('url_key');
				CmsPage::generateURL($id, $page);
				if ($page_type == 'page') { // 只有文章才自动生成关键词,添加常用变量
					CmsPage::generateKeywords($id, $page);
					CmsPage::updateVariables($page ['author'], 'author');
					if (!empty ($page ['source'])) {
						CmsPage::updateVariables($page ['source'], 'source');
					}
				}

				CmsPage::saveCustomerFieldValues($id, $page, $cform);
				$page = apply_filter('after_save_page', $page);
				$page = apply_filter('after_save_page_' . $page ['model'], $page);

				if (isset ($page ['is_new'])) {
					$html [] = '[<a href="javascript:void(0);" onclick="return add_next_page();">再添加一篇</a>]';
				}
				$rtn_url = apply_filter('get_content_list_page_url', tourl('cms/page/my/' . $page_type, false) . '?channel=' . $page ['channel'], $page);
				$html [] = '[<a href="#' . $rtn_url . '" onclick="nUI.closeAjaxDialog()">返回列表</a>]';
				$html [] = '[<a href="javascript:void(0);" onclick="return modify_current_page();">修改</a>]';

				$url = dbselect('CP.channel,CP.url,CC.root')->from('{cms_page} AS CP')->join('{cms_channel} AS CC', 'CP.channel = CC.refid');
				$url->where(array('CP.id' => $id));
				$url              = $url->get(0);
				$page ['root']    = $url ['root'];
				$page ['channel'] = $url ['channel'];
				$page ['url']     = $url ['url'];
				$url              = safe_url($url);
				$html             = apply_filter('get_extra_saved_actions', $html, $page);
				$html []          = '[<a href="' . $url . '?preview" target="_blank">预览</a>]';
				if (icando('cmc:system') && bcfg('enabled@mem')) {
					$html [] = '[<a href="' . $url . '?preview=_c2c_" target="_blank">预览并清空缓存</a>]';
				}

				return $ajax ? NuiAjaxView::dialog('<p class="text-left">请选择你的后续操作：</p><p class="text-left">' . implode('&nbsp;', $html) . '</p>', '保存完成!', array('model' => true, 'height' => 'auto', 'func' => 'pageSaved', 'page' => $page)) : $id;
			} else {
				log_warn('无法保存页面:无法保存，数据库出错.');

				return $ajax ? NuiAjaxView::error('无法保存，数据库出错.') : false;
			}
		} else {
			log_warn('无法保存页面:' . var_export($form->getErrors(), true));

			return $ajax ? NuiAjaxView::validate('PageForm', '请正确填写表单.', $form->getErrors()) : false;
		}
	}

	/*
	 * (non-PHPdoc) @see ArrayAccess::offsetExists()
	 */
	public function offsetExists($offset) {
		return isset ($this->fields [ $offset ]);
	}

	public function offsetGet($offset) {
		if (isset ($this->fields [ $offset ])) {
			return $this->fields [ $offset ];
		}

		return '';
	}

	public function offsetSet($offset, $value) {
		$this->fields [ $offset ] = $value;
	}

	public function offsetUnset($offset) {
		unset ($this->fields [ $offset ]);
	}
}