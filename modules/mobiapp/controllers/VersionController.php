<?php

/**
 * app 更新管理
 * @author DQ
 * @date   2015年11月30日 上午11:11:16
 *
 * @param
 *
 * @return
 *
 */
class VersionController extends Controller {
	protected $checkUser  = true;
	protected $acls       = array('*' => 'ver:mobi');
	public    $osList     = array(0 => '请选择操作系统', 1 => 'Android', 2 => 'iOS');
	public    $updateList = array(0 => '否', 1 => '是', 2 => '不更新');

	public function index() {
		$data               = array();
		$data ['canDelVer'] = $data ['canAddVer'] = icando('ver:mobi');
		$data ['osList']    = $this->osList;

		return view('version/index.tpl', $data);
	}

	/**
	 * 添加版本控制
	 *
	 * @author DQ
	 * @date   2015年11月30日 上午11:58:08
	 *
	 */
	public function add() {
		$data              = array();
		$form              = new AppVersionForm ();
		$data ['rules']    = $form->rules();
		$data ['formName'] = get_class($form);
		$data ['widgets']  = new DefaultFormRender ($form->buildWidgets($data));

		return view('version/form.tpl', $data);
	}

	public function edit($id) {
		$id   = intval($id);
		$data = dbselect('*')->from('{app_version}')->where(array('id' => $id))->get(0);
		$form = new AppVersionForm ($data);
		if ($data ['app_id']) {
			$temp = dbselect('name')->from('{rest_apps}')->where(array('id' => $data ['app_id']))->get();
			$data ['app_id'] .= ':' . $temp ['name'];
		}
		$data ['widgets']  = new DefaultFormRender ($form->buildWidgets($data));
		$data ['rules']    = $form->rules();
		$data ['formName'] = get_class($form);

		return view('version/form.tpl', $data);
	}

	public function del($ids) {
		$ids = safe_ids2($ids);
		if (empty ($ids)) {
			return NuiAjaxView::error('请选择版本.');
		}
		if (in_array(1, $ids)) {
			return NuiAjaxView::error('1为默认数据，无法删除！.');
		}
		if (dbupdate('{app_version}')->set(array('deleted' => 1))->where(array('id IN' => $ids))->exec()) {
			$recycle = new DefaultRecycle ($ids, 'mobiapp app_version', 'app_version', 'ID:{id};版本号:{vername}');
			RecycleHelper::recycle($recycle);

			return NuiAjaxView::reload('#mobi-ch-table', '所选版本数据已放入回收站.');
		} else {
			return NuiAjaxView::error('数据库操作失败.');
		}
	}

	public function copy($id = 0) {
		$id = intval($id);
		if ($id <= 0) {
			return NuiAjaxView::error('请选择版本.');
		}
		// 获取版本信息
		$rs = dbselect('*')->from('{app_version}')->where(array('id' => $id, 'deleted' => 0))->get();
		if (empty ($rs)) {
			return NuiAjaxView::error('不存在该版本信息！');
		}
		// 获取版本渠道信息
		$rst = dbselect('*')->from('{app_version_market}')->where(array('version_id' => $id, 'deleted' => 0));

		// 写入数据
		start_tran();
		try {
			$uid           = $this->user->getUid();
			$time          = time();
			$dataVersion   = array('update_type' => $rs ['update_type'], 'create_uid' => $uid, 'update_uid' => $uid, 'create_time' => $time, 'update_time' => $time, 'app_id' => $rs ['app_id'], 'version' => $rs ['version'], 'vername' => $rs ['vername'], 'os' => $rs ['os'], 'apk_file' => $rs ['apk_file'], 'size' => $rs ['size'], 'desc' => $rs ['desc']);
			$returnVersion = dbinsert($dataVersion)->into('{app_version}')->exec();
			if ($rst) {
				foreach ($rst as $val) {
					$dataTemp   = array('version_id' => $returnVersion [0], 'market' => $val ['market'], 'market_name' => $val ['market_name'], 'ad_config_id' => $val ['ad_config_id'], 'url' => $val ['url'], 'create_uid' => $uid, 'update_uid' => $uid, 'create_time' => $time, 'update_time' => $time);
					$returnTemp = dbinsert($dataTemp)->into('{app_version_market}')->exec();
				}
			}
			if ($returnVersion) {
				commit_tran();
			}
		} catch (Exception $e) {
			rollback_tran();
			$returnVersion = '';
		}

		if ($returnVersion) {
			return NuiAjaxView::reload('#mobi-ch-table', '所选版本已经复制完成.');
		} else {
			return NuiAjaxView::error('数据库操作失败.');
		}
	}

