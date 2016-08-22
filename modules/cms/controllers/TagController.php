<?php
/**
 * 标签库.
 *
 * @author Guangfeng
 */
class TagController extends Controller {
	protected $checkUser = true;
	protected $acls = array ('update_dict' => 'dict:cms/tag','data' => 'r:cms/tag','index' => 'r:cms/tag','add' => 'c:cms/tag','edit' => 'u:cms/tag','save' => 'id|u:cms/tag;c:cms/tag','del' => 'd:cms/tag','topic2tag' => 'id|u:cms/tag;c:cms/tag' );
	public function index() {
		$data = array ();
		$data ['canDelTag'] = icando ( 'd:cms/tag' );
		$data ['canAddTag'] = icando ( 'c:cms/tag' );
		$data ['canUpdateDict'] = icando ( 'dict:cms/tag' );
		return view ( 'tag/index.tpl', $data );
	}
	public function add($id) {
		$data = array ();
		$form = new TagForm ( array ('id' => 0 ) );
		$data ['rules'] = $form->rules ();
		return view ( 'tag/form.tpl', $data );
	}
	/**
	 * 将专题加入内链库.
	 *
	 * @param number $id        	
	 * @return NuiAjaxView
	 */
	public function topic2tag($id = 0) {
		$rst = $this->_add2tag ( $id );
		
		if ($rst === true) {
			return NuiAjaxView::ok ( '内链添加成功' );
		} else {
			return NuiAjaxView::error ( $rst );
		}
	}
	public function topic2tags($ids) {
		$ids = safe_ids ( $ids, ',', true );
		$count = count ( $ids );
		if ($count > 0) {
			$idx = array ();
			foreach ( $ids as $id ) {
				if ($this->_add2tag ( $id ) === true) {
					$count --;
				} else {
					$idx [] = $id;
				}
			}
			if ($count == 0) {
				return NuiAjaxView::ok ( '所选页面全部添加到内链库.' );
			} else {
				return NuiAjaxView::ok ( '部分页面已添加到内链库.以下页面未能加入(无短标题或标签已经存在)：' . implode ( ',', $idx ) );
			}
		} else {
			return NuiAjaxView::error ( '未选择要加入内链的页面.' );
		}
	}
	public function update_dict() {
		TagForm::generateScwsDictFile ();
		return NuiAjaxView::ok ( '字典已经更新.' );
	}
	public function edit($id) {
		$id = intval ( $id );
		if (! $id) {
			Response::showErrorMsg ( '非法的编号.', 403 );
		}
		$tag = dbselect ( '*' )->from ( '{cms_tag}' )->where ( array ('id' => $id ) );
		if ($tag [0]) {
			$data = $tag [0];
			$form = new TagForm ( $data );
			$data ['rules'] = $form->rules ();
			return view ( 'tag/form.tpl', $data );
		} else {
			Response::showErrorMsg ( '区块不存在.', 404 );
		}
	}
	public function del($ids) {
		$ids = safe_ids ( $ids, ',', true );
		if (! empty ( $ids )) {
			$data ['deleted'] = 1;
			$data ['update_time'] = time ();
			$data ['update_uid'] = $this->user->getUid ();
			if (dbupdate ( '{cms_tag}' )->set ( $data )->where ( array ('id IN' => $ids ) )->exec ()) {
				$recycle = new DefaultRecycle ( $ids, 'Tag', 'cms_tag', 'ID:{id};内链接:{tag}' );
				RecycleHelper::recycle ( $recycle );
				return NuiAjaxView::reload ( '#tag-table', '已删除.' );
			} else {
				return NuiAjaxView::error ( '数据库操作失败.' );
			}
		} else {
			Response::showErrorMsg ( '错误的编号', 500 );
		}
	}
	public function save() {
		$form = new TagForm ();
		$tag = $form->valid ();
		if ($tag) {
			$time = time ();
			$uid = $this->user->getUid ();
			$tag ['update_time'] = $time;
			$tag ['update_uid'] = $uid;
			
			if (empty ( $tag ['title'] )) {
				$tag ['title'] = $tag ['tag'];
			}
			if (empty ( $tag ['id'] )) {
				unset ( $tag ['id'] );
				$tag ['create_time'] = $time;
				$tag ['create_uid'] = $uid;
				$tag ['deleted'] = 0;
				$rst = dbinsert ( $tag )->into ( '{cms_tag}' )->exec ();
			} else {
				$id = $tag ['id'];
				unset ( $tag ['id'] );
				$rst = dbupdate ( '{cms_tag}' )->set ( $tag )->where ( array ('id' => $id ) )->exec ();
			}
			if ($rst) {
				return NuiAjaxView::click ( '#rtn2tag', '保存成功' );
			} else {
				return NuiAjaxView::error ( '保存出错啦:' . DatabaseDialect::$lastErrorMassge );
			}
		} else {
			return NuiAjaxView::validate ( 'TagForm', '表单验证出错,请重新填写表单.', $form->getErrors () );
		}
	}
	public function data($_cp = 1, $_lt = 20, $_sf = 'id', $_od = 'd', $_ct = 0) {
		$data = array ();
		$data ['canEditTag'] = icando ( 'u:cms/tag' );
		$data ['canDelTag'] = icando ( 'd:cms/tag' );
		
		$rows = dbselect ( '*' )->from ( '{cms_tag}' );
		$rows->sort ( $_sf, $_od );
		$rows->limit ( ($_cp - 1) * $_lt, $_lt );
		$tag = rqst ( 'tag' );
		
		$where = array ('deleted' => 0 );
		if ($tag) {
			$where ['tag LIKE'] = "%{$tag}%";
		}
		$rows->where ( $where );
		$total = '';
		if ($_ct) {
			$total = $rows->count ( 'id' );
		}
		$data ['total'] = $total;
		$data ['rows'] = $rows;
		return view ( 'tag/data.tpl', $data );
	}
	private function _add2tag($id) {
		$topic = dbselect ( 'title2 as tag,url' )->from ( '{cms_page}' )->where ( array ('id' => intval ( $id ) ) )->get ();
		if ($topic && $topic ['tag']) {
			$tag = dbselect ( 'id,deleted' )->from ( 'cms_tag' )->where ( array ('tag' => $topic ['tag'] ) )->get ();
			$time = time ();
			$uid = $this->user->getUid ();
			$topic ['update_time'] = $time;
			$topic ['update_uid'] = $uid;
			$topic ['deleted'] = 0;
			if ($tag && $tag ['deleted']) {
				unset ( $topic ['tag'] );
				dbupdate ( '{cms_tag}' )->set ( $topic )->where ( array ('id' => $tag ['id'] ) )->exec ();
			} else if (! $tag) {
				$topic ['create_time'] = $time;
				$topic ['create_uid'] = $uid;
				$topic ['title'] = $topic ['tag'];
				dbinsert ( $topic )->into ( '{cms_tag}' )->exec ();
			}
			return true;
		} else if (empty ( $topic ['tag'] )) {
			return '页面短标题不存在,无法加入内链库。';
		} else {
			return '页面不存在,无法加入内链库。';
		}
	}
}