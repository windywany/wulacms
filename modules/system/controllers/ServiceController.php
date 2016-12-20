<?php

namespace system\controllers;

use system\classes\IService;
use system\classes\ServiceConfigForm;

class ServiceController extends \Controller {
	protected $checkUser = true;
	protected $acls      = array('*' => 's:account/usergroup');

	public function index() {
		$data     = [];
		$services = apply_filter('get_group_services', []);
		$this->prepareService($services, 'G');
		$services = apply_filter('get_user_services', []);
		$this->prepareService($services, 'U');

		$services         = dbselect('*')->from('{services}')->desc('enabled')->toArray();
		$data['services'] = $services;

		return view('service/index.tpl', $data);
	}

	public function enable($sid) {
		$sid = intval($sid);
		dbupdate('{services}')->set(['enabled' => 1])->where(['id' => $sid])->exec();

		return \NuiAjaxView::refresh('服务已启用');
	}

	public function disable($sid) {
		$sid = intval($sid);
		dbupdate('{services}')->set(['enabled' => 0])->where(['id' => $sid])->exec();

		return \NuiAjaxView::refresh('服务已停用');
	}

	public function config($gid, $service = '') {
		$gname = dbselect('group_name,type')->from('{user_group}')->where(['group_id' => $gid])->get();
		if (!$gname) {
			\Response::respond(404);
		}
		$groupServices = apply_filter('get_group_services', []);
		$data          = ['gid' => $gid, 'service' => $service, 'gname' => $gname['group_name'], 'gtype' => $gname['type']];
		// 所有已经启用且需要配置的服务
		$gservices = dbselect('*')->from('{user_group_service}')->where(['group_id' => $gid, 'enabled' => 1])->asc('service')->toArray();
		$enabled   = [];
		foreach ($gservices as $s) {
			$sid = $s['service'];
			if (!isset($groupServices[ $sid ])) {
				dbupdate('{user_group_service}')->set(['enabled' => 0])->where(['group_id' => $gid, 'service' => $sid])->exec();
				continue;
			}
			$enabled[] = $sid;
			$form      = $groupServices[ $sid ]->getConfigForm();
			if ($form instanceof \AbstractForm) {
				$data['configs'][ $sid ] = $groupServices[ $sid ]->getName();
			}
		}
		if (empty($service)) {
			//选要启用的服务.
			$services = dbselect('*')->from('{services}')->where(['type' => 'G'])->desc('enabled')->toArray();
			$form     = new ServiceConfigForm();
			$defaults = [];
			foreach ($services as $s) {
				$defaults[ $s['service'] ] = $s['name'];
			}
			$form->getField('services')->setOptions(['defaults' => $defaults]);
			$data['rules']    = $form->rules();
			$widgets          = new \DefaultFormRender($form->buildWidgets(['services' => $enabled]));
			$data['widgets']  = $widgets;
			$data['formName'] = $form->getName();
		} elseif (isset($groupServices[ $service ])) {
			//具体服务配置
			$cs   = $groupServices[ $service ];
			$form = $cs->getConfigForm();
			if ($form instanceof \AbstractForm) {
				$config = dbselect()->from('{user_group_service}')->where(['group_id' => $gid, 'service' => $service])->get('config');
				if ($config) {
					$config = json_decode($config, true);
				} else {
					$config = [];
				}
				$data['rules']    = $form->rules();
				$data['formName'] = $form->getName();
				$widgets          = new \DefaultFormRender($form->buildWidgets($config));
				$data['widgets']  = $widgets;
			} else {
				\Response::respond(404);
			}
		} else {
			\Response::respond(404);
		}

		return view('service/config.tpl', $data);
	}

	public function savecfg($gid, $service = '') {
		$groupServices = apply_filter('get_group_services', []);
		if ($service) {
			if (isset($groupServices[ $service ])) {
				//具体服务配置
				$cs   = $groupServices[ $service ];
				$form = $cs->getConfigForm();
				if ($form instanceof \AbstractForm) {
					$cfg = $form->valid();
					if ($cfg) {
						$cfg = json_encode($cfg);
						dbupdate('{user_group_service}')->set(['config' => $cfg])->where(['group_id' => $gid, 'service' => $service])->exec();

						return \NuiAjaxView::ok('配置成功');
					} else {
						return \NuiAjaxView::validate($form->getName(), '配置不正确，请重写填写', $form->getErrors());
					}
				}

				return \NuiAjaxView::error('错误的服务实现');
			}

			return \NuiAjaxView::error('服务已经不存在.');
		} else {
			$services = rqst('services', []);
			if ($services) {
				dbupdate('{user_group_service}')->set(['enabled' => 0])->where(['group_id' => $gid, 'service !IN' => $services])->exec();
				dbupdate('{user_group_service}')->set(['enabled' => 1])->where(['group_id' => $gid, 'service IN' => $services])->exec();
				$ss   = dbselect('service')->from('{user_group_service}')->where(['group_id' => $gid])->toArray('service', 'service');
				$news = [];
				foreach ($services as $s) {
					if (!isset($ss[ $s ])) {
						$data['group_id'] = $gid;
						$data['service']  = $s;
						$data['enabled']  = 1;
						$data['config']   = '';
						$news[]           = $data;
					}
				}
				if ($news) {
					dbinsert($news, true)->into('{user_group_service}')->exec();
				}
			} else {
				dbupdate('{user_group_service}')->set(['enabled' => 0])->where(['group_id' => $gid])->exec();
			}

			return \NuiAjaxView::refresh('服务列表配置完成');
		}
	}

	private function prepareService($services, $type) {
		$datas = [];
		foreach ($services as $id => $service) {
			if ($service instanceof IService) {
				$sid = dbselect()->from('{services}')->where(['service' => $id])->get('id');
				if ($sid) {
					continue;
				}
				$data['name']        = $service->getName();
				$data['service']     = $id;
				$data['type']        = $type;
				$data['description'] = $service->getDescription();
				$datas[]             = $data;
			}
		}
		if ($datas) {
			dbinsert($datas, true)->into('{services}')->exec();
		}
	}
}