	public function save() {
		$form = new AppVersionForm ();
		$data = $form->valid();
		if ($data) {
			$time                 = time();
			$uid                  = $this->user->getUid();
			$data ['update_time'] = $time;
			$data ['update_uid']  = $uid;
			$size                 = irqst('apk_file_size');
			if ($size > 0) {
				$data ['size'] = $size;
			}
			$id = $data ['id'];
			unset ($data ['id']);
			if (empty ($id)) {
				$data ['create_time'] = $time;
				$data ['create_uid']  = $uid;
				$db                   = dbinsert($data);
				$rst                  = $db->into('{app_version}')->exec();
				if ($rst && $rst [0]) {
					$id = $rst [0];
				} else {
					$rst = false;
				}
			} else {
				$db  = dbupdate('{app_version}');
				$rst = $db->set($data)->where(array('id' => $id))->exec();
			}
			if ($rst) {
				return NuiAjaxView::click('#rtn2ads', '版本已经保存.');
			} else {
				return NuiAjaxView::error('保存版本信息出错啦:' . DatabaseDialect::$lastErrorMassge);
			}
		} else {
			return NuiAjaxView::validate(get_class($form), '表单数据格式有误', $form->getErrors());
		}
	}

	public function data($_cp = 1, $_lt = 20, $_sf = 'VER.id', $_od = 'd', $_ct = 0) {
		$app_id  = rqst('app_id', '');
		$version = rqst('version', '');
		$market  = rqst('market', '');
		$os      = rqst('os', 0);
		$os      = ($os == 1 || $os == 2) ? $os : 0;

		$rows = dbselect('VER.*,APP.name')->from('{app_version} AS VER');
		$rows->join('{rest_apps} as APP', 'VER.app_id = APP.id');
		// 排序
		$rows->sort($_sf, $_od);
		// 分页
		$rows->limit(($_cp - 1) * $_lt, $_lt);

		// 搜索
		if ($app_id) {
			$where ['VER.app_id'] = ( int )$app_id;
		}
		if ($version) {
			$where ['VER.version'] = $version;
		}
		if ($market) {
			$where ['VER.market'] = $market;
		}
		if ($os) {
			$where ['VER.os'] = $os;
		}

		$where ['VER.deleted'] = 0;
		$rows->where($where);
		// 总数
		$total = '';
		if ($_ct) {
			$total = $rows->count('VER.id');
		}
		$data ['total'] = $total;
		foreach ($rows as $row) {
			$row ['os']          = $this->osList [ $row ['os'] ];
			$row ['update_type'] = $this->updateList [ $row ['update_type'] ];
			$data ['rows'] []    = $row;
		}

		$data ['canEditVer'] = icando('ver:mobi');
		$data ['canDelVer']  = icando('ver:mobi');

		return view('version/data.tpl', $data);
	}

	public function info($id = 0) {
		$id   = intval($id);
		$data = dbselect('VER.id,VER.version,APP.name')->from('{app_version} AS VER')->join('{rest_apps} AS APP', "VER.app_id = APP.id")->where(array('VER.id' => $id))->get();

		return view('version/info.tpl', $data);
	}

	public function datainfo($_cp = 1, $_lt = 20, $_sf = 'id', $_od = 'd', $_ct = 0) {
		$versionId = rqst('version_id', '');
		$market    = trim(rqst('market', ''));

		$rows = dbselect('*')->from('{app_version_market}');
		// 排序
		$rows->sort($_sf, $_od);
		// 分页
		$rows->limit(($_cp - 1) * $_lt, $_lt);

		// 搜索
		if ($versionId) {
			$where ['version_id'] = ( int )$versionId;
		}
		if ($market) {
			$where ['market LIKE '] = "%" . $market . "%";
		}

		$where ['deleted'] = 0;
		$rows->where($where);
		// 总数
		$total = '';
		if ($_ct) {
			$total = $rows->count('id');
		}
		$data ['total'] = $total;
		foreach ($rows as $row) {
			if ($row ['ad_config_id']) {
				$temp            = dbselect('name')->from('{app_ads}')->where(array('id' => $row ['ad_config_id']))->get();
				$row ['ad_name'] = $temp ['name'];
			}
			$data ['rows'] [] = $row;
		}
		$data ['canEditVer'] = icando('ver:mobi');
		$data ['canDelVer']  = icando('ver:mobi');

		return view('version/datainfo.tpl', $data);
	}

