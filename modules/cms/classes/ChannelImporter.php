<?php

namespace cms\classes;

class ChannelImporter {
	private $channels = [];
	private $data     = [];

	public function __construct(\ParameterDef $defaultData) {
		$this->data = $defaultData->toArray();
	}

	public function getChannelByName($name) {
		if (isset($this->channels[ $name ])) {
			return $this->channels[ $name ];
		}

		return null;
	}

	public function importByNames($names, $sep = '/') {
		start_tran();
		$channels = explode($sep, $names);
		$upCh     = 0;
		$ch       = false;
		foreach ($channels as $ch) {
			if (isset($this->channels[ $ch ])) {
				$upCh = $this->channels[ $ch ]['id'];
				continue;
			}
			$py      = $ch;
			$channel = dbselect('*')->from('{cms_channel}')->where(['refid' => $py])->get();
			if ($channel) {
				$this->channels[ $ch ] = $channel;
				$upCh                  = $channel['id'];
				continue;
			}
			$channel = $this->import($ch, $upCh);
			if ($channel) {
				$this->channels[ $ch ] = $channel;
				$upCh                  = $channel['id'];
			} else {
				rollback_tran();

				return false;
			}
		}
		commit_tran();

		return $ch;
	}

	private function import($ch, $upid) {
		$data = array_merge($this->data, ['upid' => $upid, 'refid' => $ch, 'name' => $ch]);
		\Request::getInstance()->addUserData($data, true);
		$uid  = 1;
		$form = new \ChannelForm ();
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
			$time = time();

			$ch ['update_uid']  = $uid;
			$ch ['update_time'] = $time;
			$ch['list_page']    = 0;
			$ch['index_page']   = 0;
			$ch ['isfinal']     = 1;

			$ch ['hidden'] = 0;

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
				$ch ['basedir'] = \Pinyin::c($ch ['name']);
				if (!$ch['basedir']) {
					$ch['basedir'] = rand_str(6, 'a-z');
				}
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
			if (dbselect('id')->from('{cms_page}')->where(array('url_key' => $urlkey, 'id !=' => $ch ['index_page']))->exist('id')) {
				return false;
			}
			// 过后需要重新生成列表页的URL
			$ch ['list_page_url'] = $list_page_name;

			$ch ['create_uid']  = $uid;
			$ch ['create_time'] = $time;
			$rst                = dbinsert($ch)->into('{cms_channel}')->exec();
			if ($rst) {
				$id = $rst [0];
				dbupdate('{cms_channel}')->set(array('subchannels' => $id))->where(array('id' => $id))->exec();
			}
			$channel = false;
			if ($rst) {
				$channel  = dbselect('*')->from('{cms_channel}')->where(array('id' => $id))->get(0);
				$arg      = array('tid' => $id, 'trid' => $channel ['refid'], 'model' => $channel ['default_model'], 'create_time' => $channel ['create_time'], 'name' => $channel ['name'], 'title' => $channel['name'], 'path' => $channel ['path'], 'basedir' => $channel ['basedir'], 'page' => 1);
				$list_url = parse_page_url($list_page_name, $arg);
				if (dbselect('id')->from('{cms_page}')->where(array('url_key' => md5($list_url), 'id !=' => $ch ['list_page']))->exist('id')) {
					return false;
				}
				dbupdate('{cms_channel}')->set(array('list_page_url' => $list_url))->where(array('id' => $id))->exec();
				$channel ['list_page_url'] = $list_url;
				\CmsPage::updateChannelPage($channel, $index_page_tpl, $list_page_tpl, $list_page_name);

				// 更新栏目的下级栏目
				$oupid = irqst('oupid', 0);
				if ($oupid != $channel ['upid']) {
					// 取所有组数据
					$channels = dbselect('upid,id,refid')->from('{cms_channel}')->where(array('is_topic_channel' => $channel ['is_topic_channel']))->toArray();
					// 遍历树形数据
					$iterator = new \TreeIterator ($channels, 0, 'id', 'upid');
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
				return $channel;
			}
		}

		return false;
	}
}