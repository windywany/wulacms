<?php

class PageController extends Controller {
	protected $checkUser = true;
	protected $acls      = array('*' => 'm:mobi/ch');

	public function index($channel = '') {
		$data ['channels'] = $this->getMyChannels();
		$data ['models']   = array('' => '请选择内容模型');
		dbselect()->from('{cms_model}')->treeWhere(array('deleted' => 0, 'hidden' => 0))->treeKey('refid')->treeOption($data ['models']);
		$data ['channel']    = $channel;
		$data ['canPublish'] = icando('pb:mobi');

		return view('page/index.tpl', $data);
	}

	public function cursouel($channel) {
		if (empty ($channel)) {
			Response::respond(404);
		}
		$data ['models'] = array('' => '请选择内容模型');
		dbselect()->from('{cms_model}')->treeWhere(array('deleted' => 0, 'hidden' => 0))->treeKey('refid')->treeOption($data ['models']);
		$data ['canPublish'] = icando('pb:mobi');
		$data ['channel']    = $channel;
		$data ['channels']   = $this->getMyChannels();

		return view('page/cursouel.tpl', $data);
	}

	public function csort($id, $sort) {
		$id   = intval($id);
		$sort = intval($sort);
		if (!empty ($id)) {
			dbupdate('{mobi_page}')->set(array('sort' => $sort))->where(array('id' => $id))->exec();
		}

		return NuiAjaxView::reload('#page-table');
	}

	public function del($ids) {
		$ids = safe_ids2($ids);
		if (!empty ($ids)) {
			if (dbupdate('{mobi_page}')->set(array('deleted' => 1, 'status' => 0))->where(array('id IN' => $ids))->exec()) {
				$recycle = new DefaultRecycle ($ids, 'MobiPage', 'mobi_page', '({id})[{page_id}]{title}');
				RecycleHelper::recycle($recycle);

				return NuiAjaxView::reload('#page-table', '所选页面已放入回收站.');
			} else {
				return NuiAjaxView::error('数据库操作失败.');
			}
		} else {
			return NuiAjaxView::error('请选择要删除的页面');
		}
	}

	public function publish($action = '', $ids = '') {
		$text = $action === '0' ? '撤回' : '发布';
		if (icando('pb:mobi')) {
			$ids = safe_ids2($ids);
			if (!empty ($ids)) {
				$page ['update_time'] = $page ['publish_time'] = time();
				$page ['publish_day'] = date('Y-m-d', $page ['publish_time']);
				$page ['status']      = $action === '0' ? 0 : 1;
				$page ['update_uid']  = $this->user->getUid();
				dbupdate('{mobi_page}')->set($page)->where(array('id IN' => $ids))->exec();

				return NuiAjaxView::reload('#page-table', '恭喜！所选页面已经成功' . $text . '。');
			} else {
				return NuiAjaxView::error("请选择要'.$text.'的页面");
			}
		} else {
			return NuiAjaxView::error('你没有' . $text . '页面的权限，请与您的上级联系.');
		}
	}

	public function getview($cid, $pid) {
		$view = '';
		if ($cid && $pid) {
			$view = dbselect()->from('{mobi_page}')->where(array('channel' => $cid, 'page_id' => $pid))->get('list_view');
			if (is_null($view)) {
				$view = '';
			}
		}

		return new JsonView (array('success' => true, 'view' => $view));
	}

	public function edit($id, $channel = '', $lv = '') {
		$page = dbselect('channel,title,title2,description,model')->from('{cms_page}')->where(array('id' => $id))->get(0);
		if ($page) {
			$page ['id'] = $id;
			$binds       = dbselect('MCH.refid,MCH.name')->from('{mobi_channel_binds} AS MCB')->join('{mobi_channel} AS MCH', 'MCB.mobi_refid = MCH.refid')->where(array('MCB.cms_refid' => $page ['channel']))->toArray('name', 'refid');
			$eChannels   = dbselect('channel,list_view')->from('{mobi_page}')->where(array('page_id' => $id))->toArray('list_view', 'channel');
			$dfChannel   = '';
			$dfLv        = '';
			if ($eChannels) {
				foreach ($binds as $id => $name) {
					if (array_key_exists($id, $eChannels)) {
						$binds [ $id ] = '[*]' . $name;
						$dfChannel     = $id;
						$dfLv          = $eChannels [ $id ];
					}
				}
			}
			$page ['channels']  = $binds;
			$page ['channel']   = $channel ? $channel : $dfChannel;
			$page ['list_view'] = $lv ? $lv : $dfLv;
			$form               = new MobiPageForm ($page);
			$data ['widgets']   = new DefaultFormRender ($form->buildWidgets($page));
			$data ['rules']     = $form->rules();

			return view('page/editor.tpl', $data);
		} else {
			Response::respond(404);
		}
	}

