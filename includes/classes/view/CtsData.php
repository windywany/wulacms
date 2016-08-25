<?php
/**
 * 数据提供器可以提供的结果.
 */
class CtsData implements IteratorAggregate, Countable, ArrayAccess {
	protected $data = array ();
	protected $total = 0;
	protected $countTotal = 0;
	protected $dataType;
	protected $hasMore = false;
	public function __construct($data = array(), $countTotal = null) {
		$this->initData ( $data, $countTotal );
	}
	protected function initData($data, $countTotal) {
		$this->data = $data;
		if (is_array ( $data )) {
			$this->total = count ( $data );
			if ($this->total > 0 && ! isset ( $data [0] )) {
				$this->dataType = 's';
			}
		}
		$this->countTotal = $countTotal;
	}
	public function offsetExists($offset) {
		if (is_numeric ( $offset )) {
			return isset ( $this->data [$offset] );
		}
		return false;
	}
	public function offsetGet($offset) {
		if (is_numeric ( $offset ) || empty ( $offset )) {
			if (empty ( $offset )) {
				$offset = 0;
			}
			return $this->data [$offset];
		} else if ($offset == 'total') {
			return $this->total;
		} else if ($offset == 'data') {
			return $this->data;
		} else if ($offset == 'size') {
			return $this->countTotal;
		} else if ($offset == 'hasMore') {
			return $this->hasMore ? true : false;
		}
		return '';
	}
	public function offsetSet($offset, $value) {
		if ($offset == 'hasMore') {
			$this->hasMore = $value;
		}
	}
	public function offsetUnset($offset) {
	}
	
	/**
	 * 取用于ctv标签的数据.
	 *
	 * @return mixed
	 */
	public function getData() {
		if ($this->dataType == 's') {
			return $this->data;
		} else if ($this->total > 0) {
			return $this->data [0];
		}
		return array ();
	}
	public function toArray() {
		return $this->data;
	}
	public function getCountTotal() {
		return $this->countTotal;
	}
	public function total() {
		return $this->total;
	}
	/*
	 * (non-PHPdoc) @see IteratorAggregate::getIterator()
	 */
	public function getIterator() {
		if ($this->dataType == 's') {
			return new ArrayIterator ( array ($this->data ) );
		} else {
			return new ArrayIterator ( $this->data );
		}
	}
	/**
	 * 绘制分页.
	 *
	 * @param string $render        	
	 * @param array $options        	
	 * @return array
	 */
	public final function getRenderData($render, $options) {
		$router = Router::getRouter ();
		$_current_page = $router->getCurrentPageNo ();
		$_current_page = $_current_page == null ? 1 : $_current_page + 1;
		if (isset ( $options ['url'] )) {
			$url_info = $router->getUrlInfo ( $options ['url'] );
		} else {
			$url_info = $router->getUrlInfo ();
		}
		if (is_null ( $this->countTotal )) {
			$this->countTotal = count ( $this->data );
		}
		if ($this->countTotal > 0) {
			$paging = array ('orgin' => DETECTED_URL . $url_info ['orgin'],'prefix' => DETECTED_URL . $url_info ['prefix'],'current' => $_current_page,'total' => $this->countTotal,'ext' => $url_info ['suffix'] );
			$paging_data = apply_filter ( 'on_render_paging_by_' . $render, array (), $paging, $options );
			if (empty ( $paging_data )) {
				$paging_data = $this->getPageInfo ( $paging, $options );
			} else if (is_array ( $paging_data )) {
				$paging_data = array_merge2 ( array ('total' => ceil ( $this->countTotal / $this->per ),'ctotal' => $this->countTotal,'first' => '#','prev' => '#','next' => '#','last' => '#' ), $paging_data );
			}
			return $paging_data;
		} else {
			return array ();
		}
	}
	
	/**
	 * 取分页数据.
	 *
	 * @param array $paging        	
	 * @param array $args        	
	 */
	private function getPageInfo($paging, $args) {
		$url = $paging ['prefix'] . '_';
		$cur = $paging ['current'];
		$total = $paging ['total'];
		$per = isset ( $args ['limit'] ) ? intval ( $args ['limit'] ) : 10;
		if (! $per) {
			$per = 10;
		}
		$qstr = get_query_string ();
		$ext = $paging ['ext'] . $qstr;
		$tp = ceil ( $total / $per ); // 一共有多少页
		$pager = array ();
		if ($tp < 2) {
			return $pager;
		}
		$pager ['total'] = $tp;
		$pager ['ctotal'] = $total;
		if ($cur == 1) { // 当前在第一页
			$pager ['first'] = '#';
			$pager ['prev'] = '#';
		} else {
			$pager ['first'] = $paging ['orgin'] . $qstr;
			$pager ['prev'] = $cur == 2 ? $paging ['orgin'] . $qstr : $url . ($cur - 1) . $ext;
		}
		// 向前后各多少页
		$pp = isset ( $args ['pp'] ) ? intval ( $args ['pp'] ) : 10;
		$sp = $pp % 2 == 0 ? $pp / 2 : ($pp - 1) / 2;
		if ($cur <= $sp) {
			$start = 1;
			$end = $pp;
			$end = $end > $tp ? $tp : $end;
		} else {
			$start = $cur - $sp;
			$end = $cur + $sp;
			if ($pp % 2 == 0) {
				$end -= 1;
			}
			if ($end >= $tp) {
				$start -= ($end - $tp);
				$start > 0 or $start = 1;
				$end = $tp;
			}
		}
		for($i = $start; $i <= $end; $i ++) {
			if ($i == $cur) {
				$pager [$i] = '#';
			} else if ($i == 1) {
				if (preg_match ( '#(.*/)index_#', $url ) && preg_match ( '#^\.s?html?$#', $paging ['ext'] )) {
					$pager [$i] = preg_replace ( '#(.*)/index_#', '\1', $url ) . $qstr;
				} else {
					$pager [$i] = $paging ['orgin'] . $qstr;
				}
			} else {
				$pager [$i] = $url . $i . $ext;
			}
		}
		if ($cur == $tp) {
			$pager ['next'] = '#';
			$pager ['last'] = '#';
		} else {
			$pager ['next'] = $url . ($cur + 1) . $ext;
			$pager ['last'] = $url . $tp . $ext;
		}
		return $pager;
	}
	/*
	 * (non-PHPdoc) @see Countable::count()
	 */
	public function count() {
		return $this->total;
	}
}