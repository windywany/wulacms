<?php

/**
 * 自定义页面控制器.
 *
 * @author Leo Ning.
 */
class CpageController extends Controller {
	private   $status;
	protected $checkUser = true;
	protected $acls      = [
		'data'  => 'r:cms/cpage',
		'index' => 'r:cms/cpage',
		'add'   => 'c:cms/cpage',
		'edit'  => 'u:cms/cpage',
		'save'  => 'id|u:cms/cpage;c:cms/cpage'
	];

	public function preRun($method) {
		parent::preRun($method);
		$this->status = get_cms_page_status();
	}

	/**
	 * 列表页.
	 *
	 * @return SmartyView
	 */
	public function index($type = '') {
		$data                      = [];
		$data ['canDelPage']       = icando('d:cms/cpage');
		$data ['canAddPage']       = icando('c:cms/cpage');
		$data ['canEditPage']      = icando('u:cms/cpage');
		$data ['canSubmitPage']    = icando('submit:cms');
		$data ['status']           = $this->status;
		$data ['type']             = $type;
		$data ['enable_approving'] = bcfg('disable_approving@cms', false);

		return view('page/cpage.tpl', $data);
	}

	/**
	 * 新增.
	 *
	 * @return SmartyView
	 */
	public function add() {
		$data           = [];
		$form           = new DynamicForm ('CPageForm');
		$data ['rules'] = $form->rules();

		$handlers = apply_filter('get_cms_url_handlers', []);
		if ($handlers) {
			$data ['url_handlers'] [''] = '--请选择--';
			foreach ($handlers as $id => $h) {
				$data ['url_handlers'] [ $id ] = $h->getName();
			}
		}
		$data ['canApprove'] = bcfg('disable_approving@cms', false) && icando('approve:cms');

		return view('page/cpage_form.tpl', $data);
	}

	/**
	 * 修改.
	 *
	 * @param int $id
	 *
	 * @return SmartyView
	 */
	public function edit($id) {
		$page = dbselect('*')->from('{cms_page}')->where(['id' => $id])->get();
		if ($page) {
			unset ($page ['publish_time']);
			$formName             = 'CPageForm';
			$form                 = new DynamicForm ($formName, $page, true);
			$page ['rules']       = $form->rules();
			$page ['is_tpl_page'] = $page ['channel'] == '_t';
			$handlers             = apply_filter('get_cms_url_handlers', []);
			if ($handlers) {
				$page ['url_handlers'] [''] = '--请选择--';
				foreach ($handlers as $id => $h) {
					$page ['url_handlers'] [ $id ] = $h->getName();
				}
			}
			$page ['canApprove'] = bcfg('disable_approving@cms', false) && icando('approve:cms');

			return view('page/cpage_form.tpl', $page);
		} else {
			Response::showErrorMsg('自定义页面不存在"' . $id . '"不存在。', 404);
		}
	}

	/**
	 * 保存.
	 *
	 * @return NuiAjaxView
	 */
	public function save() {
		$form = new DynamicForm ('CPageForm');
		$page = $form->valid();
		if ($page) {
			$time        = time();
			$gid         = $this->user->getAttr('group_id', 0);
			$uid         = $this->user->getUid();
			$id          = $page ['id'];
			$is_tpl_page = $page ['is_tpl_page'];
			unset ($page ['id'], $page ['is_tpl_page']);
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

			if (empty ($page ['author'])) {
				$page ['author'] = $this->user->getDisplayName();
			}
			if (empty ($page ['title'])) {
				$page ['title'] = $page ['title2'];
			}
			if (empty ($page ['expire'])) {
				$page ['expire'] = 0;
			}
			$page ['url_key'] = md5($page ['url']);
			$page ['channel'] = $is_tpl_page ? '_t' : '_';
			if (empty ($id)) {
				$page ['create_time'] = $time;
				$page ['create_uid']  = $uid;

				$page ['model'] = '_customer_page';
				$page ['gid']   = $gid;

				if (1 == irqst('pubnow')) {
					$page ['status'] = 2;
				} else {
					$page ['status'] = icfg('page_update_status@cms', 3);
				}
				// 发布或待发布且发布时间为空,则使用当前时间做为发布时间.
				if (in_array($page ['status'], [2, 4]) && !isset ($page ['publish_time'])) {
					$page ['publish_time'] = time();
				}
				$rst = dbinsert($page)->into('{cms_page}')->exec();
				if ($rst) {
					$page ['is_new'] = true;
					$id              = $rst [0];
				}
			} else {
				if (1 == irqst('pubnow')) {
					$page ['status'] = 2;
				} else {
					$page ['status'] = icfg('page_new_status@cms', 1);
				}
				// 发布或待发布且发布时间为空,则使用当前时间做为发布时间.
				if (in_array($page ['status'], [2, 4]) && !isset ($page ['publish_time'])) {
					$page ['publish_time'] = time();
				}
				$rst                  = dbupdate('{cms_page}')->set($page)->where(['id' => $id])->exec();
				$page ['create_time'] = irqst('create_time', 0);
				if (!$rst) {
					$id = 0;
				}
			}
			if ($id) {
				$page ['id'] = $id;
				unset ($page ['content']);
				$html [] = '<p class="text-left">请选择你的后续操作：</p>';
				$html [] = '<p class="text-left">';
				if ($page ['is_new']) {
					$html [] = '[<a href="javascript:void(0);" onclick="return add_next_page();">再添加一篇</a>] &nbsp;';
				}
				$html [] = '[<a href="#' . tourl('cms/cpage' . ($page ['channel'] == '_t' ? '/tpl' : ''), false) . '" onclick="nUI.closeAjaxDialog()">返回列表</a>] &nbsp;';
				$html [] = '[<a href="javascript:void(0);" onclick="return modify_current_page();">修改</a>] &nbsp;';
				if ($page['channel'] == '_') {
					$url     = dbselect()->from('{cms_page}')->where(['id' => $id])->get('url');
					$html [] = '[<a href="' . safe_url($url) . '?preview" target="_blank">预览</a>]';
				}
				if (icando('cmc:system') && bcfg('enabled@mem')) {
					$html [] = '[<a href="' . safe_url($url) . '?preview=_c2c_" target="_blank">预览并清空缓存</a>]';
				}
				$html [] = '</p>';

				return NuiAjaxView::dialog(implode('', $html), '保存完成!', [
					'model'  => true,
					'height' => 'auto',
					'func'   => 'pageSaved',
					'page'   => $page
				]);
			} else {
				return NuiAjaxView::error('无法保存，数据库出错.');
			}
		} else {
			return NuiAjaxView::validate('CPageForm', '请正确填写表单.', $form->getErrors());
		}
	}