	/**
	 *
	 * @param unknown $id
	 * @param unknown $ch
	 * @param unknown $lv
	 */
	public function edit2($id, $rtn) {
		$page = dbselect('MCP.*,CP.model,CP.title,CP.title2,CP.description,CP.channel AS cms_channel,CP.flag_a,CP.flag_c,CP.flag_h,CH.is_topic_channel,CP.image')->from('{mobi_page} AS MCP');
		$page->join('{cms_page} AS CP', 'MCP.page_id = CP.id');
		$page->join('{cms_channel} AS CH', 'CP.channel = CH.refid');
		$page = $page->where(array('MCP.id' => $id))->get();
		if ($page) {
			$lv    = $page ['list_view'];
			$lvs   = MobiListView::getListViews();
			$lvClz = isset ($lvs [ $lv ]) ? $lvs [ $lv ] : null;

			if (!$lvClz) {
				Response::respond(404);
			}

			$pageViews          = dbselect('refid,name')->from('{mobi_page_view}')->where(array('models LIKE' => '%,' . $page ['model'] . ',%'))->toArray('name', 'refid');
			$page ['pageviews'] = $pageViews;

			$binds             = dbselect('MCH.refid,MCH.name')->from('{mobi_channel_binds} AS MCB')->join('{mobi_channel} AS MCH', 'MCB.mobi_refid = MCH.refid')->where(array('MCB.cms_refid' => $page ['cms_channel']))->toArray('name', 'refid');
			$page ['channels'] = $binds;

			$custom_data = @json_decode($page ['custom_data'], true);

			if ($custom_data) {
				$page ['title'] = isset ($custom_data ['title']) && $custom_data ['title'] ? $custom_data ['title'] : ($page ['title'] ? $page ['title'] : $page ['title2']);
				$page ['desc']  = isset ($custom_data ['desc']) && $custom_data ['desc'] ? $custom_data ['desc'] : $page ['description'];
				unset ($custom_data ['title'], $custom_data ['desc']);
				$page = array_merge($page, $custom_data);

			}

			$inherit_flags = array();

			if ($page ['flag_a']) {
				$inherit_flags [] = 'a';
			}

			if ($page ['flag_c']) {
				$inherit_flags [] = 'c';
			}

			if ($page ['flag_h']) {
				$inherit_flags [] = 'h';
			}

			if ($page ['is_topic_channel']) {
				$inherit_flags [] = 't';
			}

			$page ['flags'] = $page ['flags'] ? explode(',', $page ['flags']) : $inherit_flags;

			$page ['listViewClz'] = $lvClz ['clz'];

			$form = new MobiPageForm2 ($page);

			$page ['widgets']    = new DefaultFormRender ($form->buildWidgets($page));
			$page ['rules']      = $form->rules();
			$page ['rtn']        = $rtn;
			$page ['canPublish'] = icando('pb:mobi');

			return view('page/editor2.tpl', $page);
		} else {
			Response::respond(404);
		}
	}

