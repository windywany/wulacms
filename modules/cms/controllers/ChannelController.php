<?php

/**
 * 栏目.
 *
 * @author Guangfeng
 */
class ChannelController extends Controller {
	protected $checkUser = true;
	protected $acls      = array('index' => 'r:cms/channel', 'add' => 'c:cms/channel', 'edit' => 'u:cms/channel', 'save' => 'id|u:cms/channel;c:cms/channel', 'updateurl' => 'cu:cms/channel', 'updateurl_post' => 'cu:cms/channel');

	public function index($type = 0) {
		$data = array();
		$this->prepareData($data, $type);
		$data ['canAddChannel']    = icando('c:cms/channel');
		$data ['canDeleteChannel'] = icando('d:cms/channel');
		$data ['canEditChannel']   = icando('u:cms/channel');
		$data ['canUpdateURL']     = icando('cu:cms/channel');

		return view('channel/index.tpl', $data);
	}

	public function add($type = 0, $upid = 0) {
		$upid            = intval($upid);
		$data            = array();
		$type            = $this->prepareData($data, $type);
		$data ['models'] = array('' => '请选择内容模型');
		dbselect('*')->from('{cms_model}')->treeWhere(array('deleted' => 0, 'is_topic_model' => $type, 'creatable' => 1, 'hidden' => 0, 'status' => 1))->treeKey('refid')->treeOption($data ['models']);
		$form                    = new ChannelForm ();
		$data ['isfinal']        = 1;
		$data ['page_name']      = 'index.html';
		$data ['page_cache']     = 0;
		$data ['index_page_tpl'] = 'category.tpl';

		$data ['default_url_pattern'] = '{path}/{aid}.html';
		$data ['default_cache']       = 0;
		$data ['default_template']    = 'article.tpl';

		$data ['list_page_name'] = '{path}/list.html';
		$data ['list_cache']     = 0;
		$data ['list_page_tpl']  = 'category_list.tpl';

		$data ['list_page']  = 0;
		$data ['index_page'] = 0;
		$data ['upid']       = $upid ? $upid : 0;
		$data ['oupid']      = 0;
		if ($upid) {
			$updata = dbselect('page_name,default_model,default_template,default_url_pattern,list_page as list_page_id')->from('{cms_channel}')->where(array('id' => $upid))->get(0);
			if ($updata) {
				$list_page = dbselect('url_pattern,template_file')->from('{cms_page}')->where(array('id' => $updata ['list_page_id']))->get(0);
				if ($list_page) {
					$updata ['list_page_name'] = $list_page ['url_pattern'];
				}
				$data = array_merge($data, $updata);
			}
		}
		$data ['groups'] = array('' => '--请选择用户组--');
		dbselect()->from('{user_group}')->treeOption($data ['groups'], 'group_id', 'upid', 'group_name');
		$data ['rules']             = $form->rules();
		$data ['enable_group_bind'] = bcfg('enable_group_bind@cms');

		return view('channel/form.tpl', $data);
	}

	public function edit($id) {
		$id = intval($id);
		$ch = dbselect('*')->from('{cms_channel}')->where(array('id' => $id))->get(0);
		if ($ch) {
			$type          = $ch ['is_topic_channel'] ? 1 : 0;
			$type          = $this->prepareData($ch, $type);
			$ch ['models'] = array('' => '请选择内容模型');
			dbselect('*')->from('{cms_model}')->treeWhere(array('deleted' => 0, 'hidden' => 0, 'is_topic_model' => $type, 'creatable' => 1, 'status' => 1))->treeKey('refid')->treeOption($ch ['models']);

			$list_page = dbselect('url_pattern,template_file')->from('{cms_page}')->where(array('id' => $ch ['list_page']))->get(0);
			if ($list_page) {
				$ch ['list_page_name'] = $list_page ['url_pattern'];
				$ch ['list_page_tpl']  = $list_page ['template_file'];
			}
			$index_page = dbselect('template_file')->from('{cms_page}')->where(array('id' => $ch ['index_page']))->get(0);
			if ($index_page) {
				$ch ['index_page_tpl'] = $index_page ['template_file'];
			}
			$form                     = new ChannelForm ($ch);
			$ch ['rules']             = $form->rules();
			$ch ['oupid']             = $ch ['upid'];
			$ch ['enable_group_bind'] = bcfg('enable_group_bind@cms');
			$ch ['groups']            = array('' => '--请选择用户组--');
			dbselect()->from('{user_group}')->treeOption($ch ['groups'], 'group_id', 'upid', 'group_name');

			return view('channel/form.tpl', $ch);
		} else {
			Response::showErrorMsg('内容不存在', 404);
		}
	}

