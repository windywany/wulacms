<?php
/*
 * This file is part of wulacms.
 *
 * (c) Leo Ning <windywany@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace comment\classes;

use passport\classes\PassportResetService;
use redis\Redis4p;

class CommentRestService {

	/**
	 * 评论.
	 *
	 * @param array  $param
	 * @param string $key
	 * @param string $secret
	 *
	 * @return array
	 */
	public function rest_post_comment($param, $key, $secret) {
		$token = get_condition_value('token', $param);
		if (!$token) {
			return ['error' => 400, 'message' => 'token参数为空'];
		}
		// 评论内容
		$content = trim(get_condition_value('content', $param));
		if (mb_strlen($content) < 3) {
			return ['error' => 406, 'message' => '评论内容少于3个字'];
		}
		$comment['content'] = $content;
		// 用户信息
		Redis4p::select(PassportResetService::DB);
		$userInfo = Redis4p::getJSON($token);
		if (empty($userInfo)) {
			return ['error' => 401, 'message' => '用户未登录'];
		}
		$comment['create_uid']  = $comment['update_uid'] = $userInfo['mid'];
		$comment['author']      = $userInfo['nickname'];
		$comment['create_time'] = $comment['update_time'] = time();
		// 页面ID
		$pageid = get_condition_value('page_id', $param);
		if (empty($pageid) || !dbselect('id')->from('{cms_page}')->where(['id' => $pageid, 'allow_comment' => 1])->exist('id')) {
			return ['error' => 404, 'message' => '页面不存在或不允许评论'];
		}
		$comment['page_id'] = $pageid;
		// 被回复的评论ID
		$reply = get_condition_value('reply', $param);
		if ($reply && !dbselect('id')->from('{comments}')->where(['id' => $reply])->exist('id')) {
			return ['error' => 405, 'message' => '要回复的评论不存在'];
		}
		if ($reply) {
			$comment['parent'] = $reply;
		}
		$comment['status']    = 1;
		$comment['author_ip'] = \Request::getIp();
		$id                   = dbinsert($comment)->into('{comments}')->exec();
		if (!$id) {
			return ['error' => 500, 'message' => '内部错误'];
		}
		if ($reply) {
			dbupdate('{comments}')->set(['reply_count' => imv('reply_count+1')])->where(['id' => $reply])->exec();
		}
		dbupdate('{cms_page}')->set(['comments' => imv('comments+1')])->where(['id' => $pageid])->exec();

		return ['error' => 0, 'data' => ['id' => $id[0]]];
	}

	/**
	 * 评论内容.
	 *
	 * @param array  $param
	 * @param string $key
	 * @param string $secret
	 *
	 * @return array
	 */
	public function rest_get_comments($param, $key, $secret) {
		$time = intval(get_condition_value('time', $param));
		if (!$time) {
			return ['error' => 501, 'message' => '参数不正确'];
		}
		$where['create_time <='] = $time;
		//加载用户评论，不需要递归
		$token = get_condition_value('token', $param);
		if ($token) {
			Redis4p::select(PassportResetService::DB);
			$userInfo = Redis4p::getJSON($token);
			if (empty($userInfo)) {
				return ['error' => 401, 'message' => '用户未登录'];
			}
			$where['create_uid'] = $userInfo['mid'];

			return $this->loadComments($where, $param);
		}

		// 加载页面评论
		$pageid = get_condition_value('page_id', $param);
		if ($pageid) {
			$where['page_id'] = $pageid;
			$where['parent']  = 0;

			return $this->loadComments($where, $param, true);
		}

		// 加载评论的评论，不需要递归
		$cid = get_condition_value('cid', $param);
		if ($cid) {
			$where['parent'] = $cid;

			return $this->loadComments($where, $param);
		}

		return ['error' => 400, 'message' => 'token,page_id,cid不能同时为空'];
	}

	// 删除评论.
	public function rest_get_del_comment($params, $key, $secret) {
		$token = get_condition_value('token', $params);
		if (!$token) {
			return ['error' => 400, 'message' => 'token参数为空'];
		}
		$id = intval(get_condition_value('id', $params));
		if (!$id) {
			return ['error' => 402, 'message' => 'id参数为空'];
		}
		// 用户信息
		Redis4p::select(PassportResetService::DB);
		$userInfo = Redis4p::getJSON($token);
		if (empty($userInfo)) {
			return ['error' => 401, 'message' => '用户未登录'];
		}

		$comment = dbselect('parent,create_uid,page_id')->from('{comments}')->where(['id' => $id])->get();

		if (!$comment) {
			return ['error' => 404, 'message' => '评论不存在'];
		}
		if ($comment['create_uid'] != $userInfo['mid']) {
			return ['error' => 403, 'message' => '无权删除此评论'];
		}
		// 删除评论
		dbdelete()->from('{comments}')->where(['id' => $id])->exec();

		if ($comment['parent']) {
			// 更新回复数
			dbupdate('{comments}')->set(['reply_count' => imv('reply_count-1')])->where(['id' => $comment['parent'], 'reply_count >' => 0])->exec();
		}
		// 更新页面评论数
		dbupdate('{cms_page}')->set(['comments' => imv('comments-1')])->where(['id' => $comment['page_id'], 'comments >' => 0])->exec();

		return ['error' => 0, 'data' => []];
	}

	/**
	 * 加载评论.
	 *
	 * @param array $where
	 * @param array $param
	 * @param bool  $res
	 *
	 * @return array
	 */
	private function loadComments($where, $param, $res = false) {
		$page             = abs(get_condition_value('page', $param, 1));
		$limit            = abs(get_condition_value('limit', $param, 5));
		$where['status']  = 1;
		$where['deleted'] = 0;
		$cmts             = dbselect('id,create_uid as mid,create_time,author,author_ip,reply_count,content')->from('{comments}')->where($where)->limit(($page - 1) * $limit, $limit)->desc('create_time');
		$comments         = $cmts->toArray();
		if ($comments && $res) {
			$sw            = ['create_time <=' => $param['time']];
			$sw['status']  = 1;
			$sw['deleted'] = 0;
			foreach ($comments as $index => $comment) {
				$sw['parent'] = $comment['id'];
				$scms         = dbselect('id,create_uid as mid,create_time,author,author_ip,content')->from('{comments}')->where($sw)->limit(0, $limit)->desc('create_time')->toArray();
				if ($scms) {
					$comments[ $index ]['replies'] = $scms;
				}
			}
		}

		return ['error' => 0, 'data' => $comments];
	}
}