	/**
	 * 第一步保存.
	 *
	 * @return NuiAjaxView
	 */
	public function save($id) {
		// TODO: 可以不从数据库中选择.
		$page = dbselect('id')->from('{cms_page}')->where(array('id' => intval($id)))->get();
		if ($page) {
			$page ['channel']   = rqst('channel');
			$page ['list_view'] = rqst('list_view');
			$form               = new MobiPageForm ($page);
			$data               = $form->valid();
			if ($data) {
				$pg ['page_id']     = $where ['page_id'] = $id;
				$pg ['channel']     = $where ['channel'] = $data ['channel'];
				$pg ['update_time'] = time();
				$pg ['update_uid']  = $this->user->getUid();
				$pg ['list_view']   = $data ['list_view'];
				$pg ['deleted']     = 0;
				$mpg                = dbselect('id,status,deleted')->from('{mobi_page}')->where($where)->get();
				if ($mpg) { // 更新
					if ($mpg ['deleted']) {
						RecycleHelper::restore(array($mpg ['id']));
						$pg ['flags']       = '';
						$pg ['title']       = '';
						$pg ['custom_data'] = '';
						$pg ['is_carousel'] = 0;
					}
					$mpid = $mpg ['id'];
					if ($mpg ['status'] == 1) {
						$mpg ['status'] = 0;
					}
					$pg  = array_merge($mpg, $pg);
					$rst = dbupdate('{mobi_page}')->set($pg)->where($where)->exec();
				} else { // 新增
					$pg ['create_time'] = $pg ['publish_time'] = $pg ['update_time'];
					$pg ['create_uid']  = $pg ['update_uid'];
					$pg ['status']      = 2;
					$rst                = dbinsert($pg)->into('{mobi_page}')->exec();
					if ($rst) {
						$mpid = $rst [0];
					}
				}
				if ($rst) {
					return NuiAjaxView::callback('runMobiEditFormSetp2', array('id' => $mpid));
				} else {
					return NuiAjaxView::error('无法保存数据.');
				}
			} else {
				return NuiAjaxView::validate('MobiPageForm', '', $form->getErrors());
			}
		} else {
			return NuiAjaxView::callback('closeMobiEditForm', array('id' => 'mobiapp-edit-page-form'));
		}
	}

	public function save2($id) {
		$lv = dbselect()->from('{mobi_page}')->where(array('id' => $id))->get('list_view');
		if ($lv) {
			$lvs   = MobiListView::getListViews();
			$lvClz = isset ($lvs [ $lv ]) ? $lvs [ $lv ] : null;

			if (!$lvClz) {
				NuiAjaxView::error('布局样式已经不存在，请修改.');
			} else {
				$data ['listViewClz'] = $lvClz ['clz'];
				$data ['id']          = $id;
				$form                 = new MobiPageForm2 ($data);
				$page                 = $form->valid();
				if ($page) {
					$mp ['update_time'] = time();
					$mp ['update_uid']  = $this->user->getUid();
					if ('1' == rqst('publish_flag') && icando('pb:mobi')) {
						$mp ['status']       = 1;
						$mp ['publish_time'] = time();
						$mp ['publish_day']  = date('Y-m-d', $mp ['publish_time']);
					} else {
						$mp ['status'] = 0;
					}
					$mp ['page_view']      = $page ['page_view'];
					$mp ['flags']          = empty ($page ['flags']) ? '' : implode(',', $page ['flags']);
					$mp ['title']          = $page ['title'];
					$custum_data ['title'] = $page ['title'];
					$custum_data ['desc']  = $page ['desc'];
					$ctsdata               = $data ['listViewClz']->getCustomData($page);
					$mp ['is_carousel']    = $data ['listViewClz']->isCarousel();
					if (is_array($ctsdata)) {
						$custum_data = array_merge($ctsdata, $custum_data);
					}
					$mp ['custom_data'] = json_encode($custum_data);
					if (dbupdate('{mobi_page}')->set($mp)->where(array('id' => $id))->exec()) {
						return NuiAjaxView::callback('mobiPageSaved', array(), '移动内容已经保存.');
					} else {
						return NuiAjaxView::error('无法保存内容，数据库出错.');
					}
				} else {
					return NuiAjaxView::validate('MobiPageForm2', null, $form->getErrors());
				}
			}
		} else {
			return NuiAjaxView::error('布局样式已经不存在，请修改.');
		}
	}