	public function del($id = 0) {
		if (empty ($id)) {
			Response::showErrorMsg('栏目不存在', 404);
		}

		$channel = dbselect('upid,is_topic_channel')->from('{cms_channel}')->where(array('id' => $id))->get();
		if (empty ($channel)) {
			Response::showErrorMsg('栏目不存在', 404);
		}

		if (dbselect()->from('{cms_channel}')->where(array('upid' => $id))->exist('id')) {
			return NuiAjaxView::error('请先删除它的子栏目与文章');
		}

		if (dbselect()->from('{cms_page} AS CP')->join('{cms_channel} AS CH', 'CP.channel = CH.refid')->where(array('CP.hidden' => 0, 'CH.id' => $id))->exist('CP.id')) {
			return NuiAjaxView::error('此栏目或其子栏目下有页面存在,无法删除!请先删除这些内容.');
		}
		$upid = $channel['upid'];
		dbdelete()->from('{cms_channel}')->where(array('id' => $id))->exec();
		fire('on_destroy_cms_channel', [$id]);
		if ($upid) {
			// 取所有组数据
			$channels = dbselect('upid,id')->from('{cms_channel}')->where(array('is_topic_channel' => $channel ['is_topic_channel']))->toArray();
			// 遍历树形数据
			$iterator = new TreeIterator ($channels, 0, 'id', 'upid');
			$nodes    = array();
			// 取当前栏目
			$node = $iterator->getNode($upid);
			// 取当前栏目的上级栏目
			$node->getParents($nodes);
			unset ($nodes ['0'], $nodes [0]);
			// 更新它们的subchannels
			foreach ($nodes as $nid => $node) {
				$ids = implode(',', $node->getSubIds());
				dbupdate('{cms_channel}')->set(array('subchannels' => $ids))->where(array('id' => $nid))->exec();
			}
			$ids = $node->getSubIds();
			dbupdate('{cms_channel}')->set(array('subchannels' => implode(',', $ids)))->where(array('id' => $upid))->exec();
		}

		return NuiAjaxView::refresh('栏目已经删除');
	}

	/**
	 * 排序.
	 *
	 * @param int $id
	 * @param int $sort
	 *
	 * @return NuiAjaxView
	 */
	public function csort($id, $sort) {
		$id   = intval($id);
		$sort = intval($sort);
		if (!empty ($id)) {
			dbupdate('{cms_channel}')->set(array('sort' => $sort))->where(array('id' => $id))->exec();
		}

		return NuiAjaxView::reload('#channel-table');
	}

