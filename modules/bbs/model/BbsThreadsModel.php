<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/9/8 0008
 * Time: 下午 2:30
 */

namespace bbs\model;

use bbs\form\ThreadForm;
use db\model\FormModel;

class BbsThreadsModel extends FormModel {
	public  $forum_id    = 0;//版块
	public  $mid         = 0;//会员编号
	public  $type        = 1;//类型
	public  $topic       = '';//话题
	public  $subject     = '';//主题
	public  $content     = '';//正文
	public  $cost        = 0;
	public  $view_passwd = '';//查看密码
	public  $tags        = '';//标签
	public  $closeat     = 0;//自动半闭
	private $urlArgs     = ['{path}', '{slug}', '{fid}', '{tid}'];

	/**
	 * 加载帖子.
	 *
	 * @param int $id
	 *
	 * @return array 帖子信息.
	 */
	public function load($id) {
		$thread = $this->get($id);
		if ($thread) {
			$fid             = $thread['forum_id'];
			$thread['forum'] = dbselect('*')->from('{bbs_forums}')->where(['id' => $fid])->get();
		}

		return $thread;
	}

	public function loadForPage($url) {

	}

	/**
	 * @param \AbstractForm|array $data
	 * @param null                $cb
	 *
	 * @return bool|int
	 * @throws \Exception 更新数据库出错时抛出.
	 */
	public function create($data = null, $cb = null) {
		if (!$data) {
			$data = $this->getData();
		}
		if (isset($data['content'])) {
			$data['post']['content'] = $data['content'];
			unset($data['content']);
		}
		if (!isset($data['post'])) {
			$this->errors = '主帖为空';
			throw new \Exception('主帖为空');
		}
		//更新URL与最后
		$forumModel = new BbsForumsModel();
		$forum      = $forumModel->get($data['forum_id'], 'id,path,slug,thread_url_pattern,type,allow_anonymous,rank_id');
		if (!$forum) {
			$this->errors = '版块不存在';
			throw new \Exception('版块不存在');

		}
		if ($forum['type'] != $data['type']) {
			$this->errors = '此版块不允许发此帖';

			throw new \Exception('此版块不允许发此帖');
		}
		if (!$data['mid'] && !$forum['allow_anonymous']) {
			$this->errors = '此版块不允许匿名用户发帖';

			throw new \Exception('此版块不允许匿名用户发帖');
		}
		$post                 = $data['post'];
		$post['create_uid']   = $post['update_uid'] = $data['create_uid'] = $data['update_uid'] = $data['mid'];
		$post['create_time']  = $post['update_time'] = $data['create_time'] = $data['update_time'] = time();
		$post['deleted']      = $data['deleted'] = 0;
		$post['status']       = $data['status'] = 1;
		$data['last_post_id'] = 0;
		$data['url']          = '#';
		$data['url_key']      = '#';
		$data['post_count']   = 0;
		$data['post_id']      = 0;
		unset($data['post']);
		//创建帖子
		$this->applyTags($data);
		$data = apply_filter('on_bbs_thread_creating',$data);
		$tid = parent::create($data, $cb);
		if (!$tid) {
			throw new \Exception(var_export($this->errors,true));
		}
		//创建主回复
		$post['thread_id'] = $tid;
		$postModel         = new BbsPostsModel();
		$pid               = $postModel->create($post);
		if (!$pid) {
			throw new \Exception(var_export($postModel->getErrors(),true));
		}
		//更新帖子信息.
		$thread['url'] = $this->parseTheadURL($tid, $forum);
		if (!$thread['url']) {
			throw new \Exception('无法生成帖子URL');
		}
		$thread['url_key'] = md5($thread['url']);
		if ($this->exist($thread)) {
			throw new \Exception('生成帖子的URL重复');
		}
		$thread['post_id'] = $pid;
		if (!$this->update($thread, $tid)) {
			throw new \Exception('无法更新帖子');
		}
		//更新版块信息
		$forum                 = [];
		$forum['thread_count'] = imv('thread_count + 1');
		$forum['last_thread']  = $tid;
		$forum['update_time']  = time();
		$forumModel->update($forum, $data['forum_id']);
		fire('on_bbs_thread_created',$tid);
		$this->resetData();
		return $tid;
	}

	/**
	 * 删除回复.
	 *
	 * @param array $con 条件.
	 *
	 * @return bool 是否成功.
	 */
	public function deletePosts($con) {
		$postModel = new BbsPostsModel();

		return $postModel->delete($con);
	}

	/**
	 * 获取帖子URL。
	 *
	 * @param int   $tid
	 * @param array $forum
	 *
	 * @return bool|mixed 正确生成为URL,反之为false
	 */
	public function parseTheadURL($tid, $forum = null) {
		if ($forum == null) {
			$sql   = $this->select('BF.id,path,slug,thread_url_pattern', 'TD')->join('{bbs_forums} BF', 'BF.id = TD.forum_id')->where(['TD.id' => $tid]);
			$forum = $sql->get();
		}
		if ($forum) {
			$r[] = $forum['path'];
			$r[] = $forum['slug'];
			$r[] = $forum['id'];
			$r[] = $tid;

			return str_replace($this->urlArgs, $r, $forum['thread_url_pattern']);
		}

		return false;
	}

	protected function createForm($data = []) {
		return new ThreadForm($data);
	}

	private function applyTags(&$data) {
		if (empty($data['tags'])) {
			$search_string = $data['subject'] . $data['post']['content'];
			$st            = get_keywords(null, $search_string, 20);
			$data['tags']  = $st[0];
		} else {
			$data['tags'] = preg_split('#,+#', trim(trim(str_replace(array('，', ' ', '　', '-', ';', '；', '－'), ',', $data['tags'])), ','));
			$st           = get_keywords(implode(',', $data['tags']));
		}
		$data['search_tags'] = $st[1];
	}

	private function getData() {
		$data['forum_id'] = intval($this->forum_id);
		$data['mid']      = intval($this->mid);
		$data['type']     = intval($this->type);
		if ($this->topic) {
			$data['topic'] = '';
		}
		$data['subject'] = $this->subject;
		$data['content'] = $this->content;
		if ($this->cost) {
			$data['cost'] = intval($this->cost);
		}
		if ($this->view_passwd) {
			$data['view_passwd'] = '';
		}
		if ($this->tags) {
			$data['tags'] = '';
		}
		if ($this->closeat) {
			$data['closeat'] = 0;
		}
		return $data;
	}

	private function resetData() {
		$data['forum_id']    = 0;
		$data['mid']         = 0;
		$data['type']        = 1;
		$data['topic']       = '';
		$data['subject']     = '';
		$data['content']     = '';
		$data['cost']        = 0;
		$data['view_passwd'] = '';
		$data['tags']        = '';
		$data['closeat']     = 0;
	}
}