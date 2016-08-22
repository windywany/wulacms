<?php
/**
 * 条件URL生成器.
 * @author leo
 *
 */
class ConditionURLFormater implements ArrayAccess {
	private $format = '';
	private $params = array ();
	private $pos = 0;
	private $connector = '-';
	private $vars = array ();
	/**
	 * 使用$format创建一个URL格式器.
	 *
	 * @param string $format
	 *        	格式字符,形如下:<br/>
	 *        	{param1}/{param2}/{param3}[-{$param4}]/{$param5}<br/>
	 *        	1. param1..5会使用对应的URLParamProvidor替换.替换完成后，系统将清除所有[-]这样的未取到值的参数组。<br/>
	 *        	2. - 为父子参数的分隔符.
	 * @param string $connector
	 *        	连字符.
	 */
	public function __construct($format, $connector = '-') {
		$this->format = $format;
		$this->connector = $connector;
	}
	public function __invoke($name, $provider, $pos = 0) {
		$this->setParamDataProvidor ( $name, $provider, $pos );
	}
	public function setParamDataProvidor($name, $provider, $pos = 0) {
		if ($pos == 0) {
			$pos = $this->pos;
			$this->pos ++;
		} else if ($pos > $this->pos) {
			$this->pos = $pos + 1;
		}
		if ($provider instanceof URLParamProvidor) {
			$this->params [$name] = array ('pos' => $pos,'provider' => $provider );
			$val = $provider->getVar ();
			$this->vars [$val] [] = $this->params [$name];
			usort ( $this->vars [$val], ArrayComparer::compare ( 'pos' ) );
		} else {
			return null;
		}
		uasort ( $this->params, ArrayComparer::compare ( 'pos' ) );
		return $provider;
	}
	public function url($param, $value = null) {
		static $templates = array (), $defaultProviders = false;
		if ($defaultProviders === false) {
			// 计算当前活动参数，如果都不活动则使用最后一个参数做为默认.
			foreach ( $this->vars as $var => $pds ) {
				$len = count ( $pds );
				for($i = 0; $i < $len; $i ++) {
					$pd = $pds [$i];
					$pd = $pd ['provider'];
					$value1 = $pd->getURLValue ();
					if ($value1) {
						$defaultProviders [$var] = $value1;
					}
					if ($pd->active) {
						break;
					}
				}
			}
		}
		if (! isset ( $this->params [$param] )) {
			return '#';
		}
		$currrentP = $this->params [$param];
		$paramVal = $currrentP ['provider']->getVar ();
		$realVal = '<' . str_replace ( array ('{','}' ), '', $paramVal ) . '>';
		if (! isset ( $templates [$param] )) {
			$url = $this->format;
			$search = array ();
			$replace = array ();
			$url = str_replace ( $paramVal, $realVal, $url );
			foreach ( $this->params as $p => $d ) {
				$pd = $d ['provider'];
				if (! $pd->available) {
					// 参数不可用，父级未选时
					continue;
				}
				$val = $d ['provider']->getVar ();
				if (! isset ( $defaultProviders [$val] )) {
					// 参数对应的变量未设置
					continue;
				}
				
				if ($param == $p) {
					continue;
				} else {
					$v = $defaultProviders [$val];
				}
				if ($v != null && ! in_array ( $val, $search )) {
					$search [] = $val;
					$replace [] = $v;
				}
			}
			if ($search) {
				$url = str_replace ( $search, $replace, $url );
			}
			$url = str_replace ( '[' . $this->connector . ']', '', $url );
			// 删除未设置参数
			$url = preg_replace ( '#\{[a-z][a-z0-9_]*\}#i', '', $url );
			$templates [$param] = $url;
		}
		$url = $templates [$param];
		
		if ($value !== null) {
			$url = str_replace ( $realVal, $currrentP ['provider']->getURLValue ( $value ), $url );
		} else {
			$url = str_replace ( $realVal, '', $url );
		}
		// 删除空参数
		$url = str_replace ( '[' . $this->connector . ']', '', $url );
		// 删除空参数
		$url = preg_replace ( '#\[([^\]]+)\]#', '\1', $url );
		// 合并删除参数后产生的多余/
		$url = preg_replace ( '#/+#', '/', trim($url,'/') );
		return $url;
	}
	public function offsetExists($offset) {
		return isset ( $this->params [$offset] );
	}
	public function offsetGet($offset) {
		return $this->params [$offset];
	}
	public function offsetSet($offset, $value) {
	}
	public function offsetUnset($offset) {
		unset ( $this->params [$offset] );
	}
}