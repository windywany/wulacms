<?php
class CmsPageSearcher implements ArrayAccess, IteratorAggregate {
	private $url;
	private $prefix;
	private $wheres = array ();
	private $model;
	private $suffix;
	private $currentPageNo = 1;
	private $properties = array ();
	private $result = null;
	public $sfields = array ();
	public $fields = array ();
	public $unfields = array ();
	public $sf = '';
	public $sd = '';
	private $qStr = '';
	public function __construct() {
		$router = Router::getRouter ();
		$this->currentPageNo = $router->getCurrentPageNo ();
		$this->url = $router->getParsedString ();
		self::parseURL ( $this->url, $this );
		$this->qStr = get_query_string ();
		// 开启查询时,关闭缓存.
		if ($this->qStr) {
			$router->setCurrentURL ( '' );
		}
	}
	/**
	 * 查询。
	 *
	 * @return CmsPage
	 */
	public function doSearch($options = array()) {
		// 搜索必须有后缀.
		if (empty ( $this->suffix )) {
			return null;
		}
		// 模型必须存在.
		$model = $options ['model'];
		
		$limit = intval ( $options ['limit'] );
		$limit = $limit ? $limit : 10;
		$spage ['searcher'] = false;
		$spage ['limit'] = $limit;
		if ($model) {
			$contentModel = get_page_content_model ( $model );
			// 加载可搜索字段
			$fields = dbselect ( 'id,type as widget,defaults,label,name,default_value AS `default`,cstore,sort' )->from ( '{cms_model_field}' )->where ( array ('model' => $model,'deleted' => 0,'searchable' => 1 ) )->asc ( 'sort' )->toArray ( null, 'id' );
			// 从自定义模型加载选择字段.
			if ($contentModel) {
				$fields = $contentModel->getSearchFields ( $fields );
			}
			if (empty ( $fields )) {
				return new CtsData ();
			}
			// 初始化搜索字段,用于加载搜索值.
			$widgets = CustomeFieldWidgetRegister::initWidgets ( $fields );
			usort ( $widgets, ArrayComparer::compare ( 'sort' ) );
			$titles = array ();
			foreach ( $widgets as $widget ) {
				if (! isset ( $widget ['id'] )) {
					continue;
				}
				$id = $widget ['id'];
				// 加载全部可搜索值.
				$widget ['values'] = $widget ['widget']->getDataProvidor ( $widget ['defaults'] )->getData ( true );
				// 出现在条件中.
				if (isset ( $this->wheres [$id] )) {
					$widget ['value'] = $this->wheres [$id];
					$widget ['valueText'] = $widget ['values'] [$this->wheres [$id]];
					if ($widget ['valueText']) {
						$titles [$id] = $widget ['valueText'];
						$lables [$id] = $widget ['label'];
					}
					$this->sfields [$id] = $widget;
				} else {
					$this->unfields [$id] = $widget;
				}
				$this->fields [$id] = $widget;
			}
			// 无可查询条件
			if (empty ( $this->fields )) {
				return new CtsData ();
			}
			$where ['CP.model'] = $model;
			$where ['CP.deleted'] = 0;
			$where ['CP.hidden'] = 0;
			// 将ctss的参数做为自定义的条件传给ContentModel的buildQuery方法.
			$remainWheres = $options;
			if ($this->wheres) {
				foreach ( $this->wheres as $id => $v ) {
					if (empty ( $this->fields [$id] ['cstore'] )) {
						$w = dbselect ( 'id' )->from ( '{cms_page_field} AS CPF' )->where ( array ('CP.id' => imv ( 'CPF.page_id' ),'CPF.deleted' => 0,'CPF.field_id' => $id,'CPF.val' => $v ) );
						$where ['@'] [] = $w;
					} else {
						$fd = $this->fields [$id];
						$fd ['value'] = $v;
						$remainWheres [$id] = $fd;
					}
				}
			}
			$pages = CmsPage::query ();
			$pages->limit ( $this->currentPageNo * $limit, $limit );
			
			if ($contentModel) {
				$contentModel->buildQuery ( $pages, $remainWheres, $this->sf, $this->sd );
			}
			
			$query = $pages;
			$totalCount = 0;
			$pages = array ();
			if ($query) {
				$query->where ( $where );
				$totalCount = $query->count ( 'CP.id' );
				if ($totalCount > 0) {
					foreach ( $fields as $id => $f ) {
						if (empty ( $f ['cstore'] )) {
							$query->field ( dbselect ( 'CPF.val' )->from ( '{cms_page_field} AS CPF' )->where ( array ('CP.id' => imv ( 'CPF.page_id' ),'CPF.deleted' => 0,'CPF.field_id' => $id ) ), $f ['name'] );
						}
					}
					$pages = $query->toArray ();
				}
			}
			
			if ($titles) {
				$spage ['titles'] = $titles;
				$spage ['labels'] = $lables;
			} else {
				$spage ['titles'] = array (0 => '全部的' );
				$spage ['labels'] = array (0 => '' );
			}
			$spage ['searcher'] = true;
			$spage ['total'] = $totalCount;
			$this->result = new CtsData ( $pages, $totalCount );
		} else {
			$spage ['total'] = 0;
			$this->result = new CtsData ();
		}
		$spage ['sort_field'] = $this->sf;
		$spage ['sort_order'] = $this->sd;
		$this->properties = $spage;
		return $this->result;
	}
	public function search($fid, $vid = null) {
		$wheres = $this->wheres;
		if (is_null ( $vid )) {
			unset ( $wheres [$fid] );
		} else if (is_numeric ( $fid )) {
			$wheres [$fid] = $vid;
		}
		$urls [] = $this->prefix;
		if ($wheres) {
			ksort ( $wheres );
			foreach ( $wheres as $id => $v ) {
				$urls [] = $id;
				$urls [] = $v;
			}
		}
		if ($this->sf) {
			$urls [] = $this->sf;
		}
		if ($this->sd) {
			$urls [] = $this->sd;
		}
		$url = implode ( '-', $urls ) . $this->suffix;
		return safe_url ( $url ) . $this->qStr;
	}
	public function sort($field, $order = 'a') {
		$urls [] = $this->prefix;
		foreach ( $this->wheres as $id => $v ) {
			$urls [] = $id;
			$urls [] = $v;
		}
		if ($field && $order) {
			$urls [] = $field;
			if ($field == $this->sf) {
				$urls [] = $this->sd == 'a' ? 'd' : 'a';
			} else {
				$urls [] = $order;
			}
		}
		$url = implode ( '-', $urls ) . $this->suffix;
		return safe_url ( $url ) . $this->qStr;
	}
	public static function parseURL($url, $parsedObj = null, $args = true) {
		if ($parsedObj == null) {
			$parsedObj = new stdClass ();
		}
		$parsedObj->url = $url;
		$parsedObj->suffix = strrchr ( $parsedObj->url, '.' );
		if (! $parsedObj->suffix) {
			$parsedObj->suffix = '';
		}
		$url = substr ( $parsedObj->url, 0, strlen ( $parsedObj->url ) - strlen ( $parsedObj->suffix ) );
		if (! $parsedObj->suffix) {
			$parsedObj->suffix = '.html';
		}
		$cons = explode ( '-', $url );
		$parsedObj->prefix = array_shift ( $cons );
		if ($args) {
			$len = count ( $cons );
			if ($len) {
				for($i = 0; $i < $len; $i ++) {
					$f = $cons [$i];
					if (is_numeric ( $f )) {
						$i += 1;
						$v = $cons [$i];
						$parsedObj->wheres [$f] = $v;
					} else if ($parsedObj->sf) {
						$parsedObj->sd = $f;
					} else {
						$parsedObj->sf = $f;
					}
				}
				if ($parsedObj->wheres) {
					ksort ( $parsedObj->wheres );
				}
			}
		}
		return $parsedObj;
	}
	/*
	 * (non-PHPdoc) @see ArrayAccess::offsetExists()
	 */
	public function offsetExists($offset) {
		return isset ( $this->properties [$offset] );
	}
	
	/*
	 * (non-PHPdoc) @see ArrayAccess::offsetGet()
	 */
	public function offsetGet($offset) {
		return $this->properties [$offset];
	}
	
	/*
	 * (non-PHPdoc) @see ArrayAccess::offsetSet()
	 */
	public function offsetSet($offset, $value) {
		$this->properties [$offset] = $value;
	}
	
	/*
	 * (non-PHPdoc) @see ArrayAccess::offsetUnset()
	 */
	public function offsetUnset($offset) {
		unset ( $this->properties [$offset] );
	}
	/*
	 * (non-PHPdoc) @see IteratorAggregate::getIterator()
	 */
	public function getIterator() {
		if ($this->result instanceof CtsData) {
			return $this->result->getIterator ();
		}
		return new ArrayIterator ( array () );
	}
}