	/**
	 * 添加版本控制
	 *
	 * @author DQ
	 * @date   2015年11月30日 上午11:58:08
	 *
	 */
	public function addinfo($version_id = 0) {
		$version_id = ( int )$version_id;
		if ($version_id <= 0) {
			Response::showErrorMsg('版本信息丢失！', 404);
		}
		$data              = array('version_id' => $version_id);
		$form              = new AppVerMarketForm ($data);
		$data ['rules']    = $form->rules();
		$data ['formName'] = get_class($form);
		$data ['widgets']  = new DefaultFormRender ($form->buildWidgets($data));

		return view('version/forminfo.tpl', $data);
	}

	/**
	 * 编辑单个信息市场信息
	 *
	 * @param int $id
	 */
	public function editinfo($id) {
		$id   = intval($id);
		$data = dbselect('*')->from('{app_version_market}')->where(array('id' => $id))->get(0);
		$form = new AppVerMarketForm ($data);
		if ($data ['ad_config_id']) {
			$temp = dbselect('name')->from('{app_ads}')->where(array('id' => $data ['ad_config_id']))->get();
			$data ['ad_config_id'] .= ':' . $temp ['name'];
		}

		$data ['widgets']  = new DefaultFormRender ($form->buildWidgets($data));
		$data ['rules']    = $form->rules();
		$data ['formName'] = get_class($form);

		return view('version/forminfo.tpl', $data);
	}

	/**
	 * 批量保存渠道和广告的配置信息
	 */
	public function saveinfo() {
		$form = new AppVerMarketForm ();
		$data = $form->valid();
		if ($data) {
			$time                 = time();
			$uid                  = $this->user->getUid();
			$data ['update_time'] = $time;
			$data ['update_uid']  = $uid;
			$id                   = $data ['id'];
			if (empty ($data ['ad_config_id'])) {
				$data ['ad_config_id'] = 0;
			}
			unset ($data ['id']);
			if (empty ($id)) {
				// 如果是添加，则是批量添加
				$data ['create_time'] = $time;
				$data ['create_uid']  = $uid;
				$rsMarket             = array_filter(explode("\n", trim($data ['market'])));
				foreach ($rsMarket as $val) {
					$rsTemp               = explode('|', $val);
					$rsTemp [1]           = !$rsTemp [1] ? $rsTemp [0] : $rsTemp [1];
					$data ['market']      = $rsTemp [0];
					$data ['market_name'] = $rsTemp [1];
					$db                   = dbinsert($data);
					$rst                  = $db->into('{app_version_market}')->exec();
				}
			} else {
				$data ['market_name'] = rqst('market_name', '');
				$db                   = dbupdate('{app_version_market}');
				$rst                  = $db->set($data)->where(array('id' => $id))->exec();
			}
			if ($rst) {
				return NuiAjaxView::click('#rtn2ads', '市场已经保存.');
			} else {
				return NuiAjaxView::error('保存市场信息出错啦:' . DatabaseDialect::$lastErrorMassge);
			}
		} else {
			return NuiAjaxView::validate(get_class($form), '表单数据格式有误', $form->getErrors());
		}
	}

	public function delinfo($ids) {
		$ids = safe_ids2($ids);
		if (empty ($ids)) {
			return NuiAjaxView::error('请选择市场.');
		}
		if (dbupdate('{app_version_market}')->set(array('deleted' => 1))->where(array('id IN' => $ids))->exec()) {
			$recycle = new DefaultRecycle ($ids, 'mobiapp app_version_market', 'app_version_market', 'ID:{id};市场名称:{market}');
			RecycleHelper::recycle($recycle);

			return NuiAjaxView::reload('#mobi-ch-table', '所选市场数据已放入回收站.');
		} else {
			return NuiAjaxView::error('数据库操作失败.');
		}
	}
}