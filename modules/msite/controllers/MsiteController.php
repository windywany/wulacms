<?php
defined ( 'KISSGO' ) or exit ( 'No direct script access allowed' );
class MsiteController extends Controller {
	protected $acls = array ('index' => 'msite:cms','add' => 'msite:cms','edit' => 'msite:cms','save' => 'msite:cms','del' => 'msite:cms' );
	protected $checkUser = true;
	public function index() {
		$domains = dbselect ( '*' )->from ( '{cms_msite}' );
		$sites = array ();
		foreach ( $domains as $d ) {
			$channel = $d ['channel'];
			if ($channel) {
				$d ['channel'] = dbselect ()->from ( '{cms_channel}' )->where ( array ('refid' => $channel ) )->get ( 'name' );
			} else {
				$d ['channel'] = '';
			}
			$topics = $d ['topics'];
			if ($topics) {
				$topics = explode ( '><', trim ( $topics, '><' ) );
				$topics = dbselect ( 'name' )->from ( '{cms_channel}' )->where ( array ('refid IN' => $topics ) )->toArray ( 'name' );
				if ($topics) {
					$d ['topics'] = implode ( ',', $topics );
				}
			}
			$d ['theme'] = cfg ( $d ['domain'] . '@msite_theme' );
			if($d ['mdomain']){
				$d['mtheme'] = cfg ( $d ['mdomain'] . '@msite_theme' );
			}
			$sites [] = $d;
		}
		$data ['sites'] = $sites;
		$data ['canMSite'] = true;
		return view ( 'index.tpl', $data );
	}
	public function add() {
		$form = new MSiteForm ();
		$channels = dbselect ( 'name,refid' )->from ( '{cms_channel} AS CH' );
		$usedChannel = dbselect ( 'channel' )->from ( '{cms_msite} AS PF' )->where ( array ('PF.channel' => imv ( 'CH.refid' ) ) );
		$channels->where ( array ('upid' => 0,'is_topic_channel' => 0,'isfinal' => 0,'!@' => $usedChannel ) );
		
		$data ['channels'] = $channels->toArray ( 'name', 'refid', array ('' => '--请选择栏目--' ) );
		
		$usedTopics = dbselect ( 'topics' )->from ( '{cms_msite} AS PF' )->toArray ( 'topics' );
		
		$topics = array ();
		foreach ( $usedTopics as $topic ) {
			if ($topic) {
				$topic = explode ( '><', trim ( $topic, '><' ) );
				foreach ( $topic as $t ) {
					$topics [$t] = $t;
				}
			}
		}
		$where = array ('upid' => 0,'is_topic_channel' => 1 );
		if ($topics) {
			$where ['refid !IN'] = array_keys ( $topics );
		}
		$allTopics = dbselect ( 'name,refid' )->from ( '{cms_channel}' )->where ( $where )->toArray ();
		$data ['channel'] = '';
		$data ['topics'] = array ();
		$data ['all_topics'] = $allTopics;
		$data ['rules'] = $form->rules ();
		$data ['theme'] = '';
		$data ['themes'] = get_theme_list ();
		return view ( 'form.tpl', $data );
	}
	public function edit($id) {
		$id = intval ( $id );
		if (! $id) {
			Response::showErrorMsg ( '非法的编号.', 403 );
		}
		$msite = dbselect ( '*' )->from ( '{cms_msite}' )->where ( array ('id' => $id ) );
		if ($msite [0]) {
			$data = $msite [0];
			$channels = dbselect ( 'name,refid' )->from ( '{cms_channel} AS CH' );
			$usedChannel = dbselect ( 'channel' )->from ( '{cms_msite} AS PF' )->where ( array ('PF.channel' => imv ( 'CH.refid' ) ) );
			$channels->where ( array ('upid' => 0,'is_topic_channel' => 0,'isfinal' => 0,'!@' => $usedChannel ) );
			
			$data ['channels'] = $channels->toArray ( 'name', 'refid', array ('' => '--请选择栏目--' ) );
			$channel = $data ['channel'];
			$data ['channels'] [$channel] = dbselect ( 'name' )->from ( '{cms_channel}' )->where ( array ('refid' => $channel ) )->get ( 'name' );
			
			$topics = $data ['topics'];
			
			if ($topics) {
				$data ['topics'] = explode ( '><', trim ( $topics, '><' ) );
			} else {
				$data ['topics'] = array ();
			}
			
			$usedTopics = dbselect ( 'topics' )->from ( '{cms_msite} AS PF' )->toArray ( 'topics' );
			
			$topics = array ();
			foreach ( $usedTopics as $topic ) {
				if ($topic) {
					$topic = explode ( '><', trim ( $topic, '><' ) );
					foreach ( $topic as $t ) {
						if (in_array ( $t, $data ['topics'] )) {
							continue;
						}
						$topics [$t] = $t;
					}
				}
			}
			$where = array ('upid' => 0,'is_topic_channel' => 1 );
			if ($topics) {
				$where ['refid !IN'] = array_keys ( $topics );
			}
			$allTopics = dbselect ( 'name,refid' )->from ( '{cms_channel}' )->where ( $where )->toArray ();
			
			$form = new MSiteForm ( $data );
			$data ['all_topics'] = $allTopics;
			$data ['rules'] = $form->rules ();
			
			$data ['theme'] = cfg ( $data ['domain'] . '@msite_theme' );
			$data ['mtheme'] = cfg ( $data ['mdomain'] . '@msite_theme' );
			$data ['themes'] = get_theme_list ();
			return view ( 'form.tpl', $data );
		} else {
			Response::showErrorMsg ( '站点不存在.', 404 );
		}
	}
	public function del($id = 0) {
		$id = intval ( $id );
		if ($id) {
			$where = array ('id' => $id );
			$msite = dbselect ()->from ( '{cms_msite}' )->where ( $where )->get ( 'domain' );
			if ($msite) {
				dbdelete ()->from ( '{preferences}' )->where ( array ('preference_group' => 'msite_theme','name' => $msite ) )->exec ();
				dbdelete ()->from ( '{cms_msite}' )->where ( $where )->exec ();
				RtCache::delete ( 'msite_sites' );
				return NuiAjaxView::ok ( '站点已经删除!', 'click', '#refresh' );
			}
		}
		Response::showErrorMsg ( '站点不存在.', 404 );
	}
	public function save() {
		$form = new MSiteForm ();
		$msite = $form->valid ();
		if ($msite) {
			if($msite['mdomain'] == $msite['domain']){
				 return NuiAjaxView::validate ( 'MSiteForm', '表单验证出错', array('mdomain'=>'移动域名不能与主域名相同.'));
			}
			$time = time ();
			$uid = $this->user->getUid ();
			$msite ['update_time'] = $time;
			$msite ['update_uid'] = $uid;
			$id = $msite ['id'];
			$topics = $msite ['topics'];
			$theme = $msite ['theme'];
			$mtheme = rqst('mtheme');
			unset ( $msite ['id'], $msite ['topics'], $msite ['theme'] );
			if ($topics) {
				$topics = '<' . implode ( '><', $topics ) . '>';
			} else {
				$topics = '';
			}
			$msite ['topics'] = $topics;
			if (empty ( $id )) {
				$msite ['create_time'] = $time;
				$msite ['create_uid'] = $uid;
				$rst = dbinsert ( $msite )->into ( '{cms_msite}' )->exec ();
			} else {
				$rst = dbupdate ( '{cms_msite}' )->set ( $msite )->where ( array ('id' => $id ) )->exec ();
			}
			if ($rst) {
				set_cfg ( $msite ['domain'], $theme, 'msite_theme' );
				if($msite['mdomain']){
					set_cfg($msite['mdomain'],$mtheme,'msite_theme');
					set_cfg($msite['mdomain'],1,'msite_mdomain');
				}
				$oldm = rqst('oldm');
				$oldmd = rqst('oldmd');
				if($oldm && $oldm != $msite['domain']){
					set_cfg($oldm,null,'msite_theme');
				}
				if($oldmd && $oldmd != $msite['mdomain']){
					set_cfg($oldmd,null,'msite_theme');
					set_cfg($msite['mdomain'],null,'msite_mdomain');
				}
				RtCache::delete ( 'msite_sites' );
				return NuiAjaxView::click('#rtn2site','域名绑定到栏目成功,请尽快将域名绑定到服务器.');
			} else {
				return NuiAjaxView::error ( '保存出错啦:' . DatabaseDialect::$lastErrorMassge );
			}
		} else {
			return NuiAjaxView::validate ( 'MSiteForm', '表单验证出错', $form->getErrors () );
		}
	}
}
