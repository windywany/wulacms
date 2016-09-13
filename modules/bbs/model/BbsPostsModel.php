<?php
namespace bbs\model;

use bbs\form\PostForm;
use db\model\FormModel;

class BbsPostsModel extends FormModel {
	public $mid       = 0;
	public $thread_id = 0;
	public $replyto   = 0;
	public $content   = '';
	public $cost      = 0;
	public $reward    = 0;
	public $ip        = '';

	public function create($data, $cb = null) {
		if (!isset($data['ip']) || empty($data['ip'])) {
			$data['ip'] = \Request::getIp();
		}
		$data = apply_filter('on_bbs_post_creating', $data);
		$rst  = parent::create($data, $cb);
		if ($rst) {
			fire('on_bbs_post_created', $rst);
		}

		return $rst;
	}

	/**
	 * 回复.
	 *
	 * @return int 回复编号，0回复失败.
	 * @throws \Exception 更新数据库出错时抛出.
	 */
	public function post() {
		$data = $this->getData();
		if (empty($data['thread_id'])) {
			$this->errors = 'thread_id不能为空';

			return 0;
		}
		$tm     = new BbsThreadsModel();
		$thread = $tm->load($data['thread_id']);
		if (!$thread) {
			$this->errors = '帖子不存在';

			return 0;
		}
		if (!$thread['status']) {
			$this->errors = '帖子已经关闭';

			return 0;
		}
		if (empty($data['mid']) && !$thread['forum']['allow_anonymous']) {
			$this->errors = '不允许匿名用户回复';

			return 0;
		}
		$data['mid']         = intval($data['mid']);
		$data['create_uid']  = $data['update_uid'] = $data['mid'];
		$data['create_time'] = $data['update_time'] = time();
		$data['deleted']     = 0;
		$data['status']      = 1;
		unset($data['mid']);
		if (empty($data['ip'])) {
			$data['ip'] = \Request::getIp();
		}
		//创建回复
		$data = apply_filter('on_bbs_post_creating', $data);
		$rst  = $this->create($data);
		if (!$rst) {
			throw new \Exception('无法回复:' . var_export($this->errors, true));
		}
		//更新帖子，版块相关数据
		if (!$tm->update(['last_post_id' => $rst, 'post_count' => imv('post_count+1'), 'id' => $data['thread_id']])) {
			throw new \Exception('无法更新帖子:' . var_export($tm->getErrors(), true));
		}
		$fm = new BbsForumsModel();
		if (!$fm->update(['last_post' => $rst, 'last_thread' => $data['thread_id'], 'post_count' => imv('post_count+1'), 'id' => $thread['forum_id']])) {
			throw new \Exception('无法更新版块:' . var_export($fm->getErrors(), true));
		}
		$this->resetData();
		if ($rst) {
			fire('on_bbs_post_created', $rst);
		}

		return $rst;
	}

	protected function createForm($data = []) {
		return new PostForm($data);
	}

	private function getData() {
		$data['ip']        = $this->ip;
		$data['mid']       = intval($this->mid);
		$data['thread_id'] = intval($this->thread_id);
		if ($this->replyto) {
			$data['replyto'] = $this->replyto;
		}
		$data['content'] = $this->content;
		if ($this->cost) {
			$data['cost'] = intval($this->cost);
		}
		if ($this->reward) {
			$data['reward'] = intval($this->reward);
		}

		return $data;
	}

	private function resetData() {
		$this->ip        = '';
		$this->reward    = $this->cost = $this->mid = 0;
		$this->thread_id = 0;
		$this->replyto   = 0;
		$this->content   = '';
	}
}