	/**
	 * 数据.
	 *
	 * @param number $_cp
	 * @param number $_lt
	 * @param string $_sf
	 * @param string $_od
	 * @param number $_ct
	 *
	 * @return SmartyView
	 */
	public function data($_cp = 1, $_lt = 20, $_sf = 'PG.id', $_od = 'd', $_ct = 0) {
		$rows = dbselect('PG.*,CH.name as channelName,CM.name AS modelName,CU.nickname as cuname')->from('{cms_page} AS PG');
		$rows->field('UU.nickname AS uuname');
		$rows->join('{cms_channel} AS CH', 'PG.channel = CH.refid');
		$rows->join('{cms_model} AS CM', 'PG.model = CM.refid');
		$rows->join('{user} AS CU', 'PG.create_uid = CU.user_id');
		$rows->join('{user} AS UU', 'PG.update_uid = UU.user_id');
		$where ['PG.deleted'] = 0;
		$where ['PG.hidden']  = 0;
		$where ['PG.model']   = '_customer_page';
		$type                 = rqst('type');
		$flag_h               = rqst('flag_h');
		$flag_c               = rqst('flag_c');
		$flag_a               = rqst('flag_a');
		$flag_b               = rqst('flag_b');
		$flag_j               = rqst('flag_j');

		if ($type) {
			$where ['channel'] = '_t';
		} else {
			$where ['channel'] = '_';
		}

		if ($flag_h == 'on') {
			$where ['PG.flag_h'] = 1;
		}
		if ($flag_c == 'on') {
			$where ['PG.flag_c'] = 1;
		}
		if ($flag_a == 'on') {
			$where ['PG.flag_a'] = 1;
		}
		if ($flag_b == 'on') {
			$where ['PG.flag_b'] = 1;
		}
		if ($flag_j == 'on') {
			$where ['PG.flag_j'] = 1;
		}
		$status = rqst('status');
		if (is_numeric($status) || !empty ($status)) {
			$where ['PG.status'] = intval($status);
		}
		$uuname = irqst('uuname');
		if ($uuname) {
			$where ['PG.update_uid'] = $uuname;
		}
		$keywords = rqst('keywords');
		if ($keywords) {
			$t        = '%' . $keywords . '%';
			$keywords = convert_search_keywords($keywords);
			$where [] = ['search_index MATCH' => $keywords, '||PG.title LIKE' => $t];
		}
		$rows->where($where);
		$rows->sort($_sf, $_od);
		$rows->limit(($_cp - 1) * $_lt, $_lt);
		$data           = [];
		$data ['total'] = '';
		if ($_ct) {
			$data ['total'] = $rows->count('PG.id');
		}
		$data ['rows']             = $rows;
		$data ['status']           = $this->status;
		$data ['canDelPage']       = icando('d:cms/cpage');
		$data ['canEditPage']      = icando('u:cms/cpage');
		$data ['cCache']           = icando('cmc:system') && bcfg('enabled@mem');
		$data ['enable_approving'] = bcfg('disable_approving@cms', false);
		$handlers                  = apply_filter('get_cms_url_handlers', []);
		$data ['handlers'] ['']    = '';
		if ($handlers) {
			foreach ($handlers as $id => $h) {
				$data ['handlers'] [ $id ] = $h->getName();
			}
		}
		$tpl = 'page/cpage_data.tpl';

		return view($tpl, $data);
	}
}