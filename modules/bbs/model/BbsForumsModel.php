<?php

namespace bbs\model;

use bbs\form\BbsForumForm;
use db\model\FormModel;

class BbsForumsModel extends FormModel {
	private $forumUrlReplacement = ['{path}', '{slug}', '{fid}'];

	protected function config() {
		$this->setValidateRule('last_thread',['regexp(/^0|[1-9]\d*$/)'=>'最后帖子ID不合法']);
		$this->setValidateRule('last_post',['regexp(/^0|[1-9]\d*$/)'=>'最后回复ID不合法']);
	}

	public function parseForumURL($pattern, $id) {
		if (strpos($pattern, '}') > 0) {
			$data = $this->get($id, 'path,slug');
			$p[]  = trim($data['path'], '/');
			$p[]  = trim($data['slug']);
			$p[]  = $id;

			return str_replace($this->forumUrlReplacement, $p, $pattern);
		}

		return $pattern;
	}

	public function get($id, $fields = '*') {
		$data = parent::get($id, $fields);
		if (isset($data['masters'])) {
			$masters = @json_decode($data['masters'], true);
			if ($masters) {
				$i = 2;
				foreach ($masters as $m) {
					if ($m['master']) {
						$data['master1'] = $m['mid'];
					} else {
						$data[ 'master' . $i ] = $m['mid'];
						$i++;
					}
				}
			}
		}

		return $data;
	}

	/**
	 * 获取数据型数据.
	 *
	 * @param integer $upid
	 *            上级ID.
	 * @param integer $limit
	 *            获取条数.
	 * @param integer $page
	 *            页数.
	 *
	 * @return array
	 */
	public function getTreeData($upid = 0, $limit = 10, $page = 0) {
		$upid  = intval($upid);
		$limit = intval($limit);
		$start = intval($page) * $limit;
		$sql   = dbselect('*')->from($this->table)->setDialect($this->dialect)->where(['upid' => $upid, 'deleted' => 0])->asc('sort');
		$rst   = $sql->limit($start, $limit)->toArray();
		$this->checkSQL($sql);

		return $rst;
	}

	/**
	 * 数据中包括版主信息的更新.
	 *
	 * @param array $data 版块数据.
	 * @param array $con  更新条件.
	 *
	 * @return bool
	 */
	public function updateForumWithMasters($data, $con = []) {
		$this->setMasters($data);

		return $this->update($data, $con);
	}

	/**
	 * @param \AbstractForm|array $data
	 * @param null                $cb
	 *
	 * @return bool|int
	 */
	public function create($data, $cb = null) {
		$this->setMasters($data);
		if (empty($data['forum_expire'])) {
			$data['forum_expire'] = 0;
		}
		if (empty($data['thread_expire'])) {
			$data['thread_expire'] = 0;
		}
		if(!isset($data['title'])||empty($data['title'])){
			$data['title'] = $data['name'];
		}
		return parent::create($data, $cb);
	}

	/**
	 * 更新版块的URL.
	 *
	 * @param string  $url
	 * @param integer $id
	 *
	 * @return bool 更新成功返回true.
	 */
	public function updateForumUrl($url, $id) {
		if (strpos($url, '}') > 0 && $id > 0) {
			$url = $data['url']     = $this->parseForumURL($url, $id);
			$data['url_key'] = md5($data['url']);
			if ($this->exist($data)) {
				return false;
			}
			$this->update($data, ['id' => $id]);
		}

		return $url;
	}

	public function updateForumRelations($oupid, $id) {
		// 取所有组数据
		$forums = dbselect('upid,id,refid')->from('{bbs_forums}')->toArray();
		// 遍历树形数据
		$iterator = new \TreeIterator ($forums, 0, 'id', 'upid');
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
			dbupdate('{bbs_forums}')->set(array('subforums' => $ids))->where(array('id' => $nid))->exec();
		}
		// 取当前栏目
		$node = $iterator->getNode($id);
		// 取当前栏目的所有子栏目,因为他们的上级栏目发生了变化.
		$nodes         = $node->getChildren();
		$nodes [ $id ] = $node;
		foreach ($nodes as $nid => $node) {
			$parents = $node->getParentsIdList('refid');
			if ($parents) {
				$parents = implode(',', $parents);
			} else {
				$parents = '';
			}
			dbupdate('{bbs_forums}')->set(array('parents' => $parents))->where(array('id' => $nid))->exec();
		}
	}
	public function updateForumSubIds($id){
		// 取所有组数据
		$forums = dbselect ( 'upid,id' )->from ( '{bbs_forums}' )->toArray ();
		// 遍历树形数据
		$iterator = new \TreeIterator ( $forums, 0, 'id', 'upid' );
		$nodes = array ();
		// 取当前栏目
		$node = $iterator->getNode ( $id );
		// 取当前栏目的上级栏目
		$node->getParents ( $nodes );
		unset ( $nodes ['0'], $nodes [0] );
		// 更新它们的subchannels
		foreach ( $nodes as $nid => $nde ) {
			$ids = implode ( ',', $nde->getSubIds () );
			dbupdate ( '{bbs_forums}' )->set ( array ('subforums' => $ids ) )->where ( array ('id' => $nid ) )->exec ();
		}
		$ids = $node->getSubIds();
		dbupdate ( '{bbs_forums}' )->set ( array ('subforums' =>implode(',',$ids) ) )->where ( array ('id' => $id ) )->exec ();
	}
	private function setMasters(&$data) {
		$masters = [];
		if (isset($data['master1'])) {
			$masters[] = ['mid' => $data['master1'], 'master' => 1, 'name' => dbselect()->from('{member}')->where(['mid' => $data['master1']])->get('nickname')];
			unset($data['master1']);
		}

		if (isset($data['master2'])) {
			$masters[] = ['mid' => $data['master2'], 'master' => 0, 'name' => dbselect()->from('{member}')->where(['mid' => $data['master2']])->get('nickname')];
			unset($data['master2']);
		}

		if (isset($data['master3'])) {
			$masters[] = ['mid' => $data['master3'], 'master' => 0, 'name' => dbselect()->from('{member}')->where(['mid' => $data['master2']])->get('nickname')];
			unset($data['master3']);
		}

		$data['masters'] = json_encode($masters);
	}

	protected function createForm($data = []) {
		return new BbsForumForm($data);
	}
}