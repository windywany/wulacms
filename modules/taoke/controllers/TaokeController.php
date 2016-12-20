<?php

/**
 * 相册控制器.
 * @author Leo Ning.
 *
 */
class TaokeController extends Controller {
	protected $acls      = array('*' => 'r:cms/page', 'upload' => 'u:cms/page', 'upload_post' => 'u:cms/page', 'save' => 'u:cms/page', 'set_hot' => 'u:cms/page', 'edit' => 'u:cms/page', 'del' => 'd:cms/page');
	protected $checkUser = true;

	public function index() {
		$data                = array();
		$data ['canDelPage'] = icando('d:cms/page');
		$data ['canAddPage'] = icando('c:cms/page');
		$data ['channels']   = ChannelForm::getChannelTree('taoke', false, true);

		return view('taoke.tpl', $data);
	}

	public function data($_cp = 1, $_lt = 20, $_sf = 'cp.id', $_od = 'd', $_ct = 0) {
		$date     = date('Y-m-d');
		$data     = [];
		$where    = [];
		$name     = trim(rqst('title', ''));
		$wangwang = trim(rqst('wangwang', ''));
		$platform = trim(rqst('platform', ''));
		$status   = trim(rqst('status', ''));
		if ($name != '') {
			$where ['cp.title LIKE'] = '%' . $name . '%';
		}
		if ($platform != '') {
			$where ['tbk.platform'] = $platform;
		}
		if ($status != '') {
			if ($status == 0) {
				$where ['cp.flag_c'] = 1;
			}
			if ($status == 1) {
				$where ['cp.flag_a'] = 1;
			}
		}
		if ($wangwang != '') {
			$where ['tbk.wangwang LIKE'] = '%' . $wangwang . '%';
		}
		$where['cp.deleted']         = 0;
		$where['cp.model']           = 'taoke';
		$where['tbk.coupon_stop >='] = $date;
		$row                         = dbselect('cp.id as cid,cp.title as title,cp.image as image,cp.flag_c as flag_c,cp.flag_a as flag_a,tbk.*')->from('{cms_page} as cp')->join('{tbk_goods} as tbk', 'cp.id=tbk.page_id')->where($where);
		$data ['total']              = $row->count('cp.id');
		$data ['results']            = $row->limit(($_cp - 1) * $_lt, $_lt)->sort($_sf, $_od)->toArray();

		return view('data.tpl', $data);
	}

	//批量修改推荐状态
	public function changec($ids) {
		$ids = safe_ids($ids, ',', true);
		if ($ids) {
			$res = dbupdate('{cms_page}')->set(['flag_c' => 1])->where(['id in' => $ids])->exec();
			if ($res) {
				return NuiAjaxView::reload('#page-table', '修改成功');
			} else {
				return NuiAjaxView::error('修改失败.');
			}

		}

		return NuiAjaxView::error('未指定任何文章.');
	}

	//批量修改推荐状态
	public function changea($ids) {
		$ids = safe_ids($ids, ',', true);
		if ($ids) {
			$res = dbupdate('{cms_page}')->set(['flag_a' => 1])->where(['id in' => $ids])->exec();
			if ($res) {
				return NuiAjaxView::reload('#page-table', '修改成功');
			} else {
				return NuiAjaxView::error('修改失败.');
			}

		}

		return NuiAjaxView::error('未指定任何文章.');
	}

	public function saveReason() {
		$page_id  = rqst('page_id', '');
		$reason   = rqst('reason');
		$checkbox = rqst('checkbox');
		$data     = [];
		if ($page_id) {
			if ($reason) {
				dbupdate('{tbk_goods}')->set(['reason' => $reason])->where(['page_id' => $page_id])->exec();
			}
			if ($checkbox) {
				if ($checkbox[0] == 1 && $checkbox[1] == 2) {
					$data['flag_c'] = 1;
					$data['flag_a'] = 1;
				} elseif ($checkbox[0] == 1) {
					$data['flag_c'] = 1;
					$data['flag_a'] = 0;
				} elseif ($checkbox[0] == 2) {
					$data['flag_c'] = 0;
					$data['flag_a'] = 1;
				}
			} else {
				$data['flag_c'] = 0;
				$data['flag_a'] = 0;
			}
			if ($data) {
				dbupdate('{cms_page}')->set($data)->where(['id' => $page_id])->exec();
			}

			return NuiAjaxView::reload('#page-table', '修改成功');
		}

		return NuiAjaxView::error('修改失败');
	}

	//生成淘口令
	public function createtoken($id) {
		$data = dbselect('cp.id as cid,cp.title as title,cp.image as image,cp.flag_c as flag_c,cp.flag_a as flag_a,tbk.*')->from('{cms_page} as cp')->join('{tbk_goods} as tbk', 'cp.id=tbk.page_id')->where(['cp.id' => $id])->get();
		if ($data) {
			$tbk = new \taoke\classes\Createtbk();
			$res = $tbk->create($data['title'], $data['coupon_url'], cfg('user_id@taoke', ''));
			if ($res['status'] == 1) {
				return NuiAjaxView::error($res['msg']);
			}
			$token = $res['msg'];
			if ($token) {
				dbupdate('{tbk_goods}')->set(['token' => $token])->where(['page_id' => $id])->exec();

				return NuiAjaxView::callback('setTbkToken', ['token' => $token . '', 'id' => $id]);
			}

		}

		return NuiAjaxView::error('修改失败');
	}

	//生成推广语
	public function share() {
		$data = [];
		$id  = rqst('page_id', '');
		$share_word   = rqst('share_word','');
		if (!$share_word) {
			return NuiAjaxView::error('推广语好像没填');
		}
		$word = cfg('word@taoke', '');
		if ($word) {
			$data    = dbselect('cp.id as cid,cp.title as title,cp.image as image,cp.flag_c as flag_c,cp.flag_a as flag_a,tbk.*')->from('{cms_page} as cp')->join('{tbk_goods} as tbk', 'cp.id=tbk.page_id')->where(['cp.id' => $id])->get();
			$rep_arr = ['platform', 'title', 'price', 'real_price', 'token'];
			foreach ($rep_arr as $k) {

				$res  = str_replace('{' . $k . '}', $data[ $k ], $word);
				$word = $res;
			}
			if ($res) {
				$end_res = str_replace('{share_word}', $share_word, $res);
				return NuiAjaxView::callback('setTbkShare', ['word' => $end_res, 'id' => $id], '已复制到粘贴板,可直接右键粘贴');
			} else {
				return NuiAjaxView::error('好像失败了');
			}

		} else {
			return NuiAjaxView::error('暂无推广语配置,请您先填写配置');
		}

	}
}
