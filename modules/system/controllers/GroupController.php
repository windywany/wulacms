<?php
/*
 * KissCms
 */
defined ( 'KISSGO' ) or exit ( 'No direct script access allowed' );
/**
 * 用户组管理.
 *
 * @author Guangfeng
 */
class GroupController extends Controller {
	protected $checkUser = true;
	protected $acls = array ('data' => 'r:account/usergroup','index' => 'r:account/usergroup','add' => 'c:account/usergroup','edit' => 'u:account/usergroup','save' => 'group_id|u:account/usergroup;c:account/usergroup','del' => 'd:account/usergroup' );
	/**
	 * 首页.
	 *
	 * @return SmartyView
	 */
	public function index($type = 'admin') {
		$group = dbselect ( '*' )->from ( '{user_group}' )->where ( array ('type' => $type ) )->asc ( 'type' );
		$data ['groups'] = $group;
		$data ['types'] = UserGroupForm::getGroupTypes ();
		$data ['hasDusergroup'] = icando ( 'd:account/usergroup' );
		$data ['type'] = $type;
		return view ( 'group/index.tpl', $data );
	}
	/**
	 * 新增.
	 *
	 * @param int $upid
	 *        	上级用户组.
	 * @return string
	 */
	public function add($upid = 0, $type = 'admin') {
		$data ['groups'] = array ('0' => '顶级用户组' );
		if($type){
			$where = array('type'=>$type);
		}else{
			$where = array();
		}
		dbselect ()->from ( '{user_group}' )->treeWhere($where)->treeOption ( $data ['groups'], 'group_id', 'upid', 'group_name' );
		$form = new UserGroupForm ( array ('group_id' => 0 ) );
		$data ['rules'] = $form->rules ();
		$data ['types'] = UserGroupForm::getGroupTypes ();
		if ($upid == 0) {
			$data ['type'] = $type;
		}
		return view ( 'group/form.tpl', $data );
	}
	/**
	 * 编辑.
	 *
	 * @param int $id
	 */
	public function edit($id) {
		$id = intval ( $id );
		$group = dbselect ( '*' )->from ( '{user_group}' )->where ( array ('group_id' => $id ) )->get ( 0 );
		if (empty ( $group )) {
			return NuiAjaxView::error ( '用户组不存在.' );
		} else {
			$group ['types'] = UserGroupForm::getGroupTypes ();
			$group ['groups'] = array ('0' => '顶级用户组' );
			$type = $group['type'];
			dbselect ()->from ( '{user_group}' )->treeWhere(array('type'=>$type))->treeOption ( $group ['groups'], 'group_id', 'upid', 'group_name', $group ['group_id'] );
			$form = new UserGroupForm ( $group );
			$group ['rules'] = $form->rules ();
			return view ( 'group/form.tpl', $group );
		}
	}
	/**
	 * 删除.
	 *
	 * @param int $id
	 * @return NuiAjaxView
	 */
	public function del($id) {
		$id = intval ( $id );
		if (dbselect ( 'group_id' )->from ( 'user_group' )->where ( array ('upid' => $id ) )->count ( 'group_id' ) > 0) {
			return NuiAjaxView::error ( '请先删除它的下级组,然后再删除本组.' );
		} else {
			$upid = dbselect ( 'upid' )->from ( 'user_group' )->where ( array ('group_id' => $id ) )->get ( 0, 'upid' );
			$rst = dbdelete ()->from ( '{user_group}' )->where ( array ('group_id' => $id ) )->exec ();
			if ($rst) {
				if ($upid) {
					// 取所有组数据
					$groups = dbselect ( 'upid,group_id' )->from ( '{user_group}' )->toArray ();
					// 遍历树形数据
					$iterator = new TreeIterator ( $groups, 0, 'group_id', 'upid' );
					$node = $iterator->getNode ( $upid );
					$nodes = array ($upid => $node );
					$node->getParents ( $nodes );
					unset ( $nodes ['0'], $nodes [0] );
					// 更新它们的subgroups
					foreach ( $nodes as $gid => $node ) {
						$ids = implode ( ',', $node->getSubIds () );
						dbupdate ( '{user_group}' )->set ( array ('subgroups' => $ids ) )->where ( array ('group_id' => $gid ) )->exec ();
					}
				}
				return NuiAjaxView::ok ( '用户组已经删除.', 'click', '#refresh' );
			} else {
				return NuiAjaxView::error ( '删除用户组出错啦:' . DatabaseDialect::$lastErrorMassge );
			}
		}
	}
	/**
	 * 保存用户组信息.
	 */
	public function save() {
		$form = new UserGroupForm ();
		$group = $form->valid ();
		if ($group) {
			if (empty ( $group ['upid'] )) {
				$group ['upid'] = 0;
			}
			if ($group ['upid']) {
				$type = dbselect ()->from ( '{user_group}' )->where ( array ('group_id' => $group ['upid'] ) )->get ( 'type' );
				if ($type != $group ['type']) {
					return NuiAjaxView::error ( '该组类型与上级组类型不相同，无法保存.' );
				}
			}
			if (empty ( $group ['group_id'] )) {
				unset ( $group ['group_id'] );
				$rst = dbinsert ( $group )->into ( '{user_group}' )->exec ();
				if ($rst) {
					$group_id = $rst [0];
					dbupdate ( '{user_group}' )->set ( array ('subgroups' => "{$group_id}" ) )->where ( array ('group_id' => $group_id ) )->exec ();
				}
			} else {
				$group_id = $group ['group_id'];
				unset ( $group ['group_id'] );
				$rst = dbupdate ( '{user_group}' )->set ( $group )->where ( array ('group_id' => $group_id ) )->exec ();
			}
			if ($rst) {
				// 更新用户组的子组
				$oupid = irqst ( 'oupid', 0 );
				if ($oupid != $group ['upid']) {
					// 取所有组数据
					$groups = dbselect ( 'upid,group_id' )->from ( '{user_group}' )->toArray ();
					// 遍历树形数据
					$iterator = new TreeIterator ( $groups, 0, 'group_id', 'upid' );
					$nodes = array ();
					// 新上级组
					$node = $iterator->getNode ( $group_id );
					$node->getParents ( $nodes );
					// 原上级组
					$nodes [$oupid] = $iterator->getNode ( $oupid );
					$nodes [$oupid]->getParents ( $nodes );
					unset ( $nodes ['0'], $nodes [0] );
					// 更新它们的subgroups
					foreach ( $nodes as $gid => $node ) {
						$ids = implode ( ',', $node->getSubIds () );
						dbupdate ( '{user_group}' )->set ( array ('subgroups' => $ids ) )->where ( array ('group_id' => $gid ) )->exec ();
					}
					// 取当前栏目
					$node = $iterator->getNode ( $group_id );
					// 取当前栏目的所有子栏目,因为他们的上级栏目发生了变化.
					$nodes = $node->getChildren ();
					$nodes [$group_id] = $node;
					foreach ( $nodes as $nid => $node ) {
						$parents = $node->getParentsIdList ( 'group_id' );
						if ($parents) {
							$parents = implode ( ',', $parents );
						} else {
							$parents = '';
						}
						dbupdate ( '{user_group}' )->set ( array ('parents' => $parents ) )->where ( array ('group_id' => $nid ) )->exec ();
					}
				}
				return NuiAjaxView::ok ( '成功保存用户组', 'click', '#btn-rtn-grp' );
			} else {
				return NuiAjaxView::error ( '保存用户组出错啦:' . DatabaseDialect::$lastErrorMassge );
			}
		} else {
			return NuiAjaxView::validate ( 'UserGroupForm', '保存用户组出错啦,数据校验失败!', $form->getErrors () );
		}
	}
}