	public function save() {
		$form = new ChannelForm ();
		$ch   = $form->valid();
		if ($ch) {
			$index_page_tpl = $ch ['index_page_tpl'];
			$list_page_tpl  = $ch ['list_page_tpl'];
			$list_page_name = $ch ['list_page_name'];
			unset ($ch ['index_page_tpl'], $ch ['list_page_tpl'], $ch ['list_page_name']);
			// 默认页面是否链接到列表页
			$default_page = rqst('default_page');
			if ($default_page == 'on') {
				$ch ['default_page'] = 1;
			} else {
				$ch ['default_page'] = 0;
			}
			$time               = time();
			$uid                = $this->user->getUid();
			$ch ['update_uid']  = $uid;
			$ch ['update_time'] = $time;
			if ($ch ['isfinal'] == 'on') {
				$ch ['isfinal'] = 1;
			} else {
				$ch ['isfinal'] = 0;
			}
			if ($ch ['hidden'] == 'on') {
				$ch ['hidden'] = 1;
			} else {
				$ch ['hidden'] = 0;
			}
			if (empty ($ch ['gid'])) {
				$ch ['gid'] = 0;
			}
			if (empty ($ch ['sort'])) {
				$ch ['sort'] = 999;
			}
			if (empty ($ch ['page_cache'])) {
				$ch ['page_cache'] = 0;
			}
			if (empty ($ch ['list_cache'])) {
				$ch ['list_cache'] = 0;
			}
			if (empty ($ch ['default_cache'])) {
				$ch ['default_cache'] = 0;
			}
			$id = $ch ['id'];
			unset ($ch ['id']);

			if (empty ($ch ['basedir'])) {
				// 将栏目名的拼音做为栏目的路径
				$ch ['basedir'] = Pinyin::c($ch ['name']);
			}
			$path = cfg('htmlpath', '');
			if ($ch ['upid']) {
				$path = $path . '/' . dbselect('path')->from('{cms_channel}')->where(array('id' => $ch ['upid']))->get('path');
			}
			$ch ['path'] = ltrim($path . '/' . $ch ['basedir'], '/');
			if (strpos($ch ['page_name'], '}')) {
				$paths = explode('/', $ch ['path']);
				array_shift($paths);
				$ch ['url'] = str_replace(array('{path}', '{rpath}'), array($ch ['path'], implode('/', $paths)), $ch ['page_name']);
			} else {
				$ch ['url'] = $ch ['path'] . '/' . $ch ['page_name'];
			}
			$urlkey = md5($ch ['url']);
			if (dbselect('id')->from('{cms_page}')->where(array('url_key' => $urlkey, 'id !=' => $ch ['index_page']))->exist()) {
				return NuiAjaxView::error('页面保存目录已经被使用请重新填写.');
			}
			// 过后需要重新生成列表页的URL
			$ch ['list_page_url'] = $list_page_name;

			if (empty ($id)) { // 新增
				$ch ['create_uid']  = $uid;
				$ch ['create_time'] = $time;
				$rst                = dbinsert($ch)->into('{cms_channel}')->exec();
				if ($rst) {
					$id = $rst [0];
					dbupdate('{cms_channel}')->set(array('subchannels' => $id))->where(array('id' => $id))->exec();
				}
			} else { // 修改
				$rst = dbupdate('{cms_channel}')->set($ch)->where(array('id' => $id))->exec();
			}
			if ($rst) {
				$channel  = dbselect('*')->from('{cms_channel}')->where(array('id' => $id))->get(0);
				$arg      = array('tid' => $id, 'trid' => $channel ['refid'], 'model' => $channel ['default_model'], 'create_time' => $channel ['create_time'], 'name' => $channel ['name'], 'title' => $channel['name'], 'path' => $channel ['path'], 'basedir' => $channel ['basedir'], 'page' => 1);
				$list_url = parse_page_url($list_page_name, $arg);
				if (dbselect('id')->from('{cms_page}')->where(array('url_key' => md5($list_url), 'id !=' => $ch ['list_page']))->exist()) {
					return NuiAjaxView::error('列表页面的URL已经存在，请重新指定规则.', 'callback', array('id' => $id, 'func' => 'InvalidListPagePattern'));
				}
				dbupdate('{cms_channel}')->set(array('list_page_url' => $list_url))->where(array('id' => $id))->exec();
				$channel ['list_page_url'] = $list_url;
				CmsPage::updateChannelPage($channel, $index_page_tpl, $list_page_tpl, $list_page_name);

				// 更新栏目的下级栏目
				$oupid = irqst('oupid', 0);
				if ($oupid != $channel ['upid']) {
					// 取所有组数据
					$channels = dbselect('upid,id,refid')->from('{cms_channel}')->where(array('is_topic_channel' => $channel ['is_topic_channel']))->toArray();
					// 遍历树形数据
					$iterator = new TreeIterator ($channels, 0, 'id', 'upid');
					$nodes    = array();
					// 取当前栏目
					$node = $iterator->getNode($id);
					// 取当前栏目的上级栏目
					$node->getParents($nodes);
					// 取原上级栏目并合并到当前栏目的上级栏目中,因为他们的子栏目都发生了变化.
					$nodes [ $oupid ] = $iterator->getNode($oupid);
					$nodes [ $oupid ]->getParents($nodes);
					unset ($nodes ['0'], $nodes [0]);
					// 更新它们的subchannels
					foreach ($nodes as $nid => $node) {
						$ids = implode(',', $node->getSubIds());
						dbupdate('{cms_channel}')->set(array('subchannels' => $ids))->where(array('id' => $nid))->exec();
					}
					// 取当前栏目
					$node = $iterator->getNode($id);
					// 取当前栏目的所有子栏目,因为他们的上级栏目发生了变化.
					$nodes         = $node->getChildren();
					$nodes [ $id ] = $node;
					foreach ($nodes as $nid => $node) {
						$parents = $node->getParentsIdList('refid');
						if ($parents) {
							$len     = count($parents) - 1;
							$root    = $parents [ $len ];
							$parents = implode(',', $parents);
						} else {
							$data    = $node->getData();
							$root    = $data ['refid'];
							$parents = '';
						}
						dbupdate('{cms_channel}')->set(array('parents' => $parents, 'root' => $root))->where(array('id' => $nid))->exec();
					}
				} else if ($channel ['upid'] == 0) {
					dbupdate('{cms_channel}')->set(array('root' => $channel ['refid']))->where(array('id' => $id))->exec();
				}
				fire('after_save_channel', $channel);
			}

			if ($rst) {
				return NuiAjaxView::ok('保存成功', 'click', '#rtnbtn');
			} else {
				return NuiAjaxView::error('保存出错啦:' . DatabaseDialect::$lastErrorMassge);
			}
		} else {
			return NuiAjaxView::validate('ChannelForm', '表单不正确，请重新填写.', $form->getErrors());
		}
	}

