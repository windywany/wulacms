<?php
 /**
    * app 广告配置
    * @author DQ
    * @date 2015年11月30日 上午11:11:16
    * @param
    * @return 
    * 
    */
class AdsController extends Controller {
	protected $checkUser = true;
	protected $acls = array ('*' => 'ads:mobi' );
	public $osList = array(
	    0 => '请选择操作系统',
	    1=> 'Android',
	    2=> 'iOS',
	);
	
	public function index() {
		$data = array ();
		$data ['canDelAds'] = $data ['canAddAds'] = icando ( 'ads:mobi' );
		$data ['osList'] = $this->osList;
		return view ( 'ads/index.tpl', $data );
	}
	
	 /**
	    * 添加版本控制
	    * @author DQ
	    * @date 2015年11月30日 上午11:58:08
	    * 
	    */
	public function add() {
		$data = array ();
		$form = new AppAdsForm();
		$data ['rules'] = $form->rules ();
		$data ['formName'] = get_class ( $form );
		$data ['widgets'] = new DefaultFormRender ( $form->buildWidgets ( $data ) );
		return view ( 'ads/form.tpl', $data );
	}
	
	
	public function edit($id) {
		$id = intval ( $id );
		$data = dbselect ( '*' )->from ( '{app_ads}' )->where ( array ('id' => $id ) )->get ( 0 );
		$form = new AppAdsForm();
		$data ['widgets'] = new DefaultFormRender ( $form->buildWidgets ( $data ) );
		$data ['rules'] = $form->rules ();
		$data ['formName'] = get_class ( $form );
		return view ( 'ads/form.tpl', $data );
	}
	
	public function del($ids) {
		$ids = safe_ids2 ( $ids );
		if (empty ( $ids )) {
			return NuiAjaxView::error ( '请选择版本.' );
		}
		if(in_array(1,$ids) || in_array(2, $ids)){
		    return NuiAjaxView::error ( '1 or 2 跟数据无法删除！.' );
		}
		//判断是否启用，如果启用则无法删除
		$exist = dbselect('ad_config_id')->from('{app_version_market}')->where(array('ad_config_id IN' => $ids))->toArray();
		if ($exist) {
			return NuiAjaxView::error ( '该广告配置正在使用，无法删除！' );
		}
		if (dbupdate ( '{app_ads}' )->set ( array ('deleted' => 1 ) )->where ( array ('id IN' => $ids ) )->exec ()) {
			$recycle = new DefaultRecycle ( $ids, 'mobiapp ads', 'app_ads', 'ID:{id};配置名称:{name}' );
			RecycleHelper::recycle ( $recycle );
			return NuiAjaxView::reload ( '#mobi-ch-table', '所选版本数据已放入回收站.' );
		} else {
			return NuiAjaxView::error ( '数据库操作失败.' );
		}
	}
	
	public function save() {
		$form = new AppAdsForm();
		$data = $form->valid ();
		if ($data) {
			$time = time ();
			$uid = $this->user->getUid ();
			$data ['update_time'] = $time;
			$data ['update_uid'] = $uid;
			$id = $data['id'];
			unset($data['id']);
			if (empty ( $id )) {
				$data ['create_time'] = $time;
				$data ['create_uid'] = $uid;
				$db = dbinsert ( $data );
				$rst = $db->into ( '{app_ads}' )->exec ();
			} else {
			    $db = dbupdate ( '{app_ads}' );
				$rst = $db->set ( $data )->where ( array ('id' => $id ) )->exec ();
			}
			if ($rst) {
				return NuiAjaxView::click ( '#rtn2ads', '配置信息已经保存.' );
			} else {
				return NuiAjaxView::error ( '保存配置信息出错啦:' . DatabaseDialect::$lastErrorMassge );
			}
		} else {
			return NuiAjaxView::validate ( get_class ( $form ), '表单数据格式有误', $form->getErrors () );
		}
	}
	
	public function data($_cp = 1, $_lt = 20, $_sf = 'VER.id', $_od = 'd', $_ct = 0) {
	    $name = trim(rqst('name',''));
	    $os = rqst('os',0);
	    $os = ($os == 1 || $os == 2)?$os:0;
	    
		$rows = dbselect ( '*' )->from ( '{app_ads} AS VER' );
		// 排序
		$rows->sort ( $_sf, $_od );
		// 分页
		$rows->limit ( ($_cp - 1) * $_lt, $_lt );
		
		//搜索
		if($name){
		    $where ['VER.name LIKE '] = "%".$name."%";
		}
		if($os){
		    $where ['VER.os'] = $os;
		}
		
		$where ['VER.deleted'] = 0;
		$rows->where ( $where );
		// 总数
		$total = '';
		if ($_ct) {
			$total = $rows->count ( 'VER.id' );
		}
		$data ['total'] = $total;
		foreach ( $rows as $row ) {
		    $row['os'] = $this->osList[$row['os']];
			$data ['rows'] [] = $row;
		}
		
		$data ['canEditAds'] = icando ( 'ads:mobi' );
		$data ['canDelAds'] = icando ( 'ads:mobi' );
		return view ( 'ads/data.tpl', $data );
	}
	
	
	/**
	 * 信息复制
	 * @author DQ
	 * @date 2015年12月2日 上午10:42:58
	 * @param
	 * @return
	 *
	 */
	function copy($id=0){
	    $id = intval( $id );
	    if ($id<=0) {
	        return NuiAjaxView::error ( '请选择版本.' );
	    }
	    //获取版本信息
	    $rs = dbselect('*')->from('{app_ads}')->where(array('id'=>$id,'deleted'=>0))->get();
	    if(empty($rs)){
	        return NuiAjaxView::error ( '不存在该版本信息！' );
	    }
	    //写入数据
	    start_tran();
	    try {
	        $uid = $this->user->getUid ();
	        $time = time();
	        $dataVersion = array(
	            'name' => $rs['name'],
	            'os' => $rs['os'],
	            'banner' => $rs['banner'],
	            'screen' => $rs['screen'],
	            'stream' => $rs['stream'],
	            'clickinsert' => $rs['clickinsert'],
	            'probability' => $rs['probability'],
	            'create_uid' => $uid,
	            'update_uid' => $uid,
	            'create_time' => $time,
	            'update_time' => $time
	        );
	        $returnVersion = dbinsert($dataVersion)->into('{app_ads}')->exec();
	        if($returnVersion){
	            commit_tran();
	        }
	    }catch (Exception $e){
	        rollback_tran();
	        $returnVersion = '';
	    }
	    if ($returnVersion) {
	        return NuiAjaxView::reload ( '#mobi-ch-table', '所选广告配置已经复制完成.' );
	    } else {
	        return NuiAjaxView::error ( '数据库操作失败.' );
	    }
	}
	
}