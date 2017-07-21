<?php

class CmsRestService {
	/**
	 *
	 * @param RestServer $server
	 *
	 * @return \RestServer
	 */
	public static function on_init_rest_server($server) {
		$server->registerClass(new CmsRestService (), '1', 'cms');

		return $server;
	}

	/**
	 * 取栏目树.
	 *
	 * @param array  $params
	 *            <ul>
	 *            <li>topic - 是否是专题栏目</li>
	 *            <li>model - 模型</li>
	 *            </ul>
	 * @param string $key
	 * @param string $secret
	 *
	 * @return array
	 */
	public function rest_channel($params, $key, $secret) {
		$is_topic = isset ($params ['topic']) && $params ['topic'];
		$model    = isset ($params ['model']) && $params ['model'] ? $params ['model'] : null;
		$channels = ChannelForm::getChannelTree($model, $is_topic);

		return ['error' => 0, 'channels' => $channels];
	}

	/**
	 * 取一个页面的数据.id和url二选一做为参数.
	 *
	 * @param array  $params
	 *            <ul>
	 *            <li>id - 页面编号</li>
	 *            <li>url- 页面的URL</li>
	 *            </ul>
	 * @param string $key
	 * @param string $secret
	 *
	 * @return array
	 */
	public function rest_page($params, $key, $secret) {
		$id = isset ($params ['id']) ? intval($params ['id']) : false;
		if (!$id) {
			$url = isset ($params ['url']) ? $params ['url'] : false;
		}
		if (!$id && !$url) {
			return ['error' => 1, 'message' => 'id和url参数至少提供一个.'];
		}
		if ($id) {
			$page = CmsPage::load($id);
		} else {
			$page = CmsPage::load($url, true);
		}
		if ($page) {
			$fields = $page->getFields();
			unset ($fields ['__this_data']);

			return ['error' => 0, 'page' => $fields];
		} else {
			return ['error' => 2, 'message' => '页面不存在.'];
		}
	}

	/**
	 * 保存一个基本页面.
	 *
	 * @param array  $params
	 * @param string $key
	 * @param string $secret
	 *
	 * @return array
	 */
	public function rest_post_save($params, $key, $secret) {
		if (!isset($params['model'])) {
			return ['error' => 1, 'message' => '模型未指定'];
		}
		$rst = CmsPage::save('page', $params['model'], 0, false);
		if ($rst) {
			return ['error' => 0, 'message' => $rst];
		} else {
			return ['error' => 1, 'message' => last_log_msg()];
		}
	}
}