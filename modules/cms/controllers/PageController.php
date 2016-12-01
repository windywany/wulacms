<?php
/*
 * KissCms
 */
defined('KISSGO') or exit ('No direct script access allowed');

/**
 * 网站页面管理器.
 *
 * @author Leo Ning <windywany@gmail.com>
 */
class PageController extends Controller {
	private   $status;
	protected $checkUser = true;
	protected $acls      = array('data' => 'r:cms/page', 'index' => 'r:cms/page', 'csort' => 'u:cms/page', 'add' => 'c:cms/page', 'move' => 'u:cms/page', 'flags' => 'u:cms/page', 'edit' => 'u:cms/page', 'save' => 'id|u:cms/page;c:cms/page', 'del' => 'd:cms/page', 'auto_author' => 'r:cms/page', 'auto_source' => 'r:cms/page', 'auto_topic' => 'r:cms/page');

	public function preRun($method) {
		parent::preRun($method);
		$this->status = get_cms_page_status();
	}

	public function index($my = 'all', $type = 'page', $channel = '', $status = '') {
		$my                         = $my == 'all' ? 'all' : 'my';
		$listTypes                  = array('my' => '我的', 'all' => '所有');
		$pageTypes                  = array('page' => '文章', 'topic' => '专题');
		$data ['type']              = in_array($type, array_keys($pageTypes)) ? $type : 'page';
		$data ['my']                = in_array($my, array_keys($listTypes)) ? $my : 'my';
		$data ['channels']          = ChannelForm::getChannelTree(null, $data ['type'] == 'topic', true);
		$data ['is_topic']          = $data ['type'] == 'topic' ? 1 : 0;
		$data ['pageTitle']         = $pageTypes [ $data ['type'] ] . '列表';
		$data ['pageTypeName']      = $data ['type'] == 'topic' ? '专题' : '内容';
		$data ['status']            = $this->status;
		$data ['disable_approving'] = bcfg('disable_approving@cms', false);
		$data ['models']            = array(''=>'全部内容');
		dbselect()->from('{cms_model}')->treeWhere(array('deleted' => 0, 'hidden' => 0, 'is_delegated' => 1, 'is_topic_model' => $data ['type'] == 'topic' ? 1 : 0))->treeKey('refid')->treeOption($data ['models']);
		$data ['canDelPage']    = icando('d:cms/page');
		$data ['canEditPage']   = icando('u:cms/page');
		$data ['canEditTag']    = icando('u:cms/tag');
		$data ['canSubmitPage'] = icando('submit:cms');
		$data ['channel']       = $channel;
		$data ['pstatus']       = $status;
		$fields                 = apply_filter('get_customer_cms_search_field', array(), $type);
		$fields ['_flags']      = array('widget' => 'htmltag', 'defaults' => '<div class="inline-group">
										<label class="checkbox">
											<input type="checkbox" name="flag_h">
											<i></i>头条[h]</label>
										<label class="checkbox">
											<input type="checkbox" name="flag_c">
											<i></i>推荐[c]</label>
										<label class="checkbox">
											<input type="checkbox" name="flag_a">
											<i></i>特荐[a]</label>
										<label class="checkbox">
											<input type="checkbox" name="flag_b">
											<i></i>加粗[b]</label>
										<label class="checkbox">
											<input type="checkbox" name="flag_j">
											<i></i>跳转[j]</label>
									</div>', 'col' => 10);

		$fields ['_abc1'] = array('widget' => 'htmltag', 'defaults' => '<button class="btn btn-sm btn-primary" type="submit">
										<i class="fa fa-search"></i> <span>搜索</span>
									</button>', 'col' => 1);

		if ($fields) {
			$gp          = 1;
			$col         = 0;
			$csearchForm = new DynamicForm ('CustomerPageSearchForm');
			foreach ($fields as $n => $f) {
				if (!isset ($f ['col']) || !intval($f ['col'])) {
					$f ['col'] = 3;
				}
				$col += intval($f ['col']);
				if ($col > 12) {
					$gp += 1;
					$col = intval($f ['col']);
				}
				$f ['group'] = $gp;
				$csearchForm->addField($n, $f);
			}
			$data ['widgets'] = new DefaultFormRender ($csearchForm->buildWidgets(array()));
		}

		return view('page/index.tpl', $data);
	}

	public function csort($id, $sort) {
		$id   = intval($id);
		$sort = intval($sort);
		if (!empty ($id)) {
			dbupdate('{cms_page}')->set(array('display_sort' => $sort))->where(array('id' => $id))->exec();
		}

		return NuiAjaxView::reload('#page-table');
	}

	/**
	 * 新增页面.
	 *
	 * @param string $type
	 *            page or topic.
	 * @param string $model
	 *            content model.
	 */
	public function add($type, $model, $channel = '') {
		$cModel = dbselect('*')->from('{cms_model}')->where(array('refid' => $model, 'deleted' => 0, 'creatable' => 1))->get();
		if (!$cModel) {
			Response::showErrorMsg('内容模型“' . $model . '”不存在。', 404);
		}
		$options                 = ChannelForm::getChannelTree($model, $type == 'topic');
		$template                = $cModel ['template'] ? $cModel ['template'] : ($type == 'topic' ? 'topic_form.tpl' : 'page_form.tpl');
		$data ['options']        = $options;
		$data ['page_type']      = $type;
		$data ['model']          = $model;
		$data ['pageTypeName']   = $type == 'topic' ? '专题' : '内容';
		$data ['modelName']      = $cModel ['name'];
		$data ['id']             = 0;
		$data ['img_pagination'] = true;
		$formName                = ucfirst($type) . 'Form';
		$form                    = new DynamicForm ($formName);
		if (extension_loaded('scws')) {
			$data ['gkeywords'] = true;
		}
		$data ['channel']    = $channel;
		$data ['view_count'] = rand(80000, 150000);
		$widgets             = ModelFieldForm::loadCustomerFields($form, $model);
		if ($widgets) {
			$data ['widgets'] = new DefaultFormRender (AbstractForm::prepareWidgets(CustomeFieldWidgetRegister::initWidgets($widgets)));
		}
		$contentModel = get_page_content_model($model);
		$cform        = $contentModel ? $contentModel->getForm() : false;
		if ($cform) {
			$data ['cwidgets'] = new DefaultFormRender ($cform->buildWidgets($cform->toArray()));
		}
		$data ['rules'] = $form->rules($cform);
		$plgs           = apply_filter('get_editor_plugins', array());
		if ($plgs) {
			$data ['editor_plugins'] = ",'" . implode("','", $plgs) . "','|'";
		}
		$data ['editor_layout'] = apply_filter('get_editor_layout', '<div class="container"><div class="toolbar"></div><div class="edit"></div><div class="statusbar"></div></div>');
		$data ['editor_css']    = apply_filter('on_load_editor_css', 'body { margin:10px;} body, td { font-size:16px; } p {margin: 10px 0; color: #666; line-height: 110%;} ');

		// 允许第三方设置预置标题.
		$data ['title']    = sess_del('cms_page_set_title');
		$data ['content']  = sess_del('cms_page_set_content');
		$data ['keywords'] = sess_del('cms_page_set_keywords');

		if ($template{0} == '@') {
			return view($template, $data);
		} else {
			return view('page/' . $template, $data);
		}
	}

	public function edit($type, $id, $copy = '') {
		$page = dbselect('*')->from('{cms_page}')->where(array('id' => $id))->get();
		if ($page) {
			$model  = $page ['model'];
			$cModel = dbselect('*')->from('{cms_model}')->where(array('refid' => $model, 'deleted' => 0, 'creatable' => 1))->get();
			if (!$cModel) {
				Response::showErrorMsg('内容模型“' . $model . '”不存在。', 404);
			}
			$options               = ChannelForm::getChannelTree($model, $type == 'topic');
			$template              = $cModel ['template'] ? $cModel ['template'] : ($type == 'topic' ? 'topic_form.tpl' : 'page_form.tpl');
			$page ['options']      = $options;
			$page ['page_type']    = $type;
			$page ['pageTypeName'] = $type == 'topic' ? '专题' : '内容';
			$page ['modelName']    = $cModel ['name'];
			if ($page ['chunk']) {
				$chunk = dbselect('name')->from('{cms_chunk}')->where(array('id' => $page ['chunk']))->get();
				if ($chunk) {
					$page ['chunk'] = $page ['chunk'] . ':' . $chunk ['name'];
				} else {
					$page ['chunk'] = '';
				}
			} else {
				$page ['chunk'] = '';
			}
			if ($page ['topic']) {
				$topic = dbselect('title2')->from('{cms_page}')->where(array('id' => $page ['topic']))->get();
				if ($topic) {
					$page ['topic'] = $page ['topic'] . ':' . $topic ['title2'];
				} else {
					$page ['topic'] = '';
				}
			} else {
				$page ['topic'] = '';
			}

			if ($page ['flag_j']) {
				$page ['redirect'] = $page ['content'];
				$page ['content']  = '';
			}
			$formName = ucfirst($type) . 'Form';
			$form     = new DynamicForm ($formName, $page, true);
			$widgets  = ModelFieldForm::loadCustomerFields($form, $model);
			$cdatas   = CmsPage::loadCustomerFieldValues($id, array(), $model);
			if ($cdatas) {
				$page = array_merge($page, $cdatas);
			}
			if ($widgets) {
				$page ['widgets'] = new DefaultFormRender (AbstractForm::prepareWidgets(CustomeFieldWidgetRegister::initWidgets($widgets, $cdatas)));
			}
			$contentModel = get_page_content_model($model);
			$cform        = $contentModel ? $contentModel->getForm() : false;
			if ($cform) {
				$page ['cwidgets'] = new DefaultFormRender ($cform->buildWidgets($cdatas));
			}
			$page ['rules'] = $form->rules($cform);
			if (extension_loaded('scws')) {
				$page ['gkeywords'] = true;
			}
			if ($page ['publish_time']) {
				$page ['publish_date'] = date('Y-m-d', $page ['publish_time']);
				$page ['publish_time'] = date('H:i', $page ['publish_time']);
			}
			if ($copy) {
				unset ($page ['id'], $page ['url'], $page ['url_key'], $page ['create_time']);
				$page ['title'] = '复制的-' . $page ['title'];
			}
			$plgs = apply_filter('get_editor_plugins', array());
			if ($plgs) {
				$page ['editor_plugins'] = ",'" . implode("','", $plgs) . "','|'";
			}
			$page ['editor_layout'] = apply_filter('get_editor_layout', '<div class="container"><div class="toolbar"></div><div class="edit"></div><div class="statusbar"></div></div>');
			$page ['editor_css']    = apply_filter('on_load_editor_css', 'body { margin:10px;} body, td { font-size:16px; } p {margin: 10px 0; color: #666; line-height: 110%;} ');
			if ($template{0} == '@') {
				return view($template, $page);
			} else {
				return view('page/' . $template, $page);
			}
		} else {
			Response::showErrorMsg('文章不存在"' . $id . '"不存在。', 404);
		}
	}

	public function del($ids) {
		$ids = safe_ids($ids, ',', true);
		if (!empty ($ids)) {
			$data ['deleted']     = 1;
			$data ['update_time'] = time();
			$data ['update_uid']  = $this->user->getUid();
			if (dbupdate('{cms_page}')->set($data)->where(array('id IN' => $ids))->exec()) {
				fire('on_recycle_page', $ids);
				$recycle = new DefaultRecycle ($ids, 'Page', 'cms_page', 'ID:{id};标题:{title};模型:{model}');
				RecycleHelper::recycle($recycle);

				return NuiAjaxView::ok('已经放入回收站', 'click', '#refresh');
			} else {
				return NuiAjaxView::error('数据库操作失败.');
			}
		} else {
			Response::showErrorMsg('错误的编号', 500);
		}
	}

	public function move($ids, $ch, $upurl = '', $approve = '') {
		$ids = safe_ids($ids, ',', true);
		if (!empty ($ids)) {
			$model = dbselect('default_model')->from('{cms_channel}')->where(array('refid' => $ch))->get('default_model');
			if ($model) {
				$pages = dbselect('id')->from('{cms_page}')->where(array('id IN' => $ids, 'model' => $model))->toArray('id');
			}
			if ($pages) {
				$data ['update_time'] = time();
				$data ['update_uid']  = $this->user->getUid();
				$data ['channel']     = $ch;
				if ($approve) {
					$data ['status'] = 1;
				}
				if (dbupdate('{cms_page}')->set($data)->where(array('id IN' => $pages))->exec()) {
					$msg = count($ids) == count($pages) ? '所选文章已经移动到指定栏目.' : '部分文章已经移动到指定栏目.';
					$w   = array('id IN' => $pages);
					if (empty ($upurl)) {
						$w ['url'] = '';
					}
					$uppages = dbselect('*')->from('{cms_page}')->where($w);
					foreach ($uppages as $p) {
						// 更新页面的URL.
						$p ['url_key'] = '';
						$p ['url']     = '';
						CmsPage::generateURL($p ['id'], $p);
					}

					return NuiAjaxView::reload('#page-table', $msg);
				} else {
					return NuiAjaxView::error('数据库操作失败.');
				}
			} else {
				return NuiAjaxView::error('栏目模型与文章模型不一致,无法移动.');
			}
		} else {
			return NuiAjaxView::error('未指定任何文章.');
		}
	}

	public function flags($ids = '', $flags = '') {
		$ids = safe_ids($ids, ',', true);
		if (!empty ($ids)) {
			$data ['flag_h'] = 0;
			$data ['flag_c'] = 0;
			$data ['flag_a'] = 0;
			$data ['flag_b'] = 0;
			$data ['flag_j'] = 0;
			$flags           = explode(',', $flags);
			if ($flags) {
				foreach ($flags as $f) {
					if (in_array($f, array('a', 'b', 'c', 'h', 'j'))) {
						$data [ 'flag_' . $f ] = 1;
					}
				}
			}
			$data ['update_time'] = time();
			$data ['update_uid']  = $this->user->getUid();

			if (dbupdate('{cms_page}')->set($data)->where(array('id IN' => $ids))->exec()) {
				return NuiAjaxView::reload('#page-table', '文章属性修改成功.');
			} else {
				return NuiAjaxView::error('数据库操作失败.');
			}
		} else {
			return NuiAjaxView::error('未指定任何文章.');
		}
	}

	/**
	 * 保存.
	 *
	 * @return string
	 */
	public function save($page_type, $model) {
		return CmsPage::save($page_type, $model, $this->user);
	}

	public function data($my, $type, $_cp = 1, $_lt = 20, $_sf = 'CP.id', $_od = 'd', $_ct = 0) {
		$rows = dbselect('CP.id,CP.flag_h,CP.flag_c,CP.flag_a,CP.flag_b,CP.flag_j,CP.title,CP.title2,CP.status,CP.update_time,CP.create_time,CP.image,
				CP.publish_time,CP.keywords,CP.url,CP.display_sort,CH.root,CH.name as channelName,CM.name AS modelName,CU.nickname as cuname')->from('{cms_page} AS CP');
		$rows->field('UU.nickname AS uuname');
		$rows->join('{cms_channel} AS CH', 'CP.channel = CH.refid');
		$rows->join('{cms_model} AS CM', 'CP.model = CM.refid');
		$rows->join('{user} AS CU', 'CP.create_uid = CU.user_id');
		$rows->join('{user} AS UU', 'CP.update_uid = UU.user_id');
		$rows                 = apply_filter('build_page_common_query', $rows, $_GET);
		$where ['CP.deleted'] = 0;
		$where ['CP.hidden']  = 0;
		$where ['CM.hidden']  = 0;
		$uid                  = $this->user->getUid();
		if ($uid != 1) {
			if ($my == 'my') {
				$where ['CP.create_uid'] = $uid;
			} else if (bcfg('enable_group_bind@cms') && $uid != 1) {
				$where ['CH.gid IN'] = $this->user->getAttr('subgroups', array());
			}
		}
		if ($type == 'topic') {
			$where ['CH.is_topic_channel'] = 1;
		} else {
			$where ['CH.is_topic_channel'] = 0;
		}
		$pid = irqst('pid');
		if ($pid) {
			$where ['CP.id'] = $pid;
		} else {
			$flag_h = rqst('flag_h');
			$flag_c = rqst('flag_c');
			$flag_a = rqst('flag_a');
			$flag_b = rqst('flag_b');
			$flag_j = rqst('flag_j');

			if ($flag_h == 'on') {
				$where ['CP.flag_h'] = 1;
			}
			if ($flag_c == 'on') {
				$where ['CP.flag_c'] = 1;
			}
			if ($flag_a == 'on') {
				$where ['CP.flag_a'] = 1;
			}
			if ($flag_b == 'on') {
				$where ['CP.flag_b'] = 1;
			}
			if ($flag_j == 'on') {
				$where ['CP.flag_j'] = 1;
			}

			$uuname = irqst('uuname');
			if ($uuname) {
				$where ['CP.create_uid'] = $uuname;
			}

			$channel = rqst('channel');
			if (preg_match('/^[a-z].*/i', $channel)) {
				$where ['CP.channel'] = $channel;
			} else if ($channel) {
				$channel            = safe_ids2($channel);
				$where ['CH.id IN'] = $channel;
			}
			$model = rqst('model');
			if ($model) {
				$where ['CP.model'] = $model;
			} else {
				$where ['CP.model !='] = '_customer_page';
			}
			$status = rqst('status');
			if (is_numeric($status) || !empty ($status)) {
				$where ['CP.status'] = intval($status);
			}
			$keywords = rqst('keywords');
			if ($keywords) {
				if (strpos($keywords, ':') > 0) {
					$cons                        = explode(':', trim($keywords));
					$where [ 'CP.' . $cons [0] ] = $cons [1];
				} else {
					$t        = '%' . $keywords . '%';
					$keywords = convert_search_keywords($keywords);
					$where [] = array('search_index MATCH' => $keywords, '||CP.title LIKE' => $t, '||CP.title2 LIKE' => $t);
				}
			}
		}
		$rows->where($where);
		$rows->sort($_sf, $_od);
		$rows->limit(($_cp - 1) * $_lt, $_lt);
		$data           = array();
		$data ['total'] = '';
		if ($_ct) {
			$data ['total'] = $rows->count('CP.id');
		}
		$aps = [];
		foreach ($rows as $row) {
			$aps[] = apply_filter('cms_get_page_author', $row);
		}
		$data ['rows']              = $aps;
		$data ['type']              = $type;
		$data ['status']            = $this->status;
		$data ['canDelPage']        = icando('d:cms/page');
		$data ['canEditPage']       = icando('u:cms/page');
		$data ['canEditTag']        = icando('u:cms/tag');
		$data ['cCache']            = icando('cmc:system') && bcfg('enabled@mem');
		$data ['disable_approving'] = bcfg('disable_approving@cms', false);
		$data ['enableCopy']        = bcfg('enable_copy@cms');
		$tpl                        = 'page/' . $type . '_data.tpl';

		return view($tpl, $data);
	}

	public function auto_topic($q = '', $_cp = 1) {
		$data ['more'] = false;

		$topics = dbselect('title2 as text,CP.id')->from('{cms_page} AS CP')->limit(($_cp - 1) * 15, 15);
		$topics->join('{cms_channel} AS CH', 'CP.channel = CH.refid');
		$where ['CP.deleted']          = 0;
		$where ['CP.hidden']           = 0;
		$where ['CH.is_topic_channel'] = 1;
		if ($q) {
			$where ['title2 LIKE'] = $q;
		}
		$topics->where($where);
		$data ['results'] = $topics->toArray(array(array('id' => 0, 'text' => '-不绑定-')));

		return new JsonView ($data);
	}

	public function auto_page($model = '', $q = '', $_cp = 1) {
		$data ['more']          = false;
		$topics                 = dbselect('title2 as text,PG.id')->from('{cms_page} AS PG')->limit(($_cp - 1) * 15, 15);
		$where ['PG.deleted']   = 0;
		$where ['PG.title2 <>'] = '';
		if ($model) {
			$where ['PG.model'] = $model;
		}
		if ($q) {
			$where ['title2 LIKE'] = $q;
		}
		$topics->where($where);
		$data ['results'] = $topics->toArray(array(array('id' => 0, 'text' => '-请选择-')));

		return new JsonView ($data);
	}

	public function auto_author($q = '', $_cp = 1) {
		$data ['more']     = false;
		$items             = dbselect('val as id,val as text')->from('{cms_variables}')->limit(($_cp - 1) * 15, 15);
		$where ['deleted'] = 0;
		$where ['type']    = 'author';
		$results []        = array('id' => '', 'text' => '-无-');
		if ($q) {
			$where ['val LIKE'] = $q;
			$results []         = array('id' => $q, 'text' => $q);
		}
		$items->where($where);
		$data ['results'] = $items->toArray($results);

		return new JsonView ($data);
	}

	public function auto_source($q = '', $_cp = 1) {
		$data ['more']     = false;
		$items             = dbselect('val as id,val as text')->from('{cms_variables}')->limit(($_cp - 1) * 15, 15);
		$where ['deleted'] = 0;
		$where ['type']    = 'source';
		$results []        = array('id' => '', 'text' => '-无-');
		if ($q) {
			$where ['val LIKE'] = $q;
			$results []         = array('id' => $q, 'text' => $q);
		}
		$items->where($where);

		$data ['results'] = $items->toArray($results);

		return new JsonView ($data);
	}

	public function browsedialog($ss = '', $model = '') {
		$data ['models'] = array('' => '请选择内容模型');
		dbselect()->from('{cms_model}')->treeWhere(array('deleted' => 0))->treeKey('refid')->treeOption($data ['models']);
		$data ['ss']    = $ss;
		$data ['model'] = $model;

		return view('page/browser.tpl', $data);
	}

	public function browsedata($_cp = 1, $_lt = 20, $_sf = 'PG.id', $_od = 'd', $_ct = 0) {
		$rows = dbselect('PG.*,CM.name')->from('{cms_page} AS PG');
		$rows->join('{cms_model} AS CM', 'CM.refid = PG.model');
		$where ['PG.deleted'] = 0;
		// $where ['PG.hidden'] = 0;
		$model    = rqst('model');
		$keywords = rqst('keywords');
		if ($keywords) {
			$t        = '%' . $keywords . '%';
			$keywords = convert_search_keywords($keywords);
			$where [] = array('search_index MATCH' => $keywords, '||PG.title LIKE' => $t, '||PG.title2 LIKE' => $t);
		}
		if ($model) {
			$where ['model'] = $model;
		}
		$rows->where($where);
		$rows->sort($_sf, $_od);
		$rows->limit(($_cp - 1) * $_lt, $_lt);
		$data           = array();
		$data ['total'] = '';
		if ($_ct) {
			$data ['total'] = $rows->count('PG.id');
		}
		$data ['ss']   = rqst('ss');
		$data ['rows'] = $rows;

		return view('page/browserdata.tpl', $data);
	}
}