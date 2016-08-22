<?php
/**
 * ajax调用.
 * @author Guangfeng
 *
 */
class AjaxController extends Controller {
	/**
	 * ajax 验证表单.
	 *
	 * @param AbstractForm $form
	 *        	表单类名
	 * @param string $method        	
	 * @param string $field        	
	 * @return string
	 */
	public function validate($form, $method, $field) {
		$form = str_replace('.', '\\', $form);//support namespace
		if (class_exists ( $form ) && is_subclass_of2 ( $form, 'AbstractForm' )) {
			$form = new $form ();
			$form->initValidateRules ();
			$args = $form->getCallbackArgs ( $method );
			$data = array ();
			foreach ( $args as $id => $v ) {
				$data [$id] = rqst ( $id );
			}
			$value = rqst ( $field );
			$rst = call_user_func_array ( array ($form,$method ), array ($value,$data,'false' ) );
			if ($rst === true) {
				return 'true';
			}
		}
		return 'false';
	}
	/**
	 * 动态表单验证.
	 *
	 * @param string $form        	
	 * @param string $method        	
	 * @param string $field        	
	 * @return string
	 */
	public function dyvalidate($form, $method, $field) {
		$form = new DynamicForm ( $form );
		$form->initValidateRules ();
		$args = $form->getCallbackArgs ( $method );
		$data = array ();
		foreach ( $args as $id => $v ) {
			$data [$id] = rqst ( $id );
		}
		$value = rqst ( $field );
		$rst = call_user_func_array ( array ($form,$method ), array ($value,$data,'false' ) );
		if ($rst === true) {
			return 'true';
		}
		return 'false';
	}
	/**
	 * 自动完成
	 *
	 * @param unknown $table        	
	 * @param unknown $id        	
	 * @param unknown $text        	
	 */
	public function autocomplete($table, $id, $text, $acl = '', $q = '', $_ut = 'admin', $_up = '') {
		$data ['more'] = false;
		$acl = $acl ? str_replace ( '.', '/', $acl ) : false;
		$this->user = whoami ( $_ut );
		$text2 = false;
		if ($table && $acl && icando ( $acl, $this->user )) {
			
			$id = empty ( $id ) ? 'id' : $id;
			$text = empty ( $text ) ? $id : $text;
			if ($id == $text) {
				$autoData = dbselect ( 'ATABLE.' . $id . ' AS id', 'ATABLE.' . $text . ' AS text' );
			} else {
				$texts = explode ( '-', $text );
				$text = $texts [0];
				$autoData = dbselect ( "ATABLE.$text as text,ATABLE.$id AS id" );
				if (isset ( $texts [1] ) && $texts [1]) {
					$text2 = $texts [1];
					$autoData->field ( 'ATABLE.' . $text2, $text2 );
				}
			}
			$autoData->from ( '{' . $table . '} AS ATABLE' )->limit ( 0, 10 );
			$where = array ();
			if ($q) {
				$where ['ATABLE.' . $text . ' LIKE'] = '%' . $q . '%';
			}
			$autoData = apply_filter ( 'on_init_autocomplete_condition_' . $table, $autoData );
			if ($_up) {
				$autoData = apply_filter ( 'on_init_autocomplete_condition_up_' . $_up, $autoData );
			}
			$autoData->where ( $where );
			
			$filterName = rqst ( 'filter', '' );
			if (empty ( $filterName )) {
				$data ['results'] = $autoData->toArray ( array (array ('id' => '','text' => '-请选择-' ) ) );
				if ($text2 && $data ['results']) {
					foreach ( $data ['results'] as $key => $r ) {
						$data ['results'] [$key] ['text'] = $data ['results'] [$key] ['text'] . '(' . $data ['results'] [$key] [$text2] . ')';
					}
				}
			} else {
				$dataFilter = apply_filter ( 'on_init_autocomplete_filter_' . $filterName, $autoData );
				$data ['results'] = $dataFilter;
			}
		} else {
			$data ['results'] = array ();
		}
		return new JsonView ( $data );
	}
	public function tpl($q = '', $_cp = 1, $n = '') {
		$data ['more'] = false;
		$tpls = array ();
		if ($n) {
			$tpls [] = array ('id' => '','text' => '请选择模板' );
		}
		if ($q) {
			$tpls [$q] = array ('id' => $q,'text' => $q );
		}
		
		$this->getTpls ( 'default', $tpls,$q );
		$theme = get_theme ();
		if ('default' != $theme) {
			$this->getTpls ( $theme, $tpls,$q );
		}
		$tpls = array_values ( $tpls );
		$data ['results'] = $tpls;
		return new JsonView ( $data );
	}
	public function treedata($table, $idf = 'id', $namef = 'name', $upidf = 'upid', $pid = 0, $cid = 0, $params = array()) {
		$data = dbselect ( $idf . ' AS id', $namef . ' AS name' )->from ( '{' . $table . '} AS TDM' );
		$where [$upidf] = $pid;
		if ($cid) {
			$where [$idf . ' <>'] = $cid;
		}
		$data->where ( $where );
		if ($params) {
			$data->where ( $params );
		}
		$cnt = dbselect ( imv ( 'COUNT(' . $idf . ')' ) )->from ( '{' . $table . '} AS TDC' )->where ( array ('TDC.' . $upidf => imv ( 'TDM.' . $idf ) ) );
		$data->field ( $cnt, 'total' );
		$items = array ();
		foreach ( $data as $item ) {
			$item ['isParent'] = $item ['total'] > 0;
			unset ( $item ['total'] );
			$items [] = $item;
		}
		usort ( $items, ArrayComparer::bool ( 'isParent' ) );
		return new JsonView ( $items );
	}
	private function getTpls($theme, &$tpls = array(),$q) {
		$dir = THEME_PATH . THEME_DIR . DS . $theme . DS;
		if (DS != '/') {
			$dir = str_replace ( DS, '/', $dir );
		}
		if($q){
			$files = find_files ( $dir, '#^'.$q.'.+\.tpl$#', array (), 1 );
		}else{
			$files = find_files ( $dir, '#^.+\.tpl$#', array (), 1 );
		}
		foreach ( $files as $f ) {
			$f1 = str_replace ( $dir, '', $f );
			$tpls [$f1] = array ('id' => $f1,'text' => $f1 );
		}
	}
}