	/**
	 * 更新URL
	 *
	 * @param id  $id
	 * @param abc $flags
	 */
	public function updateurl($id, $flags) {
		$data    = array('success' => false);
		$channel = dbselect('refid,subchannels')->from('{cms_channel}')->where(array('id' => $id))->get(0);
		if (!$channel) {
			$data ['msg'] = '栏目不存在.';
		} else {
			$where ['CP.deleted'] = 0;
			$where ['CP.hidden']  = 0;
			$data ['flags']       = $flags;
			$tq                   = dbselect()->from('{cms_page} AS CP');
			if ($flags) {
				$data ['chs']       = $channel ['subchannels'];
				$where ['CH.id IN'] = explode(',', $channel ['subchannels']);
				$tq->join('{cms_channel} AS CH', 'CH.refid = CP.channel');
			} else {
				$data ['chs']      = $channel ['refid'];
				$where ['channel'] = $channel ['refid'];
			}
			$total            = $tq->where($where)->count('CP.id');
			$data ['start']   = 0;
			$data ['total']   = $total;
			$data ['success'] = true;
		}

		return new JsonView ($data);
	}

	public function updateurl_post($start, $total, $flags, $chs) {
		$limit                = 50;
		$where ['CP.deleted'] = 0;
		$where ['CP.hidden']  = 0;
		$tq                   = dbselect('CP.*')->from('{cms_page} AS CP');
		if ($flags) {
			$where ['CH.id IN'] = explode(',', $chs);
			$tq->join('{cms_channel} AS CH', 'CH.refid = CP.channel');
		} else {
			$where ['channel'] = $chs;
		}

		$tq->where($where)->limit($start, $limit)->sort('CP.id', 'a');
		foreach ($tq as $p) {
			$p ['url'] = '';
			if (!CmsPage::generateURL($p ['id'], $p)) {
				log_warn('cannot update url for ' . $p ['id']);
			}
		}
		$data ['start']   = $start + $limit;
		$data ['total']   = $total;
		$data ['chs']     = $chs;
		$data ['flags']   = $flags;
		$data ['success'] = true;

		return new JsonView ($data);
	}

	public function data($type = 0, $_tid = '', $_cp = 1, $_lt = 20, $_sf = 'CH.sort', $_od = 'a', $_ct = 0) {
		$data                      = array();
		$type                      = $this->prepareData($data, $type);
		$data ['canAddChannel']    = icando('c:cms/channel');
		$data ['canDeleteChannel'] = icando('d:cms/channel');
		$data ['canEditChannel']   = icando('u:cms/channel');
		$data ['canUpdateURL']     = icando('cu:cms/channel');
		$items                     = dbselect('CH.id,CH.hidden,CH.upid,CH.refid,CH.sort,CH.name,CH.url,CH.list_page_url,isfinal,default_page,CM.name AS modelName,CH.root')->from('{cms_channel} AS CH');
		$items->join('{cms_model} AS CM', 'CH.default_model = CM.refid');
		$where = array('CH.deleted' => 0, 'is_topic_channel' => $type);

		$keywords = rqst('keywords');
		if ($keywords) {
			$v               = "%{$keywords}%";
			$where []        = array('CH.name LIKE' => $v, '||CH.refid LIKE' => $v);
			$data ['search'] = 'true';
		} else {
			$where ['CH.upid'] = $_tid;
			$data ['search']   = false;
		}
		if ($_tid) {
			$_cp = 1;
			$_lt = 1000;
		}
		if (!$data ['search']) {
			$cnt = dbselect(imv('COUNT(CH1.id)'))->from('{cms_channel} AS CH1')->where(array('CH1.upid' => imv('CH.id')));
			$items->field($cnt, 'child_cnt');
		}
		$items->where($where);
		$total = '';
		if ($_ct) {
			$total = $items->count('CH.id');
		}
		$data ['total'] = $total;
		$data ['items'] = $items->asc('sort')->limit(($_cp - 1) * $_lt, $_lt);
		$data ['_tid']  = $_tid;

		return view('channel/data.tpl', $data);
	}

	private function prepareData(&$data, $type) {
		if ($type) {
			$type                 = 1;
			$data ['channelType'] = '专题分类';
		} else {
			$type                 = 0;
			$data ['channelType'] = '网站栏目';
		}
		$data ['type'] = $type;

		return $type;
	}
}