	public function data($_cp = 1, $_lt = 20, $_sf = 'MCP.publish_time', $_od = 'd', $_ct = 0) {
		$rows = dbselect('MCP.id,MCP.page_id,MCP.page_view,MCP.sort,MCP.list_view,PGV.name as page_view_name,MCP.custom_data,MCP.flags,CP.title,CP.title2,MCP.status,MCP.update_time,MCP.create_time,CP.image,
				MCP.publish_time,CH.name as channelName,CM.name AS modelName,CU.nickname as cuname,UU.nickname as uuname')->from('{mobi_page} AS MCP')->join('{cms_page} AS CP', 'MCP.page_id = CP.id');
		$rows->field('UU.nickname AS uuname');
		$rows->join('{cms_channel} AS CH', 'CP.channel = CH.refid');
		$rows->join('{cms_model} AS CM', 'CP.model = CM.refid');
		$rows->join('{user} AS CU', 'MCP.create_uid = CU.user_id');
		$rows->join('{user} AS UU', 'MCP.update_uid = UU.user_id');
		$rows->join('{mobi_page_view} AS PGV', 'PGV.refid = MCP.page_view');
		$where ['MCP.deleted'] = 0;
		$where ['CP.deleted']  = 0;
		$where ['CP.hidden']   = 0;
		$where ['is_carousel'] = 0;
		$uid                   = $this->user->getUid();
		$channel               = rqst('channel');
		if ($channel && icando('m_' . $channel . ':mobi/ch')) {
			$where ['MCP.channel'] = $channel;
			$has_carousel          = false;
			$show_carousel         = false;
			$pid                   = irqst('pid');
			if ($pid) {
				$where ['CP.id'] = $pid;
			} else {
				$status = rqst('status');
				if (is_numeric($status)) {
					$where ['MCP.status'] = intval($status) > 0 ? 1 : 0;
				} else {
					$where ['MCP.status <'] = 2;
				}
				if ($status == '1') {
					$has_carousel = dbselect()->from('{mobi_channel}')->where(array('refid' => $channel))->get('has_carousel');
					$has_carousel = 1 == $has_carousel;
					if ($has_carousel && 1 == $_cp) {
						$_lt -= 1;
						$show_carousel = true;
					}
				}
				$uuname = irqst('uuname');
				if ($uuname) {
					$where ['CP.create_uid'] = $uuname;
				}
				$model = rqst('model');
				if ($model) {
					$where ['CP.model'] = $model;
				} else {
					$where ['CP.model !='] = '_customer_page';
				}

				$keywords = rqst('keywords');
				if ($keywords) {
					$t        = '%' . $keywords . '%';
					$keywords = convert_search_keywords($keywords);
					$where [] = array('search_index MATCH' => $keywords, '||CP.title LIKE' => $t, '||CP.title2 LIKE' => $t);
				}
			}
			$rows->where($where);
			$rows->sort('MCP.publish_day', 'd');
			$rows->sort('MCP.sort', 'a');
			$rows->limit(($_cp - 1) * $_lt, $_lt);
			$data           = array();
			$data ['total'] = '';
			if ($_ct) {
				$data ['total'] = $rows->count('MCP.id');
			}
			$_rows = array();
			if ($show_carousel) {
				$cursouel            = $this->getCursouelData($channel);
				$row ['id']          = 'cursouel';
				$row ['list_view']   = 'cursouel';
				$row ['custom_data'] = $cursouel;
				$row ['channel']     = $channel;
				$_rows []            = $row;
				$data ['total'] += 1;
			}

			foreach ($rows as $row) {
				if ($row ['custom_data']) {
					$row ['custom_data'] = @json_decode($row ['custom_data'], true);
				} else {
					$row ['custom_data'] = array();
				}
				$row ['custom_edit_page'] = false;
				$_rows []                 = $row;
			}
			$data ['listTypes'] = MobiListView::getListViews();
			$data ['rows']      = $_rows;
			$data ['channel']   = $channel;
		}

		return view('page/data.tpl', $data);
	}

	public function cursoueldata($_cp = 1, $_lt = 20, $_sf = 'MCP.publish_time', $_od = 'd', $_ct = 0) {
		$rows = dbselect('MCP.id,MCP.page_id,MCP.page_view,MCP.list_view,MCP.sort,PGV.name as page_view_name,MCP.custom_data,MCP.flags,CP.title,CP.title2,MCP.status,MCP.update_time,MCP.create_time,CP.image,
				MCP.publish_time,CH.name as channelName,CM.name AS modelName,CU.nickname as cuname,UU.nickname as uuname')->from('{mobi_page} AS MCP')->join('{cms_page} AS CP', 'MCP.page_id = CP.id');
		$rows->field('UU.nickname AS uuname');
		$rows->join('{cms_channel} AS CH', 'CP.channel = CH.refid');
		$rows->join('{cms_model} AS CM', 'CP.model = CM.refid');
		$rows->join('{user} AS CU', 'MCP.create_uid = CU.user_id');
		$rows->join('{user} AS UU', 'MCP.update_uid = UU.user_id');
		$rows->join('{mobi_page_view} AS PGV', 'PGV.refid = MCP.page_view');
		$where ['MCP.deleted'] = 0;
		$where ['CP.deleted']  = 0;
		$where ['CP.hidden']   = 0;
		$where ['is_carousel'] = 1;
		$channel               = rqst('channel');
		if ($channel && icando('m_' . $channel . ':mobi/ch')) {
			$where ['MCP.channel'] = $channel;
			$pid                   = irqst('pid');
			if ($pid) {
				$where ['CP.id'] = $pid;
			} else {
				$uuname = irqst('uuname');
				if ($uuname) {
					$where ['CP.create_uid'] = $uuname;
				}
				$model = rqst('model');
				if ($model) {
					$where ['CP.model'] = $model;
				} else {
					$where ['CP.model !='] = '_customer_page';
				}
				$status = rqst('status');
				if (is_numeric($status)) {
					$where ['MCP.status'] = intval($status) > 0 ? 1 : 0;
				} else {
					$where ['MCP.status <'] = 2;
				}
				$keywords = rqst('keywords');
				if ($keywords) {
					$t        = '%' . $keywords . '%';
					$keywords = convert_search_keywords($keywords);
					$where [] = array('search_index MATCH' => $keywords, '||CP.title LIKE' => $t, '||CP.title2 LIKE' => $t);
				}
			}
			$rows->where($where);
			$rows->sort('MCP.publish_day', 'd');
			$rows->sort('MCP.sort', 'a');
			$rows->limit(($_cp - 1) * $_lt, $_lt);
			$data           = array();
			$data ['total'] = '';
			if ($_ct) {
				$data ['total'] = $rows->count('MCP.id');
			}
			$_rows = array();
			foreach ($rows as $row) {
				if ($row ['custom_data']) {
					$row ['custom_data'] = @json_decode($row ['custom_data'], true);
				} else {
					$row ['custom_data'] = array();
				}
				$_rows [] = $row;
			}
			$data ['rows']    = $_rows;
			$data ['channel'] = $channel;
		}

		return view('page/cursoueldata.tpl', $data);
	}

	private function getMyChannels() {
		$channels = MobiChannelForm::getAllChannels(true);
		$chs      = array();
		foreach ($channels as $id => $ch) {
			if (icando('m_' . $id . ':mobi/ch')) {
				$chs [ $id ] = $ch;
			}
		}

		return $chs;
	}

	private function getCursouelData($channel) {
		$rows = dbselect('MCP.id,MCP.page_id,MCP.page_view,MCP.custom_data,MCP.view_url AS url,MCP.flags,CP.title,CP.title2,MCP.status,MCP.update_time,MCP.create_time,CP.image,
				MCP.publish_time,CH.name as channelName,CM.name AS modelName,CU.nickname as cuname,UU.nickname as uuname')->from('{mobi_page} AS MCP')->join('{cms_page} AS CP', 'MCP.page_id = CP.id');

		$rows->field('UU.nickname AS uuname');
		$rows->join('{cms_channel} AS CH', 'CP.channel = CH.refid');
		$rows->join('{cms_model} AS CM', 'CP.model = CM.refid');
		$rows->join('{user} AS CU', 'MCP.create_uid = CU.user_id');
		$rows->join('{user} AS UU', 'MCP.update_uid = UU.user_id');

		$where ['MCP.deleted'] = 0;
		$where ['CP.deleted']  = 0;
		$where ['CP.hidden']   = 0;
		$where ['is_carousel'] = 1;
		$where ['MCP.status']  = 1;
		$where ['MCP.channel'] = $channel;
		$rows->where($where);
		$rows->sort('MCP.publish_day', 'd');
		$rows->sort('MCP.sort', 'a');
		$rows->limit(0, 5);
		$_rows = array();
		foreach ($rows as $row) {
			if ($row ['custom_data']) {
				$row ['custom_data'] = json_decode($row ['custom_data'], true);
			} else {
				$row ['custom_data'] = array();
			}
			$_rows [] = $row;
		}

		return $_rows;
	}
}
